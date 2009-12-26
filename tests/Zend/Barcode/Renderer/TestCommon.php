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

/**
 * @category   Zend
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Barcode_Renderer_TestCommon extends PHPUnit_Framework_TestCase
{

    /**
     * @var Zend_Barcode_Renderer
     */
    protected $_renderer = null;

    abstract protected function _getRendererObject($options = null);

    public function setUp()
    {
        Zend_Barcode::setBarcodeFont(dirname(__FILE__) . '/../Object/_fonts/Vera.ttf');
        $this->_renderer = $this->_getRendererObject();
    }

    public function tearDown()
    {
        Zend_Barcode::setBarcodeFont('');
    }

    public function testSetBarcodeObject()
    {
        require_once 'Zend/Barcode/Object/Code39.php';
        $barcode = new Zend_Barcode_Object_Code39();
        $this->_renderer->setBarcode($barcode);
        $this->assertSame($barcode, $this->_renderer->getBarcode());
    }

    /**
     * @expectedException Zend_Barcode_Renderer_Exception
     */
    public function testSetInvalidBarcodeObject()
    {
        $barcode = new StdClass();
        $this->_renderer->setBarcode($barcode);
    }

    public function testGoodModuleSize()
    {
        $this->_renderer->setModuleSize(2.34);
        $this->assertSame(2.34, $this->_renderer->getModuleSize());
    }

    /**
     * @expectedException Zend_Barcode_Renderer_Exception
     */
    public function testModuleSizeAsString()
    {
        $this->_renderer->setModuleSize('abc');
    }

    /**
     * @expectedException Zend_Barcode_Renderer_Exception
     */
    public function testModuleSizeLessThan0()
    {
        $this->_renderer->setModuleSize(-0.5);
    }

    public function testAutomaticRenderError()
    {
        $this->_renderer->setAutomaticRenderError(true);
        $this->assertSame(true, $this->_renderer->getAutomaticRenderError());
        $this->_renderer->setAutomaticRenderError(1);
        $this->assertSame(true, $this->_renderer->getAutomaticRenderError());
    }

    public function testGoodHorizontalPosition()
    {
        foreach (array('left' , 'center' , 'right') as $position) {
            $this->_renderer->setHorizontalPosition($position);
            $this->assertSame($position,
                    $this->_renderer->getHorizontalPosition());
        }
    }

    /**
     * @expectedException Zend_Barcode_Renderer_Exception
     */
    public function testBadHorizontalPosition()
    {
        $this->_renderer->setHorizontalPosition('none');
    }

    public function testGoodVerticalPosition()
    {
        foreach (array('top' , 'middle' , 'bottom') as $position) {
            $this->_renderer->setVerticalPosition($position);
            $this->assertSame($position,
                    $this->_renderer->getVerticalPosition());
        }
    }

    /**
     * @expectedException Zend_Barcode_Renderer_Exception
     */
    public function testBadVerticalPosition()
    {
        $this->_renderer->setVerticalPosition('none');
    }

    public function testGoodLeftOffset()
    {
        $this->assertSame(0, $this->_renderer->getLeftOffset());
        $this->_renderer->setLeftOffset(123);
        $this->assertSame(123, $this->_renderer->getLeftOffset());
        $this->_renderer->setLeftOffset(0);
        $this->assertSame(0, $this->_renderer->getLeftOffset());
    }

    /**
     * @expectedException Zend_Barcode_Renderer_Exception
     */
    public function testBadLeftOffset()
    {
        $this->_renderer->setLeftOffset(- 1);
    }

    public function testGoodTopOffset()
    {
        $this->assertSame(0, $this->_renderer->getTopOffset());
        $this->_renderer->setTopOffset(123);
        $this->assertSame(123, $this->_renderer->getTopOffset());
        $this->_renderer->setTopOffset(0);
        $this->assertSame(0, $this->_renderer->getTopOffset());
    }

    /**
     * @expectedException Zend_Barcode_Renderer_Exception
     */
    public function testBadTopOffset()
    {
        $this->_renderer->setTopOffset(- 1);
    }

    public function testConstructorWithArray()
    {
        $renderer = $this->_getRendererObject(
                array('automaticRenderError' => true ,
                        'unkownProperty' => 'aValue'));
        $this->assertEquals(true, $renderer->getAutomaticRenderError());
    }

    public function testConstructorWithZendConfig()
    {
        $config = new Zend_Config(
                array('automaticRenderError' => true ,
                        'unkownProperty' => 'aValue'));
        $renderer = $this->_getRendererObject($config);
        $this->assertEquals(true, $renderer->getAutomaticRenderError());
    }

    public function testSetOptions()
    {
        $this->assertEquals(false, $this->_renderer->getAutomaticRenderError());
        $this->_renderer->setOptions(
                array('automaticRenderError' => true ,
                        'unkownProperty' => 'aValue'));
        $this->assertEquals(true, $this->_renderer->getAutomaticRenderError());
    }

    public function testSetConfig()
    {
        $this->assertEquals(false, $this->_renderer->getAutomaticRenderError());
        $config = new Zend_Config(
                array('automaticRenderError' => true ,
                        'unkownProperty' => 'aValue'));
        $this->_renderer->setConfig($config);
        $this->assertEquals(true, $this->_renderer->getAutomaticRenderError());
    }

    public function testRendererNamespace()
    {
        $this->_renderer->setRendererNamespace('My_Namespace');
        $this->assertEquals('My_Namespace', $this->_renderer->getRendererNamespace());
    }

    /**
     * @expectedException Zend_Barcode_Renderer_Exception
     */
    public function testRendererWithUnkownInstructionProvideByObject()
    {
        require_once dirname(__FILE__) . '/../Object/_files/BarcodeTest.php';
        $object = new Zend_Barcode_Object_Test();
        $object->setText('test');
        $object->addInstruction(array('type' => 'unknown'));
        $this->_renderer->setBarcode($object);
        $this->_renderer->draw();
    }

    /**
     * @expectedException Zend_Barcode_Renderer_Exception
     */
    public function testBarcodeObjectProvided()
    {
        $this->_renderer->draw();
    }

    abstract public function testDrawReturnResource();

    abstract public function testDrawWithExistantResourceReturnResource();

    abstract protected function _getRendererWithWidth500AndHeight300();

    public function testHorizontalPositionToLeft()
    {
        $renderer = $this->_getRendererWithWidth500AndHeight300();
        $renderer->setModuleSize(1);
        $barcode = new Zend_Barcode_Object_Code39(array('text' => '0123456789'));
        $this->assertEquals(211, $barcode->getWidth());
        $renderer->setBarcode($barcode);
        $renderer->draw();
        $this->assertEquals(0, $renderer->getLeftOffset());
    }

    public function testHorizontalPositionToCenter()
    {
        $renderer = $this->_getRendererWithWidth500AndHeight300();
        $renderer->setModuleSize(1);
        $barcode = new Zend_Barcode_Object_Code39(array('text' => '0123456789'));
        $this->assertEquals(211, $barcode->getWidth());
        $renderer->setBarcode($barcode);
        $renderer->setHorizontalPosition('center');
        $renderer->draw();
        $this->assertEquals(144, $renderer->getLeftOffset());
    }

    public function testHorizontalPositionToRight()
    {
        $renderer = $this->_getRendererWithWidth500AndHeight300();
        $renderer->setModuleSize(1);
        $barcode = new Zend_Barcode_Object_Code39(array('text' => '0123456789'));
        $this->assertEquals(211, $barcode->getWidth());
        $renderer->setBarcode($barcode);
        $renderer->setHorizontalPosition('right');
        $renderer->draw();
        $this->assertEquals(289, $renderer->getLeftOffset());
    }

    public function testVerticalPositionToTop()
    {
        $renderer = $this->_getRendererWithWidth500AndHeight300();
        $renderer->setModuleSize(1);
        $barcode = new Zend_Barcode_Object_Code39(array('text' => '0123456789'));
        $this->assertEquals(62, $barcode->getHeight());
        $renderer->setBarcode($barcode);
        $renderer->setVerticalPosition('top');
        $renderer->draw();
        $this->assertEquals(0, $renderer->getTopOffset());
    }

    public function testVerticalPositionToMiddle()
    {
        $renderer = $this->_getRendererWithWidth500AndHeight300();
        $renderer->setModuleSize(1);
        $barcode = new Zend_Barcode_Object_Code39(array('text' => '0123456789'));
        $this->assertEquals(62, $barcode->getHeight());
        $renderer->setBarcode($barcode);
        $renderer->setVerticalPosition('middle');
        $renderer->draw();
        $this->assertEquals(119, $renderer->getTopOffset());
    }

    public function testVerticalPositionToBottom()
    {
        $renderer = $this->_getRendererWithWidth500AndHeight300();
        $renderer->setModuleSize(1);
        $barcode = new Zend_Barcode_Object_Code39(array('text' => '0123456789'));
        $this->assertEquals(62, $barcode->getHeight());
        $renderer->setBarcode($barcode);
        $renderer->setVerticalPosition('bottom');
        $renderer->draw();
        $this->assertEquals(238, $renderer->getTopOffset());
    }
}