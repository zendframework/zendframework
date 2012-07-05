<?php
namespace Zend\Validator\Barcode;

use Zend\Validator\Barcode\AbstractAdapter;

class MyBarcode4 extends AbstractAdapter
{
    public function __construct()
    {
        $this->setLength('odd');
        $this->setCharacters(128);
        $this->setChecksum('_mod10');
    }
}
