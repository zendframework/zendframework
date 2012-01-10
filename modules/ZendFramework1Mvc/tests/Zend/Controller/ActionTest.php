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
    Zend\Controller\Front as FrontController,
    Zend\Controller\Request,
    Zend\Controller\Response,
    Zend\Controller\Action\Helper;

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Action
 */
class ActionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $front = FrontController::getInstance();
        $front->resetInstance();
        $front->setControllerDirectory('.', 'default');
        $this->broker = $front->getHelperBroker();

        $this->_controller = new TestController(
            new Request\Http(),
            new Response\Cli(),
            array(
                'foo' => 'bar',
                'bar' => 'baz'
            )
        );
        $this->_controller->setHelperBroker($this->broker);

        $redirector = $this->_controller->broker('redirector');
        $redirector->setExit(false);
    }

    public function tearDown()
    {
        unset($this->_controller);
    }

    public function testInit()
    {
        $this->_controller->init();
        $this->assertEquals('bar', $this->_controller->initArgs['foo']);
        $this->assertEquals('baz', $this->_controller->initArgs['bar']);
    }

    public function testPreRun()
    {
        $this->_controller->preDispatch();
        $this->assertNotContains('Prerun ran', $this->_controller->getResponse()->getBody());

        $this->_controller->getRequest()->setParam('prerun', true);
        $this->_controller->preDispatch();
        $this->assertContains('Prerun ran', $this->_controller->getResponse()->getBody());
    }

    public function testPostRun()
    {
        $this->_controller->postDispatch();
        $this->assertNotContains('Postrun ran', $this->_controller->getResponse()->getBody());

        $this->_controller->getRequest()->setParam('postrun', true);
        $this->_controller->postDispatch();
        $this->assertContains('Postrun ran', $this->_controller->getResponse()->getBody());
    }

    public function testGetRequest()
    {
        $this->assertTrue($this->_controller->getRequest() instanceof Request\AbstractRequest);
    }

    public function testGetResponse()
    {
        $this->assertTrue($this->_controller->getResponse() instanceof Response\AbstractResponse);
    }

    public function testGetInvokeArgs()
    {
        $expected = array('foo' => 'bar', 'bar' => 'baz');
        $this->assertSame($expected, $this->_controller->getInvokeArgs());
    }

    public function testGetInvokeArg()
    {
        $this->assertSame('bar', $this->_controller->getInvokeArg('foo'));
        $this->assertSame('baz', $this->_controller->getInvokeArg('bar'));
    }

    public function testForwardActionOnly()
    {
        $this->_controller->forward('forwarded');
        $this->assertEquals('forwarded', $this->_controller->getRequest()->getActionName());
        $this->assertFalse($this->_controller->getRequest()->isDispatched());
    }

    public function testForwardActionKeepsController()
    {
        $request = $this->_controller->getRequest();
        $request->setControllerName('foo')
                ->setActionName('bar');
        $this->_controller->forward('forwarded');
        $this->assertEquals('forwarded', $request->getActionName());
        $this->assertEquals('foo', $request->getControllerName());
        $this->assertFalse($request->isDispatched());
    }

    public function testForwardActionAndController()
    {
        $request = $this->_controller->getRequest();
        $request->setControllerName('foo')
                ->setActionName('bar');
        $this->_controller->forward('forwarded', 'bar');
        $this->assertEquals('forwarded', $request->getActionName());
        $this->assertEquals('bar', $request->getControllerName());
        $this->assertFalse($request->isDispatched());
    }

    public function testForwardActionControllerAndModule()
    {
        $request = $this->_controller->getRequest();
        $request->setControllerName('foo')
                ->setActionName('bar')
                ->setModuleName('admin');
        $this->_controller->forward('forwarded', 'bar');
        $this->assertEquals('forwarded', $request->getActionName());
        $this->assertEquals('bar', $request->getControllerName());
        $this->assertEquals('admin', $request->getModuleName());
        $this->assertFalse($request->isDispatched());
    }

    public function testForwardCanSetParams()
    {
        $request = $this->_controller->getRequest();
        $request->setParams(array('admin' => 'batman'));
        $this->_controller->forward('forwarded', null, null, array('foo' => 'bar'));
        $this->assertEquals('forwarded', $request->getActionName());
        $received = $request->getParams();
        $this->assertTrue(isset($received['foo']));
        $this->assertEquals('bar', $received['foo']);
        $this->assertFalse($request->isDispatched());
    }

    public function testRun()
    {
        $response = $this->_controller->run();
        $body     = $response->getBody();
        $this->assertContains('In the index action', $body, var_export($this->_controller->getRequest(), 1));
        $this->assertNotContains('Prerun ran', $body, $body);
    }

    public function testRun2()
    {
        $this->_controller->getRequest()->setActionName('bar');
        try {
            $response = $this->_controller->run();
            $this->fail('Should not be able to call bar as action');
        } catch (\Exception $e) {
            //success!
        }
    }

    public function testRun3()
    {
        $this->_controller->getRequest()->setActionName('foo');
        $response = $this->_controller->run();
        $this->assertContains('In the foo action', $response->getBody());
        $this->assertNotContains('Prerun ran', $this->_controller->getResponse()->getBody());
    }

    public function testHasParam()
    {
        $request = $this->_controller->getRequest();
        $request->setParam('foo', 'bar');
        $request->setParam('baz', 'bal');

        $this->assertTrue($this->_controller->hasParam('foo'));
        $this->assertTrue($this->_controller->hasParam('baz'));
    }

    public function testSetParam()
    {
        $this->_controller->setParam('foo', 'bar');
        $params = $this->_controller->getParams();
        $this->assertTrue(isset($params['foo']));
        $this->assertEquals('bar', $params['foo']);
    }
    
    /**
     * @group ZF-5163
     */
    public function testGetParamForZeroValues()
    {
        $this->_controller->setParam('foo', 'bar');
        $this->_controller->setParam('bar', 0);
        $this->_controller->setParam('baz', null);
        
        $this->assertEquals('bar', $this->_controller->getParam('foo', -1));
        $this->assertEquals(0, $this->_controller->getParam('bar', -1));
        $this->assertEquals(-1, $this->_controller->getParam('baz', -1));
    }

    public function testGetParams()
    {
        $this->_controller->setParam('foo', 'bar');
        $this->_controller->setParam('bar', 'baz');
        $this->_controller->setParam('boo', 'bah');

        $params = $this->_controller->getParams();
        $this->assertEquals('bar', $params['foo']);
        $this->assertEquals('baz', $params['bar']);
        $this->assertEquals('bah', $params['boo']);
    }

    public function testRedirect()
    {
        $response = $this->_controller->getResponse();
        $response->headersSentThrowsException = false;
        $this->_controller->redirect('/baz/foo');
        $this->_controller->redirect('/foo/bar');
        $headers = $response->getHeaders();
        $found   = 0;
        $url     = '';
        foreach ($headers as $header) {
            if ('Location' == $header['name']) {
                ++$found;
                $url = $header['value'];
                break;
            }
        }
        $this->assertEquals(1, $found);
        $this->assertContains('/foo/bar', $url);
    }

    public function testInitView()
    {
        Controller\Front::getInstance()->setControllerDirectory(__DIR__ . DIRECTORY_SEPARATOR . '_files');
        require_once __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'ViewController.php';
        $controller = new \ViewController(
            new Request\Http(),
            new Response\Cli()
        );
        $view = $controller->initView();
        $this->assertTrue($view instanceof \Zend\View\Renderer);
        $scriptPath = $view->resolver()->getPaths();

        $found = false;
        $expected = new \SplFileInfo(__DIR__ . '/views/scripts/');
        foreach ($scriptPath as $path) {
            if (rtrim($path, DIRECTORY_SEPARATOR) == $expected->getPathname()) {
                $found = true;
            }
        }
        $this->assertTrue($found);
    }

    public function testRender()
    {
        $request = new Request\Http();
        $request->setControllerName('view')
                ->setActionName('index');
        $response = new Response\Cli();
        Controller\Front::getInstance()->setControllerDirectory(__DIR__ . DIRECTORY_SEPARATOR . '_files');
        $controller = new \ViewController($request, $response);

        $controller->indexAction();
        $this->assertContains('In the index action view', $response->getBody());
    }

    public function testRenderByName()
    {
        $request = new Request\Http();
        $request->setControllerName('view')
                ->setActionName('test');
        $response = new Response\Cli();
        Controller\Front::getInstance()->setControllerDirectory(__DIR__ . DIRECTORY_SEPARATOR . '_files');
        $controller = new \ViewController($request, $response);

        $controller->testAction();
        $this->assertContains('In the index action view', $response->getBody());
    }

    public function testRenderOutsideControllerSubdir()
    {
        $request = new Request\Http();
        $request->setControllerName('view')
                ->setActionName('site');
        $response = new Response\Cli();
        Controller\Front::getInstance()->setControllerDirectory(__DIR__ . DIRECTORY_SEPARATOR . '_files');
        $controller = new \ViewController($request, $response);

        $controller->siteAction();
        $this->assertContains('In the sitewide view', $response->getBody());
    }

    public function testRenderNamedSegment()
    {
        $request = new Request\Http();
        $request->setControllerName('view')
                ->setActionName('name');
        $response = new Response\Cli();
        Controller\Front::getInstance()->setControllerDirectory(__DIR__ . DIRECTORY_SEPARATOR . '_files');
        $controller = new \ViewController($request, $response);

        $controller->nameAction();
        $this->assertContains('In the name view', $response->getBody('name'));
    }

    public function testRenderNormalizesScriptName()
    {
        $request = new Request\Http();
        $request->setControllerName('foo.bar')
                ->setActionName('baz_bat');
        $response = new Response\Cli();
        Controller\Front::getInstance()->setControllerDirectory(__DIR__ . DIRECTORY_SEPARATOR . '_files');
        require_once __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'FooBarController.php';
        $controller = new \FooBarController($request, $response);

        $controller->bazBatAction();
        $this->assertContains('Inside foo-bar/baz-bat.phtml', $response->getBody());
    }

    public function testGetViewScript()
    {
        $request = new Request\Http();
        $request->setControllerName('view')
                ->setActionName('test');
        $response = new Response\Cli();
        Controller\Front::getInstance()->setControllerDirectory(__DIR__ . DIRECTORY_SEPARATOR . '_files');
        $controller = new \ViewController($request, $response);

        $script = $controller->getViewScript();
        $this->assertContains('view' . DIRECTORY_SEPARATOR . 'test.phtml', $script);

        $script = $controller->getViewScript('foo');
        $this->assertContains('view' . DIRECTORY_SEPARATOR . 'foo.phtml', $script);
    }

    public function testGetViewScriptDoesNotOverwriteNoControllerFlagWhenNullPassed()
    {
        Controller\Front::getInstance()->setControllerDirectory(__DIR__ . DIRECTORY_SEPARATOR . '_files');
        $viewRenderer = $this->broker->load('viewRenderer');

        $request    = new Request\Http();
        $request->setControllerName('view')
                ->setActionName('test');
        $response   = new Response\Cli();
        $controller = new \ViewController($request, $response);
        $controller->setHelperBroker($this->broker);

        $this->assertSame($viewRenderer->getActionController(), $controller);
        $viewRenderer->setNoController(true);

        $this->assertTrue($viewRenderer->getNoController());

        $script = $controller->getViewScript();

        $this->assertTrue($viewRenderer->getNoController());
    }

    public function testRenderScript()
    {
        $request = new Request\Http();
        $request->setControllerName('view')
                ->setActionName('script');
        $response = new Response\Cli();
        Controller\Front::getInstance()->setControllerDirectory(__DIR__ . DIRECTORY_SEPARATOR . '_files');
        $controller = new \ViewController($request, $response);

        $controller->scriptAction();
        $this->assertContains('Inside custom/renderScript.php', $response->getBody());
    }

    public function testRenderScriptToNamedResponseSegment()
    {
        $request = new Request\Http();
        $request->setControllerName('view')
                ->setActionName('script-name');
        $response = new Response\Cli();
        Controller\Front::getInstance()->setControllerDirectory(__DIR__ . DIRECTORY_SEPARATOR . '_files');
        $controller = new \ViewController($request, $response);

        $controller->scriptNameAction();

        $this->assertContains('Inside custom/renderScript.php', $response->getBody('foo'));
    }

    public function testGetHelper()
    {
        $redirector = $this->_controller->broker('redirector');
        $this->assertTrue($redirector instanceof Helper\AbstractHelper);
        $this->assertTrue($redirector instanceof Helper\Redirector);
    }

    public function testViewInjectionUsingViewRenderer()
    {
        $this->broker->register('viewRenderer', new Helper\ViewRenderer());
        $request = new Request\Http();
        $request->setControllerName('view')
                ->setActionName('script');
        $response = new Response\Cli();
        Controller\Front::getInstance()->setControllerDirectory(__DIR__ . DIRECTORY_SEPARATOR . '_files');
        $controller = new \ViewController($request, $response);
        $controller->setHelperBroker($this->broker);
        $this->assertNotNull($controller->view);
    }

    public function testRenderUsingViewRenderer()
    {
        $this->broker->register('viewRenderer', new Helper\ViewRenderer());
        $request = new Request\Http();
        $request->setControllerName('view')
                ->setActionName('script');
        $response = new Response\Cli();
        Controller\Front::getInstance()->setControllerDirectory(__DIR__ . DIRECTORY_SEPARATOR . '_files');
        $controller = new \ViewController($request, $response);

        $controller->scriptAction();
        $this->assertContains('Inside custom/renderScript.php', $response->getBody());
    }

    public function testMissingActionExceptionsDifferFromMissingMethods()
    {
        try {
            $this->_controller->bogusAction();
            $this->fail('Invalid action should throw exception');
        } catch (Controller\Exception $e) {
            $this->assertRegexp('/^Action.*?(does not exist and was not trapped in __call\(\))$/', $e->getMessage());
            $this->assertContains('bogus', $e->getMessage());
            $this->assertNotContains('bogusAction', $e->getMessage());
            $this->assertEquals(404, $e->getCode());
        }

        try {
            $this->_controller->bogus();
            $this->fail('Invalid method should throw exception');
        } catch (Controller\Exception $e) {
            $this->assertRegexp('/^Method.*?(does not exist and was not trapped in __call\(\))$/', $e->getMessage());
            $this->assertContains('bogus', $e->getMessage());
            $this->assertEquals(500, $e->getCode());
        }
    }
}

