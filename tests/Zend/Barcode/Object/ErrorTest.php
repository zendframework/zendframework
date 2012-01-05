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

/**
 * @namespace
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
