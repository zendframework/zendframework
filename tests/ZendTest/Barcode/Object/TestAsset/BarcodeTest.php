<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Barcode
 */

namespace ZendTest\Barcode\Object\TestAsset;

/**
 * @category   Zend
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @group      Zend_Barcode
 */
class BarcodeTest extends \Zend\Barcode\Object\AbstractObject
{

    protected function calculateBarcodeWidth()
    {
        return 1;
    }

    public function validateText($value)
    {}

    protected function prepareBarcode()
    {
        return array();
    }

    protected function checkSpecificParams()
    {}

    public function addTestInstruction(array $instruction)
    {
        $this->addInstruction($instruction);
    }

    public function addTestPolygon(array $points, $color = null, $filled = true)
    {
        $this->addPolygon($points, $color, $filled);
    }

    public function addTestText($text, $size, $position, $font, $color, $alignment = 'center', $orientation = 0)
    {
        $this->addText($text, $size, $position, $font, $color, $alignment, $orientation);
    }
}
