<?php
namespace Zend\Validator\Barcode;
class MyBarcode1 extends \Zend\Validator\Barcode\AbstractAdapter
{
    protected $_length     = -1;
    protected $_characters = 0;
    protected $_checksum   = 'invalid';
}
