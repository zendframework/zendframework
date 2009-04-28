<?php
// Call Zend_View_Helper_Placeholder_StandaloneContainerTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_Placeholder_StandaloneContainerTest::main");
}

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

/** Zend_View_Helper_Placeholder_Container_Standalone */
require_once 'Zend/View/Helper/Placeholder/Container/Standalone.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/** Zend_View_Helper_Placeholder_Registry */
require_once 'Zend/View/Helper/Placeholder/Registry.php';

/** Zend_View */
require_once 'Zend/View.php';

/**
 * Test class for Zend_View_Helper_Placeholder_StandaloneContainer.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 */
class Zend_View_Helper_Placeholder_StandaloneContainerTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_Placeholder_StandaloneContainerTest");
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
        $regKey = Zend_View_Helper_Placeholder_Registry::REGISTRY_KEY;
        if (Zend_Registry::isRegistered($regKey)) {
            $registry = Zend_Registry::getInstance();
            unset($registry[$regKey]);
        }
        $this->basePath = dirname(__FILE__) . '/_files/modules';
        $this->helper = new Zend_View_Helper_Placeholder_StandaloneContainerTest_Foo();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->helper);
    }

    public function testViewAccessorWorks()
    {
        $view = new Zend_View();
        $this->helper->setView($view);
        $this->assertSame($view, $this->helper->view);
    }

    public function testContainersPersistBetweenInstances()
    {
        $foo1 = new Zend_View_Helper_Placeholder_StandaloneContainerTest_Foo;
        $foo1->append('Foo');
        $foo1->setSeparator(' - ');

        $foo2 = new Zend_View_Helper_Placeholder_StandaloneContainerTest_Foo;
        $foo2->append('Bar');

        $test = $foo1->toString();
        $this->assertContains('Foo', $test);
        $this->assertContains(' - ', $test);
        $this->assertContains('Bar', $test);
    }
}

class Zend_View_Helper_Placeholder_StandaloneContainerTest_Foo extends Zend_View_Helper_Placeholder_Container_Standalone
{
    protected $_regKey = 'foo';
}

// Call Zend_View_Helper_Placeholder_StandaloneContainerTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_Placeholder_StandaloneContainerTest::main") {
    Zend_View_Helper_Placeholder_StandaloneContainerTest::main();
}
