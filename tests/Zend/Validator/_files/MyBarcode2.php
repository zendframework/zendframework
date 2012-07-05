<?php
namespace Zend\Validator\Barcode;

use Zend\Validator\Barcode\AbstractAdapter;

class MyBarcode2 extends AbstractAdapter
{
    public function __construct()
    {
        $this->setLength(array(1, 3, 6));
        $this->setCharacters(128);
        $this->setChecksum('_mod10');
    }
}
