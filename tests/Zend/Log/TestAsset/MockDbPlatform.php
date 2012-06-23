<?php
namespace ZendTest\Log\TestAsset;

class MockDbPlatform
{

    public function __call($method, $params)
    {
        $this->calls[$method][] = $params;
    }
    
}
