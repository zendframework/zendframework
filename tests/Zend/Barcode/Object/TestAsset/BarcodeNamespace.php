<?php
namespace ZendTest\Barcode\Object\TestAsset;

class BarcodeNamespace extends \Zend\Barcode\Object\Error
{
    public function getType()
    {
        return $this->type;
    }
}
