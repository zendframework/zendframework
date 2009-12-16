<?php
class MyBarcode1 extends Zend_Validate_Barcode_AdapterAbstract
{
    protected $_length     = -1;
    protected $_characters = 0;
    protected $_checksum   = 'invalid';
}
?>