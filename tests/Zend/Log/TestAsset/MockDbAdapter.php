<?php
namespace ZendTest\Log\TestAsset;

class MockDbAdapter
{
    public $calls = array();

    public function __call($method, $params)
    {
        $this->calls[$method][] = $params;
    }
}