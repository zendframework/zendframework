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
namespace ZendTest\Controller;

use Zend\Controller,
    Zend\Controller\Request,
    Zend\Controller\Response,
    Zend\Controller\Router;

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Front
 */
class FrontTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\Controller\Front
     */
    protected $_controller = null;

    public function setUp()
    {
        $this->_controller = Controller\Front::getInstance();
        $this->_controller->resetInstance();
        $this->_controller->setControllerDirectory(__DIR__ . DIRECTORY_SEPARATOR . '_files')
                          ->setParam('noErrorHandler', true)
                          ->setParam('noViewRenderer', true)
                          ->returnResponse(true)
                          ->throwExceptions(false);
    }

    public function tearDown()
    {
        unset($this->_controller);
    }

    public function testResetInstance()
    {
        $this->_controller->setParam('foo', 'bar');
        $this->assertEquals('bar', $this->_controller->getParam('foo'));

        $this->_controller->resetInstance();
        $this->assertNull($this->_controller->getParam('bar'));
        $this->assertSame(array(), $this->_controller->getParams());
        $this->assertSame(array(), $this->_controller->getControllerDirectory());
    }

    /**
     * @group ZF-3145
     */
    public function testResetInstanceShouldResetHelperBroker()
    {
        $broker = $this->_controller->getHelperBroker();
        $broker->load('viewRenderer');
        $broker->load('url');
        $helpers = $broker->getPlugins();
        $this->assertEquals(2, count($helpers));

        $this->_controller->resetInstance();
        $broker = $this->_controller->getHelperBroker();
        $helpers = $broker->getPlugins();
        $this->assertEquals(0, count($helpers));
    }

    public function testSetGetRequest()
    {
        $request = new Request\Http();
        $this->_controller->setRequest($request);
        $this->assertTrue($request === $this->_controller->getRequest());

        $this->_controller->resetInstance();
        $this->_controller->setRequest('\Zend\Controller\Request\Http');
        $request = $this->_controller->getRequest();
        $this->assertTrue($request instanceof Request\Http);
    }

    public function testSetRequestThrowsExceptionWithBadRequest()
    {
        try {
            $this->_controller->setRequest('\Zend\Controller\Response\Cli');
            $this->fail('Should not be able to set invalid request class');
        } catch (\Exception $e) {
            // success
        }
    }

    public function testSetGetResponse()
    {
        $response = new Response\Cli();
        $this->_controller->setResponse($response);
        $this->assertTrue($response === $this->_controller->getResponse());

        $this->_controller->resetInstance();
        $this->_controller->setResponse('\Zend\Controller\Response\Cli');
        $response = $this->_controller->getResponse();
        $this->assertTrue($response instanceof Response\Cli);
    }

    public function testSetResponseThrowsExceptionWithBadResponse()
    {
        try {
            $this->_controller->setResponse('\Zend\Controller\Request\Http');
            $this->fail('Should not be able to set invalid response class');
        } catch (\Exception $e) {
            // success
        }
    }

    public function testSetGetRouter()
    {
        $router = new Router\Rewrite();
        $this->_controller->setRouter($router);
        $this->assertTrue($router === $this->_controller->getRouter());

        $this->_controller->resetInstance();
        $this->_controller->setRouter('\Zend\Controller\Router\Rewrite');
        $router = $this->_controller->getRouter();
        $this->assertTrue($router instanceof Router\Rewrite);
    }

    public function testSetRouterThrowsExceptionWithBadRouter()
    {
        try {
            $this->_controller->setRouter('\Zend\Controller\Request\Http');
            $this->fail('Should not be able to set invalid router class');
        } catch (\Exception $e) {
            // success
        }
    }

    public function testSetGetDispatcher()
    {
        $dispatcher = new \Zend\Controller\Dispatcher\Standard();
        $this->_controller->setDispatcher($dispatcher);

        $this->assertTrue($dispatcher === $this->_controller->getDispatcher());
    }

    public function testSetGetControllerDirectory()
    {
        $test = $this->_controller->getControllerDirectory();
        $expected = array('application' => __DIR__ . DIRECTORY_SEPARATOR . '_files');
        $this->assertSame($expected, $test);
    }

    public function testGetSetParam()
    {
        $this->_controller->setParam('foo', 'bar');
        $this->assertEquals('bar', $this->_controller->getParam('foo'));

        $this->_controller->setParam('bar', 'baz');
        $this->assertEquals('baz', $this->_controller->getParam('bar'));
    }

    public function testGetSetParams()
    {
        $this->_controller->setParams(array('foo' => 'bar'));
        $params = $this->_controller->getParams();
        $this->assertTrue(isset($params['foo']));
        $this->assertEquals('bar', $params['foo']);

        $this->_controller->setParam('baz', 'bat');
        $params = $this->_controller->getParams();
        $this->assertTrue(isset($params['foo']));
        $this->assertEquals('bar', $params['foo']);
        $this->assertTrue(isset($params['baz']));
        $this->assertEquals('bat', $params['baz']);

        $this->_controller->setParams(array('foo' => 'bug'));
        $params = $this->_controller->getParams();
        $this->assertTrue(isset($params['foo']));
        $this->assertEquals('bug', $params['foo']);
        $this->assertTrue(isset($params['baz']));
        $this->assertEquals('bat', $params['baz']);
    }

    public function testClearParams()
    {
        $this->_controller->setParams(array('foo' => 'bar', 'baz' => 'bat'));
        $params = $this->_controller->getParams();
        $this->assertTrue(isset($params['foo']));
        $this->assertTrue(isset($params['baz']));

        $this->_controller->clearParams('foo');
        $params = $this->_controller->getParams();
        $this->assertFalse(isset($params['foo']));
        $this->assertTrue(isset($params['baz']));

        $this->_controller->clearParams();
        $this->assertSame(array(), $this->_controller->getParams());

        $this->_controller->setParams(array('foo' => 'bar', 'bar' => 'baz', 'baz' => 'bat'));
        $this->assertSame(array('foo' => 'bar', 'bar' => 'baz', 'baz' => 'bat'), $this->_controller->getParams());
        $this->_controller->clearParams(array('foo', 'baz'));
        $this->assertSame(array('bar' => 'baz'), $this->_controller->getParams());
    }

    public function testSetGetDefaultControllerName()
    {
        $this->assertEquals('index', $this->_controller->getDefaultControllerName());

        $this->_controller->setDefaultControllerName('foo');
        $this->assertEquals('foo', $this->_controller->getDefaultControllerName());
    }

    public function testSetGetDefaultAction()
    {
        $this->assertEquals('index', $this->_controller->getDefaultAction());

        $this->_controller->setDefaultAction('bar');
        $this->assertEquals('bar', $this->_controller->getDefaultAction());
    }

    /**
     * Test default action on valid controller
     */
    public function testDispatch()
    {
        $request = new Request\Http('http://example.com/index');
        $this->_controller->setResponse(new Response\Cli());
        $response = $this->_controller->dispatch($request);

        $this->assertContains('Index action called', $response->getBody());
    }

    /**
     * Test valid action on valid controller
     */
    public function testDispatch1()
    {
        $request = new Request\Http('http://example.com/index/index');
        $this->_controller->setResponse(new Response\Cli());
        $response = $this->_controller->dispatch($request);

        $this->assertContains('Index action called', $response->getBody());
    }

    /**
     * Test invalid action on valid controller
     */
    /*
    public function testDispatch2()
    {
        $request = new Zend_Controller_Request_HTTP('http://example.com/index/foo');

        try {
            $this->_controller->dispatch($request);
            $this->fail('Exception should be raised by __call');
        } catch (Exception $e) {
            // success
        }
    }
     */

    /**
     * Test invalid controller
     */
    /*
    public function testDispatch3()
    {
        $request = new Zend_Controller_Request_HTTP('http://example.com/baz');

        try {
            $this->_controller->dispatch($request);
            $this->fail('Exception should be raised; no such controller');
        } catch (Exception $e) {
            // success
        }
    }
     */

    /**
     * Test valid action on valid controller; test pre/postDispatch
     */
    public function testDispatch4()
    {
        $request = new Request\Http('http://example.com/foo/bar');
        $this->_controller->setResponse(new Response\Cli());
        $response = $this->_controller->dispatch($request);

        $body = $response->getBody();
        $this->assertContains('Bar action called', $body, $body);
        $this->assertContains('preDispatch called', $body, $body);
        $this->assertContains('postDispatch called', $body, $body);
    }

    /**
     * Test that extra arguments get passed
     */
    public function testDispatch5()
    {
        $request = new Request\Http('http://example.com/index/args');
        $this->_controller->setResponse(new Response\Cli());
        $this->_controller->setParam('foo', 'bar');
        $this->_controller->setParam('baz', 'bat');
        $response = $this->_controller->dispatch($request);

        $body = $response->getBody();
        $this->assertContains('foo: bar', $body, $body);
        $this->assertContains('baz: bat', $body);
    }

    /**
     * Test using router
     */
    public function testDispatch6()
    {
        $request = new Request\Http('http://framework.zend.com/foo/bar/var1/baz');
        $this->_controller->setResponse(new Response\Cli());
        $this->_controller->setRouter(new Router\Rewrite());
        $response = $this->_controller->dispatch($request);

        $body = $response->getBody();
        $this->assertContains('Bar action called', $body);
        $params = $request->getParams();
        $this->assertTrue(isset($params['var1']));
        $this->assertEquals('baz', $params['var1']);
    }

    /**
     * Test without router, using GET params
     */
    public function testDispatch7()
    {
        if ('cli' == strtolower(php_sapi_name())) {
            $this->markTestSkipped('Issues with $_GET in CLI interface prevents test from passing');
        }
        $request = new Request\Http('http://framework.zend.com/index.php?controller=foo&action=bar');

        $response = new Response\Cli();
        $response = $this->_controller->dispatch($request, $response);

        $body = $response->getBody();
        $this->assertContains('Bar action called', $body);
    }

    /**
     * Test that run() throws exception when called from object instance
     */
    public function _testRunThrowsException()
    {
        try {
            $this->_controller->run(__DIR__ . DIRECTORY_SEPARATOR . '_files');
            $this->fail('Should not be able to call run() from object instance');
        } catch (\Exception $e) {
            // success
        }
    }

    /**
     * Test that set/getBaseUrl() functionality works
     */
    public function testSetGetBaseUrl()
    {
        $this->assertNull($this->_controller->getBaseUrl());
        $this->_controller->setBaseUrl('/index.php');
        $this->assertEquals('/index.php', $this->_controller->getBaseUrl());
    }

    public function testSetGetBaseUrlPopulatesRequest()
    {
        $request = new Request\Http();
        $this->_controller->setRequest($request);
        $this->_controller->setBaseUrl('/index.php');
        $this->assertEquals('/index.php', $request->getBaseUrl());

        $this->assertEquals($request->getBaseUrl(), $this->_controller->getBaseUrl());
    }

    public function testSetBaseUrlThrowsExceptionOnNonString()
    {
        try {
            $this->_controller->setBaseUrl(array());
            $this->fail('Should not be able to set non-string base URL');
        } catch (\Exception $e) {
            // success
        }
    }

    /**
     * Test that a set base URL is pushed to the request during the dispatch
     * process
     */
    public function testBaseUrlPushedToRequest()
    {
        $this->_controller->setBaseUrl('/index.php');
        $request  = new Request\Http('http://example.com/index');
        $response = new Response\Cli();
        $response = $this->_controller->dispatch($request, $response);

        $this->assertContains('index.php', $request->getBaseUrl());
    }

    /**
     * Test that throwExceptions() sets and returns value properly
     */
    public function testThrowExceptions()
    {
        $this->_controller->throwExceptions(true);
        $this->assertTrue($this->_controller->throwExceptions());
        $this->_controller->throwExceptions(false);
        $this->assertFalse($this->_controller->throwExceptions());
    }

    public function testThrowExceptionsFluentInterface()
    {
        $result = $this->_controller->throwExceptions(true);
        $this->assertSame($this->_controller, $result);
    }

    /**
     * Test that with throwExceptions() set, an exception is thrown
     */
    public function testThrowExceptionsThrows()
    {
        $this->_controller->throwExceptions(true);
        $this->_controller->setControllerDirectory(__DIR__);
        $request = new Request\Http('http://framework.zend.com/bogus/baz');
        $this->_controller->setResponse(new Response\Cli());
        $this->_controller->setRouter(new Router\Rewrite());

        try {
            $response = $this->_controller->dispatch($request);
            $this->fail('Invalid controller should throw exception');
        } catch (\Exception $e) {
            // success
        }
    }

    /**
     * Test that returnResponse() sets and returns value properly
     */
    public function testReturnResponse()
    {
        $this->_controller->returnResponse(true);
        $this->assertTrue($this->_controller->returnResponse());
        $this->_controller->returnResponse(false);
        $this->assertFalse($this->_controller->returnResponse());
    }

    public function testReturnResponseFluentInterface()
    {
        $result = $this->_controller->returnResponse(true);
        $this->assertSame($this->_controller, $result);
    }

    /**
     * Test that with returnResponse set to false, output is echoed and equals that in the response
     */
    public function testReturnResponseReturnsResponse()
    {
        $request = new Request\Http('http://framework.zend.com/foo/bar/var1/baz');
        $this->_controller->setResponse(new Response\Cli());
        $this->_controller->setRouter(new Router\Rewrite());
        $this->_controller->returnResponse(false);

        ob_start();
        $this->_controller->dispatch($request);
        $body = ob_get_clean();

        $actual = $this->_controller->getResponse()->getBody();
        $this->assertContains($actual, $body);
    }

    public function testRunStatically()
    {
        $request = new Request\Http('http://example.com/index/index');
        $this->_controller->setRequest($request);
        Controller\Front::run(__DIR__ . DIRECTORY_SEPARATOR . '_files');
    }

    public function testRunDynamically()
    {
        $request = new Request\Http('http://example.com/index/index');
        $this->_controller->setRequest($request);
        $this->_controller->run(__DIR__ . DIRECTORY_SEPARATOR . '_files');
    }

    public function testModulePathDispatched()
    {
        $this->_controller->addControllerDirectory(__DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . '/Admin', 'admin');
        $request = new Request\Http('http://example.com/admin/foo/bar');
        $this->_controller->setResponse(new Response\Cli());
        $response = $this->_controller->dispatch($request);

        $body = $response->getBody();
        $this->assertContains('Admin_Foo::bar action called', $body, $body);
    }

    public function testModuleControllerDirectoryName()
    {
        $this->assertEquals('controllers', $this->_controller->getModuleControllerDirectoryName());
        $this->_controller->setModuleControllerDirectoryName('foobar');
        $this->assertEquals('foobar', $this->_controller->getModuleControllerDirectoryName());
    }

    public function testAddModuleDirectory()
    {
        $moduleDir = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'modules';
        $this->_controller->addModuleDirectory($moduleDir);
        $controllerDirs = $this->_controller->getControllerDirectory();
        $this->assertTrue(isset($controllerDirs['foo']));
        $this->assertTrue(isset($controllerDirs['bar']));
        $this->assertTrue(isset($controllerDirs['application']));
        $this->assertFalse(isset($controllerDirs['.svn']));

        $this->assertContains('modules' . DIRECTORY_SEPARATOR . 'foo', $controllerDirs['foo']);
        $this->assertContains('modules' . DIRECTORY_SEPARATOR . 'bar', $controllerDirs['bar']);
        $this->assertContains('modules' . DIRECTORY_SEPARATOR . 'application', $controllerDirs['application']);
    }

    /**
     * @group ZF-2910
     */
    public function testShouldAllowRetrievingCurrentModuleDirectory()
    {
        $this->testAddModuleDirectory();
        $request = new Request\Http();
        $request->setModuleName('bar');
        $this->_controller->setRequest($request);
        $dir = $this->_controller->getModuleDirectory();
        $this->assertContains('modules' . DIRECTORY_SEPARATOR . 'bar', $dir);
        $this->assertNotContains('controllers', $dir);
    }

    public function testShouldAllowRetrievingSpecifiedModuleDirectory()
    {
        $this->testAddModuleDirectory();
        $dir = $this->_controller->getModuleDirectory('foo');
        $this->assertContains('modules' . DIRECTORY_SEPARATOR . 'foo', $dir);
        $this->assertNotContains('controllers', $dir);
    }

    public function testShouldReturnNullWhenRetrievingNonexistentModuleDirectory()
    {
        $this->testAddModuleDirectory();
        $this->assertNull($this->_controller->getModuleDirectory('bogus-foo-bar'));
    }

    /**
     * ZF-2435
     */
    public function testCanRemoveIndividualModuleDirectory()
    {
        $moduleDir = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'modules';
        $this->_controller->addModuleDirectory($moduleDir);
        $controllerDirs = $this->_controller->getControllerDirectory();
        $this->_controller->removeControllerDirectory('foo');
        $test = $this->_controller->getControllerDirectory();
        $this->assertNotEquals($controllerDirs, $test);
        $this->assertFalse(array_key_exists('foo', $test));
    }

    public function testAddModuleDirectoryThrowsExceptionForInvalidDirectory()
    {
        $moduleDir = 'doesntexist';
        try {
            $this->_controller->addModuleDirectory($moduleDir);
            $this->fail('Exception expected but not thrown');
        }catch(\Exception $e){
            $this->assertInstanceOf('Zend\Controller\Exception',$e);
            $this->assertRegExp('/Directory \w+ not readable/',$e->getMessage());
        }
    }

    public function testGetControllerDirectoryByModuleName()
    {
        $moduleDir = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'modules';
        $this->_controller->addModuleDirectory($moduleDir);
        $barDir = $this->_controller->getControllerDirectory('bar');
        $this->assertNotNull($barDir);
        $this->assertContains('modules' . DIRECTORY_SEPARATOR . 'bar', $barDir);
    }

    public function testGetControllerDirectoryByModuleNameReturnsNullOnBadModule()
    {
        $moduleDir = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'modules';
        $this->_controller->addModuleDirectory($moduleDir);
        $dir = $this->_controller->getControllerDirectory('_bazbat');
        $this->assertNull($dir);
    }

    public function testDefaultModule()
    {
        $dispatcher = $this->_controller->getDispatcher();
        $this->assertEquals($dispatcher->getDefaultModule(), $this->_controller->getDefaultModule());
        $this->_controller->setDefaultModule('foobar');
        $this->assertEquals('foobar', $this->_controller->getDefaultModule());
        $this->assertEquals($dispatcher->getDefaultModule(), $this->_controller->getDefaultModule());
    }

    public function testErrorHandlerPluginRegisteredWhenDispatched()
    {
        $this->assertFalse($this->_controller->hasPlugin('\Zend\Controller\Plugin\ErrorHandler'));
        $request = new Request\Http('http://example.com/index/index');
        $this->_controller->setParam('noErrorHandler', false)
                          ->setResponse(new Response\Cli());
        $response = $this->_controller->dispatch($request);

        $this->assertTrue($this->_controller->hasPlugin('\Zend\Controller\Plugin\ErrorHandler'));
    }

    public function testErrorHandlerPluginNotRegisteredIfNoErrorHandlerSet()
    {
        $this->assertFalse($this->_controller->hasPlugin('\Zend\Controller\Plugin\ErrorHandler'));
        $request = new Request\Http('http://example.com/index/index');
        $this->_controller->setParam('noErrorHandler', true)
                          ->setResponse(new Response\Cli());
        $response = $this->_controller->dispatch($request);

        $this->assertFalse($this->_controller->hasPlugin('\Zend\Controller\Plugin\ErrorHandler'));
    }

    public function testReplaceRequestAndResponseMidStream()
    {
        $request = new Request\Http('http://example.com/index/replace');
        $this->_controller->setResponse(new Response\Cli());
        $response = new Response\Http();
        $responsePost = $this->_controller->dispatch($request, $response);

        $requestPost  = $this->_controller->getRequest();

        $this->assertNotSame($request, $requestPost);
        $this->assertNotSame($response, $responsePost);

        $this->assertContains('Reset action called', $responsePost->getBody());
        $this->assertNotContains('Reset action called', $response->getBody());
    }

    public function testViewRendererHelperRegisteredWhenDispatched()
    {
        $broker = $this->_controller->getHelperBroker();
        $this->assertFalse($broker->hasPlugin('viewRenderer'));
        $this->_controller->setParam('noViewRenderer', false);

        $request = new Request\Http('http://example.com/index/index');
        $this->_controller->setResponse(new Response\Cli());
        $response = $this->_controller->dispatch($request);

        $this->assertTrue($broker->hasPlugin('viewRenderer'));
    }

    public function testViewRendererHelperNotRegisteredIfNoViewRendererSet()
    {
        $broker = $this->_controller->getHelperBroker();
        $this->assertFalse($broker->hasPlugin('viewRenderer'));
        $this->_controller->setParam('noViewRenderer', true);

        $request = new Request\Http('http://example.com/index/index');
        $this->_controller->setResponse(new Response\Cli());
        $response = $this->_controller->dispatch($request);

        $this->assertFalse($broker->hasPlugin('viewRenderer'));
    }
}
