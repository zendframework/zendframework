<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZendTest\Mvc\Controller\Plugin;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Http\Response;
use Zend\Mvc\Controller\Plugin\Redirect as RedirectPlugin;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\Literal as LiteralRoute;
use Zend\Mvc\Router\SimpleRouteStack;
use ZendTest\Mvc\Controller\TestAsset\SampleController;

class RedirectTest extends TestCase
{
    public function setUp()
    {
        $this->response = new Response();

        $router = new SimpleRouteStack;
        $router->addRoute('home', LiteralRoute::factory(array(
            'route'    => '/',
            'defaults' => array(
                'controller' => 'ZendTest\Mvc\Controller\TestAsset\SampleController',
            ),
        )));

        $event = new MvcEvent();
        $event->setRouter($router);
        $event->setResponse($this->response);

        $this->controller = new SampleController();
        $this->controller->setEvent($event);

        $this->plugin = $this->controller->plugin('redirect');
    }

    public function testPluginCanRedirectToRouteWhenProperlyConfigured()
    {
        $response = $this->plugin->toRoute('home');
        $this->assertTrue($response->isRedirect());
        $headers = $response->getHeaders();
        $location = $headers->get('Location');
        $this->assertEquals('/', $location->getFieldValue());
    }

    public function testPluginCanRedirectToUrlWhenProperlyConfigured()
    {
        $response = $this->plugin->toUrl('/foo');
        $this->assertTrue($response->isRedirect());
        $headers = $response->getHeaders();
        $location = $headers->get('Location');
        $this->assertEquals('/foo', $location->getFieldValue());
    }

    public function testPluginWithoutControllerRaisesDomainException()
    {
        $plugin = new RedirectPlugin();
        $this->setExpectedException('Zend\Mvc\Exception\DomainException', 'requires a controller');
        $plugin->toRoute('home');
    }

    public function testPluginWithoutControllerEventRaisesDomainException()
    {
        $controller = new SampleController();
        $plugin     = $controller->plugin('redirect');
        $this->setExpectedException('Zend\Mvc\Exception\DomainException', 'event compose a response');
        $plugin->toRoute('home');
    }

    public function testPluginWithoutResponseInEventRaisesDomainException()
    {
        $controller = new SampleController();
        $event      = new MvcEvent();
        $controller->setEvent($event);
        $plugin = $controller->plugin('redirect');
        $this->setExpectedException('Zend\Mvc\Exception\DomainException', 'event compose a response');
        $plugin->toRoute('home');
    }

    public function testRedirectToRouteWithoutRouterInEventRaisesDomainException()
    {
        $controller = new SampleController();
        $event      = new MvcEvent();
        $event->setResponse($this->response);
        $controller->setEvent($event);
        $plugin = $controller->plugin('redirect');
        $this->setExpectedException('Zend\Mvc\Exception\DomainException', 'event compose a router');
        $plugin->toRoute('home');
    }
}
