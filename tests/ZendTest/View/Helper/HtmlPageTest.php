<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\View\Helper;

use Zend\View\Renderer\PhpRenderer as View;
use Zend\View\Helper\HtmlPage;

/**
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class HtmlPageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HtmlPage
     */
    public $helper;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->view   = new View();
        $this->helper = new HtmlPage();
        $this->helper->setView($this->view);
    }

    public function tearDown()
    {
        unset($this->helper);
    }

    public function testMakeHtmlPage()
    {
        $htmlPage = $this->helper->__invoke('/path/to/page.html');

        $objectStartElement = '<object data="&#x2F;path&#x2F;to&#x2F;page.html"'
                            . ' type="text&#x2F;html"'
                            . ' classid="clsid&#x3A;25336920-03F9-11CF-8FD0-00AA00686F13">';

        $this->assertContains($objectStartElement, $htmlPage);
        $this->assertContains('</object>', $htmlPage);
    }
}
