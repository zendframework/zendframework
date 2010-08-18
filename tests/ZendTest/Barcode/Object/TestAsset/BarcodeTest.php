<?php
namespace ZendTest\Barcode\Object\TestAsset;

class BarcodeTest extends \Zend\Barcode\Object\AbstractObject
{

    protected function _calculateBarcodeWidth()
    {
        return 1;
    }

    public function validateText($value)
    {}

    protected function _prepareBarcode()
    {
        return array();
    }

    protected function _checkParams()
    {}

    public function addInstruction(array $instruction)
    {
        $this->_addInstruction($instruction);
    }

    public function addPolygon(array $points, $color = null, $filled = true)
    {
        $this->_addPolygon($points, $color, $filled);
    }

    public function addText($text, $size, $position, $font, $color, $alignment = 'center', $orientation = 0)
    {
        $this->_addText($text, $size, $position, $font, $color, $alignment, $orientation);
    }
}
