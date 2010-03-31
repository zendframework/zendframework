<?php
class MyBarcode1 extends \Zend\Validator\Barcode\AdapterAbstract
{
    protected $_length     = -1;
    protected $_characters = 0;
    protected $_checksum   = 'invalid';
}
