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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Barcode\Object\TestAsset;

/**
 * @category   Zend
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @group      Zend_Barcode
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
