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

require_once 'Zend/Barcode/Renderer/Pdf.php';
require_once 'Zend/Pdf.php';
require_once 'Zend/Barcode/Object/Code39.php';

/**
 * @category   Zend
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @group      Zend_Barcode
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Barcode_Renderer_PdfTest extends Zend_Barcode_Renderer_TestCommon
{

    protected function _getRendererObject($options = null)
    {
        return new Zend_Barcode_Renderer_Pdf($options);
    }

    public function testType()
    {
        $this->assertSame('pdf', $this->_renderer->getType());
    }

    public function testGoodPdfResource()
    {
        $pdfResource = new Zend_Pdf();
        $this->_renderer->setResource($pdfResource, 10);
    }

    /**
     * @expectedException Zend_Barcode_Renderer_Exception
     */
    public function testObjectPdfResource()
    {
        $pdfResource = new StdClass();
        $this->_renderer->setResource($pdfResource);
    }

    public function testDrawReturnResource()
    {
        Zend_Barcode::setBarcodeFont(dirname(__FILE__) . '/../Object/_fonts/Vera.ttf');
        $barcode = new Zend_Barcode_Object_Code39(array('text' => '0123456789'));
        $this->_renderer->setBarcode($barcode);
        $resource = $this->_renderer->draw();
        $this->assertTrue($resource instanceof Zend_Pdf);
        Zend_Barcode::setBarcodeFont('');
    }

    public function testDrawWithExistantResourceReturnResource()
    {
        Zend_Barcode::setBarcodeFont(dirname(__FILE__) . '/../Object/_fonts/Vera.ttf');
        $barcode = new Zend_Barcode_Object_Code39(array('text' => '0123456789'));
        $this->_renderer->setBarcode($barcode);
        $pdfResource = new Zend_Pdf();
        $this->_renderer->setResource($pdfResource);
        $resource = $this->_renderer->draw();
        $this->assertTrue($resource instanceof Zend_Pdf);
        $this->assertSame($resource, $pdfResource);
        Zend_Barcode::setBarcodeFont('');
    }

    protected function _getRendererWithWidth500AndHeight300()
    {
        $pdf = new Zend_Pdf();
        $pdf->pages[] = new Zend_Pdf_Page('500:300:');
        return $this->_renderer->setResource($pdf);
    }

    public function testHorizontalPositionToCenter()
    {
        $renderer = $this->_getRendererWithWidth500AndHeight300();
        $barcode = new Zend_Barcode_Object_Code39(array('text' => '0123456789'));
        $this->assertEquals(211, $barcode->getWidth());
        $renderer->setBarcode($barcode);
        $renderer->setHorizontalPosition('center');
        $renderer->draw();
        $this->assertEquals(197, $renderer->getLeftOffset());
    }

    public function testHorizontalPositionToRight()
    {
        $renderer = $this->_getRendererWithWidth500AndHeight300();
        $barcode = new Zend_Barcode_Object_Code39(array('text' => '0123456789'));
        $this->assertEquals(211, $barcode->getWidth());
        $renderer->setBarcode($barcode);
        $renderer->setHorizontalPosition('right');
        $renderer->draw();
        $this->assertEquals(394.5, $renderer->getLeftOffset());
    }

    public function testVerticalPositionToMiddle()
    {
        $renderer = $this->_getRendererWithWidth500AndHeight300();
        $barcode = new Zend_Barcode_Object_Code39(array('text' => '0123456789'));
        $this->assertEquals(61, $barcode->getHeight());
        $renderer->setBarcode($barcode);
        $renderer->setVerticalPosition('middle');
        $renderer->draw();
        $this->assertEquals(134, $renderer->getTopOffset());
    }

    public function testVerticalPositionToBottom()
    {
        $renderer = $this->_getRendererWithWidth500AndHeight300();
        $barcode = new Zend_Barcode_Object_Code39(array('text' => '0123456789'));
        $this->assertEquals(61, $barcode->getHeight());
        $renderer->setBarcode($barcode);
        $renderer->setVerticalPosition('bottom');
        $renderer->draw();
        $this->assertEquals(269.5, $renderer->getTopOffset());
    }
}
