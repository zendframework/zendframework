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
namespace ZendTest\Controller\Action;

require_once __DIR__ . '/../_files/HelperBrokerController.php';
require_once __DIR__ . '/TestAsset/TestHelper.php';

use Zend\Controller\Action\HelperBroker,
    Zend\Controller\Front as FrontController,
    Zend\Controller\Request\Http as Request,
    Zend\Controller\Response\Cli as Response,
    Zend\Controller\Action\Helper,
    Zend\Loader\PluginLoader;

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Action
 * @group      Zend_Controller_Action_Helper
 */
class HelperBrokerTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->broker = new HelperBroker();
    }

    public function testHelperStackIsLifo()
    {
        $url        = $this->broker->load('url');
        $json       = $this->broker->load('json');
        $cache      = $this->broker->load('cache');
        $redirector = $this->broker->load('redirector');

        $names = array();
        foreach ($this->broker as $helper) {
            $names[] = $helper->getName();
        }
        $this->assertEquals(array('redirector', 'cache', 'json', 'url'), $names);
    }

    public function testResetRemovesBothPluginsAndStack()
    {
        $url        = $this->broker->load('url');
        $json       = $this->broker->load('json');
        $cache      = $this->broker->load('cache');
        $redirector = $this->broker->load('redirector');
        $this->broker->reset();

        $this->assertEquals(0, count($this->broker->getStack()));
        $this->assertFalse($this->broker->isLoaded('url'));
    }

    public function testStackIsAPriorityStack()
    {
        $url        = $this->broker->load('url');
        $json       = $this->broker->load('json');
        $cache      = $this->broker->load('cache');
        $redirector = $this->broker->load('redirector');

        $this->broker->getStack()->offsetSet(-90, $cache);

        $names = array();
        foreach ($this->broker as $helper) {
            $names[] = $helper->getName();
        }
        $this->assertEquals(array('redirector', 'json', 'url', 'cache'), $names);
    }

    public function testInjectsBrokerIfHelperHasSetBrokerMethod()
    {
        $url        = $this->broker->load('url');
        $this->assertSame($this->broker, $url->getBroker());
    }

    public function testGetPluginsReturnsPriorityStack()
    {
        $this->assertInstanceOf('Zend\Controller\Action\HelperPriorityStack', $this->broker->getPlugins());
    }

    public function testGetPluginsLoadsSpeccedHelpers()
    {
        $this->broker->registerSpec('url');
        $plugins = $this->broker->getPlugins();
        $this->assertEquals(1, count($plugins));
        foreach ($plugins as $plugin) {
            $this->assertEquals('url', $plugin->getName());
        }
    }

    public function testSettingActionControllerInjectsControllerIntoHelpersAndCallsInit()
    {
        $request  = new \Zend\Controller\Request\Simple;
        $response = new \Zend\Controller\Response\Http;
        $controller = new TestAsset\TestController($request, $response, array());

        $helper = new TestAsset\TestHelper();
        $this->broker->register('test', $helper);

        $this->broker->setActionController($controller);

        $this->assertEquals(1, $helper->count);
    }

    public function testCallingNotifyPreDispatchNotifiesAttachedHelpers()
    {
        $helper = new TestAsset\TestHelper();
        $this->broker->register('test', $helper);
        $this->broker->notifyPreDispatch();
        $this->assertTrue($helper->preDispatch);
    }

    public function testCallingNotifyPostDispatchNotifiesAttachedHelpers()
    {
        $helper = new TestAsset\TestHelper();
        $this->broker->register('test', $helper);
        $this->broker->notifyPostDispatch();
        $this->assertTrue($helper->postDispatch);
    }
}
