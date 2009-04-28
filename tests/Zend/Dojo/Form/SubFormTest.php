<?php
// Call Zend_Dojo_Form_SubFormTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Dojo_Form_SubFormTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';

/** Zend_Dojo_Form_SubForm */
require_once 'Zend/Dojo/Form/SubForm.php';

/** Zend_View */
require_once 'Zend/View.php';

/**
 * Test class for Zend_Dojo_SubForm
 */
class Zend_Dojo_Form_SubFormTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Dojo_Form_SubFormTest");
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
        $this->form = new Zend_Dojo_Form_SubForm();
        $this->form->addElement('TextBox', 'foo')
                   ->addDisplayGroup(array('foo'), 'dg')
                   ->setView(new Zend_View());
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

    public function testDojoFormDecoratorPathShouldBeRegisteredByDefault()
    {
        $paths = $this->form->getPluginLoader('decorator')->getPaths('Zend_Dojo_Form_Decorator');
        $this->assertTrue(is_array($paths));
    }

    public function testDojoFormElementPathShouldBeRegisteredByDefault()
    {
        $paths = $this->form->getPluginLoader('element')->getPaths('Zend_Dojo_Form_Element');
        $this->assertTrue(is_array($paths));
    }

    public function testDojoFormElementDecoratorPathShouldBeRegisteredByDefault()
    {
        $paths = $this->form->foo->getPluginLoader('decorator')->getPaths('Zend_Dojo_Form_Decorator');
        $this->assertTrue(is_array($paths));
    }

    public function testDojoFormDisplayGroupDecoratorPathShouldBeRegisteredByDefault()
    {
        $paths = $this->form->dg->getPluginLoader()->getPaths('Zend_Dojo_Form_Decorator');
        $this->assertTrue(is_array($paths));
    }

    public function testDefaultDisplayGroupClassShouldBeDojoDisplayGroupByDefault()
    {
        $this->assertEquals('Zend_Dojo_Form_DisplayGroup', $this->form->getDefaultDisplayGroupClass());
    }

    public function testDefaultDecoratorsShouldIncludeContentPane()
    {
        $this->assertNotNull($this->form->getDecorator('ContentPane'));
    }

    public function testShouldRegisterDojoViewHelperPath()
    {
        $view   = $this->form->getView();
        $loader = $view->getPluginLoader('helper');
        $paths  = $loader->getPaths('Zend_Dojo_View_Helper');
        $this->assertTrue(is_array($paths));
    }
}

// Call Zend_Dojo_Form_SubFormTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Dojo_Form_SubFormTest::main") {
    Zend_Dojo_Form_SubFormTest::main();
}
