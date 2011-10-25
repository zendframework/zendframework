<?php
namespace Zend\Validator\Barcode;
class MyBarcode1 extends \Zend\Validator\Barcode\AbstractAdapter
{
    public function __construct()
    {
        $this->setLength(-1);
        $this->setCharacters(0);
        $this->setChecksum('invalid');
    }
}
