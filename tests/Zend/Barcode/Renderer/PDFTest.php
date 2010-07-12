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
use Zend\PDF;
use Zend\Barcode;
use Zend\Barcode\Object;

/**
 * @category   Zend
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @group      Zend_Barcode
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PDFTest extends TestCommon
{
    protected function _getRendererObject($options = null)
    {
        return new \Zend\Barcode\Renderer\PDF($options);
    }

    public function testType()
    {
        $this->assertSame('PDF', $this->_renderer->getType());
    }

    public function testGoodPdfResource()
    {
        $pdfResource = new PDF\PDFDocument();
        $this->_renderer->setResource($pdfResource, 10);
    }

    /**
     * @expectedException \Zend\Barcode\Renderer\Exception
     */
    public function testObjectPdfResource()
    {
        $pdfResource = new \StdClass();
        $this->_renderer->setResource($pdfResource);
    }

    public function testDrawReturnResource()
    {
        Barcode\Barcode::setBarcodeFont(__DIR__ . '/../Object/_fonts/Vera.ttf');
        $barcode = new Object\Code39(array('text' => '0123456789'));
        $this->_renderer->setBarcode($barcode);
        $resource = $this->_renderer->draw();
        $this->assertTrue($resource instanceof PDF\PDFDocument);
        Barcode\Barcode::setBarcodeFont('');
    }

    public function testDrawWithExistantResourceReturnResource()
    {
        Barcode\Barcode::setBarcodeFont(__DIR__ . '/../Object/_fonts/Vera.ttf');
        $barcode = new Object\Code39(array('text' => '0123456789'));
        $this->_renderer->setBarcode($barcode);
        $pdfResource = new PDF\PDFDocument();
        $this->_renderer->setResource($pdfResource);
        $resource = $this->_renderer->draw();
        $this->assertTrue($resource instanceof PDF\PDFDocument);
        $this->assertSame($resource, $pdfResource);
        Barcode\Barcode::setBarcodeFont('');
    }

    protected function _getRendererWithWidth500AndHeight300()
    {
        $pdf = new PDF\PDFDocument();
        $pdf->pages[] = new PDF\Page('500:300:');
        return $this->_renderer->setResource($pdf);
    }

    public function testHorizontalPositionToCenter()
    {
        $renderer = $this->_getRendererWithWidth500AndHeight300();
        $barcode = new Object\Code39(array('text' => '0123456789'));
        $this->assertEquals(211, $barcode->getWidth());
        $renderer->setBarcode($barcode);
        $renderer->setHorizontalPosition('center');
        $renderer->draw();
        $this->assertEquals(197, $renderer->getLeftOffset());
    }

    public function testHorizontalPositionToRight()
    {
        $renderer = $this->_getRendererWithWidth500AndHeight300();
        $barcode = new Object\Code39(array('text' => '0123456789'));
        $this->assertEquals(211, $barcode->getWidth());
        $renderer->setBarcode($barcode);
        $renderer->setHorizontalPosition('right');
        $renderer->draw();
        $this->assertEquals(394.5, $renderer->getLeftOffset());
    }

    public function testVerticalPositionToMiddle()
    {
        $renderer = $this->_getRendererWithWidth500AndHeight300();
        $barcode = new Object\Code39(array('text' => '0123456789'));
        $this->assertEquals(62, $barcode->getHeight());
        $renderer->setBarcode($barcode);
        $renderer->setVerticalPosition('middle');
        $renderer->draw();
        $this->assertEquals(134, $renderer->getTopOffset());
    }

    public function testVerticalPositionToBottom()
    {
        $renderer = $this->_getRendererWithWidth500AndHeight300();
        $barcode = new Object\Code39(array('text' => '0123456789'));
        $this->assertEquals(62, $barcode->getHeight());
        $renderer->setBarcode($barcode);
        $renderer->setVerticalPosition('bottom');
        $renderer->draw();
        $this->assertEquals(269, $renderer->getTopOffset());
    }
}
