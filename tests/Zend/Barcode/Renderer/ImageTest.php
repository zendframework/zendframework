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

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/TestHelper.php';

require_once dirname(__FILE__) . '/TestCommon.php';

require_once 'Zend/Barcode/Renderer/Image.php';
require_once 'Zend/Barcode/Object/Code39.php';

/**
 * @category   Zend
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @group      Zend_Barcode
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Barcode_Renderer_ImageTest extends Zend_Barcode_Renderer_TestCommon
{

    protected function _getRendererObject($options = null)
    {
        return new Zend_Barcode_Renderer_Image($options);
    }

    public function testType()
    {
        $this->assertSame('image', $this->_renderer->getType());
    }

    public function testGoodImageResource()
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped(
                    'GD extension is required to run this test');
        }
        $imageResource = imagecreatetruecolor(1, 1);
        $this->_renderer->setResource($imageResource);
    }

    /**
     * @expectedException Zend_Barcode_Renderer_Exception
     */
    public function testObjectImageResource()
    {
        $imageResource = new StdClass();
        $this->_renderer->setResource($imageResource);
    }

    public function testGoodHeight()
    {
        $this->assertSame(0, $this->_renderer->getHeight());
        $this->_renderer->setHeight(123);
        $this->assertSame(123, $this->_renderer->getHeight());
        $this->_renderer->setHeight(0);
        $this->assertSame(0, $this->_renderer->getHeight());
    }

    /**
     * @expectedException Zend_Barcode_Renderer_Exception
     */
    public function testBadHeight()
    {
        $this->_renderer->setHeight(- 1);
    }

    public function testGoodWidth()
    {
        $this->assertSame(0, $this->_renderer->getWidth());
        $this->_renderer->setWidth(123);
        $this->assertSame(123, $this->_renderer->getWidth());
        $this->_renderer->setWidth(0);
        $this->assertSame(0, $this->_renderer->getWidth());
    }

    /**
     * @expectedException Zend_Barcode_Renderer_Exception
     */
    public function testBadWidth()
    {
        $this->_renderer->setWidth(- 1);
    }

    public function testAllowedImageType()
    {
        $types = array('gif' => 'gif' , 'jpg' => 'jpeg' , 'jpeg' => 'jpeg' ,
                'png' => 'png');
        foreach ($types as $type => $expectedType) {
            $this->_renderer->setImageType($type);
            $this->assertSame($expectedType,
                    $this->_renderer->getImageType());
        }
    }

    /**
     * @expectedException Zend_Barcode_Renderer_Exception
     */
    public function testNonAllowedImageType()
    {
        $this->_renderer->setImageType('other');
    }

    public function testDrawReturnResource()
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped(
                    'GD extension is required to run this test');
        }
        $barcode = new Zend_Barcode_Object_Code39(array('text' => '0123456789'));
        $this->_renderer->setBarcode($barcode);
        $resource = $this->_renderer->draw();
        $this->assertTrue(gettype($resource) == 'resource', 'Image must be a resource');
        $this->assertTrue(get_resource_type($resource) == 'gd',
                'Image must be a GD resource');
    }

    public function testDrawWithExistantResourceReturnResource()
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped(
                    'GD extension is required to run this test');
        }
        $barcode = new Zend_Barcode_Object_Code39(array('text' => '0123456789'));
        $this->_renderer->setBarcode($barcode);
        $imageResource = imagecreatetruecolor(500, 500);
        $this->_renderer->setResource($imageResource);
        $resource = $this->_renderer->draw();
        $this->assertTrue(gettype($resource) == 'resource', 'Image must be a resource');
        $this->assertTrue(get_resource_type($resource) == 'gd',
                'Image must be a GD resource');
        $this->assertSame($resource, $imageResource);
    }

    public function testGoodUserHeight()
    {
        $barcode = new Zend_Barcode_Object_Code39(array('text' => '0123456789'));
        $this->assertEquals(62, $barcode->getHeight());
        $this->_renderer->setBarcode($barcode);
        $this->_renderer->setHeight(62);
        $this->assertTrue($this->_renderer->checkParams());
    }

    /**
     * @expectedException Zend_Barcode_Renderer_Exception
     */
    public function testBadUserHeightLessThanBarcodeHeight()
    {
        $barcode = new Zend_Barcode_Object_Code39(array('text' => '0123456789'));
        $this->assertEquals(62, $barcode->getHeight());
        $this->_renderer->setBarcode($barcode);
        $this->_renderer->setHeight(61);
        $this->_renderer->checkParams();
    }

    public function testGoodUserWidth()
    {
        $barcode = new Zend_Barcode_Object_Code39(array('text' => '0123456789'));
        $this->assertEquals(211, $barcode->getWidth());
        $this->_renderer->setBarcode($barcode);
        $this->_renderer->setWidth(211);
        $this->assertTrue($this->_renderer->checkParams());
    }

    /**
     * @expectedException Zend_Barcode_Renderer_Exception
     */
    public function testBadUserWidthLessThanBarcodeWidth()
    {
        $barcode = new Zend_Barcode_Object_Code39(array('text' => '0123456789'));
        $this->assertEquals(211, $barcode->getWidth());
        $this->_renderer->setBarcode($barcode);
        $this->_renderer->setWidth(210);
        $this->_renderer->checkParams();
    }

    public function testGoodHeightOfUserResource()
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped(
                    'GD extension is required to run this test');
        }
        $barcode = new Zend_Barcode_Object_Code39(array('text' => '0123456789'));
        $this->assertEquals(62, $barcode->getHeight());
        $imageResource = imagecreatetruecolor(500, 62);
        $this->_renderer->setResource($imageResource);
        $this->_renderer->setBarcode($barcode);
        $this->assertTrue($this->_renderer->checkParams());
    }

    /**
     * @expectedException Zend_Barcode_Renderer_Exception
     */
    public function testBadHeightOfUserResource()
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped(
                    'GD extension is required to run this test');
        }
        $barcode = new Zend_Barcode_Object_Code39(array('text' => '0123456789'));
        $this->assertEquals(62, $barcode->getHeight());
        $this->_renderer->setBarcode($barcode);
        $imageResource = imagecreatetruecolor(500, 61);
        $this->_renderer->setResource($imageResource);
        $this->_renderer->checkParams();
    }

    public function testGoodWidthOfUserResource()
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped(
                    'GD extension is required to run this test');
        }
        $barcode = new Zend_Barcode_Object_Code39(array('text' => '0123456789'));
        $this->assertEquals(211, $barcode->getWidth());
        $imageResource = imagecreatetruecolor(211, 500);
        $this->_renderer->setResource($imageResource);
        $this->_renderer->setBarcode($barcode);
        $this->assertTrue($this->_renderer->checkParams());
    }

    /**
     * @expectedException Zend_Barcode_Renderer_Exception
     */
    public function testBadWidthOfUserResource()
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped(
                    'GD extension is required to run this test');
        }
        $barcode = new Zend_Barcode_Object_Code39(array('text' => '0123456789'));
        $this->assertEquals(211, $barcode->getWidth());
        $this->_renderer->setBarcode($barcode);
        $imageResource = imagecreatetruecolor(210, 500);
        $this->_renderer->setResource($imageResource);
        $this->_renderer->checkParams();
    }

    /**
     * @expectedException Zend_Barcode_Renderer_Exception
     */
    public function testNoFontWithOrientation()
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped(
                    'GD extension is required to run this test');
        }
        Zend_Barcode::setBarcodeFont('');
        $barcode = new Zend_Barcode_Object_Code39(array('text' => '0123456789'));
        $barcode->setOrientation(1);
        $this->_renderer->setBarcode($barcode);
        $this->_renderer->draw();
    }

    protected function _getRendererWithWidth500AndHeight300()
    {
        return $this->_renderer->setHeight(300)->setWidth(500);
    }

    /**
     * @expectedException Zend_Barcode_Renderer_Exception
     */
    public function testRendererWithUnkownInstructionProvideByObject()
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped(
                    'GD extension is required to run this test');
        }
        parent::testRendererWithUnkownInstructionProvideByObject();
    }

    public function testHorizontalPositionToLeft()
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped(
                    'GD extension is required to run this test');
        }
        parent::testHorizontalPositionToLeft();
    }

    public function testHorizontalPositionToCenter()
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped(
                    'GD extension is required to run this test');
        }
        parent::testHorizontalPositionToCenter();
    }

    public function testHorizontalPositionToRight()
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped(
                    'GD extension is required to run this test');
        }
        parent::testHorizontalPositionToRight();
    }

    public function testVerticalPositionToTop()
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped(
                    'GD extension is required to run this test');
        }
        parent::testVerticalPositionToTop();
    }

    public function testVerticalPositionToMiddle()
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped(
                    'GD extension is required to run this test');
        }
        parent::testVerticalPositionToMiddle();
    }

    public function testVerticalPositionToBottom()
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped(
                    'GD extension is required to run this test');
        }
        parent::testVerticalPositionToBottom();
    }
}
