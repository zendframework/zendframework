<?php
// Call Zend_View_Helper_Placeholder_RegistryTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_Placeholder_RegistryTest::main");
}

require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/TestHelper.php';
require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

/** Zend_View_Helper_Placeholder_Registry */
require_once 'Zend/View/Helper/Placeholder/Registry.php';

/**
 * Test class for Zend_View_Helper_Placeholder_Registry.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 */
class Zend_View_Helper_Placeholder_RegistryTest extends PHPUnit_Framework_TestCase 
{
    /**
     * @var Zend_View_Helper_Placeholder_Registry
     */
    public $registry;

    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_Placeholder_RegistryTest");
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
        $registry = Zend_Registry::getInstance();
        if (isset($registry[Zend_View_Helper_Placeholder_Registry::REGISTRY_KEY])) {
            unset($registry[Zend_View_Helper_Placeholder_Registry::REGISTRY_KEY]);
        }
        $this->registry = new Zend_View_Helper_Placeholder_Registry();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->registry);
    }

    /**
     * @return void
     */
    public function testCreateContainer()
    {
        $this->assertFalse($this->registry->containerExists('foo'));
        $this->registry->createContainer('foo');
        $this->assertTrue($this->registry->containerExists('foo'));
    }

    /**
     * @return void
     */
    public function testCreateContainerCreatesDefaultContainerClass()
    {
        $this->assertFalse($this->registry->containerExists('foo'));
        $container = $this->registry->createContainer('foo');
        $this->assertTrue($container instanceof Zend_View_Helper_Placeholder_Container);
    }

    /**
     * @return void
     */
    public function testGetContainerCreatesContainerIfNonExistent()
    {
        $this->assertFalse($this->registry->containerExists('foo'));
        $container = $this->registry->getContainer('foo');
        $this->assertTrue($container instanceof Zend_View_Helper_Placeholder_Container_Abstract);
        $this->assertTrue($this->registry->containerExists('foo'));
    }

    /**
     * @return void
     */
    public function testSetContainerCreatesRegistryEntry()
    {
        $foo = new Zend_View_Helper_Placeholder_Container(array('foo', 'bar'));
        $this->assertFalse($this->registry->containerExists('foo'));
        $this->registry->setContainer('foo', $foo);
        $this->assertTrue($this->registry->containerExists('foo'));
    }

    /**
     * @return void
     */
    public function testSetContainerCreatesRegistersContainerInstance()
    {
        $foo = new Zend_View_Helper_Placeholder_Container(array('foo', 'bar'));
        $this->assertFalse($this->registry->containerExists('foo'));
        $this->registry->setContainer('foo', $foo);
        $container = $this->registry->getContainer('foo');
        $this->assertSame($foo, $container);
    }

    /**
     * @return void
     */
    public function testContainerClassAccessorsSetState()
    {
        $this->assertEquals('Zend_View_Helper_Placeholder_Container', $this->registry->getContainerClass());
        $this->registry->setContainerClass('Zend_View_Helper_Placeholder_RegistryTest_Container');
        $this->assertEquals('Zend_View_Helper_Placeholder_RegistryTest_Container', $this->registry->getContainerClass());
    }

    /**
     * @return void
     */
    public function testSetContainerClassThrowsExceptionWithInvalidContainerClass()
    {
        try {
            $this->registry->setContainerClass('Zend_View_Helper_Placeholder_RegistryTest_BogusContainer');
            $this->fail('Invalid container classes should not be accepted');
        } catch (Exception $e) {
        }
    }

    public function testDeletingContainerRemovesFromRegistry()
    {
        $this->registry->createContainer('foo');
        $this->assertTrue($this->registry->containerExists('foo'));
        $result = $this->registry->deleteContainer('foo');
        $this->assertFalse($this->registry->containerExists('foo'));
        $this->assertTrue($result);
    }

    public function testDeleteContainerReturnsFalseIfContainerDoesNotExist()
    {
        $result = $this->registry->deleteContainer('foo');
        $this->assertFalse($result);
    }

    /**
     * @return void
     */
    public function testUsingCustomContainerClassCreatesContainersOfCustomClass()
    {
        $this->registry->setContainerClass('Zend_View_Helper_Placeholder_RegistryTest_Container');
        $container = $this->registry->createContainer('foo');
        $this->assertTrue($container instanceof Zend_View_Helper_Placeholder_RegistryTest_Container);
    }

    public function testGetRegistryReturnsRegistryInstance()
    {
        $registry = Zend_View_Helper_Placeholder_Registry::getRegistry();
        $this->assertTrue($registry instanceof Zend_View_Helper_Placeholder_Registry);
    }

    public function testGetRegistrySubsequentTimesReturnsSameInstance()
    {
        $registry1 = Zend_View_Helper_Placeholder_Registry::getRegistry();
        $registry2 = Zend_View_Helper_Placeholder_Registry::getRegistry();
        $this->assertSame($registry1, $registry2);
    }

    public function testGetRegistryRegistersWithGlobalRegistry()
    {
        $this->assertFalse(Zend_Registry::isRegistered(Zend_View_Helper_Placeholder_Registry::REGISTRY_KEY));
        $registry = Zend_View_Helper_Placeholder_Registry::getRegistry();
        $this->assertTrue(Zend_Registry::isRegistered(Zend_View_Helper_Placeholder_Registry::REGISTRY_KEY));

        $registered = Zend_Registry::get(Zend_View_Helper_Placeholder_Registry::REGISTRY_KEY);
        $this->assertSame($registry, $registered);
    }
}

class Zend_View_Helper_Placeholder_RegistryTest_Container extends Zend_View_Helper_Placeholder_Container_Abstract
{
}

class Zend_View_Helper_Placeholder_RegistryTest_BogusContainer
{
}

// Call Zend_View_Helper_Placeholder_RegistryTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_Placeholder_RegistryTest::main") {
    Zend_View_Helper_Placeholder_RegistryTest::main();
}
