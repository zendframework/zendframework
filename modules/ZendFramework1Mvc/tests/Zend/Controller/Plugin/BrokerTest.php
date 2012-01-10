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
use Zend\Controller\Response;

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Plugin
 */
class BrokerTest extends \PHPUnit_Framework_TestCase
{
    public $controller;

    public function setUp()
    {
        $this->controller = \Zend\Controller\Front::getInstance();
        $this->controller->resetInstance();
        $this->controller->setParam('noViewRenderer', true)
                         ->setParam('noErrorHandler', true);
    }

    public function testDuplicatePlugin()
    {
        $broker = new Plugin\Broker();
        $plugin = new TestPlugin();
        $broker->registerPlugin($plugin);
        try {
            $broker->registerPlugin($plugin);
            $this->fail('Duplicate registry of plugin object should be disallowed');
        } catch (\Exception $expected) {
            $this->assertContains('already', $expected->getMessage());
        }
    }


    public function testUsingFrontController()
    {
        $this->controller->setControllerDirectory(dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files');
        $request = new Request\Http('http://framework.zend.com/empty');
        $this->controller->setResponse(new Response\Cli());
        $plugin = new TestPlugin();
        $this->controller->registerPlugin($plugin);
        $this->controller->returnResponse(true);
        $response = $this->controller->dispatch($request);
        $this->assertEquals('123456', $response->getBody());
        $this->assertEquals('123456', $plugin->getResponse()->getBody());
    }

    public function testUnregisterPluginWithObject()
    {
        $broker = new Plugin\Broker();
        $plugin = new TestPlugin();
        $broker->registerPlugin($plugin);
        $plugins = $broker->getPlugins();
        $this->assertEquals(1, count($plugins));
        $broker->unregisterPlugin($plugin);
        $plugins = $broker->getPlugins();
        $this->assertEquals(0, count($plugins));
    }

    public function testUnregisterPluginByClassName()
    {
        $broker = new Plugin\Broker();
        $plugin = new TestPlugin();
        $broker->registerPlugin($plugin);
        $plugins = $broker->getPlugins();
        $this->assertEquals(1, count($plugins));
        $broker->unregisterPlugin('ZendTest\Controller\Plugin\TestPlugin');
        $plugins = $broker->getPlugins();
        $this->assertEquals(0, count($plugins));
    }

    public function testGetPlugins()
    {
        $broker = new Plugin\Broker();
        $plugin = new TestPlugin();
        $broker->registerPlugin($plugin);
        $plugins = $broker->getPlugins();
        $this->assertEquals(1, count($plugins));
        $this->assertSame($plugin, $plugins[0]);
    }

    public function testGetPluginByName()
    {
        $broker = new Plugin\Broker();
        $plugin = new TestPlugin();
        $broker->registerPlugin($plugin);
        $retrieved = $broker->getPlugin('ZendTest\Controller\Plugin\TestPlugin');
        $this->assertTrue($retrieved instanceof TestPlugin);
        $this->assertSame($plugin, $retrieved);
    }

    public function testGetPluginByNameReturnsFalseWithBadClassName()
    {
        $broker = new Plugin\Broker();
        $plugin = new TestPlugin();
        $broker->registerPlugin($plugin);
        $retrieved = $broker->getPlugin('TestPlugin');
        $this->assertFalse($retrieved);
    }

    public function testGetPluginByNameReturnsArray()
    {
        $broker = new Plugin\Broker();
        $plugin = new TestPlugin();
        $broker->registerPlugin($plugin);

        $plugin2 = new TestPlugin();
        $broker->registerPlugin($plugin2);

        $retrieved = $broker->getPlugin('ZendTest\Controller\Plugin\TestPlugin');
        $this->assertTrue(is_array($retrieved));
        $this->assertEquals(2, count($retrieved));
        $this->assertSame($plugin, $retrieved[0]);
        $this->assertSame($plugin2, $retrieved[1]);
    }

    public function testHasPlugin()
    {
        $broker = new Plugin\Broker();
        $this->assertFalse($broker->hasPlugin('ZendTest\Controller\Plugin\TestPlugin'));

        $plugin = new TestPlugin();
        $broker->registerPlugin($plugin);
        $this->assertTrue($broker->hasPlugin('ZendTest\Controller\Plugin\TestPlugin'));
    }

    public function testBrokerCatchesExceptions()
    {
        $request  = new Request\Http('http://framework.zend.com/empty');
        $response = new Response\Cli();
        $broker   = new Plugin\Broker();
        $broker->setResponse($response);
        $broker->registerPlugin(new ExceptionTestPlugin());
        try {
            $broker->routeStartup($request);
            $broker->routeShutdown($request);
            $broker->dispatchLoopStartup($request);
            $broker->preDispatch($request);
            $broker->postDispatch($request);
            $broker->dispatchLoopShutdown();
        } catch (\Exception $e) {
            $this->fail('Broker should catch exceptions');
        }

        $this->assertTrue($response->hasExceptionOfMessage('routeStartup triggered exception'));
        $this->assertTrue($response->hasExceptionOfMessage('routeShutdown triggered exception'));
        $this->assertTrue($response->hasExceptionOfMessage('dispatchLoopStartup triggered exception'));
        $this->assertTrue($response->hasExceptionOfMessage('preDispatch triggered exception'));
        $this->assertTrue($response->hasExceptionOfMessage('postDispatch triggered exception'));
        $this->assertTrue($response->hasExceptionOfMessage('dispatchLoopShutdown triggered exception'));
    }

    public function testRegisterPluginStackOrderIsSane()
    {
        $broker   = new Plugin\Broker();
        $plugin1  = new TestPlugin();
        $plugin2  = new ExceptionTestPlugin();
        $plugin3  = new TestPlugin2();
        $broker->registerPlugin($plugin1, 5);
        $broker->registerPlugin($plugin2, -5);
        $broker->registerPlugin($plugin3, 2);

        $plugins = $broker->getPlugins();
        $expected = array(-5 => $plugin2, 2 => $plugin3, 5 => $plugin1);
        $this->assertSame($expected, $plugins);
    }

    public function testRegisterPluginThrowsExceptionOnDuplicateStackIndex()
    {
        $broker   = new Plugin\Broker();
        $plugin1  = new TestPlugin();
        $plugin2  = new ExceptionTestPlugin();
        $broker->registerPlugin($plugin1, 5);
        try {
            $broker->registerPlugin($plugin2, 5);
            $this->fail('Registering plugins with same stack index should raise exception');
        } catch (\Exception $e) {
        }
    }

    public function testRegisterPluginStackOrderWithAutmaticNumbersIncrementsCorrectly()
    {
        $broker   = new Plugin\Broker();
        $plugin1  = new TestPlugin();
        $plugin2  = new ExceptionTestPlugin();
        $plugin3  = new TestPlugin2();
        $broker->registerPlugin($plugin1, 2);
        $broker->registerPlugin($plugin2, 3);
        $broker->registerPlugin($plugin3);

        $plugins = $broker->getPlugins();
        $expected = array(2 => $plugin1, 3 => $plugin2, 4 => $plugin3);
        $this->assertSame($expected, $plugins);
    }

    /**
     * Test for ZF-2305
     * @return void
     */
    public function testRegisterPluginSetsRequestAndResponse()
    {
        $broker   = new Plugin\Broker();
        $request  = new Request\Simple();
        $response = new Response\Cli();
        $broker->setRequest($request);
        $broker->setResponse($response);

        $plugin   = new TestPlugin();
        $broker->registerPlugin($plugin);

        $this->assertSame($request, $plugin->getRequest());
        $this->assertSame($response, $plugin->getResponse());
    }
}

class TestPlugin extends Plugin\AbstractPlugin
{
    public function routeStartup(Request\AbstractRequest $request)
    {
        $this->getResponse()->appendBody('1');
    }

