<?php
// Call Zend_Form_Decorator_FormTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Form_Decorator_FormTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';

require_once 'Zend/Form/Decorator/Form.php';
require_once 'Zend/Form.php';


/**
 * Test class for Zend_Form_Decorator_Form
 */
class Zend_Form_Decorator_FormTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Form_Decorator_FormTest");
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
        $this->decorator = new Zend_Form_Decorator_Form();
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

    public function getView()
    {
        require_once 'Zend/View.php';
        $view = new Zend_View();
        $view->addHelperPath(dirname(__FILE__) . '/../../../../library/Zend/View/Helper');
        return $view;
    }

    public function testHelperIsFormByDefault()
    {
        $this->assertEquals('form', $this->decorator->getHelper());
    }

    public function testCanSetHelperWithOption()
    {
        $this->testHelperIsFormByDefault();
        $this->decorator->setOption('helper', 'formForm');
        $this->assertEquals('formForm', $this->decorator->getHelper());

        require_once 'Zend/Form/DisplayGroup.php';
        require_once 'Zend/Loader/PluginLoader.php';
        $attribs = array(
            'enctype' => 'ascii',
            'charset' => 'us-ascii'
        );
        $loader = new Zend_Loader_PluginLoader(array('Zend_Form_Decorator' => 'Zend/Form/Decorator/'));
        $displayGroup = new Zend_Form_DisplayGroup('foo', $loader, array('attribs' => $attribs));
        $this->decorator->setElement($displayGroup);
        $options = $this->decorator->getOptions();
        $this->assertTrue(isset($options['enctype']));
        $this->assertEquals($attribs['enctype'], $options['enctype']);
        $this->assertTrue(isset($options['charset']));
        $this->assertEquals($attribs['charset'], $options['charset']);
    }

    /**
     * @see ZF-3643
     */
    public function testShouldPreferFormIdAttributeOverFormName()
    {
        $form = new Zend_Form();
        $form->setMethod('post')
             ->setAction('/foo/bar')
             ->setName('foobar')
             ->setAttrib('id', 'bazbat')
             ->setView($this->getView());
        $html = $form->render();
        $this->assertContains('id="bazbat"', $html, $html);
    }

    public function testEmptyFormNameShouldNotRenderEmptyFormId()
    {
        $form = new Zend_Form();
        $form->setMethod('post')
             ->setAction('/foo/bar')
             ->setView($this->getView());
        $html = $form->render();
        $this->assertNotContains('id=""', $html, $html);
    }
}

// Call Zend_Form_Decorator_FormTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Form_Decorator_FormTest::main") {
    Zend_Form_Decorator_FormTest::main();
}
