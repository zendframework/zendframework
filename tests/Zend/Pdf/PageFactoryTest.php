<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace ZendTest\Pdf;

use Zend\Pdf;

/** \Zend\Pdf\Page */

/** PHPUnit Test Case */

/**
 * @category   Zend
 * @package    Zend_PDF
 * @subpackage UnitTests
 * @group      Zend_PDF
 */
class PageFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testNewPageCreator()
    {
        $pdf = new Pdf\PdfDocument();
        $page = $pdf->newPage(Pdf\Page::SIZE_A4);

        $this->assertTrue($page instanceof Pdf\Page);
    }
}
