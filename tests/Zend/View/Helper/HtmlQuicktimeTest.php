<?php
// Call Zend_View_Helper_HtmlQuicktimeTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_HtmlQuicktimeTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';

require_once 'Zend/View.php';
require_once 'Zend/View/Helper/HtmlQuicktime.php';

class Zend_View_Helper_HtmlQuicktimeTest extends PHPUnit_Framework_TestCase 
{
    /**
     * @var Zend_View_Helper_HtmlQuicktime
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

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_HtmlQuicktimeTest");
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
        $this->helper = new Zend_View_Helper_HtmlQuicktime();
        $this->helper->setView($this->view);
    }

    public function tearDown()
    {
        unset($this->helper);
    }

    public function testMakeHtmlQuicktime()
    {
        $htmlQuicktime = $this->helper->htmlQuicktime('/path/to/quicktime.mov');

        $objectStartElement = '<object data="/path/to/quicktime.mov"'
                            . ' type="video/quicktime"'
                            . ' classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"'
                            . ' codebase="http://www.apple.com/qtactivex/qtplugin.cab">';
        
        $this->assertContains($objectStartElement, $htmlQuicktime);
        $this->assertContains('</object>', $htmlQuicktime);
    }
}

// Call Zend_View_Helper_HtmlQuicktimeTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_HtmlQuicktimeTest::main") {
    Zend_View_Helper_HtmlQuicktimeTest::main();
}
