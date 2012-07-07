<?php

namespace ZendTest\Config\Writer\TestAssets;

class PhpReader
{
    public function fromFile($filename)
    {
        return include $filename;
    }
}
