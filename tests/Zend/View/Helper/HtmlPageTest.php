<?php
// Call Zend_View_Helper_HtmlPageTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_HtmlPageTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';

require_once 'Zend/View.php';
require_once 'Zend/View/Helper/HtmlPage.php';

class Zend_View_Helper_HtmlPageTest extends PHPUnit_Framework_TestCase 
{
    /**
     * @var Zend_View_Helper_HtmlPage
     */
    public $helper;

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_HtmlPageTest");
        PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->view = new Zend_View();
        $this->helper = new Zend_View_Helper_HtmlPage();
        $this->helper->setView($this->view);
    }

    public function tearDown()
    {
        unset($this->helper);
    }

    public function testMakeHtmlPage()
    {
        $htmlPage = $this->helper->htmlPage('/path/to/page.html');

        $objectStartElement = '<object data="/path/to/page.html"'
                            . ' type="text/html"'
                            . ' classid="clsid:25336920-03F9-11CF-8FD0-00AA00686F13">';
        
        $this->assertContains($objectStartElement, $htmlPage);
        $this->assertContains('</object>', $htmlPage);
    }
}

// Call Zend_View_Helper_HtmlPageTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_HtmlPageTest::main") {
    Zend_View_Helper_HtmlPageTest::main();
}
