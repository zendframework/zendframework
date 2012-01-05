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
 * @package    Zend_Layout
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Layout;

use Zend\Controller,
    Zend\Layout,
    Zend\Layout\Controller\Plugin,
    Zend\Controller\Request,
    Zend\Controller\Response;

/**
 * Test class for Zend_Layout_Controller_Plugin_Layout
 *
 * @category   Zend
 * @package    Zend_Layout
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Layout
 */
class PluginTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $front = Controller\Front::getInstance();
        $front->resetInstance();

        \Zend\Layout\Layout::resetMvcInstance();

        $broker = $front->getHelperBroker();
        if ($broker->isLoaded('Layout')) {
            $broker->unregister('Layout');
        }
        if ($broker->isLoaded('viewRenderer')) {
            $broker->unregister('viewRenderer');
        }
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        Layout\Layout::resetMvcInstance();
    }

    public function testConstructorWithLayoutObject()
    {
        $layout = new Layout\Layout(array('mvcEnabled' => false));
        $plugin = new Plugin\Layout($layout);
        $this->assertSame($layout, $plugin->getLayout());
    }

    public function testGetLayoutReturnsNullWithNoLayoutPresent()
    {
        $plugin = new Plugin\Layout();
        $this->assertNull($plugin->getLayout());
    }

    public function testLayoutAccessorsWork()
    {
        $plugin = new Plugin\Layout();
        $this->assertNull($plugin->getLayout());

        $layout = new Layout\Layout(array('mvcEnabled' => false));
        $plugin->setlayout($layout);
        $this->assertSame($layout, $plugin->getLayout());
    }

    public function testGetLayoutReturnsLayoutObjectWhenPulledFromPluginBroker()
    {
        $layout = Layout\Layout::startMvc();
        $front  = Controller\Front::getInstance();
        $this->assertTrue($front->hasPlugin('Zend\Layout\Controller\Plugin\Layout'));
        $plugin = $front->getPlugin('Zend\Layout\Controller\Plugin\Layout');
        $this->assertSame($layout, $plugin->getLayout());
    }

    public function testPostDispatchRendersLayout()
    {
        $front    = Controller\Front::getInstance();
        $request  = new Request\Simple();
        $response = new Response\Cli();

        $request->setDispatched(true);
        $response->setBody('Application content');
        $front->setRequest($request)
              ->setResponse($response);

        $layout = Layout\Layout::startMvc();
        $layout->setLayoutPath(__DIR__ . '/_files/layouts')
               ->setLayout('plugin.phtml')
               ->disableInflector();

        $helper = $front->getHelperBroker()->load('layout');
        $plugin = $front->getPlugin('Zend\Layout\Controller\Plugin\Layout');
        $plugin->setResponse($response);

        $helper->postDispatch();
        $plugin->postDispatch($request);

        $body = $response->getBody();
        $this->assertContains('Application content', $body, $body);
        $this->assertContains('Site Layout', $body, $body);
    }

    public function testPostDispatchDoesNotRenderLayoutWhenForwardDetected()
    {
        $front    = Controller\Front::getInstance();
        $request  = new Request\Simple();
        $response = new Response\Cli();

        $request->setDispatched(false);
        $response->setBody('Application content');
        $front->setRequest($request)
              ->setResponse($response);

        $layout = Layout\Layout::startMvc();
        $layout->setLayoutPath(__DIR__ . '/_files/layouts')
               ->setLayout('plugin.phtml')
               ->disableInflector();

        $plugin = $front->getPlugin('Zend\Layout\Controller\Plugin\Layout');
        $plugin->setResponse($response);
        $plugin->postDispatch($request);

        $body = $response->getBody();
        $this->assertContains('Application content', $body);
        $this->assertNotContains('Site Layout', $body);
    }

    public function testPostDispatchDoesNotRenderLayoutWhenLayoutDisabled()
    {
        $front    = Controller\Front::getInstance();
        $request  = new Request\Simple();
        $response = new Response\Cli();

        $request->setDispatched(true);
        $response->setBody('Application content');
        $front->setRequest($request)
              ->setResponse($response);

        $layout = Layout\Layout::startMvc();
        $layout->setLayoutPath(__DIR__ . '/_files/layouts')
               ->setLayout('plugin.phtml')
               ->disableInflector()
               ->disableLayout();

        $plugin = $front->getPlugin('Zend\Layout\Controller\Plugin\Layout');
        $plugin->setResponse($response);
        $plugin->postDispatch($request);

        $body = $response->getBody();
        $this->assertContains('Application content', $body);
        $this->assertNotContains('Site Layout', $body);
    }

    /**
     * @group ZF-8041
     */
    public function testPostDispatchDoesNotRenderLayoutWhenResponseRedirected()
    {
        $front    = Controller\Front::getInstance();
        $request  = new Request\Simple();
        $response = new Response\Cli();

        $request->setDispatched(true);
        $response->setHttpResponseCode(302);
        $response->setBody('Application content');
        $front->setRequest($request)
              ->setResponse($response);

        $layout = Layout\Layout::startMvc();
        $layout->setLayoutPath(__DIR__ . '/_files/layouts')
               ->setLayout('plugin.phtml')
               ->setMvcSuccessfulActionOnly(false)
               ->disableInflector();

        $plugin = $front->getPlugin('Zend\Layout\Controller\Plugin\Layout');
        $plugin->setResponse($response);
        $plugin->postDispatch($request);

        $body = $response->getBody();
        $this->assertContains('Application content', $body);
        $this->assertNotContains('Site Layout', $body);
    }
}

