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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Action
 * @group      Zend_Controller_Action_Helper
 */
class HelperBrokerTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->front = FrontController::getInstance();
        $this->front->resetInstance();
        $this->front->setParam('noViewRenderer', true)
                    ->setParam('noErrorHandler', true)
                    ->throwExceptions(true);
        HelperBroker::resetHelpers();

        $viewRenderer = HelperBroker::getStaticHelper('viewRenderer');
        $viewRenderer->setActionController();
    }

    public function testLoadingAndReturningHelper()
    {
        $this->front->setControllerDirectory(dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files');
        $request = new Request('http://framework.zend.com/helper-broker/test-get-redirector/');
        $this->front->setResponse(new Response());

        $this->front->returnResponse(true);
        $response = $this->front->dispatch($request);
        $this->assertEquals('Zend\Controller\Action\Helper\Redirector', $response->getBody());
    }

    public function testLoadingAndReturningHelperStatically()
    {
        $helper = new \TestHelper();
        HelperBroker::addHelper($helper);
        $received = HelperBroker::getExistingHelper('testHelper');
        $this->assertSame($received, $helper);
    }

    public function testGetExistingHelperThrowsExceptionWithUnregisteredHelper()
    {
        try {
            $received = HelperBroker::getExistingHelper('testHelper');
            $this->fail('Retrieving unregistered helpers should throw an exception');
        } catch (\Exception $e) {
            // success
        }
    }

    public function testLoadingHelperOnlyInitializesOnce()
    {
        $this->front->setControllerDirectory(dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files');
        $request = new Request();
        $request->setModuleName('default')
                ->setControllerName('zend_controller_action_helper-broker')
                ->setActionName('index');
        $response = new Response();
        $this->front->setResponse($response);

        $helper = new \TestHelper();
        HelperBroker::addHelper($helper);

        $controller = new \HelperBrokerController($request, $response, array());
        $controller->test();
        $received = $controller->getHelper('testHelper');
        $this->assertSame($helper, $received);
        $this->assertEquals(1, $helper->count);
    }

    public function testLoadingAndCheckingHelpersStatically()
    {
        $helper = new Helper\Redirector();
        HelperBroker::addHelper($helper);

        $this->assertTrue(HelperBroker::hasHelper('redirector'));
    }

    public function testLoadingAndRemovingHelpersStatically()
    {
        $helper = new Helper\Redirector();
        HelperBroker::addHelper($helper);

        $this->assertTrue(HelperBroker::hasHelper('redirector'));
        HelperBroker::removeHelper('redirector');
        $this->assertFalse(HelperBroker::hasHelper('redirector'));
    }
     public function testReturningHelper()
    {
        $this->front->setControllerDirectory(dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files');
        $request = new Request('http://framework.zend.com/helper-broker/test-get-redirector/');
        $this->front->setResponse(new Response());

        $this->front->returnResponse(true);
        $response = $this->front->dispatch($request);
        $this->assertEquals('Zend\Controller\Action\Helper\Redirector', $response->getBody());
    }

    public function testReturningHelperViaMagicGet()
    {
        $this->front->setControllerDirectory(dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files');
        $request = new Request('http://framework.zend.com/helper-broker/test-helper-via-magic-get/');
        $this->front->setResponse(new Response());

        $this->front->returnResponse(true);
        $response = $this->front->dispatch($request);
        $this->assertEquals('Zend\Controller\Action\Helper\Redirector', $response->getBody());
    }

    public function testReturningHelperViaMagicCall()
    {
        $this->front->setControllerDirectory(dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files');
        $request = new Request('http://framework.zend.com/helper-broker/test-helper-via-magic-call/');
        $this->front->setResponse(new Response());

        $this->front->returnResponse(true);

        require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files/Helpers/TestHelper.php';
        HelperBroker::addHelper(new \MyApp\TestHelper());

        $response = $this->front->dispatch($request);
        $this->assertEquals('running direct call', $response->getBody());
    }

    public function testNonExistentHelper()
    {
        $this->front->setControllerDirectory(dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files');
        $request = new Request('http://framework.zend.com/helper-broker/test-bad-helper/');
        $this->front->setResponse(new Response());

        $this->front->returnResponse(true);
        $response = $this->front->dispatch($request);
        $this->assertContains('not found', $response->getBody());
    }

    public function testCustomHelperRegistered()
    {
        $this->front->setControllerDirectory(dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files');
        $request = new Request('http://framework.zend.com/helper-broker/test-custom-helper/');
        $this->front->setResponse(new Response());

        $this->front->returnResponse(true);

        require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files/Helpers/TestHelper.php';
        HelperBroker::addHelper(new \MyApp\TestHelper());

        $response = $this->front->dispatch($request);
        $this->assertEquals('MyApp\TestHelper', $response->getBody());
    }

    public function testCustomHelperFromPath()
    {
        $this->front->setControllerDirectory(dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files');
        $request = new Request('http://framework.zend.com/helper-broker/test-custom-helper/');
        $this->front->setResponse(new Response());

        $this->front->returnResponse(true);

        HelperBroker::addPath(
            dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'Helpers',
            'MyApp'
            );

        $response = $this->front->dispatch($request);
        $this->assertEquals('MyApp\TestHelper', $response->getBody());
    }

    public function testGetExistingHelpers()
    {
        HelperBroker::addHelper(new Helper\Redirector());
        // already included in setup, techinically we shouldnt be able to do this, but until 2.0 - its allowed
        HelperBroker::addHelper(new Helper\ViewRenderer()); // @todo in future this should throw an exception

        $helpers = HelperBroker::getExistingHelpers();
        $this->assertTrue(is_array($helpers));
        $this->assertEquals(2, count($helpers));
        $this->assertContains('ViewRenderer', array_keys($helpers));
        $this->assertContains('Redirector', array_keys($helpers));
    }

    public function testGetHelperStatically()
    {
        $helper = HelperBroker::getStaticHelper('viewRenderer');
        $this->assertTrue($helper instanceof Helper\ViewRenderer);

        $helpers = HelperBroker::getExistingHelpers();
        $this->assertTrue(is_array($helpers));
        $this->assertEquals(1, count($helpers));
    }

    public function testHelperPullsResponseFromRegisteredActionController()
    {
        $helper = HelperBroker::getStaticHelper('viewRenderer');

        $aRequest   = new Request();
        $aRequest->setModuleName('default')
                 ->setControllerName('zend_controller_action_helper-broker')
                 ->setActionName('index');
        $aResponse  = new Response();
        $controller = new \HelperBrokerController($aRequest, $aResponse, array());

        $fRequest   = new Request();
        $fRequest->setModuleName('foo')
                 ->setControllerName('foo-bar')
                 ->setActionName('baz');
        $fResponse  = new Response();
        $this->front->setRequest($fRequest)
                    ->setResponse($fResponse);

        $helper->setActionController($controller);

        $hRequest  = $helper->getRequest();
        $this->assertSame($hRequest, $aRequest);
        $this->assertNotSame($hRequest, $fRequest);
        $hResponse = $helper->getResponse();
        $this->assertSame($hResponse, $aResponse);
        $this->assertNotSame($hResponse, $fResponse);
    }

    public function testHelperPullsResponseFromFrontControllerWithNoRegisteredActionController()
    {
        $helper = HelperBroker::getStaticHelper('viewRenderer');
        $this->assertNull($helper->getActionController());

        $aRequest   = new Request();
        $aRequest->setModuleName('default')
                 ->setControllerName('zend_controller_action_helper-broker')
                 ->setActionName('index');
        $aResponse  = new Response();

        $fRequest   = new Request();
        $fRequest->setModuleName('foo')
                 ->setControllerName('foo-bar')
                 ->setActionName('baz');
        $fResponse  = new Response();
        $this->front->setRequest($fRequest)
                    ->setResponse($fResponse);

        $hRequest  = $helper->getRequest();
        $this->assertNotSame($hRequest, $aRequest);
        $this->assertSame($hRequest, $fRequest);
        $hResponse = $helper->getResponse();
        $this->assertNotSame($hResponse, $aResponse);
        $this->assertSame($hResponse, $fResponse);
    }

    public function testHelperPathStackIsLifo()
    {
        HelperBroker::addPath(
            dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'Helpers',
            'MyApp'
            );

        $urlHelper = HelperBroker::getStaticHelper('url');
        $this->assertTrue($urlHelper instanceof \MyApp\Url);
    }

    /**
     * @group ZF-4704
     */
    public function testPluginLoaderShouldHaveDefaultPrefixPath()
    {
        $loader = HelperBroker::getPluginLoader();
        $paths  = $loader->getPaths('Zend\Controller\Action\Helper');
        $this->assertFalse(empty($paths));
    }

    /**
     * @group ZF-4704
     */
    public function testBrokerShouldAcceptCustomPluginLoaderInstance()
    {
        $loader = HelperBroker::getPluginLoader();
        $custom = new PluginLoader();
        HelperBroker::setPluginLoader($custom);
        $test   = HelperBroker::getPluginLoader();
        $this->assertNotSame($loader, $test);
        $this->assertSame($custom, $test);
    }
}

