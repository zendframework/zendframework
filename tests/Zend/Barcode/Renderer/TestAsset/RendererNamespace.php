<?php
namespace ZendTest\Barcode\Renderer\TestAsset;

class RendererNamespace extends \Zend\Barcode\Renderer\Image
{
    public function getType()
    {
        return $this->type;
    }
}
