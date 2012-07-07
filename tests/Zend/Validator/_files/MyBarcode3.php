<?php
namespace Zend\Validator\Barcode;

use Zend\Validator\Barcode\AbstractAdapter;

class MyBarcode3 extends AbstractAdapter
{
    public function __construct()
    {
        $this->setLength(array(1, 3, 6, -1));
        $this->setCharacters(128);
        $this->setChecksum('_mod10');
    }
}
