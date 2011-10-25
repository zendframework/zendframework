<?php
namespace Zend\Validator\Barcode;
class MyBarcode4 extends \Zend\Validator\Barcode\AbstractAdapter
{
    public function __construct()
    {
        $this->setLength('odd');
        $this->setCharacters(128);
        $this->setChecksum('_mod10');
    }
}
