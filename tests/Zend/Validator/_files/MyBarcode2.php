<?php
namespace Zend\Validator\Barcode;
class MyBarcode2 extends \Zend\Validator\Barcode\AbstractAdapter
{
    protected $_length     = array(1,3,6);
    protected $_characters = 128;
    protected $_checksum   = '_mod10';
}
