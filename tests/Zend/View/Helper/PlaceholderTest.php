<?php
// Call Zend_View_Helper_PlaceholderTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_PlaceholderTest::main");
}

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/TestHelper.php';
require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

/** Zend_View_Helper_Placeholder */
require_once 'Zend/View/Helper/Placeholder.php';

/** Zend_View_Helper_Placeholder_Registry */
require_once 'Zend/View/Helper/Placeholder/Registry.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/**
 * Test class for Zend_View_Helper_Placeholder.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 */
class Zend_View_Helper_PlaceholderTest extends PHPUnit_Framework_TestCase 
{
    /**
     * @var Zend_View_Helper_Placeholder
     */
    public $placeholder;

    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_PlaceholderTest");
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
        $this->placeholder = new Zend_View_Helper_Placeholder();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->placeholder);
        Zend_Registry::getInstance()->offsetUnset(Zend_View_Helper_Placeholder_Registry::REGISTRY_KEY);
    }

    /**
     * @return void
     */
    public function testConstructorCreatesRegistryOffset()
    {
        $this->assertTrue(Zend_Registry::isRegistered(Zend_View_Helper_Placeholder_Registry::REGISTRY_KEY));
    }

    public function testMultiplePlaceholdersUseSameRegistry()
    {
        $this->assertTrue(Zend_Registry::isRegistered(Zend_View_Helper_Placeholder_Registry::REGISTRY_KEY));
        $registry = Zend_Registry::get(Zend_View_Helper_Placeholder_Registry::REGISTRY_KEY);
        $this->assertSame($registry, $this->placeholder->getRegistry());

        $placeholder = new Zend_View_Helper_Placeholder();

        $this->assertSame($registry, $placeholder->getRegistry());
        $this->assertSame($this->placeholder->getRegistry(), $placeholder->getRegistry());
    }

    /**
     * @return void
     */
    public function testSetView()
    {
        include_once 'Zend/View.php';
        $view = new Zend_View();
        $this->placeholder->setView($view);
        $this->assertSame($view, $this->placeholder->view);
    }

    /**
     * @return void
     */
    public function testPlaceholderRetrievesContainer()
    {
        $container = $this->placeholder->placeholder('foo');
        $this->assertTrue($container instanceof Zend_View_Helper_Placeholder_Container_Abstract);
    }

    /**
     * @return void
     */
    public function testPlaceholderRetrievesSameContainerOnSubsequentCalls()
    {
        $container1 = $this->placeholder->placeholder('foo');
        $container2 = $this->placeholder->placeholder('foo');
        $this->assertSame($container1, $container2);
    }
}

// Call Zend_View_Helper_PlaceholderTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_PlaceholderTest::main") {
    Zend_View_Helper_PlaceholderTest::main();
}
