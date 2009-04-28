<?php
// Call Zend_FormErrorsTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_FormErrorsTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';

require_once 'Zend/View/Helper/FormErrors.php';
require_once 'Zend/View.php';

/**
 * Test class for Zend_View_Helper_FormErrors
 */
class Zend_View_Helper_FormErrorsTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_FormErrorsTest");
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
        $this->view   = new Zend_View();
        $this->helper = new Zend_View_Helper_FormErrors();
        $this->helper->setView($this->view);
        ob_start();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        ob_end_clean();
    }

    public function testGetElementEndReturnsDefaultValue()
    {
        $this->assertEquals('</li></ul>', $this->helper->getElementEnd());
    }

    public function testGetElementSeparatorReturnsDefaultValue()
    {
        $this->assertEquals('</li><li>', $this->helper->getElementSeparator());
    }

    public function testGetElementStartReturnsDefaultValue()
    {
        $this->assertEquals('<ul%s><li>', $this->helper->getElementStart());
    }

    public function testCanSetElementEndString()
    {
        $this->testGetElementEndReturnsDefaultValue();
        $this->helper->setElementEnd('</pre></div>');
        $this->assertEquals('</pre></div>', $this->helper->getElementEnd());
    }

    public function testCanSetElementSeparatorString()
    {
        $this->testGetElementSeparatorReturnsDefaultValue();
        $this->helper->setElementSeparator('<br />');
        $this->assertEquals('<br />', $this->helper->getElementSeparator());
    }

    public function testCanSetElementStartString()
    {
        $this->testGetElementStartReturnsDefaultValue();
        $this->helper->setElementStart('<div><pre>');
        $this->assertEquals('<div><pre>', $this->helper->getElementStart());
    }

    public function testFormErrorsRendersUnorderedListByDefault()
    {
        $errors = array('foo', 'bar', 'baz');
        $html = $this->helper->formErrors($errors);
        $this->assertContains('<ul', $html);
        foreach ($errors as $error) {
            $this->assertContains('<li>' . $error . '</li>', $html);
        }
        $this->assertContains('</ul>', $html);
    }

    public function testFormErrorsRendersWithSpecifiedStrings()
    {
        $this->helper->setElementStart('<dl><dt>')
                     ->setElementSeparator('</dt><dt>')
                     ->setElementEnd('</dt></dl>');
        $errors = array('foo', 'bar', 'baz');
        $html = $this->helper->formErrors($errors);
        $this->assertContains('<dl>', $html);
        foreach ($errors as $error) {
            $this->assertContains('<dt>' . $error . '</dt>', $html);
        }
        $this->assertContains('</dl>', $html);
    }

    public function testFormErrorsPreventsXssAttacks()
    {
        $errors = array(
            'bad' => '\"><script>alert("xss");</script>',
        );
        $html = $this->helper->formErrors($errors);
        $this->assertNotContains($errors['bad'], $html);
        $this->assertContains('&', $html);
    }

    public function testCanDisableEscapingErrorMessages()
    {
        $errors = array(
            'foo' => '<b>Field is required</b>',
            'bar' => '<a href="/help">Please click here for more information</a>'
        );
        $html = $this->helper->formErrors($errors, array('escape' => false));
        $this->assertContains($errors['foo'], $html);
        $this->assertContains($errors['bar'], $html);
    }

    /**
     * @issue ZF-3477
     * @link http://framework.zend.com/issues/browse/ZF-3477
     */
    public function testCanSetClassAttribute()
    {
        $options = array('class' => 'custom-class');
        $acutallHtml = $this->helper->formErrors(array(), $options);
        $this->assertEquals('<ul class="custom-class"><li></li></ul>', $acutallHtml);
    }
}

// Call Zend_View_Helper_FormErrorsTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_FormErrorsTest::main") {
    Zend_View_Helper_FormErrorsTest::main();
}
