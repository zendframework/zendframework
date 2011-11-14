<?php
namespace ZendTest\Barcode\Object\TestAsset;

class Error extends \Zend\Barcode\Object\Error
{
    public function getType()
    {
        return $this->type;
    }
}
