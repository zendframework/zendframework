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
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\View\Helper;

use Zend\Controller,
    Zend\Controller\Request,
    Zend\Controller\Response,
    Zend\View\Helper;

/**
 * Test class for Zend_View_Helper_Action.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class ActionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->_origServer = $_SERVER;
        $_SERVER = array(
            'SCRIPT_FILENAME' => __FILE__,
            'PHP_SELF'        => __FILE__,
        );

        $front = Controller\Front::getInstance();
        $front->resetInstance();

        $this->request  = new Request\Http('http://framework.zend.com/action-foo');
        $this->response = new Response\Http();
        $this->response->headersSentThrowsException = false;
        $front->setRequest($this->request)
              ->setResponse($this->response)
              ->addModuleDirectory(__DIR__ . '/_files/modules');

        $this->view   = new \Zend\View\PhpRenderer();
        $this->helper = new Helper\Action();
        $this->helper->setView($this->view);
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->request, $this->response, $this->helper);
        $_SERVER = $this->_origServer;
    }

    /**
     * @return void
     */
    public function testInitialStateHasClonedObjects()
    {
        $this->assertNotSame($this->request, $this->helper->request);
        $this->assertNotSame($this->response, $this->helper->response);

        $dispatcher = Controller\Front::getInstance()->getDispatcher();
        $this->assertNotSame($dispatcher, $this->helper->dispatcher);
    }

    /**
     * @return void
     */
    public function testInitialStateHasDefaultModuleName()
    {
        $dispatcher = Controller\Front::getInstance()->getDispatcher();
        $module     = $dispatcher->getDefaultModule();
        $this->assertEquals($module, $this->helper->defaultModule);

        $dispatcher->setDefaultModule('foo');
        $helper = new Helper\Action();
        $this->assertEquals('foo', $helper->defaultModule);
    }

    /**
     * @return void
     */
    public function testResetObjectsClearsRequestVars()
    {
        $this->helper->request->setParam('foo', 'action-bar');
        $this->helper->resetObjects();
        $this->assertNull($this->helper->request->getParam('foo'));
    }

    /**
     * @return void
     */
    public function testResetObjectsClearsResponseBody()
    {
        $this->helper->response->setBody('foobarbaz');
        $this->helper->resetObjects();
        $body = $this->helper->response->getBody();
        $this->assertTrue(empty($body));
    }

    /**
     * @return void
     */
    public function testResetObjectsClearsResponseHeaders()
    {
        $this->helper->response->setHeader('X-Foo', 'Bar')
                               ->setRawHeader('HTTP/1.1');
        $this->helper->resetObjects();
        $headers    = $this->helper->response->getHeaders();
        $rawHeaders = $this->helper->response->getRawHeaders();
        $this->assertTrue(empty($headers));
        $this->assertTrue(empty($rawHeaders));
    }

    public function testActionReturnsContentFromDefaultModule()
    {
        $value = $this->helper->__invoke('bar', 'action-foo');
        $this->assertContains('In default module, FooController::barAction()', $value);
    }

    public function testActionReturnsContentFromSpecifiedModule()
    {
        $value = $this->helper->__invoke('bar', 'foo', 'foo');
        $this->assertContains('In foo module, Foo_FooController::barAction()', $value);
    }

    /**
     * @return void
     */
    public function testActionReturnsContentReflectingPassedParams()
    {
        $value = $this->helper->__invoke('baz', 'action-foo', null, array('bat' => 'This is my message'));
        $this->assertNotContains('BOGUS', $value, var_export($this->helper->request->getUserParams(), 1));
        $this->assertContains('This is my message', $value);
    }

    /**
     * @return void
     */
    public function testActionReturnsEmptyStringWhenForwardDetected()
    {
        $value = $this->helper->__invoke('forward', 'action-foo');
        $this->assertEquals('', $value);
    }

    /**
     * @return void
     */
    public function testActionReturnsEmptyStringWhenRedirectDetected()
    {
        $value = $this->helper->__invoke('redirect', 'action-foo');
        $this->assertEquals('', $value);
    }

    /**
     * @return void
     */
    public function testConstructorThrowsExceptionWithNoControllerDirsInFrontController()
    {
        Controller\Front::getInstance()->resetInstance();
        try {
            $helper = new Helper\Action();
            $this->fail('Empty front controller should cause action helper to throw exception');
        } catch (\Exception $e) {
        }
    }

    /**
     * @return void
     */
    public function testConstructorThrowsExceptionWithNoRequestInFrontController()
    {
        $front = Controller\Front::getInstance();
        $front->resetInstance();

        $response = new Response\Http();
        $response->headersSentThrowsException = false;
        $front->setResponse($response)
              ->addModuleDirectory(__DIR__ . '/_files/modules');
        try {
            $helper = new Helper\Action();
            $this->fail('No request in front controller should cause action helper to throw exception');
        } catch (\Exception $e) {
        }
    }

    /**
     * @return void
     */
    public function testConstructorThrowsExceptionWithNoResponseInFrontController()
    {
        $front = Controller\Front::getInstance();
        $front->resetInstance();

        $request = new Request\Http('http://framework.zend.com/foo');
        $front->setRequest($this->request)
              ->addModuleDirectory(__DIR__ . '/_files/modules');
        try {
            $helper = new Helper\Action();
            $this->fail('No response in front controller should cause action helper to throw exception');
        } catch (\Exception $e) {
        }
    }

    public function testViewObjectRemainsUnchangedAfterAction()
    {
        $value = $this->helper->__invoke('bar', 'foo', 'foo');
        $this->assertContains('In foo module, Foo_FooController::barAction()', $value);
        $this->assertNull($this->view->vars()->bar);
    }

    public function testNestingActionsDoesNotBreakPlaceholderHelpers()
    {
        $html = $this->helper->__invoke('nest', 'foo', 'foo');
        $title = $this->view->plugin('headTitle')->toString();
        $this->assertContains(' - ', $title, $title);
        $this->assertContains('Foo Nest', $title);
        $this->assertContains('Nested Stuff', $title);
    }

    /**
     * @issue ZF-2716
     */
    public function testActionWithPartialsUseOfViewRendererReturnsToOriginatingViewState()
    {
        $partial = new \Zend\View\Helper\Partial();
        $this->view->resolver()->addPath(__DIR__ . '/_files/modules/application/views/scripts/');
        $partial->setView($this->view);

        $front  = Controller\Front::getInstance();
        $broker = $front->getHelperBroker();
        $broker->load('viewRenderer')->view = $this->view;

        $partial->__invoke('partialActionCall.phtml');

        $this->assertSame($this->view, $broker->load('viewRenderer')->view);

    }

    /**
     * Future ViewRenderer State issues should be included in this test.
     *
     * @group ZF-2846
     */
    public function testActionReturnsViewRendererToOriginalState()
    {
        /* Setup the VR as if we were inside an action controller */
        $viewRenderer = new \Zend\Controller\Action\Helper\ViewRenderer();
        $viewRenderer->init();
        $front  = Controller\Front::getInstance();
        $broker = $front->getHelperBroker();
        $broker->register('viewRenderer', $viewRenderer);

        // make sure noRender is false
        $this->assertFalse($viewRenderer->getNoRender());

        $value = $this->helper->__invoke('bar', 'action-foo');

        $viewRendererPostAction = $broker->load('viewRenderer');

        // ViewRenderer noRender should still be false
        $this->assertFalse($viewRendererPostAction->getNoRender());
        $this->assertSame($viewRenderer, $viewRendererPostAction);
    }

    /**
     * Multiple call state issue
     *
     *
     * @group ZF-3456
     */
    public function testActionCalledWithinActionResetsResponseState()
    {
        $value = $this->helper->__invoke('bar-one', 'baz', 'foo');
        $this->assertRegexp('/Baz-Three-View-Script\s+Baz-Two-View-Script\s+Baz-One-View-Script/s', $value);
    }
}
