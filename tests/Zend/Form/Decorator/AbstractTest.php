<?php
// Call Zend_Form_Decorator_AbstractTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Form_Decorator_AbstractTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';
require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Form/Decorator/Errors.php';

require_once 'Zend/Config.php';
require_once 'Zend/Form.php';
require_once 'Zend/Form/DisplayGroup.php';
require_once 'Zend/Form/Element.php';
require_once 'Zend/Loader/PluginLoader.php';

/**
 * Test class for Zend_Form_Decorator_Abstract
 *
 * Uses Zend_Form_Decorator_Errors as a concrete implementation
 */
class Zend_Form_Decorator_AbstractTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Form_Decorator_AbstractTest");
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
        $this->decorator = new Zend_Form_Decorator_Errors();
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

    public function getOptions()
    {
        $options = array(
            'foo' => 'fooval',
            'bar' => 'barval',
            'baz' => 'bazval'
        );
        return $options;
    }

    public function testCanSetOptions()
    {
        $options = $this->getOptions();
        $this->decorator->setOptions($options);
        $this->assertEquals($options, $this->decorator->getOptions());
    }

    public function testCanSetOptionsFromConfigObject()
    {
        $config = new Zend_Config($this->getOptions());
        $this->decorator->setConfig($config);
        $this->assertEquals($config->toArray(), $this->decorator->getOptions());
    }

    public function testSetElementAllowsFormElements()
    {
        $element = new Zend_Form_Element('foo');
        $this->decorator->setElement($element);
        $this->assertSame($element, $this->decorator->getElement());
    }

    public function testSetElementAllowsForms()
    {
        $form = new Zend_Form();
        $this->decorator->setElement($form);
        $this->assertSame($form, $this->decorator->getElement());
    }

    public function testSetElementAllowsDisplayGroups()
    {
        $loader = new Zend_Loader_PluginLoader(array('Zend_Form_Decorator' => 'Zend/Form/Decorator'));
        $group  = new Zend_Form_DisplayGroup('foo', $loader);
        $this->decorator->setElement($group);
        $this->assertSame($group, $this->decorator->getElement());
    }

    public function testSetElementThrowsExceptionWithInvalidElementTypes()
    {
        $config = new Zend_Config(array());
        try {
            $this->decorator->setElement($config);
            $this->fail('Invalid element type should raise exception');
        } catch (Zend_Form_Exception $e) {
            $this->assertContains('Invalid element', $e->getMessage());
        }
    }

    public function testPlacementDefaultsToAppend()
    {
        $this->assertEquals(Zend_Form_Decorator_Abstract::APPEND, $this->decorator->getPlacement());
    }

    public function testCanSetPlacementViaPlacementOption()
    {
        $this->testPlacementDefaultsToAppend();
        $this->decorator->setOptions(array('placement' => 'PREPEND'));
        $this->assertEquals(Zend_Form_Decorator_Abstract::PREPEND, $this->decorator->getPlacement());
    }

    public function testSeparatorDefaultsToPhpEol()
    {
        $this->assertEquals(PHP_EOL, $this->decorator->getSeparator());
    }

    public function testCanSetSeparatorViaSeparatorOption()
    {
        $this->testSeparatorDefaultsToPhpEol();
        $this->decorator->setOptions(array('separator' => '<br />'));
        $this->assertEquals('<br />', $this->decorator->getSeparator());
    }

    public function testCanSetIndividualOptions()
    {
        $this->assertNull($this->decorator->getOption('foo'));
        $this->decorator->setOption('foo', 'bar');
        $this->assertEquals('bar', $this->decorator->getOption('foo'));
    }

    public function testCanRemoveIndividualOptions()
    {
        $this->assertNull($this->decorator->getOption('foo'));
        $this->decorator->setOption('foo', 'bar');
        $this->assertEquals('bar', $this->decorator->getOption('foo'));
        $this->decorator->removeOption('foo');
        $this->assertNull($this->decorator->getOption('foo'));
    }

    public function testCanClearAllOptions()
    {
        $this->assertNull($this->decorator->getOption('foo'));
        $this->assertNull($this->decorator->getOption('bar'));
        $this->assertNull($this->decorator->getOption('baz'));
        $options = array('foo' => 'bar', 'bar' => 'baz', 'baz' => 'bat');
        $this->decorator->setOptions($options);
        $received = $this->decorator->getOptions();
        $this->assertEquals($options, $received);
        $this->decorator->clearOptions();
        $this->assertEquals(array(), $this->decorator->getOptions());
    }
}

// Call Zend_Form_Decorator_AbstractTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Form_Decorator_AbstractTest::main") {
    Zend_Form_Decorator_AbstractTest::main();
}
