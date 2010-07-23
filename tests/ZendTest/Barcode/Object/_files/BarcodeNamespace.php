<?php
namespace ZendTest\Barcode\Object\Namespace1;

class Error extends \Zend\Barcode\Object\Error
{
    public function getType()
    {
        return $this->_type;
    }
}
