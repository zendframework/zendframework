<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Barcode\Object\TestAsset;

/**
 * @category   Zend
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @group      Zend_Barcode
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
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
