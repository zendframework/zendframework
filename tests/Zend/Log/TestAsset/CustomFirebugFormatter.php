<?php
namespace ZendTest\Log\TestAsset;

use Zend\Log\Formatter\Firebug as FirebugFormatter;

class CustomFirebugFormatter extends FirebugFormatter
{
    public function format($event)
    {
        return $event['testLabel'] . ' : ' . $event['message'];
    }
}