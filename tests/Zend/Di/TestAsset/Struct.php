<?php
namespace ZendTest\Di\TestAsset;

class Struct
{
    public function __construct($param1, $param2)
    {
        $this->param1 = $param1;
        $this->param2 = $param2;
    }
}
