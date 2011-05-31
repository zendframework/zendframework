<?php
namespace ZendTest\Di\TestAsset;

class OptionalArg
{
    public function __construct($param = null)
    {
        $this->param = $param;
    }

    public function inject($param1 = null, $param2 = null)
    {
    }
}

