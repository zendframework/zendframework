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

/**
 * @namespace
 */
namespace ZendTest\Barcode\Renderer;

use ZendTest\Barcode\Object\TestAsset as TestAsset;
use Zend\Barcode;
use Zend\Barcode\Object;
use Zend\Config;

/**
 * @category   Zend
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class TestCommon extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Zend\Barcode\Renderer
     */
    protected $_renderer = null;

    abstract protected function _getRendererObject($options = null);

    public function setUp()
    {
        // Set timezone to avoid "It is not safe to rely on the system's timezone settings."
        // message if timezone is not set within php.ini
        date_default_timezone_set('GMT');

        Barcode\Barcode::setBarcodeFont(__DIR__ . '/../Object/_fonts/Vera.ttf');
        $this->_renderer = $this->_getRendererObject();
    }

    public function tearDown()
    {
        Barcode\Barcode::setBarcodeFont(null);
    }

    public function testSetBarcodeObject()
    {
        $barcode = new Object\Code39();
        $this->_renderer->setBarcode($barcode);
        $this->assertSame($barcode, $this->_renderer->getBarcode());
    }

    public function testSetInvalidBarcodeObject()
    {
        $this->setExpectedException('\Zend\Barcode\Renderer\Exception');
        $barcode = new \StdClass();
        $this->_renderer->setBarcode($barcode);
    }

    public function testGoodModuleSize()
    {
        $this->_renderer->setModuleSize(2.34);
        $this->assertSame(2.34, $this->_renderer->getModuleSize());
    }

    public function testModuleSizeAsString()
    {
        $this->setExpectedException('\Zend\Barcode\Renderer\Exception');
        $this->_renderer->setModuleSize('abc');
    }

    public function testModuleSizeLessThan0()
    {
        $this->setExpectedException('\Zend\Barcode\Renderer\Exception');
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

    public function testBadHorizontalPosition()
    {
        $this->setExpectedException('\Zend\Barcode\Renderer\Exception');
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

    public function testBadVerticalPosition()
    {
        $this->setExpectedException('\Zend\Barcode\Renderer\Exception');
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

    public function testBadLeftOffset()
    {
        $this->setExpectedException('\Zend\Barcode\Renderer\Exception');
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

    public function testBadTopOffset()
    {
        $this->setExpectedException('\Zend\Barcode\Renderer\Exception');
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
        $config = new Config\Config(
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
        $config = new Config\Config(
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

    public function testRendererWithUnkownInstructionProvideByObject()
    {
        $this->setExpectedException('\Zend\Barcode\Renderer\Exception');
        $object = new TestAsset\BarcodeTest();
        $object->setText('test');
        $object->addInstruction(array('type' => 'unknown'));
        $this->_renderer->setBarcode($object);
        $this->_renderer->draw();
    }

    public function testBarcodeObjectProvided()
    {
        $this->setExpectedException('\Zend\Barcode\Renderer\Exception');
        $this->_renderer->draw();
    }

    abstract public function testDrawReturnResource();

    abstract public function testDrawWithExistantResourceReturnResource();

    abstract protected function _getRendererWithWidth500AndHeight300();

    public function testHorizontalPositionToLeft()
    {
        $renderer = $this->_getRendererWithWidth500AndHeight300();
        $renderer->setModuleSize(1);
        $barcode = new Object\Code39(array('text' => '0123456789'));
        $this->assertEquals(211, $barcode->getWidth());
        $renderer->setBarcode($barcode);
        $renderer->draw();
        $this->assertEquals(0, $renderer->getLeftOffset());
    }

    public function testHorizontalPositionToCenter()
    {
        $renderer = $this->_getRendererWithWidth500AndHeight300();
        $renderer->setModuleSize(1);
        $barcode = new Object\Code39(array('text' => '0123456789'));
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
        $barcode = new Object\Code39(array('text' => '0123456789'));
        $this->assertEquals(211, $barcode->getWidth());
        $renderer->setBarcode($barcode);
        $renderer->setHorizontalPosition('right');
        $renderer->draw();
        $this->assertEquals(289, $renderer->getLeftOffset());
    }

    public function testLeftOffsetOverrideHorizontalPosition()
    {
        $renderer = $this->_getRendererWithWidth500AndHeight300();
        $renderer->setModuleSize(1);
        $barcode = new Object\Code39(array('text' => '0123456789'));
        $this->assertEquals(211, $barcode->getWidth());
        $renderer->setBarcode($barcode);
        $renderer->setLeftOffset(12);
        $renderer->setHorizontalPosition('center');
        $renderer->draw();
        $this->assertEquals(12, $renderer->getLeftOffset());
    }

    public function testVerticalPositionToTop()
    {
        $renderer = $this->_getRendererWithWidth500AndHeight300();
        $renderer->setModuleSize(1);
        $barcode = new Object\Code39(array('text' => '0123456789'));
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
        $barcode = new Object\Code39(array('text' => '0123456789'));
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
        $barcode = new Object\Code39(array('text' => '0123456789'));
        $this->assertEquals(62, $barcode->getHeight());
        $renderer->setBarcode($barcode);
        $renderer->setVerticalPosition('bottom');
        $renderer->draw();
        $this->assertEquals(238, $renderer->getTopOffset());
    }

    public function testTopOffsetOverrideVerticalPosition()
    {
        $renderer = $this->_getRendererWithWidth500AndHeight300();
        $renderer->setModuleSize(1);
        $barcode = new Object\Code39(array('text' => '0123456789'));
        $this->assertEquals(62, $barcode->getHeight());
        $renderer->setBarcode($barcode);
        $renderer->setTopOffset(12);
        $renderer->setVerticalPosition('middle');
        $renderer->draw();
        $this->assertEquals(12, $renderer->getTopOffset());
    }
}
