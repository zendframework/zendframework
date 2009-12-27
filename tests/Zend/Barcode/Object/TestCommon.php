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
 * Test helper
 */
require_once dirname(__FILE__) . '/_files/BarcodeTest.php';

/**
 * @see Zend_Config
 */
require_once 'Zend/Config.php';

/**
 * @category   Zend
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Barcode_Object_TestCommon extends PHPUnit_Framework_TestCase
{

    /**
     * @var Zend_Barcode_Object
     */
    protected $_object = null;

    abstract protected function _getBarcodeObject($options = null);

    protected function loadInstructionsFile($fileName)
    {
        return include_once (dirname(__FILE__) . "/_files/$fileName.php");
    }

    public function setUp()
    {
        $this->_object = $this->_getBarcodeObject();
    }

    public function tearDown()
    {
        $this->_object = null;
    }

    public function testStaticFontAsString()
    {
        Zend_Barcode_Object_ObjectAbstract::setBarcodeFont('my_static_font.ttf');
        $this->assertEquals('', $this->_object->getFont());
        $object = $this->_getBarcodeObject();
        $this->assertEquals('my_static_font.ttf', $object->getFont());
        Zend_Barcode_Object_ObjectAbstract::setBarcodeFont('');
    }

    public function testStaticFontAsNumber()
    {
        for ($i = 1; $i < 5; $i++) {
            Zend_Barcode_Object_ObjectAbstract::setBarcodeFont($i);
            $this->assertEquals('', $this->_object->getFont());
            $object = $this->_getBarcodeObject();
            $this->assertEquals($i, $object->getFont());
            Zend_Barcode_Object_ObjectAbstract::setBarcodeFont('');
        }
    }

    public function testConstructorWithArray()
    {
        $object = $this->_getBarcodeObject(
                array('barHeight' => 150 ,
                        'unkownProperty' => 'aValue'));
        $this->assertEquals(150, $object->getBarHeight());
    }

    public function testConstructorWithZendConfig()
    {
        $config = new Zend_Config(
                array('barHeight' => 150 ,
                        'unkownProperty' => 'aValue'));
        $object = $this->_getBarcodeObject($config);
        $this->assertEquals(150, $object->getBarHeight());
    }

    public function testSetOptions()
    {
        $this->_object->setOptions(
                array('barHeight' => 150 ,
                        'unkownProperty' => 'aValue'));
        $this->assertEquals(150, $this->_object->getBarHeight());
    }

    public function testSetConfig()
    {
        $config = new Zend_Config(
                array('barHeight' => 150 ,
                        'unkownProperty' => 'aValue'));
        $this->_object->setConfig($config);
        $this->assertEquals(150, $this->_object->getBarHeight());
    }

    public function testBarcodeNamespace()
    {
        $this->_object->setBarcodeNamespace('My_Namespace');
        $this->assertEquals('My_Namespace', $this->_object->getBarcodeNamespace());
    }

    public function testBarHeight()
    {
        $this->_object->setBarHeight(1);
        $this->assertSame(1, $this->_object->getBarHeight());
        $this->_object->setBarHeight(true);
        $this->assertSame(1, $this->_object->getBarHeight());
        $this->_object->setBarHeight('200a');
        $this->assertSame(200, $this->_object->getBarHeight());
    }

    /**
     * @expectedException Zend_Barcode_Object_Exception
     */
    public function testNegativeBarHeight()
    {
        $this->_object->setBarHeight(- 1);
    }

    public function testBarThinWidth()
    {
        $this->_object->setBarThinWidth(1);
        $this->assertSame(1, $this->_object->getBarThinWidth());
        $this->_object->setBarThinWidth(true);
        $this->assertSame(1, $this->_object->getBarThinWidth());
        $this->_object->setBarThinWidth('200a');
        $this->assertSame(200, $this->_object->getBarThinWidth());
    }

    /**
     * @expectedException Zend_Barcode_Object_Exception
     */
    public function testNegativeBarThinWidth()
    {
        $this->_object->setBarThinWidth(- 1);
    }

    public function testBarThickWidth()
    {
        $this->_object->setBarThickWidth(1);
        $this->assertSame(1, $this->_object->getBarThickWidth());
        $this->_object->setBarThickWidth(true);
        $this->assertSame(1, $this->_object->getBarThickWidth());
        $this->_object->setBarThickWidth('200a');
        $this->assertSame(200, $this->_object->getBarThickWidth());
    }

    /**
     * @expectedException Zend_Barcode_Object_Exception
     */
    public function testNegativeBarThickWidth()
    {
        $this->_object->setBarThickWidth(- 1);
    }

    public function testFactor()
    {
        $this->_object->setFactor(1);
        $this->assertSame(1.0, $this->_object->getFactor());
        $this->_object->setFactor(1.25);
        $this->assertSame(1.25, $this->_object->getFactor());
        $this->_object->setFactor(true);
        $this->assertSame(1.0, $this->_object->getFactor());
        $this->_object->setFactor('200a');
        $this->assertSame(200.0, $this->_object->getFactor());
    }

    /**
     * @expectedException Zend_Barcode_Object_Exception
     */
    public function testNegativeFactor()
    {
        $this->_object->setFactor(- 1);
    }

    public function testForeColor()
    {
        $this->_object->setForeColor('#333333');
        $this->assertSame(3355443, $this->_object->getForeColor());
        $this->_object->setForeColor(1000);
        $this->assertSame(1000, $this->_object->getForeColor());
    }

    /**
     * @expectedException Zend_Barcode_Object_Exception
     */
    public function testNegativeForeColor()
    {
        $this->_object->setForeColor(- 1);
    }

    /**
     * @expectedException Zend_Barcode_Object_Exception
     */
    public function testTooHighForeColor()
    {
        $this->_object->setForeColor(16777126);
    }

    public function testBackgroundColor()
    {
        $this->_object->setBackgroundColor('#333333');
        $this->assertSame(3355443, $this->_object->getBackgroundColor());
        $this->_object->setBackgroundColor(1000);
        $this->assertSame(1000, $this->_object->getBackgroundColor());
    }

    /**
     * @expectedException Zend_Barcode_Object_Exception
     */
    public function testNegativeBackgroundColor()
    {
        $this->_object->setBackgroundColor(- 1);
    }

    /**
     * @expectedException Zend_Barcode_Object_Exception
     */
    public function testTooHighBackgroundColor()
    {
        $this->_object->setBackgroundColor(16777126);
    }

    public function testWithBorder()
    {
        $this->_object->setWithBorder(1);
        $this->assertSame(true, $this->_object->getWithBorder());
        $this->_object->setWithBorder(true);
        $this->assertSame(true, $this->_object->getWithBorder());
    }

    public function testReverseColor()
    {
        $this->_object->setForeColor(11);
        $this->_object->setBackgroundColor(111);
        $this->_object->setReverseColor();
        $this->assertSame(111, $this->_object->getForeColor());
        $this->assertSame(11, $this->_object->getBackgroundColor());
    }

    public function testOrientation()
    {
        $this->_object->setOrientation(1);
        $this->assertSame(1.0, $this->_object->getOrientation());
        $this->_object->setOrientation(1.25);
        $this->assertSame(1.25, $this->_object->getOrientation());
        $this->_object->setOrientation(true);
        $this->assertSame(1.0, $this->_object->getOrientation());
        $this->_object->setOrientation('200a');
        $this->assertSame(200.0, $this->_object->getOrientation());
        $this->_object->setOrientation(360);
        $this->assertSame(0.0, $this->_object->getOrientation());
    }

    public function testDrawText()
    {
        $this->_object->setDrawText(1);
        $this->assertSame(true, $this->_object->getDrawText());
        $this->_object->setDrawText(true);
        $this->assertSame(true, $this->_object->getDrawText());
    }

    public function testStretchText()
    {
        $this->_object->setStretchText(1);
        $this->assertSame(true, $this->_object->getStretchText());
        $this->_object->setStretchText(true);
        $this->assertSame(true, $this->_object->getStretchText());
    }

    public function testWithChecksum()
    {
        $this->_object->setWithChecksum(1);
        $this->assertSame(true, $this->_object->getWithChecksum());
        $this->_object->setWithChecksum(true);
        $this->assertSame(true, $this->_object->getWithChecksum());
    }

    public function testWithChecksumInText()
    {
        $this->_object->setWithChecksumInText(1);
        $this->assertSame(true, $this->_object->getWithChecksumInText());
        $this->_object->setWithChecksumInText(true);
        $this->assertSame(true, $this->_object->getWithChecksumInText());
    }

    public function testSetFontAsNumberForGdImage()
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped(
                    'GD extension is required to run this test');
        }
        $gdFontSize = array(8 , 13 , 13 , 16 , 15);
        for ($i = 1; $i <= 5; $i ++) {
            $this->_object->setFont($i);
            $this->assertSame($i, $this->_object->getFont());
            $this->assertSame($gdFontSize[$i - 1],
                    $this->_object->getFontSize());
        }
    }

    /**
     * @expectedException Zend_Barcode_Object_Exception
     */
    public function testSetLowFontAsNumberForGdImage()
    {
        $this->_object->setFont(0);
    }

    /**
     * @expectedException Zend_Barcode_Object_Exception
     */
    public function testSetHighFontAsNumberForGdImage()
    {
        $this->_object->setFont(6);
    }

    public function testSetFontAsString()
    {
        $this->_object->setFont('my_font.ttf');
        $this->assertSame('my_font.ttf', $this->_object->getFont());
    }

    /**
     * @expectedException Zend_Barcode_Object_Exception
     */
    public function testSetFontAsBoolean()
    {
        $this->_object->setFont(true);
    }

    public function testFontAsNumberWithoutGd()
    {
        if (extension_loaded('gd')) {
            $this->markTestSkipped(
                    'GD extension must not be loaded to run this test');
        }
        $this->setExpectedException('Zend_Barcode_Object_Exception');
        $this->_object->setFont(1);
    }

    public function testFontSize()
    {
        $this->_object->setFontSize(22);
        $this->assertSame(22, $this->_object->getFontSize());
    }

    public function testFontSizeWithoutEffectWithGdInternalFont()
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped(
                    'GD extension is required to run this test');
        }
        $this->_object->setFont(1);
        $this->_object->setFontSize(22);
        $this->assertSame(8, $this->_object->getFontSize());
    }

    /**
     * @expectedException Zend_Barcode_Object_Exception
     */
    public function testStringFontSize()
    {
        $this->_object->setFontSize('22a');
    }

    public function testStandardQuietZone()
    {
        $this->_object->setBarThinWidth(3);
        $this->_object->setFactor(4);
        $this->assertSame(120.0, $this->_object->getQuietZone());
    }

    public function testAddInstruction()
    {
        $object = new Zend_Barcode_Object_Test();
        $instructions = array('type' => 'text' , 'text' => 'text' , 'size' => 10 ,
                'position' => array(5 , 5) ,
                'font' => 'my_font.ttf' ,
                'color' => '#123456' ,
                'alignment' => 'center' ,
                'orientation' => 45);
        $object->addInstruction($instructions);
        $this->assertSame(array($instructions), $object->getInstructions());
    }

    public function testAddPolygon()
    {
        $object = new Zend_Barcode_Object_Test();
        $points = array();
        $color = '#123456';
        $filled = false;
        $instructions = array('type' => 'polygon' , 'points' => $points ,
                'color' => $color , 'filled' => $filled);
        $object->addPolygon($points, $color, $filled);
        $this->assertSame(array($instructions), $object->getInstructions());
    }

    public function testAddPolygonWithDefaultColor()
    {
        $object = new Zend_Barcode_Object_Test();
        $points = array();
        $color = 123456;
        $object->setForeColor($color);
        $filled = false;
        $instructions = array('type' => 'polygon' , 'points' => $points ,
                'color' => $color , 'filled' => $filled);
        $object->addPolygon($points, null, $filled);
        $this->assertSame(array($instructions), $object->getInstructions());
    }

    public function testAddText()
    {
        $object = new Zend_Barcode_Object_Test();
        $size = 10;
        $text = 'foobar';
        $position = array();
        $font = 'my_font.ttf';
        $color = '#123456';
        $alignment = 'right';
        $orientation = 45;
        $instructions = array('type' => 'text' , 'text' => $text , 'size' => $size ,
                'position' => $position ,
                'font' => $font , 'color' => $color ,
                'alignment' => $alignment ,
                'orientation' => $orientation);
        $object->addText($text, $size, $position, $font, $color, $alignment,
                $orientation);
        $this->assertSame(array($instructions), $object->getInstructions());
    }

    public function testAddTextWithDefaultColor()
    {
        $object = new Zend_Barcode_Object_Test();
        $size = 10;
        $text = 'foobar';
        $position = array();
        $font = 'my_font.ttf';
        $color = 123456;
        $object->setForeColor($color);
        $alignment = 'right';
        $orientation = 45;
        $instructions = array('type' => 'text' , 'text' => $text , 'size' => $size ,
                'position' => $position ,
                'font' => $font , 'color' => $color ,
                'alignment' => $alignment ,
                'orientation' => $orientation);
        $object->addText($text, $size, $position, $font, null, $alignment, $orientation);
        $this->assertSame(array($instructions), $object->getInstructions());
    }

    /**
     * @expectedException Zend_Barcode_Object_Exception
     */
    public function testCheckParamsFontWithOrientation()
    {
        $this->_object->setText('0');
        $this->_object->setFont(1);
        $this->_object->setOrientation(45);
        $this->_object->checkParams();
    }

    public function testGetDefaultHeight()
    {
        $this->assertEquals(62, $this->_object->getHeight());
    }
}
