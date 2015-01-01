<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Paginator\Adapter;

use Zend\Db\Sql\Where;
use Zend\Db\Sql\Having;
use Zend\Db\TableGateway\TableGateway;

class DbTableGateway extends DbSelect
{
    /**
     * Constructs instance.
     *
     * @param TableGateway                      $tableGateway
     * @param null|Where|\Closure|string|array  $where
     * @param null|string|array                 $order
     * @param null|string|array                 $group
     * @param null|Having|\Closure|string|array $having
     */
    public function __construct(TableGateway $tableGateway, $where = null, $order = null, $group = null, $having = null)
    {
        $sql    = $tableGateway->getSql();
        $select = $sql->select();
        if ($where) {
            $select->where($where);
        }
        if ($order) {
            $select->order($order);
        }
        if ($group) {
            $select->group($group);
        }
        if ($having) {
            $select->having($having);
        }

        $resultSetPrototype = $tableGateway->getResultSetPrototype();
        parent::__construct($select, $sql, $resultSetPrototype);
    }
}
