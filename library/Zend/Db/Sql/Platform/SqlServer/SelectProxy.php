<?php

namespace Zend\Db\Sql\Platform\SqlServer;

use Zend\Db\Sql\Select,
    Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\Driver\StatementInterface,
    Zend\Db\Adapter\Platform\PlatformInterface,
    Zend\Db\Adapter\ParameterContainerInterface;

class SelectProxy extends Select
{
    protected $select = null;
    public function __construct(Select $select)
    {
        $this->select = $select;
    }

    public function prepareStatement(Adapter $adapter, StatementInterface $statement)
    {
        foreach (get_object_vars($this->select) as $name => $value) {
            $this->{$name} = $value;
        }
        
        // set specifications
        unset($this->specifications[self::SPECIFICATION_LIMIT]);
        unset($this->specifications[self::SPECIFICATION_OFFSET]);

        $this->specifications['LIMITOFFSET'] = null;
        parent::prepareStatement($adapter, $statement);
    }

    protected function processLimitOffset(PlatformInterface $platform, Adapter $adapter = null, ParameterContainerInterface $parameterContainer = null, &$sqls, &$parameters)
    {
        if ($this->limit === null && $this->offset === null) {
            return null;
        }
        
        $driver = $adapter->getDriver();
        
        
        $selectParameters = $parameters[self::SPECIFICATION_SELECT];
        
        $isStar = false;
        $starSuffix = $platform->getIdentifierSeparator() . self::SQL_STAR;
        foreach ($selectParameters[0] as $i => $columnParameters) {
            if ($columnParameters[0] == self::SQL_STAR || (isset($columnParameters[1]) && $columnParameters[1] == self::SQL_STAR) || strpos($columnParameters[0], $starSuffix)) {
                $isStar = true;
                $selectParameters[0] = array(array(self::SQL_STAR));
                break;
            }
            if (isset($columnParameters[1])) {
                array_shift($columnParameters);
                $selectParameters[0][$i] = $columnParameters;
            } 
        }

        // first, produce column list without compound names (using the AS portion only)
        array_unshift($sqls, $this->createSqlFromSpecificationAndParameters(
                array('SELECT %1$s FROM (' => current($this->specifications[self::SPECIFICATION_SELECT])),
                $selectParameters
        ));
        
        // create bottom part of query, with offset and limit using row_number
        array_push($sqls, ') AS [ZEND_SQL_SERVER_LIMIT_OFFSET_EMULATION] WHERE [ZEND_SQL_SERVER_LIMIT_OFFSET_EMULATION].[__ZEND_ROW_NUMBER] BETWEEN ?+1 AND ?+?');

        // add a column for row_number() using the order specification
        $parameters[self::SPECIFICATION_SELECT][0][] = array('ROW_NUMBER() OVER (' . $sqls[self::SPECIFICATION_ORDER] . ')', '[__ZEND_ROW_NUMBER]');
        
        unset($sqls[self::SPECIFICATION_ORDER]);
        
        $sqls[self::SPECIFICATION_SELECT] = $this->createSqlFromSpecificationAndParameters(
            $this->specifications[self::SPECIFICATION_SELECT],
            $parameters[self::SPECIFICATION_SELECT]
        );
    
        $parameterContainer->offsetSet('offset', $this->offset);
        $parameterContainer->offsetSet('limit', $this->limit);
        $parameterContainer->offsetSetReference('offsetForSum', 'offset');
    }

}