<?php
namespace Zend\Validator\Barcode;

class MyBarcode5
{
    public function __construct()
    {
        $setLength = 'odd';
        $setCharacters = 128;
        $setChecksum = '_mod10';
    }
}
