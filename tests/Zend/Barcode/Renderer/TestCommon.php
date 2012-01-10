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
namespace ZendTest\Barcode\Renderer;

use ZendTest\Barcode\Object\TestAsset as TestAsset;
use Zend\Barcode;
use Zend\Barcode\Object;
use Zend\Config;

/**
 * @category   Zend
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class TestCommon extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Zend\Barcode\Renderer
     */
    protected $renderer = null;

    /**
     * Stores the original set timezone
     * @var string
     */
    private $originaltimezone;

    abstract protected function getRendererObject($options = null);

    public function setUp()
    {
        $this->originaltimezone = date_default_timezone_get();

        // Set timezone to avoid "It is not safe to rely on the system's timezone settings."
        // message if timezone is not set within php.ini
        date_default_timezone_set('GMT');

        Barcode\Barcode::setBarcodeFont(__DIR__ . '/../Object/_fonts/Vera.ttf');
        $this->renderer = $this->getRendererObject();
    }

    public function tearDown()
    {
        Barcode\Barcode::setBarcodeFont(null);
        date_default_timezone_set($this->originaltimezone);
    }

    public function testSetBarcodeObject()
    {
        $barcode = new Object\Code39();
        $this->renderer->setBarcode($barcode);
        $this->assertSame($barcode, $this->renderer->getBarcode());
    }

    public function testSetInvalidBarcodeObject()
    {
        $this->setExpectedException('\Zend\Barcode\Renderer\Exception');
        $barcode = new \StdClass();
        $this->renderer->setBarcode($barcode);
    }

    public function testGoodModuleSize()
    {
        $this->renderer->setModuleSize(2.34);
        $this->assertSame(2.34, $this->renderer->getModuleSize());
    }

    public function testModuleSizeAsString()
    {
        $this->setExpectedException('\Zend\Barcode\Renderer\Exception');
        $this->renderer->setModuleSize('abc');
    }

    public function testModuleSizeLessThan0()
    {
        $this->setExpectedException('\Zend\Barcode\Renderer\Exception');
        $this->renderer->setModuleSize(-0.5);
    }

    public function testAutomaticRenderError()
    {
        $this->renderer->setAutomaticRenderError(true);
        $this->assertSame(true, $this->renderer->getAutomaticRenderError());
        $this->renderer->setAutomaticRenderError(1);
        $this->assertSame(true, $this->renderer->getAutomaticRenderError());
    }

    public function testGoodHorizontalPosition()
    {
        foreach (array('left' , 'center' , 'right') as $position) {
            $this->renderer->setHorizontalPosition($position);
            $this->assertSame($position,
                    $this->renderer->getHorizontalPosition());
        }
    }

    public function testBadHorizontalPosition()
    {
        $this->setExpectedException('\Zend\Barcode\Renderer\Exception');
        $this->renderer->setHorizontalPosition('none');
    }

    public function testGoodVerticalPosition()
    {
        foreach (array('top' , 'middle' , 'bottom') as $position) {
            $this->renderer->setVerticalPosition($position);
            $this->assertSame($position,
                    $this->renderer->getVerticalPosition());
        }
    }

    public function testBadVerticalPosition()
    {
        $this->setExpectedException('\Zend\Barcode\Renderer\Exception');
        $this->renderer->setVerticalPosition('none');
    }

    public function testGoodLeftOffset()
    {
        $this->assertSame(0, $this->renderer->getLeftOffset());
        $this->renderer->setLeftOffset(123);
        $this->assertSame(123, $this->renderer->getLeftOffset());
        $this->renderer->setLeftOffset(0);
        $this->assertSame(0, $this->renderer->getLeftOffset());
    }

    public function testBadLeftOffset()
    {
        $this->setExpectedException('\Zend\Barcode\Renderer\Exception');
        $this->renderer->setLeftOffset(- 1);
    }

    public function testGoodTopOffset()
    {
        $this->assertSame(0, $this->renderer->getTopOffset());
        $this->renderer->setTopOffset(123);
        $this->assertSame(123, $this->renderer->getTopOffset());
        $this->renderer->setTopOffset(0);
        $this->assertSame(0, $this->renderer->getTopOffset());
    }

    public function testBadTopOffset()
    {
        $this->setExpectedException('\Zend\Barcode\Renderer\Exception');
        $this->renderer->setTopOffset(- 1);
    }

    public function testConstructorWithArray()
    {
        $renderer = $this->getRendererObject(
                array('automaticRenderError' => true ,
                        'unkownProperty' => 'aValue'));
        $this->assertEquals(true, $renderer->getAutomaticRenderError());
    }

    public function testConstructorWithZendConfig()
    {
        $config = new Config\Config(
                array('automaticRenderError' => true ,
                        'unkownProperty' => 'aValue'));
        $renderer = $this->getRendererObject($config);
        $this->assertEquals(true, $renderer->getAutomaticRenderError());
    }

    public function testSetOptions()
    {
        $this->assertEquals(false, $this->renderer->getAutomaticRenderError());
        $this->renderer->setOptions(
                array('automaticRenderError' => true ,
                        'unkownProperty' => 'aValue'));
        $this->assertEquals(true, $this->renderer->getAutomaticRenderError());
    }

    public function testRendererNamespace()
    {
        $this->renderer->setRendererNamespace('My_Namespace');
        $this->assertEquals('My_Namespace', $this->renderer->getRendererNamespace());
    }

    public function testRendererWithUnkownInstructionProvideByObject()
    {
        $this->setExpectedException('\Zend\Barcode\Renderer\Exception');
        $object = new TestAsset\BarcodeTest();
        $object->setText('test');
        $object->addTestInstruction(array('type' => 'unknown'));
        $this->renderer->setBarcode($object);
        $this->renderer->draw();
    }

    public function testBarcodeObjectProvided()
    {
        $this->setExpectedException('\Zend\Barcode\Renderer\Exception');
        $this->renderer->draw();
    }

    abstract public function testDrawReturnResource();

    abstract public function testDrawWithExistantResourceReturnResource();

    abstract protected function getRendererWithWidth500AndHeight300();

    public function testHorizontalPositionToLeft()
    {
        $renderer = $this->getRendererWithWidth500AndHeight300();
        $renderer->setModuleSize(1);
        $barcode = new Object\Code39(array('text' => '0123456789'));
        $this->assertEquals(211, $barcode->getWidth());
        $renderer->setBarcode($barcode);
        $renderer->draw();
        $this->assertEquals(0, $renderer->getLeftOffset());
    }

    public function testHorizontalPositionToCenter()
    {
        $renderer = $this->getRendererWithWidth500AndHeight300();
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
        $renderer = $this->getRendererWithWidth500AndHeight300();
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
        $renderer = $this->getRendererWithWidth500AndHeight300();
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
        $renderer = $this->getRendererWithWidth500AndHeight300();
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
        $renderer = $this->getRendererWithWidth500AndHeight300();
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
        $renderer = $this->getRendererWithWidth500AndHeight300();
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
        $renderer = $this->getRendererWithWidth500AndHeight300();
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
