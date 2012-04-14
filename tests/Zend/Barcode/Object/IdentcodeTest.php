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

namespace ZendTest\Barcode\Object;
use Zend\Barcode;

/**
 * @category   Zend
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @group      Zend_Barcode
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IdentcodeTest extends TestCommon
{
    protected function getBarcodeObject($options = null)
    {
        return new Barcode\Object\Identcode($options);
    }

    public function testType()
    {
        $this->assertSame('identcode', $this->object->getType());
    }

    public function testChecksum()
    {
        $this->assertSame(6, $this->object->getChecksum('12345678901'));
    }

    public function testSetText()
    {
        $this->object->setText('00123456789');
        $this->assertSame('00123456789', $this->object->getRawText());
        $this->assertSame('001234567890', $this->object->getText());
        $this->assertSame('00.123 456.789 0', $this->object->getTextToDisplay());
    }

    public function testSetTextWithout13Characters()
    {
        $this->object->setText('123456789');
        $this->assertSame('123456789', $this->object->getRawText());
        $this->assertSame('001234567890', $this->object->getText());
        $this->assertSame('00.123 456.789 0', $this->object->getTextToDisplay());
    }

    public function testSetTextWithoutChecksumHasNoEffect()
    {
        $this->object->setText('00123456789');
        $this->object->setWithChecksum(false);
        $this->assertSame('00123456789', $this->object->getRawText());
        $this->assertSame('001234567890', $this->object->getText());
        $this->assertSame('00.123 456.789 0', $this->object->getTextToDisplay());
    }

    public function testSetTextWithSpaces()
    {
        $this->object->setText(' 00123456789 ');
        $this->assertSame('00123456789', $this->object->getRawText());
        $this->assertSame('001234567890', $this->object->getText());
        $this->assertSame('00.123 456.789 0', $this->object->getTextToDisplay());
    }

    public function testSetTextWithChecksumNotDisplayed()
    {
        $this->object->setText('00123456789');
        $this->object->setWithChecksumInText(false);
        $this->assertSame('00123456789', $this->object->getRawText());
        $this->assertSame('001234567890', $this->object->getText());
        $this->assertSame('00.123 456.789 0', $this->object->getTextToDisplay());
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
        $this->object->setText('00123456789');
        $this->assertTrue($this->object->checkParams());
    }


    public function testGetKnownWidthWithoutOrientation()
    {
        $this->object->setText('00123456789');
        $this->assertEquals(137, $this->object->getWidth());
        $this->object->setWithQuietZones(false);
        $this->assertEquals(117, $this->object->getWidth(true));
    }

    public function testCompleteGeneration()
    {
        $this->object->setText('00123456789');
        $this->object->draw();
        $instructions = $this->loadInstructionsFile('Identcode_00123456789_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testCompleteGenerationWithBorder()
    {
        $this->object->setText('00123456789');
        $this->object->setWithBorder(true);
        $this->object->draw();
        $instructions = $this->loadInstructionsFile(
                'Identcode_00123456789_border_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testCompleteGenerationWithOrientation()
    {
        $this->object->setText('00123456789');
        $this->object->setOrientation(60);
        $this->object->draw();
        $instructions = $this->loadInstructionsFile(
                'Identcode_00123456789_oriented_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testCompleteGenerationWithBorderWithOrientation()
    {
        $this->object->setText('00123456789');
        $this->object->setOrientation(60);
        $this->object->setWithBorder(true);
        $this->object->draw();
        $instructions = $this->loadInstructionsFile(
                'Identcode_00123456789_border_oriented_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testGetDefaultHeight()
    {
        // Checksum activated => text needed
        $this->object->setText('00123456789');
        $this->assertEquals(62, $this->object->getHeight(true));
    }
}
