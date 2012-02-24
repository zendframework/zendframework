<?php

namespace Zend\Db\Adapter\Driver;

interface ConnectionInterface
{
    public function getDefaultCatalog();
    public function getDefaultSchema();
    public function getResource();
    public function connect();
    public function isConnected();
    public function disconnect();
    public function beginTransaction();
    public function commit();
    public function rollback();
    public function execute($sql); // return result set

    public function getLastGeneratedId();
    
    /**
     * @return \Zend\Db\Adapter\DriverStatementInterface
     */
    public function prepare($sql); // must return StatementInterface object
}
