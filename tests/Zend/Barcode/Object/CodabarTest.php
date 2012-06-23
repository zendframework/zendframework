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
class CodabarTest extends TestCommon
{
    protected function getBarcodeObject($options = null)
    {
        return new Barcode\Object\Codabar($options);
    }

    public function testType()
    {
        $this->assertSame('codabar', $this->object->getType());
    }

    public function testSetText()
    {
        $this->object->setText('A0123456789B');
        $this->assertSame('A0123456789B', $this->object->getRawText());
        $this->assertSame('A0123456789B', $this->object->getText());
        $this->assertSame('A0123456789B', $this->object->getTextToDisplay());
    }

    public function testSetTextWithOddNumberOfCharacters()
    {
        $this->object->setText('A123456789B');
        $this->assertSame('A123456789B', $this->object->getRawText());
        $this->assertSame('A123456789B', $this->object->getText());
        $this->assertSame('A123456789B', $this->object->getTextToDisplay());
    }

    public function testSetTextWithSpaces()
    {
        $this->object->setText(' A0123456789B ');
        $this->assertSame('A0123456789B', $this->object->getRawText());
        $this->assertSame('A0123456789B', $this->object->getText());
        $this->assertSame('A0123456789B', $this->object->getTextToDisplay());
    }

    public function testBadTextAlwaysAllowed()
    {
        $this->object->setText('a');
        $this->assertSame('a', $this->object->getText());
    }

    public function testCheckGoodParams()
    {
        $this->object->setText('A0123456789B');
        $this->assertTrue($this->object->checkParams());
    }

    public function testGetKnownWidthWithoutOrientation()
    {
        $this->object->setText('A0123456789B');
        $this->assertEquals(141, $this->object->getWidth());
        $this->object->setWithQuietZones(false);
        $this->assertEquals(121, $this->object->getWidth(true));
    }

    public function testCompleteGeneration()
    {
        $this->object->setText('A0123456789B');
        $this->object->draw();
        $instructions = $this->loadInstructionsFile('Codabar_A0123456789B_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testCompleteGenerationWithStretchText()
    {
        $this->object->setText('A0123456789B');
        $this->object->setStretchText(true);
        $this->object->draw();
        $instructions = $this->loadInstructionsFile(
                'Codabar_A0123456789B_stretchtext_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testCompleteGenerationWithBorder()
    {
        $this->object->setText('A0123456789B');
        $this->object->setWithBorder(true);
        $this->object->draw();
        $instructions = $this->loadInstructionsFile(
                'Codabar_A0123456789B_border_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testCompleteGenerationWithOrientation()
    {
        $this->object->setText('A0123456789B');
        $this->object->setOrientation(60);
        $this->object->draw();
        $instructions = $this->loadInstructionsFile(
                'Codabar_A0123456789B_oriented_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testCompleteGenerationWithStretchTextWithOrientation()
    {
        $this->object->setText('A0123456789B');
        $this->object->setOrientation(60);
        $this->object->setStretchText(true);
        $this->object->draw();
        $instructions = $this->loadInstructionsFile(
                'Codabar_A0123456789B_stretchtext_oriented_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testCompleteGenerationWithBorderWithOrientation()
    {
        $this->object->setText('A0123456789B');
        $this->object->setOrientation(60);
        $this->object->setWithBorder(true);
        $this->object->draw();
        $instructions = $this->loadInstructionsFile(
                'Codabar_A0123456789B_border_oriented_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }
}
