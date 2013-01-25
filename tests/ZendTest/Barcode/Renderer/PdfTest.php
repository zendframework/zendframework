<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Barcode
 */

namespace ZendTest\Barcode\Renderer;

use ZendPdf as Pdf;
use Zend\Barcode;
use Zend\Barcode\Object;

/**
 * @category   Zend
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @group      Zend_Barcode
 */
class PdfTest extends TestCommon
{
    public function setUp()
    {
        if (!constant('TESTS_ZEND_BARCODE_PDF_SUPPORT')) {
            $this->markTestSkipped('Enable TESTS_ZEND_BARCODE_PDF_SUPPORT to test PDF render');
        }
        parent::setUp();
    }

    protected function getRendererObject($options = null)
    {
        return new \Zend\Barcode\Renderer\Pdf($options);
    }

    public function testType()
    {
        $this->assertSame('pdf', $this->renderer->getType());
    }

    public function testGoodPdfResource()
    {
        $pdfResource = new Pdf\PdfDocument();
        $this->renderer->setResource($pdfResource, 10);
    }

    public function testDrawReturnResource()
    {
        Barcode\Barcode::setBarcodeFont(__DIR__ . '/../Object/_fonts/Vera.ttf');
        $barcode = new Object\Code39(array('text' => '0123456789'));
        $this->renderer->setBarcode($barcode);
        $resource = $this->renderer->draw();
        $this->assertTrue($resource instanceof Pdf\PdfDocument);
        Barcode\Barcode::setBarcodeFont('');
    }

    public function testDrawWithExistantResourceReturnResource()
    {
        Barcode\Barcode::setBarcodeFont(__DIR__ . '/../Object/_fonts/Vera.ttf');
        $barcode = new Object\Code39(array('text' => '0123456789'));
        $this->renderer->setBarcode($barcode);
        $pdfResource = new Pdf\PdfDocument();
        $this->renderer->setResource($pdfResource);
        $resource = $this->renderer->draw();
        $this->assertTrue($resource instanceof Pdf\PdfDocument);
        $this->assertSame($resource, $pdfResource);
        Barcode\Barcode::setBarcodeFont('');
    }

    protected function getRendererWithWidth500AndHeight300()
    {
        $pdf = new Pdf\PdfDocument();
        $pdf->pages[] = new Pdf\Page('500:300:');
        return $this->renderer->setResource($pdf);
    }

    public function testHorizontalPositionToCenter()
    {
        $renderer = $this->getRendererWithWidth500AndHeight300();
        $barcode = new Object\Code39(array('text' => '0123456789'));
        $this->assertEquals(211, $barcode->getWidth());
        $renderer->setBarcode($barcode);
        $renderer->setHorizontalPosition('center');
        $renderer->draw();
        $this->assertEquals(197, $renderer->getLeftOffset());
    }

    public function testHorizontalPositionToRight()
    {
        $renderer = $this->getRendererWithWidth500AndHeight300();
        $barcode = new Object\Code39(array('text' => '0123456789'));
        $this->assertEquals(211, $barcode->getWidth());
        $renderer->setBarcode($barcode);
        $renderer->setHorizontalPosition('right');
        $renderer->draw();
        $this->assertEquals(394.5, $renderer->getLeftOffset());
    }

    public function testVerticalPositionToMiddle()
    {
        $renderer = $this->getRendererWithWidth500AndHeight300();
        $barcode = new Object\Code39(array('text' => '0123456789'));
        $this->assertEquals(62, $barcode->getHeight());
        $renderer->setBarcode($barcode);
        $renderer->setVerticalPosition('middle');
        $renderer->draw();
        $this->assertEquals(134, $renderer->getTopOffset());
    }

    public function testVerticalPositionToBottom()
    {
        $renderer = $this->getRendererWithWidth500AndHeight300();
        $barcode = new Object\Code39(array('text' => '0123456789'));
        $this->assertEquals(62, $barcode->getHeight());
        $renderer->setBarcode($barcode);
        $renderer->setVerticalPosition('bottom');
        $renderer->draw();
        $this->assertEquals(269, $renderer->getTopOffset());
    }
}
