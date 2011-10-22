<?php
namespace Zend\Validator\Barcode;
class MyBarcode3 extends \Zend\Validator\Barcode\AbstractAdapter
{
    public function __construct()
    {
        $this->setLength(array(1, 3, 6, -1));
        $this->setCharacters(128);
        $this->setChecksum('_mod10');
    }
}
