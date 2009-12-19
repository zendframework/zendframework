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

require_once dirname(dirname(dirname(__FILE__))) . '/TestHelper.php';

require_once 'Zend/Barcode.php';
require_once 'Zend/Config.php';

/**
 * @category   Zend
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @group      Zend_Barcode
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Barcode_FactoryTest extends PHPUnit_Framework_TestCase
{

    public function testMinimalFactory()
    {
        $renderer = Zend_Barcode::factory('code39');
        $this->assertTrue($renderer instanceof Zend_Barcode_Renderer_Image);
        $this->assertTrue($renderer->getBarcode() instanceof Zend_Barcode_Object_Code39);
    }

    public function testMinimalFactoryWithRenderer()
    {
        $renderer = Zend_Barcode::factory('code39', 'pdf');
        $this->assertTrue($renderer instanceof Zend_Barcode_Renderer_Pdf);
        $this->assertTrue($renderer->getBarcode() instanceof Zend_Barcode_Object_Code39);
    }

    public function testFactoryWithOptions()
    {
        $options = array('barHeight' => 123);
        $renderer = Zend_Barcode::factory('code39', 'image', $options);
        $this->assertEquals(123, $renderer->getBarcode()->getBarHeight());
    }

    public function testFactoryWithAutomaticExceptionRendering()
    {
        $options = array('barHeight' => - 1);
        $renderer = Zend_Barcode::factory('code39', 'image', $options);
        $this->assertTrue($renderer instanceof Zend_Barcode_Renderer_Image);
        $this->assertTrue($renderer->getBarcode() instanceof Zend_Barcode_Object_Error);
    }

    /**
     * @expectedException Zend_Barcode_Object_Exception
     */
    public function testFactoryWithoutAutomaticObjectExceptionRendering()
    {
        $options = array('barHeight' => - 1);
        $renderer = Zend_Barcode::factory('code39', 'image', $options, array(), false);
    }

    /**
     * @expectedException Zend_Barcode_Renderer_Exception
     */
    public function testFactoryWithoutAutomaticRendererExceptionRendering()
    {
        $options = array('imageType' => 'my');
        $renderer = Zend_Barcode::factory('code39', 'image', array(), $options, false);
        $this->markTestIncomplete('Need to throw a configuration exception in renderer');
    }

    public function testFactoryWithZendConfig()
    {
        $config = new Zend_Config(
                array('barcode' => 'code39' ,
                        'renderer' => 'image'));
        $renderer = Zend_Barcode::factory($config);
        $this->assertTrue($renderer instanceof Zend_Barcode_Renderer_Image);
        $this->assertTrue($renderer->getBarcode() instanceof Zend_Barcode_Object_Code39);

    }

    public function testFactoryWithZendConfigAndObjectOptions()
    {
        $config = new Zend_Config(
                array('barcode' => 'code25' ,
                        'barcodeParams' => array(
                                'barHeight' => 123)));
        $renderer = Zend_Barcode::factory($config);
        $this->assertTrue($renderer instanceof Zend_Barcode_Renderer_Image);
        $this->assertTrue($renderer->getBarcode() instanceof Zend_Barcode_Object_Code25);
        $this->assertEquals(123, $renderer->getBarcode()->getBarHeight());
    }

    public function testFactoryWithZendConfigAndRendererOptions()
    {
        $config = new Zend_Config(
                array('barcode' => 'code25' ,
                        'rendererParams' => array(
                                'imageType' => 'gif')));
        $renderer = Zend_Barcode::factory($config);
        $this->assertTrue($renderer instanceof Zend_Barcode_Renderer_Image);
        $this->assertTrue($renderer->getBarcode() instanceof Zend_Barcode_Object_Code25);
        $this->assertSame('gif', $renderer->getImageType());
    }

    public function testFactoryWithoutBarcodeWithAutomaticExceptionRender()
    {
        $renderer = Zend_Barcode::factory(null);
        $this->assertTrue($renderer instanceof Zend_Barcode_Renderer_Image);
        $this->assertTrue($renderer->getBarcode() instanceof Zend_Barcode_Object_Error);
    }

    public function testFactoryWithoutBarcodeWithAutomaticExceptionRenderWithZendConfig()
    {
        $config = new Zend_Config(array('barcode' => null));
        $renderer = Zend_Barcode::factory($config);
        $this->assertTrue($renderer instanceof Zend_Barcode_Renderer_Image);
        $this->assertTrue($renderer->getBarcode() instanceof Zend_Barcode_Object_Error);
    }

    public function testFactoryWithExistingBarcodeObject()
    {
        $barcode = new Zend_Barcode_Object_Code25();
        $renderer = Zend_Barcode::factory($barcode);
        $this->assertSame($barcode, $renderer->getBarcode());
    }

    public function testBarcodeObjectFactoryWithExistingBarcodeObject()
    {
        $barcode = new Zend_Barcode_Object_Code25();
        $generatedBarcode = Zend_Barcode::makeBarcode($barcode);
        $this->assertSame($barcode, $generatedBarcode);
    }

    public function testBarcodeObjectFactoryWithBarcodeAsString()
    {
        $barcode = Zend_Barcode::makeBarcode('code25');
        $this->assertTrue($barcode instanceof Zend_Barcode_Object_Code25);
    }

    public function testBarcodeObjectFactoryWithBarcodeAsStringAndConfigAsArray()
    {
        $barcode = Zend_Barcode::makeBarcode('code25', array('barHeight' => 123));
        $this->assertTrue($barcode instanceof Zend_Barcode_Object_Code25);
        $this->assertSame(123, $barcode->getBarHeight());
    }

    public function testBarcodeObjectFactoryWithBarcodeAsStringAndConfigAsZendConfig()
    {
        $config = new Zend_Config(array('barHeight' => 123));
        $barcode = Zend_Barcode::makeBarcode('code25', $config);
        $this->assertTrue($barcode instanceof Zend_Barcode_Object_Code25);
        $this->assertSame(123, $barcode->getBarHeight());
    }

    public function testBarcodeObjectFactoryWithBarcodeAsZendConfig()
    {
        $config = new Zend_Config(
                array('barcode' => 'code25' ,
                        'barcodeParams' => array(
                                'barHeight' => 123)));
        $barcode = Zend_Barcode::makeBarcode($config);
        $this->assertTrue($barcode instanceof Zend_Barcode_Object_Code25);
        $this->assertSame(123, $barcode->getBarHeight());
    }

    /**
     * @expectedException Zend_Barcode_Exception
     */
    public function testBarcodeObjectFactoryWithBarcodeAsZendConfigButNoBarcodeParameter()
    {
        $config = new Zend_Config(
                array(
                        'barcodeParams' => array(
                                'barHeight' => 123)));
        $barcode = Zend_Barcode::makeBarcode($config);
    }

    /**
     * @expectedException Zend_Barcode_Exception
     */
    public function testBarcodeObjectFactoryWithBarcodeAsZendConfigAndBadBarcodeParameters()
    {
        $barcode = Zend_Barcode::makeBarcode('code25', null);
    }

    public function testBarcodeObjectFactoryWithNamespace()
    {
        require_once dirname(__FILE__) . '/Object/_files/BarcodeNamespace.php';
        $barcode = Zend_Barcode::makeBarcode('error',
                array(
                        'barcodeNamespace' => 'My_Namespace'));
        $this->assertTrue($barcode instanceof My_Namespace_Error);
    }

    /**
     * @expectedException Zend_Barcode_Exception
     */
    public function testBarcodeObjectFactoryWithNamespaceButWithoutExtendingObjectAbstract()
    {
        require_once dirname(__FILE__) . '/Object/_files/BarcodeNamespaceWithoutExtendingObjectAbstract.php';
        $barcode = Zend_Barcode::makeBarcode('error',
                array(
                        'barcodeNamespace' => 'My_Namespace_Other'));
    }

    public function testBarcodeRendererFactoryWithExistingBarcodeRenderer()
    {
        $renderer = new Zend_Barcode_Renderer_Image();
        $generatedBarcode = Zend_Barcode::makeRenderer($renderer);
        $this->assertSame($renderer, $generatedBarcode);
    }

    public function testBarcodeRendererFactoryWithBarcodeAsString()
    {
        $renderer = Zend_Barcode::makeRenderer('image');
        $this->assertTrue($renderer instanceof Zend_Barcode_Renderer_Image);
    }

    public function testBarcodeRendererFactoryWithBarcodeAsStringAndConfigAsArray()
    {
        $renderer = Zend_Barcode::makeRenderer('image', array('imageType' => 'gif'));
        $this->assertTrue($renderer instanceof Zend_Barcode_Renderer_Image);
        $this->assertSame('gif', $renderer->getImageType());
    }

    public function testBarcodeRendererFactoryWithBarcodeAsStringAndConfigAsZendConfig()
    {
        $config = new Zend_Config(array('imageType' => 'gif'));
        $renderer = Zend_Barcode::makeRenderer('image', $config);
        $this->assertTrue($renderer instanceof Zend_Barcode_Renderer_Image);
        $this->assertSame('gif', $renderer->getimageType());
    }

    public function testBarcodeRendererFactoryWithBarcodeAsZendConfig()
    {
        $config = new Zend_Config(
                array('renderer' => 'image' ,
                        'rendererParams' => array(
                                'imageType' => 'gif')));
        $renderer = Zend_Barcode::makeRenderer($config);
        $this->assertTrue($renderer instanceof Zend_Barcode_Renderer_Image);
        $this->assertSame('gif', $renderer->getimageType());
    }

    /**
     * @expectedException Zend_Barcode_Exception
     */
    public function testBarcodeRendererFactoryWithBarcodeAsZendConfigButNoBarcodeParameter()
    {
        $config = new Zend_Config(
                array(
                        'rendererParams' => array(
                                'imageType' => 'gif')));
        $renderer = Zend_Barcode::makeRenderer($config);
    }

    /**
     * @expectedException Zend_Barcode_Exception
     */
    public function testBarcodeRendererFactoryWithBarcodeAsZendConfigAndBadBarcodeParameters()
    {
        $renderer = Zend_Barcode::makeRenderer('image', null);
    }

    public function testBarcodeRendererFactoryWithNamespace()
    {
        require_once dirname(__FILE__) . '/Renderer/_files/RendererNamespace.php';
        $renderer = Zend_Barcode::makeRenderer('image',
                array(
                        'rendererNamespace' => 'My_Namespace'));
        $this->assertTrue($renderer instanceof My_Namespace_Image);
    }

    /**
     * @expectedException Zend_Barcode_Exception
     */
    public function testBarcodeFactoryWithNamespaceButWithoutExtendingRendererAbstract()
    {
        require_once dirname(__FILE__) . '/Renderer/_files/RendererNamespaceWithoutExtendingRendererAbstract.php';
        $renderer = Zend_Barcode::makeRenderer('image',
                array(
                        'rendererNamespace' => 'My_Namespace_Other'));
    }

    public function testProxyBarcodeRendererDrawAsImage()
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped(
                    'GD extension is required to run this test');
        }
        $resource = Zend_Barcode::draw('code25', 'image');
        $this->assertTrue(gettype($resource) == 'resource', 'Image must be a resource');
        $this->assertTrue(get_resource_type($resource) == 'gd', 'Image must be a GD resource');
    }

    public function testProxyBarcodeRendererDrawAsPdf()
    {
        Zend_Barcode::setBarcodeFont(dirname(__FILE__) . '/Object/_fonts/Vera.ttf');
        $resource = Zend_Barcode::draw('code25', 'pdf');
        $this->assertTrue($resource instanceof Zend_Pdf);
        Zend_Barcode::setBarcodeFont('');
    }

    public function testProxyBarcodeObjectFont()
    {
        Zend_Barcode::setBarcodeFont('my_font.ttf');
        $barcode = new Zend_Barcode_Object_Code25();
        $this->assertSame('my_font.ttf', $barcode->getFont());
        Zend_Barcode::setBarcodeFont('');
    }
}
