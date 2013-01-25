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
class Ean13Test extends TestCommon
{
    protected function getBarcodeObject($options = null)
    {
        return new Barcode\Object\Ean13($options);
    }

    public function testType()
    {
        $this->assertSame('ean13', $this->object->getType());
    }

    public function testChecksum()
    {
        $this->assertSame(2, $this->object->getChecksum('012345678901'));
    }

    public function testSetText()
    {
        $this->object->setText('000123456789');
        $this->assertSame('000123456789', $this->object->getRawText());
        $this->assertSame('0001234567895', $this->object->getText());
        $this->assertSame('0001234567895', $this->object->getTextToDisplay());
    }

    public function testSetTextWithout13Characters()
    {
        $this->object->setText('123456789');
        $this->assertSame('123456789', $this->object->getRawText());
        $this->assertSame('0001234567895', $this->object->getText());
        $this->assertSame('0001234567895', $this->object->getTextToDisplay());
    }

    public function testSetTextWithoutChecksumHasNoEffect()
    {
        $this->object->setText('000123456789');
        $this->object->setWithChecksum(false);
        $this->assertSame('000123456789', $this->object->getRawText());
        $this->assertSame('0001234567895', $this->object->getText());
        $this->assertSame('0001234567895', $this->object->getTextToDisplay());
    }

    public function testSetTextWithSpaces()
    {
        $this->object->setText(' 000123456789 ');
        $this->assertSame('000123456789', $this->object->getRawText());
        $this->assertSame('0001234567895', $this->object->getText());
        $this->assertSame('0001234567895', $this->object->getTextToDisplay());
    }

    public function testSetTextWithChecksumNotDisplayed()
    {
        $this->object->setText('000123456789');
        $this->object->setWithChecksumInText(false);
        $this->assertSame('000123456789', $this->object->getRawText());
        $this->assertSame('0001234567895', $this->object->getText());
        $this->assertSame('0001234567895', $this->object->getTextToDisplay());
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
        $this->object->setText('000123456789');
        $this->assertTrue($this->object->checkParams());
    }


    public function testGetKnownWidthWithoutOrientation()
    {
        $this->object->setText('000123456789');
        $this->assertEquals(115, $this->object->getWidth());
        $this->object->setWithQuietZones(false);
        $this->assertEquals(115, $this->object->getWidth(true));
    }

    public function testCompleteGeneration()
    {
        $this->object->setText('000123456789');
        $this->object->draw();
        $instructions = $this->loadInstructionsFile('Ean13_000123456789_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testCompleteGenerationWithBorder()
    {
        $this->object->setText('000123456789');
        $this->object->setWithBorder(true);
        $this->object->draw();
        $instructions = $this->loadInstructionsFile(
                'Ean13_000123456789_border_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testCompleteGenerationWithOrientation()
    {
        $this->object->setText('000123456789');
        $this->object->setOrientation(60);
        $this->object->draw();
        $instructions = $this->loadInstructionsFile(
                'Ean13_000123456789_oriented_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testCompleteGenerationWithBorderWithOrientation()
    {
        $this->object->setText('000123456789');
        $this->object->setOrientation(60);
        $this->object->setWithBorder(true);
        $this->object->draw();
        $instructions = $this->loadInstructionsFile(
                'Ean13_000123456789_border_oriented_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testGetDefaultHeight()
    {
        // Checksum activated => text needed
        $this->object->setText('000123456789');
        $this->assertEquals(62, $this->object->getHeight(true));
    }
}
