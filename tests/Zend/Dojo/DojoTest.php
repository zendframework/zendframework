<?php
// Call Zend_Dojo_FormTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Dojo_DojoTest::main");
}

require_once dirname(__FILE__) . '/../../TestHelper.php';

/** Zend_Dojo */
require_once 'Zend/Dojo.php';

/** Zend_Form */
require_once 'Zend/Form.php';

/** Zend_Form_Element */
require_once 'Zend/Form/Element.php';

/** Zend_Form_SubForm */
require_once 'Zend/Form/SubForm.php';

/** Zend_View */
require_once 'Zend/View.php';

/**
 * Test class for Zend_Dojo
 */
class Zend_Dojo_DojoTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Dojo_DojoTest");
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

    public function getForm()
    {
        $form = new Zend_Form();
        $form->addElement('text', 'foo')
             ->addElement('text', 'bar')
             ->addElement('text', 'baz')
             ->addElement('text', 'bat');
        $subForm = new Zend_Form_SubForm();
        $subForm->addElement('text', 'foo')
                ->addElement('text', 'bar')
                ->addElement('text', 'baz')
                ->addElement('text', 'bat');
        $form->addDisplayGroup(array('foo', 'bar'), 'foobar')
             ->addSubForm($subForm, 'sub')
             ->setView(new Zend_View);
        return $form;
    }

    public function testEnableFormShouldSetAppropriateDecoratorAndElementPaths()
    {
        $form = $this->getForm();
        Zend_Dojo::enableForm($form);

        $decPluginLoader = $form->getPluginLoader('decorator');
        $paths = $decPluginLoader->getPaths('Zend_Dojo_Form_Decorator');
        $this->assertTrue(is_array($paths));

        $elPluginLoader = $form->getPluginLoader('element');
        $paths = $elPluginLoader->getPaths('Zend_Dojo_Form_Element');
        $this->assertTrue(is_array($paths));

        $decPluginLoader = $form->baz->getPluginLoader('decorator');
        $paths = $decPluginLoader->getPaths('Zend_Dojo_Form_Decorator');
        $this->assertTrue(is_array($paths));

        $decPluginLoader = $form->foobar->getPluginLoader();
        $paths = $decPluginLoader->getPaths('Zend_Dojo_Form_Decorator');
        $this->assertTrue(is_array($paths));

        $decPluginLoader = $form->sub->getPluginLoader('decorator');
        $paths = $decPluginLoader->getPaths('Zend_Dojo_Form_Decorator');
        $this->assertTrue(is_array($paths));

        $elPluginLoader = $form->sub->getPluginLoader('element');
        $paths = $elPluginLoader->getPaths('Zend_Dojo_Form_Element');
        $this->assertTrue(is_array($paths));
    }

    public function testEnableFormShouldSetAppropriateDefaultDisplayGroup()
    {
        $form = $this->getForm();
        Zend_Dojo::enableForm($form);
        $this->assertEquals('Zend_Dojo_Form_DisplayGroup', $form->getDefaultDisplayGroupClass());
    }

    public function testEnableFormShouldSetAppropriateViewHelperPaths()
    {
        $form = $this->getForm();
        Zend_Dojo::enableForm($form);
        $view = $form->getView();
        $helperLoader = $view->getPluginLoader('helper');
        $paths = $helperLoader->getPaths('Zend_Dojo_View_Helper');
        $this->assertTrue(is_array($paths));
    }

    public function testEnableViewShouldSetAppropriateViewHelperPaths()
    {
        $view = new Zend_View;
        Zend_Dojo::enableView($view);
        $helperLoader = $view->getPluginLoader('helper');
        $paths = $helperLoader->getPaths('Zend_Dojo_View_Helper');
        $this->assertTrue(is_array($paths));
    }
}

// Call Zend_Dojo_DojoTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Dojo_DojoTest::main") {
    Zend_Dojo_DojoTest::main();
}
