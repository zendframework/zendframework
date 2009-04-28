<?php
// Call Zend_View_Helper_FormTextTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_FormTextTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';

require_once 'Zend/View.php';
require_once 'Zend/View/Helper/FormText.php';
require_once 'Zend/Registry.php';

/**
 * Zend_View_Helper_FormTextTest 
 *
 * Tests formText helper, including some common functionality of all form helpers
 * 
 * @uses PHPUnit_Framework_TestCase
 * @version $Id$
 */
class Zend_View_Helper_FormTextTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_FormTextTest");
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
        $this->helper = new Zend_View_Helper_FormText();
        $this->helper->setView($this->view);
    }

    public function testIdSetFromName()
    {
        $element = $this->helper->formText('foo');
        $this->assertContains('name="foo"', $element);
        $this->assertContains('id="foo"', $element);
    }

    public function testSetIdFromAttribs()
    {
        $element = $this->helper->formText('foo', null, array('id' => 'bar'));
        $this->assertContains('name="foo"', $element);
        $this->assertContains('id="bar"', $element);
    }

    public function testSetValue()
    {
        $element = $this->helper->formText('foo', 'bar');
        $this->assertContains('name="foo"', $element);
        $this->assertContains('value="bar"', $element);
    }

    public function testReadOnlyAttribute()
    {
        $element = $this->helper->formText('foo', null, array('readonly' => 'readonly'));
        $this->assertContains('readonly="readonly"', $element);
    }

    /**
     * ZF-1666
     */
    public function testCanDisableElement()
    {
        $html = $this->helper->formText(array(
            'name'    => 'foo',
            'value'   => 'bar',
            'attribs' => array('disable' => true)
        ));

        $this->assertRegexp('/<input[^>]*?(disabled="disabled")/', $html);
    }

    /**
     * ZF-1666
     */
    public function testDisablingElementDoesNotRenderHiddenElements()
    {
        $html = $this->helper->formText(array(
            'name'    => 'foo',
            'value'   => 'bar',
            'attribs' => array('disable' => true)
        ));

        $this->assertNotRegexp('/<input[^>]*?(type="hidden")/', $html);
    }

    public function testRendersAsHtmlByDefault()
    {
        $test = $this->helper->formText('foo', 'bar');
        $this->assertNotContains(' />', $test);
    }

    public function testCanRendersAsXHtml()
    {
        $this->view->doctype('XHTML1_STRICT');
        $test = $this->helper->formText('foo', 'bar');
        $this->assertContains(' />', $test);
    }
}

// Call Zend_View_Helper_FormTextTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_FormTextTest::main") {
    Zend_View_Helper_FormTextTest::main();
}
