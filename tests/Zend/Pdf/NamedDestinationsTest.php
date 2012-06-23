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
 * @package    Zend_PDF
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Pdf;
use Zend\Pdf;
use Zend\Pdf\Destination;
use Zend\Pdf\Action;

/** Zend_PDF */


/** PHPUnit Test Case */

/**
 * @category   Zend
 * @package    Zend_PDF
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_PDF
 */
class NamedDestinationsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Stores the original set timezone
     * @var string
     */
    private $_originaltimezone;

    public function setUp()
    {
        $this->_originaltimezone = date_default_timezone_get();
        date_default_timezone_set('GMT');
    }

    /**
     * Teardown environment
     */
    public function tearDown()
    {
        date_default_timezone_set($this->_originaltimezone);
    }

    public function testProcessing()
    {
        $pdf = new Pdf\PdfDocument();
        $page1 = $pdf->newPage(Pdf\Page::SIZE_A4);
        $page2 = $pdf->newPage(Pdf\Page::SIZE_A4);
        $page3 = $pdf->newPage(Pdf\Page::SIZE_A4); // not actually included into pages array

        $pdf->pages[] = $page1;
        $pdf->pages[] = $page2;


        $this->assertTrue(count($pdf->getNamedDestinations()) == 0);

        $destination1 = Destination\Fit::create($page1);
        $destination2 = Destination\Fit::create($page2);
        $action1 = Action\GoToAction::create($destination1);

        $pdf->setNamedDestination('GoToPage1', $action1);
        $this->assertTrue($pdf->getNamedDestination('GoToPage1') === $action1);
        $this->assertTrue($pdf->getNamedDestination('GoToPage9') === null);

        $pdf->setNamedDestination('Page2', $destination2);
        $this->assertTrue($pdf->getNamedDestination('Page2') === $destination2);
        $this->assertTrue($pdf->getNamedDestination('Page9') === null);

        $pdf->setNamedDestination('Page1',   $destination1);
        $pdf->setNamedDestination('Page1_1', Destination\Fit::create(1));
        $pdf->setNamedDestination('Page9_1', Destination\Fit::create(9)); // will be egnored

        $action3 = Action\GoToAction::create(Destination\Fit::create($page3));
        $pdf->setNamedDestination('GoToPage3', $action3);

        $this->assertTrue(strpos($pdf->render(), '[(GoToPage1) <</Type /Action /S /GoTo /D [3 0 R /Fit ] >> (Page1) [3 0 R /Fit ] (Page1_1) [1 /Fit ] (Page2) [4 0 R /Fit ] ]') !== false);
    }
}
