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
class Ean5Test extends TestCommon
{
    protected function getBarcodeObject($options = null)
    {
        return new Barcode\Object\Ean5($options);
    }

    public function testType()
    {
        $this->assertSame('ean5', $this->object->getType());
    }

    public function testChecksum()
    {
        $this->assertSame(2, $this->object->getChecksum('45678'));
    }

    public function testSetText()
    {
        $this->object->setText('45678');
        $this->assertSame('45678', $this->object->getRawText());
        $this->assertSame('45678', $this->object->getText());
        $this->assertSame('45678', $this->object->getTextToDisplay());
    }

    public function testSetTextWithout13Characters()
    {
        $this->object->setText('4567');
        $this->assertSame('4567', $this->object->getRawText());
        $this->assertSame('04567', $this->object->getText());
        $this->assertSame('04567', $this->object->getTextToDisplay());
    }

    public function testSetTextWithoutChecksumHasNoEffect()
    {
        $this->object->setText('45678');
        $this->object->setWithChecksum(false);
        $this->assertSame('45678', $this->object->getRawText());
        $this->assertSame('45678', $this->object->getText());
        $this->assertSame('45678', $this->object->getTextToDisplay());
    }

    public function testSetTextWithSpaces()
    {
        $this->object->setText(' 45678 ');
        $this->assertSame('45678', $this->object->getRawText());
        $this->assertSame('45678', $this->object->getText());
        $this->assertSame('45678', $this->object->getTextToDisplay());
    }

    public function testSetTextWithChecksumNotDisplayed()
    {
        $this->object->setText('45678');
        $this->object->setWithChecksumInText(false);
        $this->assertSame('45678', $this->object->getRawText());
        $this->assertSame('45678', $this->object->getText());
        $this->assertSame('45678', $this->object->getTextToDisplay());
    }

    public function testCheckGoodParams()
    {
        $this->object->setText('45678');
        $this->assertTrue($this->object->checkParams());
    }


    public function testGetKnownWidthWithoutOrientation()
    {
        $this->object->setText('45678');
        $this->assertEquals(68, $this->object->getWidth());
        $this->object->setWithQuietZones(false);
        $this->assertEquals(48, $this->object->getWidth(true));
    }

    public function testCompleteGeneration()
    {
        $this->object->setText('45678');
        $this->object->draw();
        $instructions = $this->loadInstructionsFile('Ean5_45678_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testCompleteGenerationWithBorder()
    {
        $this->object->setText('45678');
        $this->object->setWithBorder(true);
        $this->object->draw();
        $instructions = $this->loadInstructionsFile(
                'Ean5_45678_border_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testCompleteGenerationWithOrientation()
    {
        $this->object->setText('45678');
        $this->object->setOrientation(60);
        $this->object->draw();
        $instructions = $this->loadInstructionsFile(
                'Ean5_45678_oriented_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testCompleteGenerationWithBorderWithOrientation()
    {
        $this->object->setText('45678');
        $this->object->setOrientation(60);
        $this->object->setWithBorder(true);
        $this->object->draw();
        $instructions = $this->loadInstructionsFile(
                'Ean5_45678_border_oriented_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testGetDefaultHeight()
    {
        // Checksum activated => text needed
        $this->object->setText('45678');
        $this->assertEquals(62, $this->object->getHeight(true));
    }
}
