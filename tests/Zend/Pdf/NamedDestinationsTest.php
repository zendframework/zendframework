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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/** Zend_Pdf */


/** PHPUnit Test Case */

/**
 * @category   Zend
 * @package    Zend_Pdf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Pdf
 */
class Zend_Pdf_NamedDestinationsTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        date_default_timezone_set('GMT');
    }

    public function testProcessing()
    {
        $pdf = new Zend_Pdf();
        $page1 = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        $page2 = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        $page3 = $pdf->newPage(Zend_Pdf_Page::SIZE_A4); // not actually included into pages array

        $pdf->pages[] = $page1;
        $pdf->pages[] = $page2;


        $this->assertTrue(count($pdf->getNamedDestinations()) == 0);

        $destination1 = Zend_Pdf_Destination_Fit::create($page1);
        $destination2 = Zend_Pdf_Destination_Fit::create($page2);
        $action1 = Zend_Pdf_Action_GoTo::create($destination1);

        $pdf->setNamedDestination('GoToPage1', $action1);
        $this->assertTrue($pdf->getNamedDestination('GoToPage1') === $action1);
        $this->assertTrue($pdf->getNamedDestination('GoToPage9') === null);

        $pdf->setNamedDestination('Page2', $destination2);
        $this->assertTrue($pdf->getNamedDestination('Page2') === $destination2);
        $this->assertTrue($pdf->getNamedDestination('Page9') === null);

        $pdf->setNamedDestination('Page1',   $destination1);
        $pdf->setNamedDestination('Page1_1', Zend_Pdf_Destination_Fit::create(1));
        $pdf->setNamedDestination('Page9_1', Zend_Pdf_Destination_Fit::create(9)); // will be egnored

        $action3 = Zend_Pdf_Action_GoTo::create(Zend_Pdf_Destination_Fit::create($page3));
        $pdf->setNamedDestination('GoToPage3', $action3);

        $this->assertTrue(strpos($pdf->render(), '[(GoToPage1) <</Type /Action /S /GoTo /D [3 0 R /Fit ] >> (Page1) [3 0 R /Fit ] (Page1_1) [1 /Fit ] (Page2) [4 0 R /Fit ] ]') !== false);
    }
}
