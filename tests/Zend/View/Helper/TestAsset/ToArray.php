<?php

namespace ZendTest\View\Helper\TestAsset;

class ToArray
{
    public $array = array();

    public function toArray()
    {
        return $this->array;
    }
}
