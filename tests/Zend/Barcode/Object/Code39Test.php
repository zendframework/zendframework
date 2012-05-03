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
class Code39Test extends TestCommon
{
    protected function getBarcodeObject($options = null)
    {
        return new Barcode\Object\Code39($options);
    }

    public function testType()
    {
        $this->assertSame('code39', $this->object->getType());
    }

    public function testChecksum()
    {
        $this->assertSame(2, $this->object->getChecksum('0123456789'));
        $this->assertSame('W', $this->object->getChecksum('CODE39'));
        $this->assertSame('J', $this->object->getChecksum('FRAMEWORK-ZEND-COM'));
    }

    public function testSetText()
    {
        $this->object->setText('0123456789');
        $this->assertSame('0123456789', $this->object->getRawText());
        $this->assertSame('*0123456789*', $this->object->getText());
        $this->assertSame('*0123456789*', $this->object->getTextToDisplay());
    }

    public function testSetTextWithSpaces()
    {
        $this->object->setText(' 0123456789 ');
        $this->assertSame(' 0123456789 ', $this->object->getRawText());
        $this->assertSame('* 0123456789 *', $this->object->getText());
        $this->assertSame('* 0123456789 *', $this->object->getTextToDisplay());
    }

    public function testSetTextWithChecksum()
    {
        $this->object->setText('0123456789');
        $this->object->setWithChecksum(true);
        $this->assertSame('0123456789', $this->object->getRawText());
        $this->assertSame('*01234567892*', $this->object->getText());
        $this->assertSame('*0123456789*', $this->object->getTextToDisplay());
    }

    public function testSetTextWithChecksumDisplayed()
    {
        $this->object->setText('0123456789');
        $this->object->setWithChecksum(true);
        $this->object->setWithChecksumInText(true);
        $this->assertSame('0123456789', $this->object->getRawText());
        $this->assertSame('*01234567892*', $this->object->getText());
        $this->assertSame('*01234567892*', $this->object->getTextToDisplay());
    }

    public function testBadTextAlwaysAllowed()
    {
        $this->object->setText('&');
        $this->assertSame('&', $this->object->getRawText());
    }

    public function testBadTextDetectedIfChecksumWished()
    {
        $this->setExpectedException('\Zend\Barcode\Object\Exception\ExceptionInterface');
        $this->object->setText('&');
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
        $this->object->setText('TEST');
        $this->object->setBarThinWidth(21);
        $this->object->setBarThickWidth(40);
        $this->object->checkParams();
    }

    public function testCheckParamsWithHighRatio()
    {
        $this->setExpectedException('\Zend\Barcode\Object\Exception\ExceptionInterface');
        $this->object->setText('TEST');
        $this->object->setBarThinWidth(20);
        $this->object->setBarThickWidth(61);
        $this->object->checkParams();
    }

    public function testGetKnownWidthWithoutOrientation()
    {
        $this->object->setText('0123456789');
        $this->assertEquals(211, $this->object->getWidth());
        $this->object->setWithQuietZones(false);
        $this->assertEquals(191, $this->object->getWidth(true));
    }

    public function testCompleteGeneration()
    {
        $this->object->setText('0123456789');
        $this->object->draw();
        $instructions = $this->loadInstructionsFile('Code39_0123456789_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testCompleteGenerationWithStretchText()
    {
        $this->object->setText('0123456789');
        $this->object->setStretchText(true);
        $this->object->draw();
        $instructions = $this->loadInstructionsFile(
                'Code39_0123456789_stretchtext_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testCompleteGenerationWithBorder()
    {
        $this->object->setText('0123456789');
        $this->object->setWithBorder(true);
        $this->object->draw();
        $instructions = $this->loadInstructionsFile(
                'Code39_0123456789_border_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testCompleteGenerationWithOrientation()
    {
        $this->object->setText('0123456789');
        $this->object->setOrientation(60);
        $this->object->draw();
        $instructions = $this->loadInstructionsFile(
                'Code39_0123456789_oriented_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testCompleteGenerationWithStretchTextWithOrientation()
    {
        $this->object->setText('0123456789');
        $this->object->setOrientation(60);
        $this->object->setStretchText(true);
        $this->object->draw();
        $instructions = $this->loadInstructionsFile(
                'Code39_0123456789_stretchtext_oriented_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testCompleteGenerationWithBorderWithOrientation()
    {
        $this->object->setText('0123456789');
        $this->object->setOrientation(60);
        $this->object->setWithBorder(true);
        $this->object->draw();
        $instructions = $this->loadInstructionsFile(
                'Code39_0123456789_border_oriented_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }
}
