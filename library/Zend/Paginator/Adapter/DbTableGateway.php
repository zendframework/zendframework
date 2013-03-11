<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Paginator\Adapter;

use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\TableGateway\TableGateway;

class DbTableGateway extends DbSelect
{
    /**
     * Construnct
     * 
     * @param TableGateway                $tableGateway
     * @param Where|\Closure|string|array $where
     */
    public function __construct(TableGateway $tableGateway, $where = null)
    {
        $select             = $tableGateway->getSql()->select($where);
        $dbAdapter          = $tableGateway->getAdapter();
        $resultSetPrototype = $tableGateway->getResultSetPrototype();
        
        parent::__construct($select, $dbAdapter, $resultSetPrototype);
    }
}
