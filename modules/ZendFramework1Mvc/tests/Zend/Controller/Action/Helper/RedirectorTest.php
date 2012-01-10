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

use Zend\Controller,
    Zend\Controller\Action,
    Zend\Controller\Router\Route;

/**
 * Test class for Zend_Controller_Action_Helper_Redirector.
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
class RedirectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Controller_Action_Helper_Redirector
     */
    public $redirector;

    /**
     * @var Zend_Controller_Request_HTTP
     */
    public $request;

    /**
     * @var Zend_Controller_Response_HTTP
     */
    public $response;

    /**
     * @var Zend_Controller_Action
     */
    public $controller;


    /**
     * Set up redirector
     *
     * Creates request, response, and action controller objects; sets action
     * controller in redirector, and sets exit to false.
     *
     * Also resets the front controller instance.
     */
    public function setUp()
    {
        $front = Controller\Front::getInstance();
        $front->resetInstance();
        $broker = $front->getHelperBroker();

        $this->redirector = new \Zend\Controller\Action\Helper\Redirector();
        $this->redirector->setBroker($broker);

        $this->router     = $front->getRouter();
        $this->request    = new \Zend\Controller\Request\Http();
        $this->response   = new \Zend\Controller\Response\Http();
        $this->controller = new TestController(
            $this->request,
            $this->response,
            array()
        );

        // Add default routes
        $this->router->addDefaultRoutes();

        // do this so setting headers does not throw exceptions
        $this->response->headersSentThrowsException = false;

        $this->redirector->setExit(false)
                         ->setActionController($this->controller);
        $this->_server = $_SERVER;
    }

    /**
     * Unset all properties
     */
    public function tearDown()
    {
        unset($this->redirector);
        unset($this->controller);
        unset($this->request);
        unset($this->response);
        $_SERVER = $this->_server;
    }

    public function testCode()
    {
        $this->assertEquals(302, $this->redirector->getCode(), 'Default code should be 302');
        $this->redirector->setCode(301);
        $this->assertEquals(301, $this->redirector->getCode());

        try {
            $this->redirector->setCode(251);
            $this->fail('Invalid redirect code should throw exception');
        } catch (\Exception $e) {
        }

        try {
            $this->redirector->setCode(351);
            $this->fail('Invalid redirect code should throw exception');
        } catch (\Exception $e) {
        }
    }

    public function testCodeAsAStringIsAllowed()
    {
        $this->redirector->setCode('303');
        $this->assertEquals(303, $this->redirector->getCode());

        try {
            $this->redirector->setCode('251');
            $this->fail('Invalid redirect code should throw exception');
        } catch (\Exception $e) {
        }

        try {
            $this->redirector->setCode('351');
            $this->fail('Invalid redirect code should throw exception');
        } catch (\Exception $e) {
        }
    }

    public function testRedirectorShouldOnlyAllowValidHttpRedirectCodes()
    {
        try {
            $this->redirector->setCode('306');
            $this->fail('Invalid redirect code should throw exception');
        } catch (Action\Exception $e) {
        }
        try {
            $this->redirector->setCode('304');
            $this->fail('Invalid redirect code should throw exception');
        } catch (Action\Exception $e) {
        }
    }

    public function testExit()
    {
        $this->assertFalse($this->redirector->getExit());
        $this->redirector->setExit(true);
        $this->assertTrue($this->redirector->getExit());
    }

    public function testPrependBase()
    {
        $this->assertTrue($this->redirector->getPrependBase());
        $this->redirector->setPrependBase(false);
        $this->assertFalse($this->redirector->getPrependBase());
    }

    public function testCloseSessionOnExit()
    {
        $this->assertTrue($this->redirector->getCloseSessionOnExit());
        $this->redirector->setCloseSessionOnExit(false);
        $this->assertFalse($this->redirector->getCloseSessionOnExit());
    }

    public function testGetRedirectUrlNullByDefault()
    {
        $this->assertNull($this->redirector->getRedirectUrl());
    }

    public function testSetGotoWithActionOnly()
    {
        $request = $this->request;
        $request->setModuleName('blog')
                ->setControllerName('list')
                ->setActionName('all');

        $this->redirector->setGoto('error');
        $this->assertEquals('/blog/list/error', $this->redirector->getRedirectUrl());
    }

    public function testSetGotoWithActionAndController()
    {
        $request = $this->request;
        $request->setModuleName('blog')
                ->setControllerName('list')
                ->setActionName('all');

        $this->redirector->setGoto('item', 'view');
        $this->assertEquals('/blog/view/item', $this->redirector->getRedirectUrl());
    }

    public function testSetGotoWithActionControllerAndModule()
    {
        $request = $this->request;
        $request->setModuleName('blog')
                ->setControllerName('list')
                ->setActionName('all');

        $this->redirector->setGoto('item', 'view', 'news');
        $this->assertEquals('/news/view/item', $this->redirector->getRedirectUrl());
    }

    public function testSetGotoWithActionControllerModuleAndParams()
    {
        $request = $this->request;
        $request->setModuleName('blog')
                ->setControllerName('list')
                ->setActionName('all');

        $this->redirector->setGoto('item', 'view', 'news', array('id' => 42));
        $this->assertEquals('/news/view/item/id/42', $this->redirector->getRedirectUrl());
    }

    /**
     * ZF-2351
     */
    public function testGotoDoesNotUtilizeDefaultSegments()
    {
        $request = $this->request;
        $request->setModuleName('application');
        $this->redirector->setGoto('index', 'index');
        $this->assertEquals('/', $this->redirector->getRedirectUrl());

        $this->redirector->setGoto('index', 'blog');
        $this->assertEquals('/blog', $this->redirector->getRedirectUrl());
    }


    public function testSetGotoRoute()
    {
        $router = Controller\Front::getInstance()->getRouter();
        $route = new Route\Route(
            'blog/archive/:id',
            array('controller' => 'blog', 'action' => 'view', 'id' => false),
            array('id' => '\d+')
        );
        $router->addRoute('blogArchive', $route);

        $this->redirector->setGotoRoute(
            array('id' => 281),
            'blogArchive'
        );

        $this->assertEquals('/blog/archive/281', $this->redirector->getRedirectUrl());
    }

    public function testSetGotoUrl()
    {
        $this->redirector->setGotoUrl('/foo/bar');
        $this->assertEquals('/foo/bar', $this->redirector->getRedirectUrl());
    }

    public function testSetGotoUrlWithBaseUrlUsingPrependBaseProperty()
    {
        $this->request->setBaseUrl('/my');
        $this->redirector->setPrependBase(true);
        $this->redirector->setGotoUrl('/foo/bar');
        $this->assertEquals('/my/foo/bar', $this->redirector->getRedirectUrl());
    }

    public function testSetGotoUrlWithBaseUrlUsingPrependBaseOption()
    {
        $this->request->setBaseUrl('/my');
        $this->redirector->setGotoUrl('/foo/bar', array('prependBase' => true));
        $this->assertEquals('/my/foo/bar', $this->redirector->getRedirectUrl());
    }

    public function testSetGotoUrlWithHttpCodeUsingCodeProperty()
    {
        $this->redirector->setCode(301);
        $this->redirector->setGotoUrl('/foo/bar');
        $this->assertEquals('/foo/bar', $this->redirector->getRedirectUrl());
        $this->assertEquals(301, $this->response->getHttpResponseCode());
    }

    public function testSetGotoUrlWithHttpCodeUsingCodeOption()
    {
        $this->redirector->setGotoUrl('/foo/bar', array('code' => 301));
        $this->assertEquals('/foo/bar', $this->redirector->getRedirectUrl());
        $this->assertEquals(301, $this->response->getHttpResponseCode());
    }

    /**
     * goto() is an alias for setGoto(); just do a single test case
     */
    public function testGoto()
    {
        $request = $this->request;
        $request->setModuleName('blog')
                ->setControllerName('list')
                ->setActionName('all');

        $this->redirector->goto('error');
        $this->assertEquals('/blog/list/error', $this->redirector->getRedirectUrl());
    }

    public function testGotoAndExit()
    {
        $this->markTestSkipped(
          "Testing Zend_Controller_Action_Helper_Redirector::gotoAndExit() would break the test suite"
        );
    }

    /**
     * gotoRoute() is an alias for setGotoRoute()
     */
    public function testGotoRoute()
    {
        $router = Controller\Front::getInstance()->getRouter();
        $route = new Route\Route(
            'blog/archive/:id',
            array('controller' => 'blog', 'action' => 'view', 'id' => false),
            array('id' => '\d+')
        );
        $router->addRoute('blogArchive', $route);

        $this->redirector->gotoRoute(
            array('id' => 281),
            'blogArchive'
        );

        $this->assertEquals('/blog/archive/281', $this->redirector->getRedirectUrl());
    }

    public function testGotoRouteAndExit()
    {
        $this->markTestSkipped(
          "Testing Zend_Controller_Action_Helper_Redirector::gotoRouteAndExit() would break the test suite"
        );
    }

    /**
     * gotoUrl() is an alias for setGotoUrl()
     */
    public function testGotoUrl()
    {
        $this->redirector->gotoUrl('/foo/bar');
        $this->assertEquals('/foo/bar', $this->redirector->getRedirectUrl());
    }

    public function testGotoUrlAndExit()
    {
        $this->markTestSkipped(
          "Testing Zend_Controller_Action_Helper_Redirector::gotoUrlAndExit() would break the test suite"
        );
    }

    public function testRedirectAndExit()
    {
        $this->markTestSkipped(
          "Testing Zend_Controller_Action_Helper_Redirector::redirectAndExit() would break the test suite"
        );
    }

    /**
     * direct() is an alias for goto(), which is an alias for setGoto()
     */
    public function testDirect()
    {
        $request = $this->request;
        $request->setModuleName('blog')
                ->setControllerName('list')
                ->setActionName('all');

        $this->redirector->direct('error');
        $this->assertEquals('/blog/list/error', $this->redirector->getRedirectUrl());
    }

    public function testUseAbsoluteUriFlag()
    {
        $this->assertFalse($this->redirector->getUseAbsoluteUri());
        $this->redirector->setUseAbsoluteUri(true);
        $this->assertTrue($this->redirector->getUseAbsoluteUri());
    }

    public function testUseAbsoluteUriSetsFullUriInResponse()
    {
        $_SERVER['HTTP_HOST']   = 'foobar.example.com';
        $_SERVER['SERVER_PORT'] = '4443';
        $_SERVER['HTTPS']       = 1;
        $this->redirector->setUseAbsoluteUri(true);
        $this->redirector->gotoUrl('/bar/baz');
        $headers = $this->response->getHeaders();
        $uri = false;
        foreach ($headers as $header) {
            if ('Location' == $header['name']) {
                $uri = $header['value'];
            }
        }
        if (!$uri) {
            $this->fail('No redirect header set in response');
        }

        $this->assertEquals('https://foobar.example.com:4443/bar/baz', $uri);
    }

    /**
     * ZF-2602
     */
    public function testPassingEmptyStringToGotoUrlRedirectsToRoot()
    {
        $this->redirector->gotoUrl('');
        $this->assertEquals('/', $this->redirector->getRedirectUrl());
    }

    /**
     * @group ZF-1734
     */
    public function testPassingNullActionAndNullControllerWithModuleShouldGoToDefaultControllerAndActions()
    {
        $this->request->setModuleName('admin')
                      ->setControllerName('class')
                      ->setActionName('view');
        $this->redirector->gotoSimple(null, null, 'admin');
        $test = $this->redirector->getRedirectUrl();
        $this->assertEquals('/admin', $test, $test);
    }

    public function testPassingNullActionShouldGoToDefaultActionOfCurrentController()
    {
        $this->request->setModuleName('admin')
                      ->setControllerName('class')
                      ->setActionName('view');
        $this->redirector->gotoSimple(null);
        $test = $this->redirector->getRedirectUrl();
        $this->assertEquals('/admin/class', $test, $test);
    }

    public function testPassingDefaultModuleShouldNotRenderModuleNameInRedirectUrl()
    {
        $this->request->setModuleName('admin')
                      ->setControllerName('class')
                      ->setActionName('view');
        $this->redirector->gotoSimple('login', 'account', 'application');
        $test = $this->redirector->getRedirectUrl();
        $this->assertEquals('/account/login', $test, $test);
    }

    /**
     * @group ZF-4318
     */
    public function testServerVariableHttpsToOffDoesNotBuildHttpsUrl()
    {
        // Set Preconditions from Issue:
        $_SERVER['HTTPS'] = "off";
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['SERVER_PORT'] = 80;
        $this->redirector->setUseAbsoluteUri(true);

        $this->request->setModuleName('admin')
                      ->setControllerName('class')
                      ->setActionName('view');
        $this->redirector->gotoUrl('/bar/baz');
        $test = $this->redirector->getRedirectUrl();

        $this->assertNotContains('https://', $test);
        $this->assertEquals('http://localhost/bar/baz', $test);
    }
}

/**
 * Test controller for use with redirector tests
 */
class TestController extends Action
{
}
