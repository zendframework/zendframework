<?php

namespace Zend\Db\Table\Feature;

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
