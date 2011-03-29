<?php
namespace ZendTest\Log\TestAsset;

class StringObject
{
    public function __toString()
    {
        return 'Hello World';
    }
}