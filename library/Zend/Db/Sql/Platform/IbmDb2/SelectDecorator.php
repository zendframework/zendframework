<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Sql\Platform\IbmDb2;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Driver\DriverInterface;
use Zend\Db\Adapter\ParameterContainer;
use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\Adapter\StatementContainerInterface;
use Zend\Db\Sql\Platform\PlatformDecoratorInterface;
use Zend\Db\Sql\Select;

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
     * @param AdapterInterface            $adapter
     * @param StatementContainerInterface $statementContainer
     */
    public function prepareStatement(AdapterInterface $adapter, StatementContainerInterface $statementContainer)
    {
        // localize variables
        foreach (get_object_vars($this->select) as $name => $value) {
            $this->{$name} = $value;
        }
        // set specifications
        unset($this->specifications[self::LIMIT]);
        unset($this->specifications[self::OFFSET]);

        $this->specifications['LIMITOFFSET'] = null;
        parent::prepareStatement($adapter, $statementContainer);
    }

    /**
     * @param  PlatformInterface $platform
     * @return string
     */
    public function getSqlString(PlatformInterface $platform = null)
    {
        // localize variables
        foreach (get_object_vars($this->select) as $name => $value) {
            $this->{$name} = $value;
        }

        unset($this->specifications[self::LIMIT]);
        unset($this->specifications[self::OFFSET]);
        $this->specifications['LIMITOFFSET'] = null;

        return parent::getSqlString($platform);
    }

    /**
     * @param  PlatformInterface  $platform
     * @param  DriverInterface    $driver
     * @param  ParameterContainer $parameterContainer
     * @param $sqls
     * @param $parameters
     * @return null
     */

    protected function processLimitOffset(PlatformInterface $platform, DriverInterface $driver = null, ParameterContainer $parameterContainer = null, &$sqls, &$parameters)
    {
        if ($this->limit === null && $this->offset === null) {
            return null;
        }

        if ($parameterContainer) {
            if ((int) $this->offset > 0) {
                $parameterContainer->offsetSet('offset', (int) $this->offset);
            }
            $parameterContainer->offsetSet('limit', (int) $this->limit);
        }

           if (isset($sqls[self::ORDER])) {
            $orderBy = $sqls[self::ORDER];
            unset($sqls[self::ORDER]);
        } else {
            $orderBy = null;
        }

        if (preg_match('/DISTINCT/i',$sqls[self::SELECT],$match)) {
            $sqlSyntax = 'DENSE_RANK()';
        } else {
            $sqlSyntax = 'ROW_NUMBER()';
        }

        $offset = (int) $this->offset;
        $limit  = (int) $this->limit;

        if (empty($offset)) {
            $sqls[self::SELECT] .= " FETCH FIRST $limit ROW ONLY";
        } else {
            $sqls[self::SELECT] = sprintf(
                "SELECT z2.* FROM " .
                "(SELECT %s OVER(%s) AS \"ZEND_ROWNUM\", z1.* FROM (%s) z1) z2 " .
                "WHERE z2.ZEND_ROWNUM BETWEEN %d AND %d",
                $sqlSyntax, $orderBy, $sqls[self::SELECT], $offset, $limit + $offset
            );
        }
    }
}
