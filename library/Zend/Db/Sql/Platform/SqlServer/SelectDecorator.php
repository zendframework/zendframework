<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\Sql\Platform\SqlServer;

use Zend\Db\Sql\Select,
    Zend\Db\Sql\Platform\PlatformDecoratorInterface,
    Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\Driver\StatementInterface,
    Zend\Db\Adapter\Platform\PlatformInterface,
    Zend\Db\Adapter\ParameterContainer;

class SelectDecorator extends Select implements PlatformDecoratorInterface
{
    /**
     * @var Select
     */
    protected $select = null;

    /**
     * @param Select $select
     */
    public function setSubject($select)
    {
        $this->select = $select;
    }

    /**
     * @param Adapter $adapter
     * @param StatementInterface $statement
     */
    public function prepareStatement(Adapter $adapter, StatementInterface $statement)
    {
        // localize variables
        foreach (get_object_vars($this->select) as $name => $value) {
            $this->{$name} = $value;
        }

        // set specifications
        unset($this->specifications[self::SPECIFICATION_LIMIT]);
        unset($this->specifications[self::SPECIFICATION_OFFSET]);

        $this->specifications['LIMITOFFSET'] = null;
        parent::prepareStatement($adapter, $statement);
    }

    /**
     * @param Adapter $adapter
     * @param StatementInterface $statement
     */
    public function getSqlString(PlatformInterface $platform = null)
    {
        // localize variables
        foreach (get_object_vars($this->select) as $name => $value) {
            $this->{$name} = $value;
        }

        // set specifications
        unset($this->specifications[self::SPECIFICATION_LIMIT]);
        unset($this->specifications[self::SPECIFICATION_OFFSET]);

        $this->specifications['LIMITOFFSET'] = null;
        return parent::getSqlString($platform);
    }

    /**
     * @param PlatformInterface $platform
     * @param Adapter $adapter
     * @param ParameterContainer $parameterContainer
     * @param $sqls
     * @param $parameters
     * @return null
     */
    protected function processLimitOffset(PlatformInterface $platform, Adapter $adapter = null, ParameterContainer $parameterContainer = null, &$sqls, &$parameters)
    {
        if ($this->limit === null && $this->offset === null) {
            return null;
        }

        $selectParameters = $parameters[self::SPECIFICATION_SELECT];

        $starSuffix = $platform->getIdentifierSeparator() . self::SQL_STAR;
        foreach ($selectParameters[0] as $i => $columnParameters) {
            if ($columnParameters[0] == self::SQL_STAR || (isset($columnParameters[1]) && $columnParameters[1] == self::SQL_STAR) || strpos($columnParameters[0], $starSuffix)) {
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

        if ($parameterContainer) {
            // create bottom part of query, with offset and limit using row_number
            array_push($sqls, ') AS [ZEND_SQL_SERVER_LIMIT_OFFSET_EMULATION] WHERE [ZEND_SQL_SERVER_LIMIT_OFFSET_EMULATION].[__ZEND_ROW_NUMBER] BETWEEN ?+1 AND ?+?');
            $parameterContainer->offsetSet('offset', $this->offset);
            $parameterContainer->offsetSet('limit', $this->limit);
            $parameterContainer->offsetSetReference('offsetForSum', 'offset');
        } else {
            array_push($sqls, ') AS [ZEND_SQL_SERVER_LIMIT_OFFSET_EMULATION] WHERE [ZEND_SQL_SERVER_LIMIT_OFFSET_EMULATION].[__ZEND_ROW_NUMBER] BETWEEN '
                . (int) $this->offset . '+1 AND '
                . (int) $this->limit . '+' . (int) $this->offset
            );
        }

        if (isset($sqls[self::SPECIFICATION_ORDER])) {
            $orderBy = $sqls[self::SPECIFICATION_ORDER];
            unset($sqls[self::SPECIFICATION_ORDER]);
        } else {
            $orderBy = 'SELECT 1';
        }

        // add a column for row_number() using the order specification
        $parameters[self::SPECIFICATION_SELECT][0][] = array('ROW_NUMBER() OVER (' . $orderBy . ')', '[__ZEND_ROW_NUMBER]');

        $sqls[self::SPECIFICATION_SELECT] = $this->createSqlFromSpecificationAndParameters(
            $this->specifications[self::SPECIFICATION_SELECT],
            $parameters[self::SPECIFICATION_SELECT]
        );

    }

}
