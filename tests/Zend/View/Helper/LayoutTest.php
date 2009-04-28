<?php
// Call Zend_LayoutTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_LayoutTest::main");
}

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/TestHelper.php';
require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/View/Helper/Layout.php';
require_once 'Zend/Layout.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Action/HelperBroker.php';

/**
 * Test class for Zend_View_Helper_Layout
 */
class Zend_View_Helper_LayoutTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_LayoutTest");
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
        Zend_Controller_Front::getInstance()->resetInstance();
        if (Zend_Controller_Action_HelperBroker::hasHelper('Layout')) {
            Zend_Controller_Action_HelperBroker::removeHelper('Layout');
        }
        if (Zend_Controller_Action_HelperBroker::hasHelper('viewRenderer')) {
            Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
        }

        Zend_View_Helper_LayoutTest_Layout::$_mvcInstance = null;
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

    public function testGetLayoutCreatesLayoutObjectWhenNoPluginRegistered()
    {
        $helper = new Zend_View_Helper_Layout();
        $layout = $helper->getLayout();
        $this->assertTrue($layout instanceof Zend_Layout);
    }

    public function testGetLayoutPullsLayoutObjectFromRegisteredPlugin()
    {
        $layout = Zend_Layout::startMvc();
        $helper = new Zend_View_Helper_Layout();
        $this->assertSame($layout, $helper->getLayout());
    }

    public function testSetLayoutReplacesExistingLayoutObject()
    {
        $layout = Zend_Layout::startMvc();
        $helper = new Zend_View_Helper_Layout();
        $this->assertSame($layout, $helper->getLayout());

        $newLayout = new Zend_Layout();
        $this->assertNotSame($layout, $newLayout);

        $helper->setLayout($newLayout);
        $this->assertSame($newLayout, $helper->getLayout());
    }

    public function testHelperMethodFetchesLayoutObject()
    {
        $layout = Zend_Layout::startMvc();
        $helper = new Zend_View_Helper_Layout();

        $received = $helper->layout();
        $this->assertSame($layout, $received);
    }
}

/**
 * Zend_Layout extension to allow resetting MVC instance
 */
class Zend_View_Helper_LayoutTest_Layout extends Zend_Layout
{
    public static $_mvcInstance;
}

// Call Zend_View_Helper_LayoutTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_LayoutTest::main") {
    Zend_View_Helper_LayoutTest::main();
}
