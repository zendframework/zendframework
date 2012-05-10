<?php
namespace ZendTest\Log\TestAsset;

use Zend\Db\Adapter\Adapter as DbAdapter;

class MockDbAdapter extends DbAdapter
{
    public $plaftorm;
    public $driver;
    
    public $calls = array();

    public function __call($method, $params)
    {
        $this->calls[$method][] = $params;
    }
    
    public function __construct()
    {
        $this->platform = new MockDbPlatform;
        $this->driver = new MockDbDriver;
        
    }
    public function query($sql, $parametersOrQueryMode = DbAdapter::QUERY_MODE_PREPARE)
    {
        $this->calls[__FUNCTION__][] = $sql;
        return $this;
    }
}
