<?php
require_once 'Zend/Barcode/Object/ObjectAbstract.php';

class Zend_Barcode_Object_Test extends Zend_Barcode_Object_ObjectAbstract
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
