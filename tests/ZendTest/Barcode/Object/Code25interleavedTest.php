<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Barcode
 */

namespace ZendTest\Barcode\Object;

use Zend\Barcode;

/**
 * @category   Zend
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @group      Zend_Barcode
 */
class Code25interleavedTest extends TestCommon
{
    protected function getBarcodeObject($options = null)
    {
        return new Barcode\Object\Code25interleaved($options);
    }

    public function testType()
    {
        $this->assertSame('code25interleaved', $this->object->getType());
    }

    public function testWithBearerBars()
    {
        $this->object->setWithBearerBars(1);
        $this->assertSame(true, $this->object->getWithBearerBars());
        $this->object->setWithBearerBars(true);
        $this->assertSame(true, $this->object->getWithBearerBars());
    }

    public function testChecksum()
    {
        $this->assertSame(5, $this->object->getChecksum('0123456789'));
    }

    public function testSetText()
    {
        $this->object->setText('0123456789');
        $this->assertSame('0123456789', $this->object->getRawText());
        $this->assertSame('0123456789', $this->object->getText());
        $this->assertSame('0123456789', $this->object->getTextToDisplay());
    }

    public function testSetTextWithSpaces()
    {
        $this->object->setText(' 0123456789 ');
        $this->assertSame('0123456789', $this->object->getRawText());
        $this->assertSame('0123456789', $this->object->getText());
        $this->assertSame('0123456789', $this->object->getTextToDisplay());
    }

    public function testSetTextWithoutEvenNumberOfCharacters()
    {
        $this->object->setText('123456789');
        $this->assertSame('123456789', $this->object->getRawText());
        $this->assertSame('0123456789', $this->object->getText());
        $this->assertSame('0123456789', $this->object->getTextToDisplay());
    }

    public function testSetTextWithChecksum()
    {
        $this->object->setText('123456789');
        $this->object->setWithChecksum(true);
        $this->assertSame('123456789', $this->object->getRawText());
        $this->assertSame('1234567895', $this->object->getText());
        $this->assertSame('123456789', $this->object->getTextToDisplay());
    }

    public function testSetTextWithoutEvenNumberOfCharactersWithChecksum()
    {
        $this->object->setText('123456789');
        $this->object->setWithChecksum(true);
        $this->assertSame('123456789', $this->object->getRawText());
        $this->assertSame('1234567895', $this->object->getText());
        $this->assertSame('123456789', $this->object->getTextToDisplay());
    }

    public function testSetTextWithChecksumDisplayed()
    {
        $this->object->setText('123456789');
        $this->object->setWithChecksum(true);
        $this->object->setWithChecksumInText(true);
        $this->assertSame('123456789', $this->object->getRawText());
        $this->assertSame('1234567895', $this->object->getText());
        $this->assertSame('1234567895', $this->object->getTextToDisplay());
    }

    public function testSetTextWithoutEvenNumberOfCharactersWithChecksumDisplayed()
    {
        $this->object->setText('0123456789');
        $this->object->setWithChecksum(true);
        $this->object->setWithChecksumInText(true);
        $this->assertSame('0123456789', $this->object->getRawText());
        $this->assertSame('001234567895', $this->object->getText());
        $this->assertSame('001234567895', $this->object->getTextToDisplay());
    }

    public function testBadTextAlwaysAllowed()
    {
        $this->object->setText('a');
        $this->assertSame('0a', $this->object->getText());
    }

    public function testBadTextDetectedIfChecksumWished()
    {
        $this->setExpectedException('\Zend\Barcode\Object\Exception\ExceptionInterface');
        $this->object->setText('a');
        $this->object->setWithChecksum(true);
        $this->object->getText();
    }

    public function testCheckGoodParams()
    {
        $this->object->setText('0123456789');
        $this->assertTrue($this->object->checkParams());
    }

    public function testCheckParamsWithLowRatio()
    {
        $this->setExpectedException('\Zend\Barcode\Object\Exception\ExceptionInterface');
        $this->object->setText('0123456789');
        $this->object->setBarThinWidth(21);
        $this->object->setBarThickWidth(40);
        $this->object->checkParams();
    }

    public function testCheckParamsWithHighRatio()
    {
        $this->setExpectedException('\Zend\Barcode\Object\Exception\ExceptionInterface');
        $this->object->setText('0123456789');
        $this->object->setBarThinWidth(20);
        $this->object->setBarThickWidth(61);
        $this->object->checkParams();
    }

    public function testGetKnownWidthWithoutOrientation()
    {
        $this->object->setText('0123456789');
        $this->assertEquals(119, $this->object->getWidth());
        $this->object->setWithQuietZones(false);
        $this->assertEquals(99, $this->object->getWidth(true));
    }

    public function testCompleteGeneration()
    {
        $this->object->setText('0123456789');
        $this->object->draw();
        $instructions = $this->loadInstructionsFile('Int25_0123456789_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testCompleteGenerationWithStretchText()
    {
        $this->object->setText('0123456789');
        $this->object->setStretchText(true);
        $this->object->draw();
        $instructions = $this->loadInstructionsFile(
                'Int25_0123456789_stretchtext_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testCompleteGenerationWithBorder()
    {
        $this->object->setText('0123456789');
        $this->object->setWithBorder(true);
        $this->object->draw();
        $instructions = $this->loadInstructionsFile(
                'Int25_0123456789_border_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testCompleteGenerationWithBearerBars()
    {
        $this->object->setText('0123456789');
        $this->object->setWithBearerBars(true);
        $this->object->draw();
        $instructions = $this->loadInstructionsFile(
                'Int25_0123456789_bearerbar_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testCompleteGenerationWithOrientation()
    {
        $this->object->setText('0123456789');
        $this->object->setOrientation(60);
        $this->object->draw();
        $instructions = $this->loadInstructionsFile(
                'Int25_0123456789_oriented_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testCompleteGenerationWithStretchTextWithOrientation()
    {
        $this->object->setText('0123456789');
        $this->object->setOrientation(60);
        $this->object->setStretchText(true);
        $this->object->draw();
        $instructions = $this->loadInstructionsFile(
                'Int25_0123456789_stretchtext_oriented_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testCompleteGenerationWithBorderWithOrientation()
    {
        $this->object->setText('0123456789');
        $this->object->setOrientation(60);
        $this->object->setWithBorder(true);
        $this->object->draw();
        $instructions = $this->loadInstructionsFile(
                'Int25_0123456789_border_oriented_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testCompleteGenerationWithBearerBarsWithOrientation()
    {
        $this->object->setText('0123456789');
        $this->object->setOrientation(60);
        $this->object->setWithBearerBars(true);
        $this->object->draw();
        $instructions = $this->loadInstructionsFile(
                'Int25_0123456789_bearerbar_oriented_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }
}
