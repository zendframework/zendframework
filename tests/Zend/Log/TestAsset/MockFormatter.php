<?php
namespace ZendTest\Log\TestAsset;

use Zend\Log;

class MockFormatter implements Log\Formatter, Log\Factory
{
    public static function factory($config = array())
    {
        return new self;
    }

    public function format($event)
    {
    }
}