<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Controller\Plugin;
use Zend\Controller\Plugin;
use Zend\Controller\Request;

/**
 * Test class for Zend_Controller_Plugin_ActionStack.
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Plugin
 */
class ActionStackTest extends \PHPUnit_Framework_TestCase
{
    public $key       = 'Zend_Controller_Plugin_ActionStack';
    public $registry;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->removeRegistryEntry();
        $this->registry = \Zend\Registry::getInstance();
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
        $registry = \Zend\Registry::getInstance();
        if (isset($registry[$this->key])) {
            unset($registry[$this->key]);
        }
    }

    public function testConstructorCreatesRegistryEntry()
    {
        $registry = \Zend\Registry::getInstance();
        $this->assertFalse(isset($registry[$this->key]));

        $plugin = new Plugin\ActionStack();
        $key    = $plugin->getRegistryKey();
        $this->assertTrue(isset($registry[$key]));
    }

    public function testKeyPassedToConstructorUsedAsRegistryKey()
    {
        $this->key = $key = 'foobar';
        $registry  = \Zend\Registry::getInstance();
        $this->assertFalse(isset($registry[$key]));

        $plugin = new Plugin\ActionStack(null, $key);
        $this->assertTrue(isset($registry[$key]));
    }

    public function testRegistryPassedToConstructorUsedByPlugin()
    {
        $registry = new Registry();
        $plugin   = new Plugin\ActionStack($registry);
        $registered = $plugin->getRegistry();
        $this->assertNotSame($this->registry, $registered);
        $this->assertSame($registry, $registered);
    }

    /**
     * @return void
     */
    public function testRegistryAccessorsWork()
    {
        $registry = new Registry();
        $plugin   = new Plugin\ActionStack();
        $original = $plugin->getRegistry();

        $plugin->setRegistry($registry);
        $registered = $plugin->getRegistry();

        $this->assertSame($registry, $registered);
        $this->assertNotSame($original, $registered);
    }

    public function testRegistryKeyHasDefaultValue()
    {
        $plugin = new Plugin\ActionStack();
        $key    = $plugin->getRegistryKey();
        $this->assertNotNull($key);
        $this->assertEquals($this->key, $key);
    }

    /**
     * @return void
     */
    public function testRegistryKeyAccessorsWork()
    {
        $plugin   = new Plugin\ActionStack();
        $plugin->setRegistryKey('foobar');
        $key = $plugin->getRegistryKey();
        $this->assertEquals('foobar', $key);
    }

    /**
     * @return void
     */
    public function testGetStackInitiallyReturnsEmptyArray()
    {
        $plugin = new Plugin\ActionStack();
        $stack  = $plugin->getStack();
        $this->assertTrue(is_array($stack));
        $this->assertTrue(empty($stack));
    }

    /**
     * @return void
     */
    public function testPushStackAppendsToStack()
    {
        $plugin = new Plugin\ActionStack();

        $request1 = new Request\Simple();
        $plugin->pushStack($request1);
        $received = $plugin->getStack();
        $this->assertTrue(is_array($received));
        $this->assertEquals(1, count($received));
        $this->assertSame($request1, $received[0]);

        $request2 = new Request\Simple();
        $plugin->pushStack($request2);
        $received = $plugin->getStack();
        $this->assertTrue(is_array($received));
        $this->assertEquals(2, count($received));
        $this->assertSame($request2, $received[1]);
        $this->assertSame($request1, $received[0]);
    }

    public function getNewRequest()
    {
        $request = new Request\Simple();
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
        $plugin   = new Plugin\ActionStack();
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
        $plugin   = new Plugin\ActionStack();
        $received = $plugin->popStack();
        $this->assertFalse($received);
    }

    public function testPopStackPopsMultipleItemsWhenRequestActionEmpty()
    {
        $plugin   = new Plugin\ActionStack();
        $request1 = $this->getNewRequest();
        $request2 = new Request\Simple();
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
        $plugin   = new Plugin\ActionStack();
        $request  = $this->getNewRequest();
        $plugin->setRequest($request);

        $request1 = new Request\Simple();
        $request1->setActionName('blah');
        $plugin->pushStack($request1);

        $next = $plugin->popStack();
        $this->assertTrue($next instanceof Request\Simple);
        $this->assertEquals($request1->getActionName(), $next->getActionName());
        $this->assertEquals($request->getControllerName(), $next->getControllerName());
        $this->assertEquals($request->getModuleName(), $next->getModuleName());
    }

    public function testForwardResetsInternalRequestStateFromGivenRequest()
    {
        $plugin   = new Plugin\ActionStack();
        $request  = new Request\Simple();
        $plugin->setRequest($request);

        $next = $this->getNewRequest();
        $plugin->forward($next);

        $this->assertEquals($next->getActionName(), $request->getActionName());
        $this->assertEquals($next->getControllerName(), $request->getControllerName());
        $this->assertEquals($next->getModuleName(), $request->getModuleName());
        $this->assertFalse($request->isDispatched());
    }

    public function testForwardResetsRequestParamsIfFlagSet()
    {
        $plugin   = new Plugin\ActionStack();
        $request  = $this->getNewRequest();
        $params   = array('foo' => 'bar','baz'=>'bat');
        $request->setParams($params);
        $plugin->setRequest($request);

        $this->assertEquals($params,$plugin->getRequest()->getParams());

        $next = $this->getNewRequest();
        $plugin->forward($next);

        $this->assertEquals($params,$plugin->getRequest()->getParams());

        $plugin->setClearRequestParams(true);

        $next = $this->getNewRequest();
        $plugin->forward($next);

        $this->assertEquals(array(),$plugin->getRequest()->getParams());
    }

    /**
     * @return void
     */
    public function testPostDispatchResetsInternalRequestFromLastRequestOnStack()
    {
        $plugin   = new Plugin\ActionStack();
        $request  = new Request\Simple();
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
        $plugin   = new Plugin\ActionStack();

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
        $plugin   = new Plugin\ActionStack();
        $request  = new Request\Simple();
        $request->setDispatched(true);
        $plugin->setRequest($request);

        $request1 = new Request\Simple();
        $request2 = new Request\Simple();
        $request3 = new Request\Simple();
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
        $plugin   = new Plugin\ActionStack();
        $request  = new Request\Simple();
        $request->setDispatched(false);
        $plugin->setRequest($request);

        $request1 = new Request\Simple();
        $request2 = new Request\Simple();
        $request3 = new Request\Simple();
        $plugin->pushStack($request1)
               ->pushStack($request2)
               ->pushStack($request3);

        $plugin->postDispatch($request);
        $stack = $plugin->getStack();
        $this->assertEquals(3, count($stack));
    }
}

class Registry extends \Zend\Registry
{
    protected static $_registryClassName = 'Zend_Controller_Plugin_ActionStack_Registry';
}
