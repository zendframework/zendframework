<?php
namespace Zend\Validator\Barcode;
class MyBarcode4 extends \Zend\Validator\Barcode\AbstractAdapter
{
    protected $_length     = 'odd';
    protected $_characters = 128;
    protected $_checksum   = '_mod10';
}