    public function routeShutdown(Request\AbstractRequest $request)
    {
        $this->getResponse()->appendBody('2');
    }

    public function dispatchLoopStartup(Request\AbstractRequest $request)
    {
        $this->getResponse()->appendBody('3');
    }

    public function preDispatch(Request\AbstractRequest $request)
    {
        $this->getResponse()->appendBody('4');
    }

    public function postDispatch(Request\AbstractRequest $request)
    {
        $this->getResponse()->appendBody('5');
    }

    public function dispatchLoopShutdown()
    {
        $this->getResponse()->appendBody('6');
    }
}

class TestPlugin2 extends TestPlugin
{
}

class ExceptionTestPlugin extends Plugin\AbstractPlugin
{
    public function routeStartup(Request\AbstractRequest $request)
    {
        throw new \Exception('routeStartup triggered exception');
    }

    public function routeShutdown(Request\AbstractRequest $request)
    {
        throw new \Exception('routeShutdown triggered exception');
    }

    public function dispatchLoopStartup(Request\AbstractRequest $request)
    {
        throw new \Exception('dispatchLoopStartup triggered exception');
    }

    public function preDispatch(Request\AbstractRequest $request)
    {
        throw new \Exception('preDispatch triggered exception');
    }

    public function postDispatch(Request\AbstractRequest $request)
    {
        throw new \Exception('postDispatch triggered exception');
    }

    public function dispatchLoopShutdown()
    {
        throw new \Exception('dispatchLoopShutdown triggered exception');
    }
}

