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
use Zend\Controller\Action\HelperBroker;
use Zend\Controller\Request;
use Zend\Controller\Response;
use Zend\Controller\Action\Helper;

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
        $this->front = \Zend\Controller\Front::getInstance();
        $this->front->resetInstance();
        $this->front->setParam('noViewRenderer', true)
                    ->setParam('noErrorHandler', true)
                    ->throwExceptions(true);
        HelperBroker\HelperBroker::resetHelpers();

        $viewRenderer = HelperBroker\HelperBroker::getStaticHelper('viewRenderer');
        $viewRenderer->setActionController();
    }

    public function testLoadingAndReturningHelper()
    {
        $this->front->setControllerDirectory(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files');
        $request = new Request\HTTP('http://framework.zend.com/helper-broker/test-get-redirector/');
        $this->front->setResponse(new Response\Cli());

        $this->front->returnResponse(true);
        $response = $this->front->dispatch($request);
        $this->assertEquals('Zend\Controller\Action\Helper\Redirector', $response->getBody());
    }

    public function testLoadingAndReturningHelperStatically()
    {
        $helper = new TestHelper();
        HelperBroker\HelperBroker::addHelper($helper);
        $received = HelperBroker\HelperBroker::getExistingHelper('testHelper');
        $this->assertSame($received, $helper);
    }

    public function testGetExistingHelperThrowsExceptionWithUnregisteredHelper()
    {
        try {
            $received = HelperBroker\HelperBroker::getExistingHelper('testHelper');
            $this->fail('Retrieving unregistered helpers should throw an exception');
        } catch (\Exception $e) {
            // success
        }
    }

    public function testLoadingHelperOnlyInitializesOnce()
    {
        $this->front->setControllerDirectory(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files');
        $request = new Request\HTTP();
        $request->setModuleName('default')
                ->setControllerName('zend_controller_action_helper-broker')
                ->setActionName('index');
        $response = new Response\Cli();
        $this->front->setResponse($response);

        $helper = new TestHelper();
        HelperBroker\HelperBroker::addHelper($helper);

        $controller = new HelperBrokerController($request, $response, array());
        $controller->test();
        $received = $controller->getHelper('testHelper');
        $this->assertSame($helper, $received);
        $this->assertEquals(1, $helper->count);
    }

    public function testLoadingAndCheckingHelpersStatically()
    {
        $helper = new Helper\Redirector();
        HelperBroker\HelperBroker::addHelper($helper);

        $this->assertTrue(HelperBroker\HelperBroker::hasHelper('redirector'));
    }

    public function testLoadingAndRemovingHelpersStatically()
    {
        $helper = new Helper\Redirector();
        HelperBroker\HelperBroker::addHelper($helper);

        $this->assertTrue(HelperBroker\HelperBroker::hasHelper('redirector'));
        HelperBroker\HelperBroker::removeHelper('redirector');
        $this->assertFalse(HelperBroker\HelperBroker::hasHelper('redirector'));
    }
     public function testReturningHelper()
    {
        $this->front->setControllerDirectory(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files');
        $request = new Request\HTTP('http://framework.zend.com/helper-broker/test-get-redirector/');
        $this->front->setResponse(new Response\Cli());

        $this->front->returnResponse(true);
        $response = $this->front->dispatch($request);
        $this->assertEquals('Zend\Controller\Action\Helper\Redirector', $response->getBody());
    }

    public function testReturningHelperViaMagicGet()
    {
        $this->front->setControllerDirectory(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files');
        $request = new Request\HTTP('http://framework.zend.com/helper-broker/test-helper-via-magic-get/');
        $this->front->setResponse(new Response\Cli());

        $this->front->returnResponse(true);
        $response = $this->front->dispatch($request);
        $this->assertEquals('Zend\Controller\Action\Helper\Redirector', $response->getBody());
    }

    public function testReturningHelperViaMagicCall()
    {
        $this->front->setControllerDirectory(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files');
        $request = new Request\HTTP('http://framework.zend.com/helper-broker/test-helper-via-magic-call/');
        $this->front->setResponse(new Response\Cli());

        $this->front->returnResponse(true);

        require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files/Helpers/TestHelper.php';
        HelperBroker\HelperBroker::addHelper(new \MyApp\TestHelper());

        $response = $this->front->dispatch($request);
        $this->assertEquals('running direct call', $response->getBody());
    }

    public function testNonExistentHelper()
    {
        $this->front->setControllerDirectory(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files');
        $request = new Request\HTTP('http://framework.zend.com/helper-broker/test-bad-helper/');
        $this->front->setResponse(new Response\Cli());

        $this->front->returnResponse(true);
        $response = $this->front->dispatch($request);
        $this->assertContains('not found', $response->getBody());
    }

    public function testCustomHelperRegistered()
    {
        $this->front->setControllerDirectory(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files');
        $request = new Request\HTTP('http://framework.zend.com/helper-broker/test-custom-helper/');
        $this->front->setResponse(new Response\Cli());

        $this->front->returnResponse(true);

        require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files/Helpers/TestHelper.php';
        HelperBroker\HelperBroker::addHelper(new \MyApp\TestHelper());

        $response = $this->front->dispatch($request);
        $this->assertEquals('MyApp\TestHelper', $response->getBody());
    }

    public function testCustomHelperFromPath()
    {
        $this->front->setControllerDirectory(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files');
        $request = new Request\HTTP('http://framework.zend.com/helper-broker/test-custom-helper/');
        $this->front->setResponse(new Response\Cli());

        $this->front->returnResponse(true);

        HelperBroker\HelperBroker::addPath(
            dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'Helpers',
            'MyApp'
            );

        $response = $this->front->dispatch($request);
        $this->assertEquals('MyApp\TestHelper', $response->getBody());
    }

    public function testGetExistingHelpers()
    {
        HelperBroker\HelperBroker::addHelper(new Helper\Redirector());
        // already included in setup, techinically we shouldnt be able to do this, but until 2.0 - its allowed
        HelperBroker\HelperBroker::addHelper(new Helper\ViewRenderer()); // @todo in future this should throw an exception

        $helpers = HelperBroker\HelperBroker::getExistingHelpers();
        $this->assertTrue(is_array($helpers));
        $this->assertEquals(2, count($helpers));
        $this->assertContains('ViewRenderer', array_keys($helpers));
        $this->assertContains('Redirector', array_keys($helpers));
    }

    public function testGetHelperStatically()
    {
        $helper = HelperBroker\HelperBroker::getStaticHelper('viewRenderer');
        $this->assertTrue($helper instanceof Helper\ViewRenderer);

        $helpers = HelperBroker\HelperBroker::getExistingHelpers();
        $this->assertTrue(is_array($helpers));
        $this->assertEquals(1, count($helpers));
    }

    public function testHelperPullsResponseFromRegisteredActionController()
    {
        $helper = HelperBroker\HelperBroker::getStaticHelper('viewRenderer');

        $aRequest   = new Request\HTTP();
        $aRequest->setModuleName('default')
                 ->setControllerName('zend_controller_action_helper-broker')
                 ->setActionName('index');
        $aResponse  = new Response\Cli();
        $controller = new HelperBrokerController($aRequest, $aResponse, array());

        $fRequest   = new Request\HTTP();
        $fRequest->setModuleName('foo')
                 ->setControllerName('foo-bar')
                 ->setActionName('baz');
        $fResponse  = new Response\Cli();
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
        $helper = HelperBroker\HelperBroker::getStaticHelper('viewRenderer');
        $this->assertNull($helper->getActionController());

        $aRequest   = new Request\HTTP();
        $aRequest->setModuleName('default')
                 ->setControllerName('zend_controller_action_helper-broker')
                 ->setActionName('index');
        $aResponse  = new Response\Cli();

        $fRequest   = new Request\HTTP();
        $fRequest->setModuleName('foo')
                 ->setControllerName('foo-bar')
                 ->setActionName('baz');
        $fResponse  = new Response\Cli();
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
        HelperBroker\HelperBroker::addPath(
            dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'Helpers',
            'MyApp'
            );

        $urlHelper = HelperBroker\HelperBroker::getStaticHelper('url');
        $this->assertTrue($urlHelper instanceof \MyApp\Url);
    }

    /**
     * @group ZF-4704
     */
    public function testPluginLoaderShouldHaveDefaultPrefixPath()
    {
        $loader = HelperBroker\HelperBroker::getPluginLoader();
        $paths  = $loader->getPaths('Zend\Controller\Action\Helper');
        $this->assertFalse(empty($paths));
    }

    /**
     * @group ZF-4704
     */
    public function testBrokerShouldAcceptCustomPluginLoaderInstance()
    {
        $loader = HelperBroker\HelperBroker::getPluginLoader();
        $custom = new \Zend\Loader\PluginLoader\PluginLoader();
        HelperBroker\HelperBroker::setPluginLoader($custom);
        $test   = HelperBroker\HelperBroker::getPluginLoader();
        $this->assertNotSame($loader, $test);
        $this->assertSame($custom, $test);
    }
}

class TestHelper extends Helper\AbstractHelper
{
    public $count = 0;

    public function init()
    {
        ++$this->count;
    }
}

class HelperBrokerController extends \Zend\Controller\Action\Action
{
    public $helper;

    public function init()
    {
        $this->helper = $this->_helper->getHelper('testHelper');
    }

    public function test()
    {
        $this->_helper->getHelper('testHelper');
    }
}
