<?php
// Call Zend_Form_Element_PasswordTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Form_Element_PasswordTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';
require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Form/Element/Password.php';
require_once 'Zend/View.php';

/**
 * Test class for Zend_Form_Element_Password
 */
class Zend_Form_Element_PasswordTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Form_Element_PasswordTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->errors = array();
        $this->element = new Zend_Form_Element_Password('foo');
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
    }

    public function testPasswordElementSubclassesXhtmlElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element_Xhtml);
    }

    public function testPasswordElementInstanceOfBaseElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element);
    }

    public function testHelperAttributeSetToFormPasswordByDefault()
    {
        $this->assertEquals('formPassword', $this->element->getAttrib('helper'));
    }

    public function testPasswordElementUsesPasswordHelperInViewHelperDecoratorByDefault()
    {
        $this->_checkZf2794();

        $decorator = $this->element->getDecorator('viewHelper');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_ViewHelper);
        $decorator->setElement($this->element);
        $helper = $decorator->getHelper();
        $this->assertEquals('formPassword', $helper);
    }

    public function testPasswordValueMaskedByGetMessages()
    {
        $this->element->addValidators(array(
            'Alpha',
            'Alnum'
        ));
        $value  = 'abc-123';
        $expect = '*******';
        $this->assertFalse($this->element->isValid($value));
        foreach ($this->element->getMessages() as $message) {
            $this->assertNotContains($value, $message);
            $this->assertContains($expect, $message, $message);
        }
    }

    public function handleErrors($errno, $errmsg, $errfile, $errline, $errcontext)
    {
        if (!isset($this->errors)) {
            $this->errors = array();
        }
        $this->errors[] = $errmsg;
    }

    /**
     * ZF-2656
     */
    public function testGetMessagesReturnsEmptyArrayWhenNoMessagesRegistered()
    {
        set_error_handler(array($this, 'handleErrors'));
        $messages = $this->element->getMessages();
        restore_error_handler();
        $this->assertSame(array(), $messages);
        $this->assertTrue(empty($this->errors));
    }

    /**
     * Used by test methods susceptible to ZF-2794, marks a test as incomplete
     *
     * @link   http://framework.zend.com/issues/browse/ZF-2794
     * @return void
     */
    protected function _checkZf2794()
    {
        if (strtolower(substr(PHP_OS, 0, 3)) == 'win' && version_compare(PHP_VERSION, '5.1.4', '=')) {
            $this->markTestIncomplete('Error occurs for PHP 5.1.4 on Windows');
        }
    }

    public function testRenderPasswordAttributeShouldDefaultToFalse()
    {
        $this->assertFalse($this->element->renderPassword());
    }

    public function testShouldAllowSettingRenderPasswordFlag()
    {
        $this->testRenderPasswordAttributeShouldDefaultToFalse();
        $this->element->setRenderPassword(true);
        $this->assertTrue($this->element->renderPassword());
        $this->element->setRenderPassword(false);
        $this->assertFalse($this->element->renderPassword());
    }

    public function testShouldPassRenderPasswordAttributeToViewHelper()
    {
        $this->element->setValue('foobar')
                      ->setView(new Zend_View());
        $test = $this->element->render();
        $this->assertContains('value=""', $test);

        $this->element->setRenderPassword(true);
        $test = $this->element->render();
        $this->assertContains('value="foobar"', $test);
    }
}

// Call Zend_Form_Element_PasswordTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Form_Element_PasswordTest::main") {
    Zend_Form_Element_PasswordTest::main();
}
