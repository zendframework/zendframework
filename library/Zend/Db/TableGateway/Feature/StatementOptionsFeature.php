<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\TableGateway\Feature;

use Zend\Db\Adapter\Driver\StatementInterface;

class StatementOptionsFeature extends AbstractFeature
{

    public function preSelectExecute(StatementInterface $statement)
    {
        $adapter = $this->tableGateway->getAdapter();
        $platform = $adapter->getPlatform();
        $platformName = $platform->getName();

        switch ($platformName) {
            case "SQLServer":
                //Necessary for ResultSet count to work
                $statement->setScrollable(\SQLSRV_CURSOR_STATIC);
                break;
        }
    }

//    public function preInsertExecute(StatementInterface $statement){}
//    public function preUpdateExecute(StatementInterface $statement){}
//    public function preDeleteExecute(StatementInterface $statement){}
}
