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
namespace ZendTest\Controller\Action\Helper;
use Zend\Controller\Action\Helper;
use Zend\Controller\Request;


/**
 * Test class for Zend_Controller_Action_Helper_ActionStack.
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Action
 * @group      Zend_Controller_Action_Helper
 */
class ActionStackTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Zend_Controller_Front
     */
    public $front;

    /**
     * @var Zend_Controller_Request_HTTP
     */
    public $request;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->front = \Zend\Controller\Front::getInstance();
        $this->front->resetInstance();

        $this->request = new Request\Http();
        $this->front->setRequest($this->request);
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

    public function testConstructorInstantiatesPluginIfNotPresent()
    {
        $this->assertFalse($this->front->hasPlugin('Zend\Controller\Plugin\ActionStack'));
        $helper = new Helper\ActionStack();
        $this->assertTrue($this->front->hasPlugin('Zend\Controller\Plugin\ActionStack'));
    }

    public function testConstructorUsesExistingPluginWhenPresent()
    {
        $plugin = new \Zend\Controller\Plugin\ActionStack();
        $this->front->registerPlugin($plugin);
        $helper = new Helper\ActionStack();
        $this->assertTrue($this->front->hasPlugin('Zend\Controller\Plugin\ActionStack'));
        $registered = $this->front->getPlugin('Zend\Controller\Plugin\ActionStack');
        $this->assertSame($plugin, $registered);
    }

    public function testPushStackPushesToPluginStack()
    {
        $helper = new Helper\ActionStack();
        $plugin = $this->front->getPlugin('Zend\Controller\Plugin\ActionStack');

        $request = new Request\Simple();
        $request->setModuleName('foo')
                ->setControllerName('bar')
                ->setActionName('baz');

        $helper->pushStack($request);

        $next = $plugin->popStack();
        $this->assertTrue($next instanceof Request\AbstractRequest);
        $this->assertEquals($request->getModuleName(), $next->getModuleName());
        $this->assertEquals($request->getControllerName(), $next->getControllerName());
        $this->assertEquals($request->getActionName(), $next->getActionName());
        $this->assertFalse($next->isDispatched());
    }

    public function testActionToStackPushesNewRequestToPluginStack()
    {
        $helper = new Helper\ActionStack();
        $plugin = $this->front->getPlugin('Zend\Controller\Plugin\ActionStack');

        $helper->actionToStack('baz', 'bar', 'foo');
        $next = $plugin->popStack();
        $this->assertTrue($next instanceof Request\AbstractRequest);
        $this->assertEquals('foo', $next->getModuleName());
        $this->assertEquals('bar', $next->getControllerName());
        $this->assertEquals('baz', $next->getActionName());
        $this->assertFalse($next->isDispatched());
    }

    public function testPassingRequestToActionToStackPushesRequestToPluginStack()
    {
        $helper = new Helper\ActionStack();
        $plugin = $this->front->getPlugin('Zend\Controller\Plugin\ActionStack');

        $request = new Request\Simple();
        $request->setModuleName('foo')
                ->setControllerName('bar')
                ->setActionName('baz');

        $helper->actionToStack($request);

        $next = $plugin->popStack();
        $this->assertTrue($next instanceof Request\AbstractRequest);
        $this->assertEquals($request->getModuleName(), $next->getModuleName());
        $this->assertEquals($request->getControllerName(), $next->getControllerName());
        $this->assertEquals($request->getActionName(), $next->getActionName());
        $this->assertFalse($next->isDispatched());
    }

    public function testDirectProxiesToActionToStack()
    {
        $helper = new Helper\ActionStack();
        /** FC should be reseted to test ActionStack with a really blank FC */
        $this->front->resetInstance();
        try{
            $helper->direct('baz', 'bar', 'foo');
            $this->fail('Zend_Controller_Action_Exception should be thrown');
        }catch(\Zend\Controller\Exception $e){
            $this->assertInstanceOf('Zend\Controller\Action\Exception',
                   $e,
                   'Zend\Controller\Action\Exception expected, '.get_class($e).' caught');
        }
    }

     public function testCannotStackActionIfNoRequestAvailable()
    {
        $helper = new Helper\ActionStack();
        $plugin = $this->front->getPlugin('Zend\Controller\Plugin\ActionStack');

        $helper->direct('baz', 'bar', 'foo');
        $next = $plugin->popStack();
        $this->assertTrue($next instanceof Request\AbstractRequest);
        $this->assertEquals('foo', $next->getModuleName());
        $this->assertEquals('bar', $next->getControllerName());
        $this->assertEquals('baz', $next->getActionName());
        $this->assertFalse($next->isDispatched());
    }
}
