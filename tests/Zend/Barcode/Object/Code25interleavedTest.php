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

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/TestHelper.php';

require_once dirname(__FILE__) . '/TestCommon.php';

require_once 'Zend/Barcode/Object/Code25interleaved.php';

/**
 * @category   Zend
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @group      Zend_Barcode
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Barcode_Object_Code25interleavedTest extends Zend_Barcode_Object_TestCommon
{

    protected function _getBarcodeObject($options = null)
    {
        return new Zend_Barcode_Object_Code25interleaved($options);
    }

    public function testType()
    {
        $this->assertSame('code25interleaved', $this->_object->getType());
    }

    public function testWithBearerBars()
    {
        $this->_object->setWithBearerBars(1);
        $this->assertSame(true, $this->_object->getWithBearerBars());
        $this->_object->setWithBearerBars(true);
        $this->assertSame(true, $this->_object->getWithBearerBars());
    }

    public function testChecksum()
    {
        $this->assertSame(5, $this->_object->getChecksum('0123456789'));
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
        $this->assertSame('0123456789', $this->_object->getRawText());
        $this->assertSame('0123456789', $this->_object->getText());
        $this->assertSame('0123456789', $this->_object->getTextToDisplay());
    }

    public function testSetTextWithoutEvenNumberOfCharacters()
    {
        $this->_object->setText('123456789');
        $this->assertSame('123456789', $this->_object->getRawText());
        $this->assertSame('0123456789', $this->_object->getText());
        $this->assertSame('0123456789', $this->_object->getTextToDisplay());
    }

    public function testSetTextWithChecksum()
    {
        $this->_object->setText('123456789');
        $this->_object->setWithChecksum(true);
        $this->assertSame('123456789', $this->_object->getRawText());
        $this->assertSame('1234567895', $this->_object->getText());
        $this->assertSame('123456789', $this->_object->getTextToDisplay());
    }

    public function testSetTextWithoutEvenNumberOfCharactersWithChecksum()
    {
        $this->_object->setText('123456789');
        $this->_object->setWithChecksum(true);
        $this->assertSame('123456789', $this->_object->getRawText());
        $this->assertSame('1234567895', $this->_object->getText());
        $this->assertSame('123456789', $this->_object->getTextToDisplay());
    }

    public function testSetTextWithChecksumDisplayed()
    {
        $this->_object->setText('123456789');
        $this->_object->setWithChecksum(true);
        $this->_object->setWithChecksumInText(true);
        $this->assertSame('123456789', $this->_object->getRawText());
        $this->assertSame('1234567895', $this->_object->getText());
        $this->assertSame('1234567895', $this->_object->getTextToDisplay());
    }

    public function testSetTextWithoutEvenNumberOfCharactersWithChecksumDisplayed()
    {
        $this->_object->setText('0123456789');
        $this->_object->setWithChecksum(true);
        $this->_object->setWithChecksumInText(true);
        $this->assertSame('0123456789', $this->_object->getRawText());
        $this->assertSame('001234567895', $this->_object->getText());
        $this->assertSame('001234567895', $this->_object->getTextToDisplay());
    }

    public function testBadTextAlwaysAllowed()
    {
        $this->_object->setText('a');
        $this->assertSame('0a', $this->_object->getText());
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
        $this->_object->setText('0123456789');
        $this->assertTrue($this->_object->checkParams());
    }

    /**
     * @expectedException Zend_Barcode_Object_Exception
     */
    public function testCheckParamsWithLowRatio()
    {
        $this->_object->setText('0123456789');
        $this->_object->setBarThinWidth(21);
        $this->_object->setBarThickWidth(40);
        $this->_object->checkParams();
    }

    /**
     * @expectedException Zend_Barcode_Object_Exception
     */
    public function testCheckParamsWithHighRatio()
    {
        $this->_object->setText('0123456789');
        $this->_object->setBarThinWidth(20);
        $this->_object->setBarThickWidth(61);
        $this->_object->checkParams();
    }

    public function testGetKnownWidthWithoutOrientation()
    {
        $this->_object->setText('0123456789');
        $this->assertEquals(119, $this->_object->getWidth());
    }

    public function testCompleteGeneration()
    {
        $this->_object->setText('0123456789');
        $this->_object->draw();
        $instructions = $this->loadInstructionsFile('Int25_0123456789_instructions');
        $this->assertEquals($instructions, $this->_object->getInstructions());
    }

    public function testCompleteGenerationWithStretchText()
    {
        $this->_object->setText('0123456789');
        $this->_object->setStretchText(true);
        $this->_object->draw();
        $instructions = $this->loadInstructionsFile(
                'Int25_0123456789_stretchtext_instructions');
        $this->assertEquals($instructions, $this->_object->getInstructions());
    }

    public function testCompleteGenerationWithBorder()
    {
        $this->_object->setText('0123456789');
        $this->_object->setWithBorder(true);
        $this->_object->draw();
        $instructions = $this->loadInstructionsFile(
                'Int25_0123456789_border_instructions');
        $this->assertEquals($instructions, $this->_object->getInstructions());
    }

    public function testCompleteGenerationWithBearerBars()
    {
        $this->_object->setText('0123456789');
        $this->_object->setWithBearerBars(true);
        $this->_object->draw();
        $instructions = $this->loadInstructionsFile(
                'Int25_0123456789_bearerbar_instructions');
        $this->assertEquals($instructions, $this->_object->getInstructions());
    }

    public function testCompleteGenerationWithOrientation()
    {
        $this->_object->setText('0123456789');
        $this->_object->setOrientation(60);
        $this->_object->draw();
        $instructions = $this->loadInstructionsFile(
                'Int25_0123456789_oriented_instructions');
        $this->assertEquals($instructions, $this->_object->getInstructions());
    }

    public function testCompleteGenerationWithStretchTextWithOrientation()
    {
        $this->_object->setText('0123456789');
        $this->_object->setOrientation(60);
        $this->_object->setStretchText(true);
        $this->_object->draw();
        $instructions = $this->loadInstructionsFile(
                'Int25_0123456789_stretchtext_oriented_instructions');
        $this->assertEquals($instructions, $this->_object->getInstructions());
    }

    public function testCompleteGenerationWithBorderWithOrientation()
    {
        $this->_object->setText('0123456789');
        $this->_object->setOrientation(60);
        $this->_object->setWithBorder(true);
        $this->_object->draw();
        $instructions = $this->loadInstructionsFile(
                'Int25_0123456789_border_oriented_instructions');
        $this->assertEquals($instructions, $this->_object->getInstructions());
    }

    public function testCompleteGenerationWithBearerBarsWithOrientation()
    {
        $this->_object->setText('0123456789');
        $this->_object->setOrientation(60);
        $this->_object->setWithBearerBars(true);
        $this->_object->draw();
        $instructions = $this->loadInstructionsFile(
                'Int25_0123456789_bearerbar_oriented_instructions');
        $this->assertEquals($instructions, $this->_object->getInstructions());
    }
}
