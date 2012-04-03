<?php
namespace ZendTest\Log\TestAsset;

class MockDbDriver
{

    public function __call($method, $params)
    {
        $this->calls[$method][] = $params;
    }
    
}
