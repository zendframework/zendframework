<?php
namespace ZendTest\Log\TestAsset;

use Zend\Log\Writer\AbstractWriter;

class ConcreteWriter extends AbstractWriter
{
    protected function doWrite(array $event)
    {
    }
}
