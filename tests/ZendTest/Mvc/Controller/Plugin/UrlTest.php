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
use Zend\Mvc\Controller\Plugin\Url as UrlPlugin;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\Literal as LiteralRoute;
use Zend\Mvc\Router\SimpleRouteStack;
use ZendTest\Mvc\Controller\TestAsset\SampleController;

class UrlTest extends TestCase
{
    public function setUp()
    {
        $router = new SimpleRouteStack;
        $router->addRoute('home', LiteralRoute::factory(array(
            'route'    => '/',
            'defaults' => array(
                'controller' => 'ZendTest\Mvc\Controller\TestAsset\SampleController',
            ),
        )));

        $event = new MvcEvent();
        $event->setRouter($router);

        $this->controller = new SampleController();
        $this->controller->setEvent($event);

        $this->plugin = $this->controller->plugin('url');
    }

    public function testPluginCanGenerateUrlWhenProperlyConfigured()
    {
        $url = $this->plugin->fromRoute('home');
        $this->assertEquals('/', $url);
    }

    public function testPluginWithoutControllerRaisesDomainException()
    {
        $plugin = new UrlPlugin();
        $this->setExpectedException('Zend\Mvc\Exception\DomainException', 'requires a controller');
        $plugin->fromRoute('home');
    }

    public function testPluginWithoutControllerEventRaisesDomainException()
    {
        $controller = new SampleController();
        $plugin     = $controller->plugin('url');
        $this->setExpectedException('Zend\Mvc\Exception\DomainException', 'event compose a router');
        $plugin->fromRoute('home');
    }

    public function testPluginWithoutRouterInEventRaisesDomainException()
    {
        $controller = new SampleController();
        $event      = new MvcEvent();
        $controller->setEvent($event);
        $plugin = $controller->plugin('url');
        $this->setExpectedException('Zend\Mvc\Exception\DomainException', 'event compose a router');
        $plugin->fromRoute('home');
    }
}
