<?php
// Call Zend_Controller_Plugin_ActionStackTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    require_once dirname(__FILE__) . '/../../../TestHelper.php';
    define("PHPUnit_MAIN_METHOD", "Zend_Controller_Plugin_ActionStackTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Controller/Plugin/ActionStack.php';
require_once 'Zend/Controller/Request/Simple.php';
require_once 'Zend/Registry.php';

/**
 * Test class for Zend_Controller_Plugin_ActionStack.
 */
class Zend_Controller_Plugin_ActionStackTest extends PHPUnit_Framework_TestCase 
{
    public $key       = 'Zend_Controller_Plugin_ActionStack';
    public $registry;

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Controller_Plugin_ActionStackTest");
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
        $this->removeRegistryEntry();
        $this->registry = Zend_Registry::getInstance();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    protected function tearDown()
    {
        $this->removeRegistryEntry();
    }

    /**
     * Ensure registry is clean
     *
     * @return void
     */
    public function removeRegistryEntry()
    {
        $registry = Zend_Registry::getInstance();
        if (isset($registry[$this->key])) {
            unset($registry[$this->key]);
        }
    }

    public function testConstructorCreatesRegistryEntry()
    {
        $registry = Zend_Registry::getInstance();
        $this->assertFalse(isset($registry[$this->key]));

        $plugin = new Zend_Controller_Plugin_ActionStack();
        $key    = $plugin->getRegistryKey();
        $this->assertTrue(isset($registry[$key]));
    }

    public function testKeyPassedToConstructorUsedAsRegistryKey()
    {
        $this->key = $key = 'foobar';
        $registry  = Zend_Registry::getInstance();
        $this->assertFalse(isset($registry[$key]));

        $plugin = new Zend_Controller_Plugin_ActionStack(null, $key);
        $this->assertTrue(isset($registry[$key]));
    }

    public function testRegistryPassedToConstructorUsedByPlugin()
    {
        $registry = new Zend_Controller_Plugin_ActionStack_Registry();
        $plugin   = new Zend_Controller_Plugin_ActionStack($registry);
        $registered = $plugin->getRegistry();
        $this->assertNotSame($this->registry, $registered);
        $this->assertSame($registry, $registered);
    }

    /**
     * @return void
     */
    public function testRegistryAccessorsWork()
    {
        $registry = new Zend_Controller_Plugin_ActionStack_Registry();
        $plugin   = new Zend_Controller_Plugin_ActionStack();
        $original = $plugin->getRegistry();

        $plugin->setRegistry($registry);
        $registered = $plugin->getRegistry();

        $this->assertSame($registry, $registered);
        $this->assertNotSame($original, $registered);
    }

    public function testRegistryKeyHasDefaultValue()
    {
        $plugin = new Zend_Controller_Plugin_ActionStack();
        $key    = $plugin->getRegistryKey();
        $this->assertNotNull($key);
        $this->assertEquals($this->key, $key);
    }

    /**
     * @return void
     */
    public function testRegistryKeyAccessorsWork()
    {
        $plugin   = new Zend_Controller_Plugin_ActionStack();
        $plugin->setRegistryKey('foobar');
        $key = $plugin->getRegistryKey();
        $this->assertEquals('foobar', $key);
    }

    /**
     * @return void
     */
    public function testGetStackInitiallyReturnsEmptyArray()
    {
        $plugin = new Zend_Controller_Plugin_ActionStack();
        $stack  = $plugin->getStack();
        $this->assertTrue(is_array($stack));
        $this->assertTrue(empty($stack));
    }

    /**
     * @return void
     */
    public function testPushStackAppendsToStack()
    {
        $plugin = new Zend_Controller_Plugin_ActionStack();

        $request1 = new Zend_Controller_Request_Simple();
        $plugin->pushStack($request1);
        $received = $plugin->getStack();
        $this->assertTrue(is_array($received));
        $this->assertEquals(1, count($received));
        $this->assertSame($request1, $received[0]);

        $request2 = new Zend_Controller_Request_Simple();
        $plugin->pushStack($request2);
        $received = $plugin->getStack();
        $this->assertTrue(is_array($received));
        $this->assertEquals(2, count($received));
        $this->assertSame($request2, $received[1]);
        $this->assertSame($request1, $received[0]);
    }

    public function getNewRequest()
    {
        $request = new Zend_Controller_Request_Simple();
        $request->setActionName('baz')
                ->setControllerName('bar')
                ->setModuleName('foo');
        return $request;
    }

    /**
     * @return void
     */
    public function testPopStackPullsFromEndOfStack()
    {
        $plugin   = new Zend_Controller_Plugin_ActionStack();
        $request1 = $this->getNewRequest();
        $request2 = $this->getNewRequest();
        $request3 = $this->getNewRequest();
        $plugin->pushStack($request1)
               ->pushStack($request2)
               ->pushStack($request3);
        $stack    = $plugin->getStack();
        $this->assertEquals(3, count($stack));

        $received = $plugin->popStack();
        $stack    = $plugin->getStack();
        $this->assertSame($request3, $received);
        $this->assertEquals(2, count($stack));
    }

    public function testPopEmptyStackReturnsFalse()
    {
        $plugin   = new Zend_Controller_Plugin_ActionStack();
        $received = $plugin->popStack();
        $this->assertFalse($received);
    }

    public function testPopStackPopsMultipleItemsWhenRequestActionEmpty()
    {
        $plugin   = new Zend_Controller_Plugin_ActionStack();
        $request1 = $this->getNewRequest();
        $request2 = new Zend_Controller_Request_Simple();
        $plugin->pushStack($request1)
               ->pushStack($request2);
        $stack    = $plugin->getStack();
        $this->assertEquals(2, count($stack));

        $received = $plugin->popStack();
        $stack    = $plugin->getStack();
        $this->assertSame($request1, $received);
        $this->assertEquals(0, count($stack));
    }

    public function testPopStackPopulatesControllerAndModuleFromRequestIfEmpty()
    {
        $plugin   = new Zend_Controller_Plugin_ActionStack();
        $request  = $this->getNewRequest();
        $plugin->setRequest($request);

        $request1 = new Zend_Controller_Request_Simple();
        $request1->setActionName('blah');
        $plugin->pushStack($request1);

        $next = $plugin->popStack();
        $this->assertTrue($next instanceof Zend_Controller_Request_Simple);
        $this->assertEquals($request1->getActionName(), $next->getActionName());
        $this->assertEquals($request->getControllerName(), $next->getControllerName());
        $this->assertEquals($request->getModuleName(), $next->getModuleName());
    }

    public function testForwardResetsInternalRequestStateFromGivenRequest()
    {
        $plugin   = new Zend_Controller_Plugin_ActionStack();
        $request  = new Zend_Controller_Request_Simple();
        $plugin->setRequest($request);

        $next = $this->getNewRequest();
        $plugin->forward($next);

        $this->assertEquals($next->getActionName(), $request->getActionName());
        $this->assertEquals($next->getControllerName(), $request->getControllerName());
        $this->assertEquals($next->getModuleName(), $request->getModuleName());
        $this->assertFalse($request->isDispatched());
    }

    /**
     * @return void
     */
    public function testPostDispatchResetsInternalRequestFromLastRequestOnStack()
    {
        $plugin   = new Zend_Controller_Plugin_ActionStack();
        $request  = new Zend_Controller_Request_Simple();
        $request->setDispatched(true);
        $plugin->setRequest($request);

        $request1 = $this->getNewRequest();
        $request2 = $this->getNewRequest();
        $request3 = $this->getNewRequest();
        $request3->setActionName('foobar')
                 ->setControllerName('bazbat')
                 ->setModuleName('bogus');
        $plugin->pushStack($request1)
               ->pushStack($request2)
               ->pushStack($request3);

        $plugin->postDispatch($request);

        $this->assertEquals($request3->getActionName(), $request->getActionName());
        $this->assertEquals($request3->getControllerName(), $request->getControllerName());
        $this->assertEquals($request3->getModuleName(), $request->getModuleName());
        $this->assertFalse($request->isDispatched());
    }

    public function testPostDispatchDoesNothingWithEmptyStack()
    {
        $plugin   = new Zend_Controller_Plugin_ActionStack();

        $request  = $this->getNewRequest();
        $request->setDispatched(true);

        $clone    = clone $request;

        $plugin->postDispatch($request);

        $this->assertEquals($clone->getActionName(), $request->getActionName());
        $this->assertEquals($clone->getControllerName(), $request->getControllerName());
        $this->assertEquals($clone->getModuleName(), $request->getModuleName());
        $this->assertTrue($request->isDispatched());
    }

    public function testPostDispatchDoesNothingWithStackThatEvaluatesToEmpty()
    {
        $plugin   = new Zend_Controller_Plugin_ActionStack();
        $request  = new Zend_Controller_Request_Simple();
        $request->setDispatched(true);
        $plugin->setRequest($request);

        $request1 = new Zend_Controller_Request_Simple();
        $request2 = new Zend_Controller_Request_Simple();
        $request3 = new Zend_Controller_Request_Simple();
        $plugin->pushStack($request1)
               ->pushStack($request2)
               ->pushStack($request3);

        $clone    = clone $request;
        $plugin->postDispatch($request);

        $this->assertEquals($clone->getActionName(), $request->getActionName());
        $this->assertEquals($clone->getControllerName(), $request->getControllerName());
        $this->assertEquals($clone->getModuleName(), $request->getModuleName());
        $this->assertTrue($request->isDispatched());
    }

    public function testPostDispatchDoesNothingWithExistingForwardRequest()
    {
        $plugin   = new Zend_Controller_Plugin_ActionStack();
        $request  = new Zend_Controller_Request_Simple();
        $request->setDispatched(false);
        $plugin->setRequest($request);

        $request1 = new Zend_Controller_Request_Simple();
        $request2 = new Zend_Controller_Request_Simple();
        $request3 = new Zend_Controller_Request_Simple();
        $plugin->pushStack($request1)
               ->pushStack($request2)
               ->pushStack($request3);

        $plugin->postDispatch($request);
        $stack = $plugin->getStack();
        $this->assertEquals(3, count($stack));
    }
}

class Zend_Controller_Plugin_ActionStack_Registry extends Zend_Registry
{
    protected static $_registryClassName = 'Zend_Controller_Plugin_ActionStack_Registry';
}

// Call Zend_Controller_Plugin_ActionStackTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Controller_Plugin_ActionStackTest::main") {
    Zend_Controller_Plugin_ActionStackTest::main();
}
