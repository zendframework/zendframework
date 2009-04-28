<?php
// Call Zend_Form_Decorator_CallbackTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Form_Decorator_CallbackTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';
require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Form/Decorator/Callback.php';

require_once 'Zend/Form/Element.php';

/**
 * Test class for Zend_Form_Decorator_Callback
 */
class Zend_Form_Decorator_CallbackTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Form_Decorator_CallbackTest");
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
        $this->decorator = new Zend_Form_Decorator_Callback();
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

    public function testCanSetCallback()
    {
        $callback = 'Zend_Form_Decorator_CallbackTest_TestCallback';
        $this->decorator->setCallback($callback);
        $this->assertSame($callback, $this->decorator->getCallback());

        $callback = array('Zend_Form_Decorator_CallbackTest_TestCallbackClass', 'direct');
        $this->decorator->setCallback($callback);
        $this->assertSame($callback, $this->decorator->getCallback());
    }

    public function testCanSetCallbackViaOptions()
    {
        $callback = 'Zend_Form_Decorator_CallbackTest_TestCallback';
        $this->decorator->setOptions(array('callback' => $callback));
        $this->assertSame($callback, $this->decorator->getCallback());
    }

    public function testInvalidCallbackRaisesException()
    {
        try {
            $this->decorator->setCallback(true);
            $this->fail('Only string or array callbacks should be allowed');
        } catch (Zend_Form_Exception $e) {
            $this->assertContains('Invalid', $e->getMessage());
        }

        try {
            $o = new stdClass;
            $this->decorator->setCallback($o);
            $this->fail('Only string or array callbacks should be allowed');
        } catch (Zend_Form_Exception $e) {
            $this->assertContains('Invalid', $e->getMessage());
        }

        try {
            $this->decorator->setCallback(array('foo', 'bar', 'baz'));
            $this->fail('Only arrays of two elements should be allowed as callbacks');
        } catch (Zend_Form_Exception $e) {
            $this->assertContains('Invalid', $e->getMessage());
        }

        try {
            $this->decorator->setCallback(array('foo'));
            $this->fail('Only arrays of two elements should be allowed as callbacks');
        } catch (Zend_Form_Exception $e) {
            $this->assertContains('Invalid', $e->getMessage());
        }
    }

    public function testRenderCallsFunctionCallback()
    {
        $callback = 'Zend_Form_Decorator_CallbackTest_TestCallback';
        $element  = new Zend_Form_Element('foobar');
        $element->setLabel('Label Me');

        $this->decorator->setOptions(array('callback' => $callback))
                        ->setElement($element);

        $content = $this->decorator->render('foo bar');
        $this->assertContains('foo bar', $content);
        $this->assertContains($element->getName(), $content);
        $this->assertContains($element->getLabel(), $content);
    }

    public function testRenderCallsMethodCallback()
    {
        $callback = array('Zend_Form_Decorator_CallbackTest_TestCallbackClass', 'direct');
        $element  = new Zend_Form_Element('foobar');
        $element->setLabel('Label Me');

        $this->decorator->setOptions(array('callback' => $callback))
                        ->setElement($element);

        $content = $this->decorator->render('foo bar');
        $this->assertContains('foo bar', $content);
        $this->assertContains($element->getName(), $content);
        $this->assertContains($element->getLabel(), $content);
        $this->assertContains('Item ', $content);
    }

    public function testRenderCanPrepend()
    {
        $callback = 'Zend_Form_Decorator_CallbackTest_TestCallback';
        $element  = new Zend_Form_Element('foobar');
        $element->setLabel('Label Me');

        $this->decorator->setOptions(array('callback' => $callback, 'placement' => 'prepend'))
                        ->setElement($element);

        $content = $this->decorator->render('foo bar');
        $this->assertContains('foo bar', $content);
        $this->assertContains($element->getName(), $content);
        $this->assertContains($element->getLabel(), $content);
        $this->assertRegexp('/foo bar$/s', $content);
    }

    public function testRenderCanReplaceContent()
    {
        $callback = 'Zend_Form_Decorator_CallbackTest_TestCallback';
        $element  = new Zend_Form_Element('foobar');
        $element->setLabel('Label Me');

        $this->decorator->setOptions(array('callback' => $callback, 'placement' => false))
                        ->setElement($element);

        $content = $this->decorator->render('foo bar');
        $this->assertNotContains('foo bar', $content, $content);
        $this->assertContains($element->getName(), $content);
        $this->assertContains($element->getLabel(), $content);
    }
}

function Zend_Form_Decorator_CallbackTest_TestCallback($content, $element, array $options)
{
    $name  = $element->getName();
    $label = '';
    if (method_exists($element, 'getLabel')) {
        $label = $element->getLabel();
    }
    $html =<<<EOH
$label: $name

EOH;
    return $html;
}

class Zend_Form_Decorator_CallbackTest_TestCallbackClass
{
    public static function direct($content, $element, array $options)
    {
        $name  = $element->getName();
        $label = '';
        if (method_exists($element, 'getLabel')) {
            $label = $element->getLabel();
        }
        $html =<<<EOH
Item "$label": $name

EOH;
        return $html;
    }
}

// Call Zend_Form_Decorator_CallbackTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Form_Decorator_CallbackTest::main") {
    Zend_Form_Decorator_CallbackTest::main();
}
