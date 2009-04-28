<?php
// Call Zend_View_Helper_FormFileTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_FormFileTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';

require_once 'Zend/View.php';
require_once 'Zend/View/Helper/FormFile.php';
require_once 'Zend/Registry.php';

/**
 * Zend_View_Helper_FormFileTest 
 *
 * Tests formFile helper
 * 
 * @uses PHPUnit_Framework_TestCase
 */
class Zend_View_Helper_FormFileTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_FormFileTest");
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
        $this->helper = new Zend_View_Helper_FormFile();
        $this->helper->setView($this->view);
    }

    /**
     * ZF-1666
     */
    public function testCanDisableElement()
    {
        $html = $this->helper->formFile(array(
            'name'    => 'foo',
            'attribs' => array('disable' => true)
        ));

        $this->assertRegexp('/<input[^>]*?(disabled="disabled")/', $html);
    }

    /**
     * ZF-1666
     */
    public function testDisablingElementDoesNotRenderHiddenElements()
    {
        $html = $this->helper->formFile(array(
            'name'    => 'foo',
            'attribs' => array('disable' => true)
        ));

        $this->assertNotRegexp('/<input[^>]*?(type="hidden")/', $html);
    }


    public function testRendersAsHtmlByDefault()
    {
        $test = $this->helper->formFile(array(
            'name'    => 'foo',
        ));
        $this->assertNotContains(' />', $test);
    }

    public function testCanRendersAsXHtml()
    {
        $this->view->doctype('XHTML1_STRICT');
        $test = $this->helper->formFile(array(
            'name'    => 'foo',
        ));
        $this->assertContains(' />', $test);
    }
}

// Call Zend_View_Helper_FormFileTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_FormFileTest::main") {
    Zend_View_Helper_FormFileTest::main();
}
