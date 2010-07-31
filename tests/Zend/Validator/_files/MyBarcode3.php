<?php
namespace Zend\Validator\Barcode;
class MyBarcode3 extends \Zend\Validator\Barcode\AbstractAdapter
{
    protected $_length     = array(1,3,6, -1);
    protected $_characters = 128;
    protected $_checksum   = '_mod10';
}
