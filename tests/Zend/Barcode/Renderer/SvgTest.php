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
 * @version    $Id: SvgTest.php 20096 2010-01-06 02:05:09Z bkarwin $
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/TestHelper.php';

require_once dirname(__FILE__) . '/TestCommon.php';

require_once 'Zend/Barcode/Renderer/Svg.php';
require_once 'Zend/Barcode/Object/Code39.php';

/**
 * @category   Zend
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @group      Zend_Barcode
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Barcode_Renderer_SvgTest extends Zend_Barcode_Renderer_TestCommon
{

    protected function _getRendererObject($options = null)
    {
        return new Zend_Barcode_Renderer_Svg($options);
    }

    public function testType()
    {
        $this->assertSame('svg', $this->_renderer->getType());
    }

    public function testGoodSvgResource()
    {
        $svgResource = new DOMDocument();
        $this->_renderer->setResource($svgResource, 10);
    }

    /**
     * @expectedException Zend_Barcode_Renderer_Exception
     */
    public function testObjectSvgResource()
    {
        $svgResource = new StdClass();
        $this->_renderer->setResource($svgResource);
    }

    public function testDrawReturnResource()
    {
        Zend_Barcode::setBarcodeFont(dirname(__FILE__) . '/../Object/_fonts/Vera.ttf');
        $barcode = new Zend_Barcode_Object_Code39(array('text' => '0123456789'));
        $this->_renderer->setBarcode($barcode);
        $resource = $this->_renderer->draw();
        $this->assertTrue($resource instanceof DOMDocument);
        Zend_Barcode::setBarcodeFont('');
    }

    public function testDrawWithExistantResourceReturnResource()
    {
        Zend_Barcode::setBarcodeFont(dirname(__FILE__) . '/../Object/_fonts/Vera.ttf');
        $barcode = new Zend_Barcode_Object_Code39(array('text' => '0123456789'));
        $this->_renderer->setBarcode($barcode);
        $svgResource = new DOMDocument();
        $rootElement = $svgResource->createElement('svg');
        $rootElement->setAttribute('xmlns', "http://www.w3.org/2000/svg");
        $rootElement->setAttribute('version', '1.1');
        $rootElement->setAttribute('width', 500);
        $rootElement->setAttribute('height', 300);
        $svgResource->appendChild($rootElement);
        $this->_renderer->setResource($svgResource);
        $resource = $this->_renderer->draw();
        $this->assertTrue($resource instanceof DOMDocument);
        $this->assertSame($resource, $svgResource);
        Zend_Barcode::setBarcodeFont('');
    }

    protected function _getRendererWithWidth500AndHeight300()
    {
        $svg = new DOMDocument();
        $rootElement = $svg->createElement('svg');
        $rootElement->setAttribute('xmlns', "http://www.w3.org/2000/svg");
        $rootElement->setAttribute('version', '1.1');
        $rootElement->setAttribute('width', 500);
        $rootElement->setAttribute('height', 300);
        $svg->appendChild($rootElement);
        return $this->_renderer->setResource($svg);
    }
}
