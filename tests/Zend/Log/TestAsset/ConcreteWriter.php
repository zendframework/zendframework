<?php
namespace ZendTest\Log\TestAsset;

use Zend\Log\Writer\AbstractWriter;

class ConcreteWriter extends AbstractWriter
{
    protected function _write($event)
    {
    }

    static public function factory($config = array())
    {
    }
}
