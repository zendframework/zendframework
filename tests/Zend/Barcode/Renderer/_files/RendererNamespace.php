<?php
namespace ZendTest\Barcode\Renderer\Namespace1;

class Image extends \Zend\Barcode\Renderer\Image
{
    public function getType()
    {
        return $this->_type;
    }
}
