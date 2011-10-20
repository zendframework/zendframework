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
 * @version    $Id: Code25Test.php 21667 2010-03-28 17:45:14Z mikaelkael $
 */

/**
 * @namespace
 */
namespace ZendTest\Barcode\Object;
use Zend\Barcode;

/**
 * @category   Zend
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @group      Zend_Barcode
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Code128Test extends TestCommon
{

    protected function _getBarcodeObject($options = null)
    {
        return new Barcode\Object\Code128($options);
    }

    public function testType()
    {
        $this->assertSame('code128', $this->_object->getType());
    }

    public function testChecksum()
    {
        $this->assertSame(33, $this->_object->getChecksum('BarCode 1'));
    	$this->assertSame(47, $this->_object->getChecksum('CODE-128'));
        $this->assertSame(32, $this->_object->getChecksum('FRAMEWORK-ZEND-COM'));
    }

    public function testKnownBarcodeConversion()
    {
    	$barcode = new TestAsset\Code128Test();
    	$this->assertSame(array(104, 13, 17, 18, 19), $barcode->_convertToBarcodeChars(-123));
    	$this->assertSame(array(104, 40, 41, 99, 34, 56, 78), $barcode->_convertToBarcodeChars('HI345678'));
    }

    public function testSetText()
    {
        $this->_object->setText('0123456789');
        $this->assertSame('0123456789', $this->_object->getRawText());
        $this->assertSame('0123456789', $this->_object->getText());
        $this->assertSame('0123456789', $this->_object->getTextToDisplay());
    }

    public function testSetTextWithSpaces()
    {
        $this->_object->setText(' 0123456789 ');
        $this->assertSame(' 0123456789 ', $this->_object->getRawText());
        $this->assertSame(' 0123456789 ', $this->_object->getText());
        $this->assertSame(' 0123456789 ', $this->_object->getTextToDisplay());
    }

    public function testSetTextWithChecksum()
    {
        $this->_object->setText('0123456789');
        $this->_object->setWithChecksum(true);
        $this->assertSame('0123456789', $this->_object->getRawText());
        $this->assertSame('0123456789', $this->_object->getText());
        $this->assertSame('0123456789', $this->_object->getTextToDisplay());
    }

    public function testSetTextWithChecksumDisplayed()
    {
        $this->_object->setText('0123456789');
        $this->_object->setWithChecksum(true);
        $this->_object->setWithChecksumInText(true);
        $this->assertSame('0123456789', $this->_object->getRawText());
        $this->assertSame('0123456789', $this->_object->getText());
        $this->assertSame('0123456789', $this->_object->getTextToDisplay());
    }

    public function testGetKnownWidthWithoutOrientation()
    {
        $this->_object->setText('CODE-128');
        $this->assertEquals(143, $this->_object->getWidth());
        $this->_object->setWithQuietZones(false);
        $this->assertEquals(123, $this->_object->getWidth(true));
    }

    public function testCompleteGeneration()
    {
        $this->_object->setText('HI345678');
        $this->_object->draw();
        $instructions = $this->loadInstructionsFile('Code128_HI345678_instructions');
        $this->assertEquals($instructions, $this->_object->getInstructions());
    }

    public function testCompleteGenerationWithStretchText()
    {
        $this->_object->setText('HI345678');
        $this->_object->setStretchText(true);
        $this->_object->draw();
        $instructions = $this->loadInstructionsFile(
                'Code128_HI345678_stretchtext_instructions');
        $this->assertEquals($instructions, $this->_object->getInstructions());
    }

    public function testCompleteGenerationWithBorder()
    {
        $this->_object->setText('HI345678');
        $this->_object->setWithBorder(true);
        $this->_object->draw();
        $instructions = $this->loadInstructionsFile(
                'Code128_HI345678_border_instructions');
        $this->assertEquals($instructions, $this->_object->getInstructions());
    }

    public function testCompleteGenerationWithOrientation()
    {
        $this->_object->setText('HI345678');
        $this->_object->setOrientation(60);
        $this->_object->draw();
        $instructions = $this->loadInstructionsFile(
                'Code128_HI345678_oriented_instructions');
        $this->assertEquals($instructions, $this->_object->getInstructions());
    }

    public function testCompleteGenerationWithStretchTextWithOrientation()
    {
        $this->_object->setText('HI345678');
        $this->_object->setOrientation(60);
        $this->_object->setStretchText(true);
        $this->_object->draw();
        $instructions = $this->loadInstructionsFile(
                'Code128_HI345678_stretchtext_oriented_instructions');
        $this->assertEquals($instructions, $this->_object->getInstructions());
    }

    public function testCompleteGenerationWithBorderWithOrientation()
    {
        $this->_object->setText('HI345678');
        $this->_object->setOrientation(60);
        $this->_object->setWithBorder(true);
        $this->_object->draw();
        $instructions = $this->loadInstructionsFile(
                'Code128_HI345678_border_oriented_instructions');
        $this->assertEquals($instructions, $this->_object->getInstructions());
    }
}
