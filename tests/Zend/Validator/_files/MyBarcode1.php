<?php
namespace Zend\Validator\Barcode;

use Zend\Validator\Barcode\AbstractAdapter;

class MyBarcode1 extends AbstractAdapter
{
    public function __construct()
    {
        $this->setLength(-1);
        $this->setCharacters(0);
        $this->setChecksum('invalid');
    }
}
