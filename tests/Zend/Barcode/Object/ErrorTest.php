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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

require_once dirname(__FILE__) . '/TestCommon.php';

require_once 'Zend/Barcode/Object/Error.php';

/**
 * @category   Zend
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Barcode_Object_ErrorTest extends Zend_Barcode_Object_TestCommon
{

    protected function _getBarcodeObject($options = null)
    {
        return new Zend_Barcode_Object_Error($options);
    }

    public function testType()
    {
        $this->assertSame('error', $this->_object->getType());
    }

    public function testSetText()
    {
        $this->_object->setText('This is an error text');
        $this->assertSame('This is an error text', $this->_object->getRawText());
        $this->assertSame('This is an error text', $this->_object->getText());
        $this->assertSame('This is an error text', $this->_object->getTextToDisplay());
    }

    public function testCheckGoodParams()
    {
        $this->_object->setText('This is an error text');
        $this->assertTrue($this->_object->checkParams());
    }

    public function testGetDefaultHeight()
    {
        $this->assertEquals(40, $this->_object->getHeight());
    }

    public function testGetDefaultWidth()
    {
        $this->assertEquals(400, $this->_object->getWidth());
    }

    public function testCompleteGeneration()
    {
        $this->_object->setText('This is an error text');
        $this->_object->draw();
        $instructions = $this->loadInstructionsFile('Error_errortext_instructions');
        $this->assertEquals($instructions, $this->_object->getInstructions());
    }
}