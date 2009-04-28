<?php
// Call Zend_View_Helper_HtmlFlashTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_HtmlFlashTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';

require_once 'Zend/View.php';
require_once 'Zend/View/Helper/HtmlFlash.php';

class Zend_View_Helper_HtmlFlashTest extends PHPUnit_Framework_TestCase 
{
    /**
     * @var Zend_View_Helper_HtmlFlash
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

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_HtmlFlashTest");
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
        $this->helper = new Zend_View_Helper_HtmlFlash();
        $this->helper->setView($this->view);
    }

    public function tearDown()
    {
        unset($this->helper);
    }

    public function testMakeHtmlFlash()
    {
        $htmlFlash = $this->helper->htmlFlash('/path/to/flash.swf');

        $objectStartElement = '<object data="/path/to/flash.swf" type="application/x-shockwave-flash">';
  
        $this->assertContains($objectStartElement, $htmlFlash);
        $this->assertContains('</object>', $htmlFlash);
    }
}

// Call Zend_View_Helper_HtmlFlashTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_HtmlFlashTest::main") {
    Zend_View_Helper_HtmlFlashTest::main();
}
