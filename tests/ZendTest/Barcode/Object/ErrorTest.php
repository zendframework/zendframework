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
class ErrorTest extends TestCommon
{
    protected function getBarcodeObject($options = null)
    {
        return new Barcode\Object\Error($options);
    }

    public function testType()
    {
        $this->assertSame('error', $this->object->getType());
    }

    public function testSetText()
    {
        $this->object->setText('This is an error text');
        $this->assertSame('This is an error text', $this->object->getRawText());
        $this->assertSame('This is an error text', $this->object->getText());
        $this->assertSame('This is an error text', $this->object->getTextToDisplay());
    }

    public function testCheckGoodParams()
    {
        $this->object->setText('This is an error text');
        $this->assertTrue($this->object->checkParams());
    }

    public function testGetDefaultHeight()
    {
        $this->assertEquals(40, $this->object->getHeight());
    }

    public function testGetDefaultWidth()
    {
        $this->assertEquals(400, $this->object->getWidth());
    }

    public function testCompleteGeneration()
    {
        $this->object->setText('This is an error text');
        $this->object->draw();
        $instructions = $this->loadInstructionsFile('Error_errortext_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }
}
