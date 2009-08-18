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
 * @package    Zend_Pdf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/** Zend_Pdf_Destination */
require_once 'Zend/Pdf/Destination.php';

/** Zend_Pdf_Action */
require_once 'Zend/Pdf/ElementFactory.php';

/** Zend_Pdf */
require_once 'Zend/Pdf.php';


/** PHPUnit Test Case */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @category   Zend
 * @package    Zend_Pdf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Pdf
 */
class Zend_Pdf_DestinationTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        date_default_timezone_set('GMT');
    }

    public function testLoad()
    {
        $pdf = new Zend_Pdf();
        $page1 = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        $page2 = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);


        // Zend_Pdf_Destination_Zoom
        $destArray = new Zend_Pdf_Element_Array();
        $destArray->items[] = $page2->getPageDictionary();
        $destArray->items[] = new Zend_Pdf_Element_Name('XYZ');
        $destArray->items[] = new Zend_Pdf_Element_Numeric(0);    // left
        $destArray->items[] = new Zend_Pdf_Element_Numeric(842);  // top
        $destArray->items[] = new Zend_Pdf_Element_Numeric(1);    // zoom

        $destination = Zend_Pdf_Destination::load($destArray);

        $this->assertTrue($destination instanceof Zend_Pdf_Destination_Zoom);
        $this->assertEquals($destination->getResource()->toString(), '[4 0 R /XYZ 0 842 1 ]');


        // Zend_Pdf_Destination_Fit
        $destArray = new Zend_Pdf_Element_Array();
        $destArray->items[] = $page2->getPageDictionary();
        $destArray->items[] = new Zend_Pdf_Element_Name('Fit');

        $destination = Zend_Pdf_Destination::load($destArray);

        $this->assertTrue($destination instanceof Zend_Pdf_Destination_Fit);
        $this->assertEquals($destination->getResource()->toString(), '[4 0 R /Fit ]');


        // Zend_Pdf_Destination_FitHorizontally
        $destArray = new Zend_Pdf_Element_Array();
        $destArray->items[] = $page2->getPageDictionary();
        $destArray->items[] = new Zend_Pdf_Element_Name('FitH');
        $destArray->items[] = new Zend_Pdf_Element_Numeric(842);  // top

        $destination = Zend_Pdf_Destination::load($destArray);

        $this->assertTrue($destination instanceof Zend_Pdf_Destination_FitHorizontally);
        $this->assertEquals($destination->getResource()->toString(), '[4 0 R /FitH 842 ]');


        // Zend_Pdf_Destination_FitVertically
        $destArray = new Zend_Pdf_Element_Array();
        $destArray->items[] = $page2->getPageDictionary();
        $destArray->items[] = new Zend_Pdf_Element_Name('FitV');
        $destArray->items[] = new Zend_Pdf_Element_Numeric(0);    // left

        $destination = Zend_Pdf_Destination::load($destArray);

        $this->assertTrue($destination instanceof Zend_Pdf_Destination_FitVertically);
        $this->assertEquals($destination->getResource()->toString(), '[4 0 R /FitV 0 ]');


        // Zend_Pdf_Destination_FitRectangle
        $destArray = new Zend_Pdf_Element_Array();
        $destArray->items[] = $page2->getPageDictionary();
        $destArray->items[] = new Zend_Pdf_Element_Name('FitR');
        $destArray->items[] = new Zend_Pdf_Element_Numeric(0);    // left
        $destArray->items[] = new Zend_Pdf_Element_Numeric(10);   // bottom
        $destArray->items[] = new Zend_Pdf_Element_Numeric(595);  // right
        $destArray->items[] = new Zend_Pdf_Element_Numeric(842);  // top

        $destination = Zend_Pdf_Destination::load($destArray);

        $this->assertTrue($destination instanceof Zend_Pdf_Destination_FitRectangle);
        $this->assertEquals($destination->getResource()->toString(), '[4 0 R /FitR 0 10 595 842 ]');


        // Zend_Pdf_Destination_FitBoundingBox
        $destArray = new Zend_Pdf_Element_Array();
        $destArray->items[] = $page2->getPageDictionary();
        $destArray->items[] = new Zend_Pdf_Element_Name('FitB');

        $destination = Zend_Pdf_Destination::load($destArray);

        $this->assertTrue($destination instanceof Zend_Pdf_Destination_FitBoundingBox);
        $this->assertEquals($destination->getResource()->toString(), '[4 0 R /FitB ]');


        // Zend_Pdf_Destination_FitBoundingBoxHorizontally
        $destArray = new Zend_Pdf_Element_Array();
        $destArray->items[] = $page2->getPageDictionary();
        $destArray->items[] = new Zend_Pdf_Element_Name('FitBH');
        $destArray->items[] = new Zend_Pdf_Element_Numeric(842);  // top

        $destination = Zend_Pdf_Destination::load($destArray);

        $this->assertTrue($destination instanceof Zend_Pdf_Destination_FitBoundingBoxHorizontally);
        $this->assertEquals($destination->getResource()->toString(), '[4 0 R /FitBH 842 ]');


        // Zend_Pdf_Destination_FitBoundingBoxVertically
        $destArray = new Zend_Pdf_Element_Array();
        $destArray->items[] = $page2->getPageDictionary();
        $destArray->items[] = new Zend_Pdf_Element_Name('FitBV');
        $destArray->items[] = new Zend_Pdf_Element_Numeric(0);    // left

        $destination = Zend_Pdf_Destination::load($destArray);

        $this->assertTrue($destination instanceof Zend_Pdf_Destination_FitBoundingBoxVertically);
        $this->assertEquals($destination->getResource()->toString(), '[4 0 R /FitBV 0 ]');
    }

    public function testGettersSetters()
    {
        $pdf = new Zend_Pdf();
        $page1 = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        $page2 = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);


        // Zend_Pdf_Destination_Zoom
        $destArray = new Zend_Pdf_Element_Array();
        $destArray->items[] = $page2->getPageDictionary();
        $destArray->items[] = new Zend_Pdf_Element_Name('XYZ');
        $destArray->items[] = new Zend_Pdf_Element_Numeric(0);    // left
        $destArray->items[] = new Zend_Pdf_Element_Numeric(842);  // top
        $destArray->items[] = new Zend_Pdf_Element_Numeric(1);    // zoom

        $destination = Zend_Pdf_Destination::load($destArray);

        $this->assertEquals($destination->getLeftEdge(), 0);
        $destination->setLeftEdge(5);
        $this->assertEquals($destination->getLeftEdge(), 5);

        $this->assertEquals($destination->getTopEdge(), 842);
        $destination->setTopEdge(825);
        $this->assertEquals($destination->getTopEdge(), 825);

        $this->assertEquals($destination->getZoomFactor(), 1);
        $destination->setZoomFactor(0.5);
        $this->assertEquals($destination->getZoomFactor(), 0.5);


        // Zend_Pdf_Destination_FitHorizontally
        $destArray = new Zend_Pdf_Element_Array();
        $destArray->items[] = $page2->getPageDictionary();
        $destArray->items[] = new Zend_Pdf_Element_Name('FitH');
        $destArray->items[] = new Zend_Pdf_Element_Numeric(842);  // top

        $destination = Zend_Pdf_Destination::load($destArray);

        $this->assertEquals($destination->getTopEdge(), 842);
        $destination->setTopEdge(825);
        $this->assertEquals($destination->getTopEdge(), 825);


        // Zend_Pdf_Destination_FitVertically
        $destArray = new Zend_Pdf_Element_Array();
        $destArray->items[] = $page2->getPageDictionary();
        $destArray->items[] = new Zend_Pdf_Element_Name('FitV');
        $destArray->items[] = new Zend_Pdf_Element_Numeric(0);    // left

        $destination = Zend_Pdf_Destination::load($destArray);

        $this->assertEquals($destination->getLeftEdge(), 0);
        $destination->setLeftEdge(5);
        $this->assertEquals($destination->getLeftEdge(), 5);


        // Zend_Pdf_Destination_FitRectangle
        $destArray = new Zend_Pdf_Element_Array();
        $destArray->items[] = $page2->getPageDictionary();
        $destArray->items[] = new Zend_Pdf_Element_Name('FitR');
        $destArray->items[] = new Zend_Pdf_Element_Numeric(0);    // left
        $destArray->items[] = new Zend_Pdf_Element_Numeric(10);   // bottom
        $destArray->items[] = new Zend_Pdf_Element_Numeric(595);  // right
        $destArray->items[] = new Zend_Pdf_Element_Numeric(842);  // top

        $destination = Zend_Pdf_Destination::load($destArray);

        $this->assertEquals($destination->getLeftEdge(), 0);
        $destination->setLeftEdge(5);
        $this->assertEquals($destination->getLeftEdge(), 5);

        $this->assertEquals($destination->getBottomEdge(), 10);
        $destination->setBottomEdge(20);
        $this->assertEquals($destination->getBottomEdge(), 20);

        $this->assertEquals($destination->getRightEdge(), 595);
        $destination->setRightEdge(590);
        $this->assertEquals($destination->getRightEdge(), 590);

        $this->assertEquals($destination->getTopEdge(), 842);
        $destination->setTopEdge(825);
        $this->assertEquals($destination->getTopEdge(), 825);


        // Zend_Pdf_Destination_FitBoundingBoxHorizontally
        $destArray = new Zend_Pdf_Element_Array();
        $destArray->items[] = $page2->getPageDictionary();
        $destArray->items[] = new Zend_Pdf_Element_Name('FitBH');
        $destArray->items[] = new Zend_Pdf_Element_Numeric(842);  // top

        $destination = Zend_Pdf_Destination::load($destArray);

        $this->assertEquals($destination->getTopEdge(), 842);
        $destination->setTopEdge(825);
        $this->assertEquals($destination->getTopEdge(), 825);


        // Zend_Pdf_Destination_FitBoundingBoxVertically
        $destArray = new Zend_Pdf_Element_Array();
        $destArray->items[] = $page2->getPageDictionary();
        $destArray->items[] = new Zend_Pdf_Element_Name('FitBV');
        $destArray->items[] = new Zend_Pdf_Element_Numeric(0);    // left

        $destination = Zend_Pdf_Destination::load($destArray);

        $this->assertEquals($destination->getLeftEdge(), 0);
        $destination->setLeftEdge(5);
        $this->assertEquals($destination->getLeftEdge(), 5);
    }

    public function testCreate()
    {
        $pdf = new Zend_Pdf();
        $page1 = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        $page2 = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);

        $destination = Zend_Pdf_Destination_Zoom::create($page2, 0, 842, 0.5);
        $this->assertTrue($destination instanceof Zend_Pdf_Destination_Zoom);
        $this->assertEquals($destination->getResource()->toString(), '[4 0 R /XYZ 0 842 0.5 ]');

        $destination = Zend_Pdf_Destination_Fit::create($page2);
        $this->assertTrue($destination instanceof Zend_Pdf_Destination_Fit);
        $this->assertEquals($destination->getResource()->toString(), '[4 0 R /Fit ]');

        $destination = Zend_Pdf_Destination_FitHorizontally::create($page2, 842);
        $this->assertTrue($destination instanceof Zend_Pdf_Destination_FitHorizontally);
        $this->assertEquals($destination->getResource()->toString(), '[4 0 R /FitH 842 ]');

        $destination = Zend_Pdf_Destination_FitVertically::create(2, 0);
        $this->assertTrue($destination instanceof Zend_Pdf_Destination_FitVertically);
        $this->assertEquals($destination->getResource()->toString(), '[2 /FitV 0 ]');

        $destination = Zend_Pdf_Destination_FitRectangle::create($page1, 0, 10, 595, 842);
        $this->assertTrue($destination instanceof Zend_Pdf_Destination_FitRectangle);
        $this->assertEquals($destination->getResource()->toString(), '[3 0 R /FitR 0 10 595 842 ]');

        $destination = Zend_Pdf_Destination_FitBoundingBox::create(1);
        $this->assertTrue($destination instanceof Zend_Pdf_Destination_FitBoundingBox);
        $this->assertEquals($destination->getResource()->toString(), '[1 /FitB ]');

        $destination = Zend_Pdf_Destination_FitBoundingBoxHorizontally::create($page2, 842);
        $this->assertTrue($destination instanceof Zend_Pdf_Destination_FitBoundingBoxHorizontally);
        $this->assertEquals($destination->getResource()->toString(), '[4 0 R /FitBH 842 ]');

        $destination = Zend_Pdf_Destination_FitBoundingBoxVertically::create($page2, 0);
        $this->assertTrue($destination instanceof Zend_Pdf_Destination_FitBoundingBoxVertically);
        $this->assertEquals($destination->getResource()->toString(), '[4 0 R /FitBV 0 ]');
    }
}
