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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Barcode\Renderer;
use Zend\Barcode;
use Zend\Barcode\Renderer\Svg;
use Zend\Barcode\Object\Code39;

/**
 * @category   Zend
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @group      Zend_Barcode
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SvgTest extends TestCommon
{

    protected function _getRendererObject($options = null)
    {
        return new Svg($options);
    }

    public function testType()
    {
        $this->assertSame('svg', $this->_renderer->getType());
    }

    public function testGoodHeight()
    {
        $this->assertSame(0, $this->_renderer->getHeight());
        $this->_renderer->setHeight(123);
        $this->assertSame(123, $this->_renderer->getHeight());
        $this->_renderer->setHeight(0);
        $this->assertSame(0, $this->_renderer->getHeight());
    }

    public function testBadHeight()
    {
        $this->setExpectedException('\Zend\Barcode\Renderer\Exception');
        $this->_renderer->setHeight(-1);
    }

    public function testGoodWidth()
    {
        $this->assertSame(0, $this->_renderer->getWidth());
        $this->_renderer->setWidth(123);
        $this->assertSame(123, $this->_renderer->getWidth());
        $this->_renderer->setWidth(0);
        $this->assertSame(0, $this->_renderer->getWidth());
    }

    public function testBadWidth()
    {
        $this->setExpectedException('\Zend\Barcode\Renderer\Exception');
        $this->_renderer->setWidth(-1);
    }

    public function testGoodSvgResource()
    {
        $svgResource = new \DOMDocument();
        $this->_renderer->setResource($svgResource, 10);
    }

    public function testObjectSvgResource()
    {
        $this->setExpectedException('Zend\Barcode\Renderer\Exception');
        $svgResource = new \StdClass();
        $this->_renderer->setResource($svgResource);
    }

    public function testDrawReturnResource()
    {
        Barcode\Barcode::setBarcodeFont(__DIR__ . '/../Object/_fonts/Vera.ttf');
        $barcode = new Code39(array('text' => '0123456789'));
        $this->_renderer->setBarcode($barcode);
        $resource = $this->_renderer->draw();
        $this->assertTrue($resource instanceof \DOMDocument);
        Barcode\Barcode::setBarcodeFont('');
    }

    public function testDrawWithExistantResourceReturnResource()
    {
        Barcode\Barcode::setBarcodeFont(__DIR__ . '/../Object/_fonts/Vera.ttf');
        $barcode = new Code39(array('text' => '0123456789'));
        $this->_renderer->setBarcode($barcode);
        $svgResource = new \DOMDocument();
        $rootElement = $svgResource->createElement('svg');
        $rootElement->setAttribute('xmlns', "http://www.w3.org/2000/svg");
        $rootElement->setAttribute('version', '1.1');
        $rootElement->setAttribute('width', 500);
        $rootElement->setAttribute('height', 300);
        $svgResource->appendChild($rootElement);
        $this->_renderer->setResource($svgResource);
        $resource = $this->_renderer->draw();
        $this->assertTrue($resource instanceof \DOMDocument);
        $this->assertSame($resource, $svgResource);
        Barcode\Barcode::setBarcodeFont('');
    }

    protected function _getRendererWithWidth500AndHeight300()
    {
        $svg = new \DOMDocument();
        $rootElement = $svg->createElement('svg');
        $rootElement->setAttribute('xmlns', "http://www.w3.org/2000/svg");
        $rootElement->setAttribute('version', '1.1');
        $rootElement->setAttribute('width', 500);
        $rootElement->setAttribute('height', 300);
        $svg->appendChild($rootElement);
        return $this->_renderer->setResource($svg);
    }
}
