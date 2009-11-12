<?php

class Test_MyCompany1_Dbadapter extends Zend_Db_Adapter_Abstract
{
    protected function _connect()
    {}

    function _checkRequiredOptions(array $config)
    {}

    public function isConnected()
    {}

    public function closeConnection()
    {}

    public function prepare($sql)
    {}

    public function lastInsertId($tableName = null, $primaryKey = null)
    {}

    protected function _beginTransaction()
    {}

    protected function _commit()
    {}

    protected function _rollBack()
    {}

    public function listTables()
    {}

    public function describeTable($tableName, $schemaName = null)
    {}

    public function setFetchMode($mode)
    {}

    public function limit($sql, $count, $offset = 0)
    {}

    public function supportsParameters($type)
    {}

    public function getServerVersion()
    {}

}