class TestController extends \Zend\Controller\Action
{
    public $initArgs = array();

    public function init()
    {
        $this->initArgs['foo'] = $this->getInvokeArg('foo');
        $this->initArgs['bar'] = $this->getInvokeArg('bar');
    }

    public function preDispatch()
    {
        if (false !== ($param = $this->_getParam('prerun', false))) {
            $this->getResponse()->appendBody("Prerun ran\n");
        }
    }

    public function postDispatch()
    {
        if (false !== ($param = $this->_getParam('postrun', false))) {
            $this->getResponse()->appendBody("Postrun ran\n");
        }
    }

    public function noRouteAction()
    {
        return $this->indexAction();
    }

    public function indexAction()
    {
        $this->getResponse()->appendBody("In the index action\n");
    }

    public function fooAction()
    {
        $this->getResponse()->appendBody("In the foo action\n");
    }

    public function bar()
    {
        $this->getResponse()->setBody("Should never see this\n");
    }

    public function forward($action, $controller = null, $module = null, array $params = null)
    {
        $this->_forward($action, $controller, $module, $params);
    }

    public function hasParam($param)
    {
        return $this->_hasParam($param);
    }

    public function getParams()
    {
        return $this->_getAllParams();
    }

    public function setParam($key, $value)
    {
        $this->_setParam($key, $value);
        return $this;
    }
    
    public function getParam($key, $default)
    {
        return $this->_getParam($key, $default);
    }

    public function redirect($url, $code = 302, $prependBase = true)
    {
        $this->_redirect($url, array('code' => $code, 'prependBase' => $prependBase));
    }
}

