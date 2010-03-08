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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */




/**
 * @category   Zend
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @group      Zend_Barcode
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Barcode_Object_Itf14Test extends Zend_Barcode_Object_TestCommon
{

    protected function _getBarcodeObject($options = null)
    {
        return new Zend_Barcode_Object_Itf14($options);
    }

    public function testType()
    {
        $this->assertSame('itf14', $this->_object->getType());
    }

    public function testChecksum()
    {
        $this->assertSame(5, $this->_object->getChecksum('0000123456789'));
    }

    public function testSetText()
    {
        $this->_object->setText('0000123456789');
        $this->assertSame('0000123456789', $this->_object->getRawText());
        $this->assertSame('00001234567895', $this->_object->getText());
        $this->assertSame('00001234567895', $this->_object->getTextToDisplay());
    }

    public function testSetTextWithout14Characters()
    {
        $this->_object->setText('123456789');
        $this->assertSame('123456789', $this->_object->getRawText());
        $this->assertSame('00001234567895', $this->_object->getText());
        $this->assertSame('00001234567895', $this->_object->getTextToDisplay());
    }

    public function testSetTextWithoutChecksumHasNoEffect()
    {
        $this->_object->setText('0000123456789');
        $this->_object->setWithChecksum(false);
        $this->assertSame('0000123456789', $this->_object->getRawText());
        $this->assertSame('00001234567895', $this->_object->getText());
        $this->assertSame('00001234567895', $this->_object->getTextToDisplay());
    }

    public function testSetTextWithSpaces()
    {
        $this->_object->setText(' 0000123456789 ');
        $this->assertSame('0000123456789', $this->_object->getRawText());
        $this->assertSame('00001234567895', $this->_object->getText());
        $this->assertSame('00001234567895', $this->_object->getTextToDisplay());
    }

    public function testSetTextWithChecksumNotDisplayed()
    {
        $this->_object->setText('0000123456789');
        $this->_object->setWithChecksumInText(false);
        $this->assertSame('0000123456789', $this->_object->getRawText());
        $this->assertSame('00001234567895', $this->_object->getText());
        $this->assertSame('00001234567895', $this->_object->getTextToDisplay());
    }

    /**
     * @expectedException Zend_Barcode_Object_Exception
     */
    public function testBadTextDetectedIfChecksumWished()
    {
        $this->_object->setText('a');
        $this->_object->setWithChecksum(true);
        $this->_object->getText();
    }

    public function testCheckGoodParams()
    {
        $this->_object->setText('0000123456789');
        $this->assertTrue($this->_object->checkParams());
    }

    /**
     * @expectedException Zend_Barcode_Object_Exception
     */
    public function testCheckParamsWithLowRatio()
    {
        $this->_object->setText('0000123456789');
        $this->_object->setBarThinWidth(21);
        $this->_object->setBarThickWidth(40);
        $this->_object->checkParams();
    }

    /**
     * @expectedException Zend_Barcode_Object_Exception
     */
    public function testCheckParamsWithHighRatio()
    {
        $this->_object->setText('0000123456789');
        $this->_object->setBarThinWidth(20);
        $this->_object->setBarThickWidth(61);
        $this->_object->checkParams();
    }

    public function testGetKnownWidthWithoutOrientation()
    {
        $this->_object->setText('0000123456789');
        $this->assertEquals(155, $this->_object->getWidth());
    }

    public function testCompleteGeneration()
    {
        $this->_object->setText('0000123456789');
        $this->_object->draw();
        $instructions = $this->loadInstructionsFile('Itf14_0000123456789_instructions');
        $this->assertEquals($instructions, $this->_object->getInstructions());
    }

    public function testCompleteGenerationWithStretchText()
    {
        $this->_object->setText('0000123456789');
        $this->_object->setStretchText(true);
        $this->_object->draw();
        $instructions = $this->loadInstructionsFile(
                'Itf14_0000123456789_stretchtext_instructions');
        $this->assertEquals($instructions, $this->_object->getInstructions());
    }

    public function testCompleteGenerationWithBorder()
    {
        $this->_object->setText('0000123456789');
        $this->_object->setWithBorder(true);
        $this->_object->draw();
        $instructions = $this->loadInstructionsFile(
                'Itf14_0000123456789_border_instructions');
        $this->assertEquals($instructions, $this->_object->getInstructions());
    }

    public function testCompleteGenerationWithBearerBars()
    {
        $this->_object->setText('0000123456789');
        $this->_object->setWithBearerBars(true);
        $this->_object->draw();
        $instructions = $this->loadInstructionsFile(
                'Itf14_0000123456789_bearerbar_instructions');
        $this->assertEquals($instructions, $this->_object->getInstructions());
    }

    public function testCompleteGenerationWithOrientation()
    {
        $this->_object->setText('0000123456789');
        $this->_object->setOrientation(60);
        $this->_object->draw();
        $instructions = $this->loadInstructionsFile(
                'Itf14_0000123456789_oriented_instructions');
        $this->assertEquals($instructions, $this->_object->getInstructions());
    }

    public function testCompleteGenerationWithStretchTextWithOrientation()
    {
        $this->_object->setText('0000123456789');
        $this->_object->setOrientation(60);
        $this->_object->setStretchText(true);
        $this->_object->draw();
        $instructions = $this->loadInstructionsFile(
                'Itf14_0000123456789_stretchtext_oriented_instructions');
        $this->assertEquals($instructions, $this->_object->getInstructions());
    }

    public function testCompleteGenerationWithBorderWithOrientation()
    {
        $this->_object->setText('0000123456789');
        $this->_object->setOrientation(60);
        $this->_object->setWithBorder(true);
        $this->_object->draw();
        $instructions = $this->loadInstructionsFile(
                'Itf14_0000123456789_border_oriented_instructions');
        $this->assertEquals($instructions, $this->_object->getInstructions());
    }

    public function testCompleteGenerationWithBearerBarsWithOrientation()
    {
        $this->_object->setText('0000123456789');
        $this->_object->setOrientation(60);
        $this->_object->setWithBearerBars(true);
        $this->_object->draw();
        $instructions = $this->loadInstructionsFile(
                'Itf14_0000123456789_bearerbar_oriented_instructions');
        $this->assertEquals($instructions, $this->_object->getInstructions());
    }

    public function testGetDefaultHeight()
    {
        // Checksum activated => text needed
        $this->_object->setText('0000123456789');
        $this->assertEquals(62, $this->_object->getHeight(true));
    }
}
