<?php
// Call Zend_View_Helper_FormPasswordTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_FormPasswordTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';

require_once 'Zend/View.php';
require_once 'Zend/View/Helper/FormPassword.php';
require_once 'Zend/Registry.php';

/**
 * Zend_View_Helper_FormPasswordTest 
 *
 * Tests formPassword helper
 * 
 * @uses PHPUnit_Framework_TestCase
 */
class Zend_View_Helper_FormPasswordTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_FormPasswordTest");
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
        if (Zend_Registry::isRegistered('Zend_View_Helper_Doctype')) {
            $registry = Zend_Registry::getInstance();
            unset($registry['Zend_View_Helper_Doctype']);
        }
        $this->view = new Zend_View();
        $this->helper = new Zend_View_Helper_FormPassword();
        $this->helper->setView($this->view);
    }

    /**
     * @see ZF-1666
     */
    public function testCanDisableElement()
    {
        $html = $this->helper->formPassword(array(
            'name'    => 'foo',
            'value'   => 'bar',
            'attribs' => array('disable' => true)
        ));

        $this->assertRegexp('/<input[^>]*?(disabled="disabled")/', $html);
    }

    /**
     * @see ZF-1666
     */
    public function testDisablingElementDoesNotRenderHiddenElements()
    {
        $html = $this->helper->formPassword(array(
            'name'    => 'foo',
            'value'   => 'bar',
            'attribs' => array('disable' => true)
        ));

        $this->assertNotRegexp('/<input[^>]*?(type="hidden")/', $html);
    }

    public function testShouldRenderAsHtmlByDefault()
    {
        $test = $this->helper->formPassword('foo', 'bar');
        $this->assertNotContains(' />', $test);
    }

    public function testShouldAllowRenderingAsXhtml()
    {
        $this->view->doctype('XHTML1_STRICT');
        $test = $this->helper->formPassword('foo', 'bar');
        $this->assertContains(' />', $test);
    }

    public function testShouldNotRenderValueByDefault()
    {
        $test = $this->helper->formPassword('foo', 'bar');
        $this->assertNotContains('bar', $test);
    }

    /**
     * @see ZF-2860
     */
    public function testShouldRenderValueWhenRenderPasswordFlagPresentAndTrue()
    {
        $test = $this->helper->formPassword('foo', 'bar', array('renderPassword' => true));
        $this->assertContains('value="bar"', $test);
    }

    /**
     * @see ZF-2860
     */
    public function testRenderPasswordAttribShouldNeverBeRendered()
    {
        $test = $this->helper->formPassword('foo', 'bar', array('renderPassword' => true));
        $this->assertNotContains('renderPassword', $test);
        $test = $this->helper->formPassword('foo', 'bar', array('renderPassword' => false));
        $this->assertNotContains('renderPassword', $test);
    }
}

// Call Zend_View_Helper_FormPasswordTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_FormPasswordTest::main") {
    Zend_View_Helper_FormPasswordTest::main();
}
