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

require_once 'Zend/Barcode/Object/Ean5.php';

/**
 * @category   Zend
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @group      Zend_Barcode
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Barcode_Object_Ean5Test extends Zend_Barcode_Object_TestCommon
{

    protected function _getBarcodeObject($options = null)
    {
        return new Zend_Barcode_Object_Ean5($options);
    }

    public function testType()
    {
        $this->assertSame('ean5', $this->_object->getType());
    }

    public function testChecksum()
    {
        $this->assertSame(2, $this->_object->getChecksum('45678'));
    }

    public function testSetText()
    {
        $this->_object->setText('45678');
        $this->assertSame('45678', $this->_object->getRawText());
        $this->assertSame('45678', $this->_object->getText());
        $this->assertSame('45678', $this->_object->getTextToDisplay());
    }

    public function testSetTextWithout13Characters()
    {
        $this->_object->setText('4567');
        $this->assertSame('4567', $this->_object->getRawText());
        $this->assertSame('04567', $this->_object->getText());
        $this->assertSame('04567', $this->_object->getTextToDisplay());
    }

    public function testSetTextWithoutChecksumHasNoEffect()
    {
        $this->_object->setText('45678');
        $this->_object->setWithChecksum(false);
        $this->assertSame('45678', $this->_object->getRawText());
        $this->assertSame('45678', $this->_object->getText());
        $this->assertSame('45678', $this->_object->getTextToDisplay());
    }

    public function testSetTextWithSpaces()
    {
        $this->_object->setText(' 45678 ');
        $this->assertSame('45678', $this->_object->getRawText());
        $this->assertSame('45678', $this->_object->getText());
        $this->assertSame('45678', $this->_object->getTextToDisplay());
    }

    public function testSetTextWithChecksumNotDisplayed()
    {
        $this->_object->setText('45678');
        $this->_object->setWithChecksumInText(false);
        $this->assertSame('45678', $this->_object->getRawText());
        $this->assertSame('45678', $this->_object->getText());
        $this->assertSame('45678', $this->_object->getTextToDisplay());
    }

    public function testCheckGoodParams()
    {
        $this->_object->setText('45678');
        $this->assertTrue($this->_object->checkParams());
    }


    public function testGetKnownWidthWithoutOrientation()
    {
        $this->_object->setText('45678');
        $this->assertEquals(68, $this->_object->getWidth());
        $this->_object->setWithQuietZones(false);
        $this->assertEquals(48, $this->_object->getWidth(true));
    }

    public function testCompleteGeneration()
    {
        $this->_object->setText('45678');
        $this->_object->draw();
        $instructions = $this->loadInstructionsFile('Ean5_45678_instructions');
        $this->assertEquals($instructions, $this->_object->getInstructions());
    }

    public function testCompleteGenerationWithBorder()
    {
        $this->_object->setText('45678');
        $this->_object->setWithBorder(true);
        $this->_object->draw();
        $instructions = $this->loadInstructionsFile(
                'Ean5_45678_border_instructions');
        $this->assertEquals($instructions, $this->_object->getInstructions());
    }

    public function testCompleteGenerationWithOrientation()
    {
        $this->_object->setText('45678');
        $this->_object->setOrientation(60);
        $this->_object->draw();
        $instructions = $this->loadInstructionsFile(
                'Ean5_45678_oriented_instructions');
        $this->assertEquals($instructions, $this->_object->getInstructions());
    }

    public function testCompleteGenerationWithBorderWithOrientation()
    {
        $this->_object->setText('45678');
        $this->_object->setOrientation(60);
        $this->_object->setWithBorder(true);
        $this->_object->draw();
        $instructions = $this->loadInstructionsFile(
                'Ean5_45678_border_oriented_instructions');
        $this->assertEquals($instructions, $this->_object->getInstructions());
    }

    public function testGetDefaultHeight()
    {
        // Checksum activated => text needed
        $this->_object->setText('45678');
        $this->assertEquals(62, $this->_object->getHeight(true));
    }
}
