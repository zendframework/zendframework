<?php
// Call Zend_View_Helper_FormImageTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_FormImageTest::main");
}

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/TestHelper.php';

require_once 'Zend/View.php';
require_once 'Zend/View/Helper/FormImage.php';

/**
 * Test class for Zend_View_Helper_FormImage.
 */
class Zend_View_Helper_FormImageTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_FormImageTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
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
        $this->helper = new Zend_View_Helper_FormImage();
        $this->helper->setView($this->view);
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
    }

    public function testFormImageRendersFormImageXhtml()
    {
        $button = $this->helper->formImage('foo', 'bar');
        $this->assertRegexp('/<input[^>]*?src="bar"/', $button);
        $this->assertRegexp('/<input[^>]*?name="foo"/', $button);
        $this->assertRegexp('/<input[^>]*?type="image"/', $button);
    }

    public function testDisablingFormImageRendersImageInputWithDisableAttribute()
    {
        $button = $this->helper->formImage('foo', 'bar', array('disable' => true));
        $this->assertRegexp('/<input[^>]*?disabled="disabled"/', $button);
        $this->assertRegexp('/<input[^>]*?src="bar"/', $button);
        $this->assertRegexp('/<input[^>]*?name="foo"/', $button);
        $this->assertRegexp('/<input[^>]*?type="image"/', $button);
    }
}

// Call Zend_View_Helper_FormImageTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_FormImageTest::main") {
    Zend_View_Helper_FormImageTest::main();
}
