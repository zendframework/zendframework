<?php
namespace ZendTest\Log\TestAsset;

class MockDbAdapter 
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
    public function query($sql)
    {
        $this->calls[__FUNCTION__][] = $sql;
        return $this;
    }
}