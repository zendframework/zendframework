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
namespace ZendTest\Barcode;
use Zend\Barcode;
use Zend\Barcode\Renderer;
use Zend\Barcode\Object;
use Zend\Config;
use Zend\Pdf;

/**
 * @category   Zend
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @group      Zend_Barcode
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // Set timezone to avoid "It is not safe to rely on the system's timezone settings."
        // message if timezone is not set within php.ini
        date_default_timezone_set('GMT');
    }

    public function testMinimalFactory()
    {
        $this->_checkGDRequirement();

        $renderer = Barcode\Barcode::factory('code39');
        $this->assertTrue($renderer instanceof Renderer\Image);
        $this->assertTrue($renderer->getBarcode() instanceof Object\Code39);
    }

    public function testMinimalFactoryWithRenderer()
    {
        $renderer = Barcode\Barcode::factory('code39', 'PDF');
        $this->assertTrue($renderer instanceof Renderer\Pdf);
        $this->assertTrue($renderer->getBarcode() instanceof Object\Code39);
    }

    public function testFactoryWithOptions()
    {
        $this->_checkGDRequirement();

        $options = array('barHeight' => 123);
        $renderer = Barcode\Barcode::factory('code39', 'image', $options);
        $this->assertEquals(123, $renderer->getBarcode()->getBarHeight());
    }

    public function testFactoryWithAutomaticExceptionRendering()
    {
        $this->_checkGDRequirement();

        $options = array('barHeight' => - 1);
        $renderer = Barcode\Barcode::factory('code39', 'image', $options);
        $this->assertTrue($renderer instanceof Renderer\Image);
        $this->assertTrue($renderer->getBarcode() instanceof Object\Error);
    }

    /**
     * @expectedException \Zend\Barcode\Object\Exception
     */
    public function testFactoryWithoutAutomaticObjectExceptionRendering()
    {
        $options = array('barHeight' => - 1);
        $renderer = Barcode\Barcode::factory('code39', 'image', $options, array(), false);
    }

    /**
     * @expectedException \Zend\Barcode\Renderer\Exception
     */
    public function testFactoryWithoutAutomaticRendererExceptionRendering()
    {
        $this->_checkGDRequirement();

        $options = array('imageType' => 'my');
        $renderer = Barcode\Barcode::factory('code39', 'image', array(), $options, false);
        $this->markTestIncomplete('Need to throw a configuration exception in renderer');
    }

    public function testFactoryWithZendConfig()
    {
        $this->_checkGDRequirement();

        $config = new Config\Config(array('barcode'  => 'code39',
                                          'renderer' => 'image'));
        $renderer = Barcode\Barcode::factory($config);
        $this->assertTrue($renderer instanceof Renderer\Image);
        $this->assertTrue($renderer->getBarcode() instanceof Object\Code39);

    }

    public function testFactoryWithZendConfigAndObjectOptions()
    {
        $this->_checkGDRequirement();

        $config = new Config\Config(array('barcode'       => 'code25' ,
                                          'barcodeParams' => array(
                                          'barHeight'     => 123)));
        $renderer = Barcode\Barcode::factory($config);
        $this->assertTrue($renderer instanceof Renderer\Image);
        $this->assertTrue($renderer->getBarcode() instanceof Object\Code25);
        $this->assertEquals(123, $renderer->getBarcode()->getBarHeight());
    }

    public function testFactoryWithZendConfigAndRendererOptions()
    {
        $this->_checkGDRequirement();

        $config = new Config\Config(array('barcode'        => 'code25' ,
                                          'rendererParams' => array(
                                          'imageType'      => 'gif')));
        $renderer = Barcode\Barcode::factory($config);
        $this->assertTrue($renderer instanceof Renderer\Image);
        $this->assertTrue($renderer->getBarcode() instanceof Object\Code25);
        $this->assertSame('gif', $renderer->getImageType());
    }

    public function testFactoryWithoutBarcodeWithAutomaticExceptionRender()
    {
        $this->_checkGDRequirement();

        $renderer = Barcode\Barcode::factory(null);
        $this->assertTrue($renderer instanceof Renderer\Image);
        $this->assertTrue($renderer->getBarcode() instanceof Object\Error);
    }

    public function testFactoryWithoutBarcodeWithAutomaticExceptionRenderWithZendConfig()
    {
        $this->_checkGDRequirement();

        $config = new Config\Config(array('barcode' => null));
        $renderer = Barcode\Barcode::factory($config);
        $this->assertTrue($renderer instanceof Renderer\Image);
        $this->assertTrue($renderer->getBarcode() instanceof Object\Error);
    }

    public function testFactoryWithExistingBarcodeObject()
    {
        $this->_checkGDRequirement();

        $barcode = new Object\Code25();
        $renderer = Barcode\Barcode::factory($barcode);
        $this->assertSame($barcode, $renderer->getBarcode());
    }

    public function testBarcodeObjectFactoryWithExistingBarcodeObject()
    {
        $barcode = new Object\Code25();
        $generatedBarcode = Barcode\Barcode::makeBarcode($barcode);
        $this->assertSame($barcode, $generatedBarcode);
    }

    public function testBarcodeObjectFactoryWithBarcodeAsString()
    {
        $barcode = Barcode\Barcode::makeBarcode('code25');
        $this->assertTrue($barcode instanceof Object\Code25);
    }

    public function testBarcodeObjectFactoryWithBarcodeAsStringAndConfigAsArray()
    {
        $barcode = Barcode\Barcode::makeBarcode('code25', array('barHeight' => 123));
        $this->assertTrue($barcode instanceof Object\Code25);
        $this->assertSame(123, $barcode->getBarHeight());
    }

    public function testBarcodeObjectFactoryWithBarcodeAsStringAndConfigAsZendConfig()
    {
        $config = new Config\Config(array('barHeight' => 123));
        $barcode = Barcode\Barcode::makeBarcode('code25', $config);
        $this->assertTrue($barcode instanceof Object\Code25);
        $this->assertSame(123, $barcode->getBarHeight());
    }

    public function testBarcodeObjectFactoryWithBarcodeAsZendConfig()
    {
        $config = new Config\Config(
                array('barcode' => 'code25' ,
                        'barcodeParams' => array(
                                'barHeight' => 123)));
        $barcode = Barcode\Barcode::makeBarcode($config);
        $this->assertTrue($barcode instanceof Object\Code25);
        $this->assertSame(123, $barcode->getBarHeight());
    }

    /**
     * @expectedException \Zend\Barcode\Exception
     */
    public function testBarcodeObjectFactoryWithBarcodeAsZendConfigButNoBarcodeParameter()
    {
        $config = new Config\Config(array( 'barcodeParams' => array('barHeight' => 123) ));
        $barcode = Barcode\Barcode::makeBarcode($config);
    }

    /**
     * @expectedException \Zend\Barcode\Exception
     */
    public function testBarcodeObjectFactoryWithBarcodeAsZendConfigAndBadBarcodeParameters()
    {
        $barcode = Barcode\Barcode::makeBarcode('code25', null);
    }

    public function testBarcodeObjectFactoryWithNamespace()
    {
        require_once __DIR__ . '/Object/_files/BarcodeNamespace.php';

        $barcode = Barcode\Barcode::makeBarcode('error',
                                                array('barcodeNamespace' => '\ZendTest\Barcode\Object\Namespace1'));
        $this->assertTrue($barcode instanceof \ZendTest\Barcode\Object\Namespace1\Error);
    }

    /**
     * @expectedException \Zend\Barcode\Exception
     */
    public function testBarcodeObjectFactoryWithNamespaceButWithoutExtendingObjectAbstract()
    {
        require_once __DIR__ . '/Object/_files/BarcodeNamespaceWithoutExtendingObjectAbstract.php';
        $barcode = Barcode\Barcode::makeBarcode('error',
                                                array('barcodeNamespace' => '\ZendTest\Barcode\Object\Namespace2'));
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testBarcodeObjectFactoryWithUnexistantBarcode()
    {
        $barcode = Barcode\Barcode::makeBarcode('zf123', array());
    }

    public function testBarcodeRendererFactoryWithExistingBarcodeRenderer()
    {
        $this->_checkGDRequirement();

        $renderer = new Renderer\Image();
        $generatedBarcode = Barcode\Barcode::makeRenderer($renderer);
        $this->assertSame($renderer, $generatedBarcode);
    }

    public function testBarcodeRendererFactoryWithBarcodeAsString()
    {
        $this->_checkGDRequirement();

        $renderer = Barcode\Barcode::makeRenderer('image');
        $this->assertTrue($renderer instanceof Renderer\Image);
    }

    public function testBarcodeRendererFactoryWithBarcodeAsStringAndConfigAsArray()
    {
        $this->_checkGDRequirement();

        $renderer = Barcode\Barcode::makeRenderer('image', array('imageType' => 'gif'));
        $this->assertTrue($renderer instanceof Renderer\Image);
        $this->assertSame('gif', $renderer->getImageType());
    }

    public function testBarcodeRendererFactoryWithBarcodeAsStringAndConfigAsZendConfig()
    {
        $this->_checkGDRequirement();

        $config = new Config\Config(array('imageType' => 'gif'));
        $renderer = Barcode\Barcode::makeRenderer('image', $config);
        $this->assertTrue($renderer instanceof Renderer\Image);
        $this->assertSame('gif', $renderer->getimageType());
    }

    public function testBarcodeRendererFactoryWithBarcodeAsZendConfig()
    {
        $this->_checkGDRequirement();

        $config = new Config\Config(array('renderer'       => 'image' ,
                                          'rendererParams' => array('imageType' => 'gif')));
        $renderer = Barcode\Barcode::makeRenderer($config);
        $this->assertTrue($renderer instanceof Renderer\Image);
        $this->assertSame('gif', $renderer->getimageType());
    }

    /**
     * @expectedException \Zend\Barcode\Exception
     */
    public function testBarcodeRendererFactoryWithBarcodeAsZendConfigButNoBarcodeParameter()
    {
        $config = new Config\Config(array( 'rendererParams' => array('imageType' => 'gif') ));
        $renderer = Barcode\Barcode::makeRenderer($config);
    }

    /**
     * @expectedException \Zend\Barcode\Exception
     */
    public function testBarcodeRendererFactoryWithBarcodeAsZendConfigAndBadBarcodeParameters()
    {
        $renderer = Barcode\Barcode::makeRenderer('image', null);
    }

    public function testBarcodeRendererFactoryWithNamespace()
    {
        $this->_checkGDRequirement();

        require_once __DIR__ . '/Renderer/_files/RendererNamespace.php';
        $renderer = Barcode\Barcode::makeRenderer('image',
                                                  array('rendererNamespace' => '\ZendTest\Barcode\Renderer\Namespace1'));
        $this->assertTrue($renderer instanceof \ZendTest\Barcode\Renderer\Namespace1\Image);
    }

    /**
     * @expectedException \Zend\Barcode\Exception
     */
    public function testBarcodeFactoryWithNamespaceButWithoutExtendingRendererAbstract()
    {
        require_once __DIR__ . '/Renderer/_files/RendererNamespaceWithoutExtendingRendererAbstract.php';
        $renderer = Barcode\Barcode::makeRenderer('image',
                                                  array('rendererNamespace' => '\ZendTest\Barcode\Renderer\Namespace2'));
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testBarcodeRendererFactoryWithUnexistantRenderer()
    {
        $renderer = Barcode\Barcode::makeRenderer('zend', array());
    }

    public function testProxyBarcodeRendererDrawAsImage()
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped('GD extension is required to run this test');
        }
        $resource = Barcode\Barcode::draw('code25', 'image');
        $this->assertTrue(gettype($resource) == 'resource', 'Image must be a resource');
        $this->assertTrue(get_resource_type($resource) == 'gd', 'Image must be a GD resource');
    }

    public function testProxyBarcodeRendererDrawAsPdf()
    {
        Barcode\Barcode::setBarcodeFont(__DIR__ . '/Object/_fonts/Vera.ttf');
        $resource = Barcode\Barcode::draw('code25', 'PDF');
        $this->assertTrue($resource instanceof Pdf\PdfDocument);
        Barcode\Barcode::setBarcodeFont('');
    }

    public function testProxyBarcodeObjectFont()
    {
        Barcode\Barcode::setBarcodeFont('my_font.ttf');
        $barcode = new Object\Code25();
        $this->assertSame('my_font.ttf', $barcode->getFont());
        Barcode\Barcode::setBarcodeFont('');
    }

    protected function _checkGDRequirement()
    {
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('This test requires the GD extension');
        }
    }
}
