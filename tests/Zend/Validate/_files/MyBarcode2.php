<?php
class MyBarcode2 extends Zend_Validate_Barcode_AdapterAbstract
{
    protected $_length     = array(1,3,6);
    protected $_characters = 128;
    protected $_checksum   = '_mod10';
}
?>