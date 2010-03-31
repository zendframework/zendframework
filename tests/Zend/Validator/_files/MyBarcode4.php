<?php
class MyBarcode4 extends \Zend\Validator\Barcode\AdapterAbstract
{
    protected $_length     = 'odd';
    protected $_characters = 128;
    protected $_checksum   = '_mod10';
}
