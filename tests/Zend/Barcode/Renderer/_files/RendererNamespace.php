<?php
require_once 'Zend/Barcode/Renderer/Image.php';

class My_Namespace_Image extends Zend_Barcode_Renderer_Image
{

    public function getType()
    {
        return $this->_type;
    }
}