<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mvc\Service;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Mvc\Router\RoutePluginManager;
use Zend\Mvc\Service\RouterFactory;
use Zend\ServiceManager\ServiceManager;

class RouterFactoryTest extends TestCase
{
    public function setUp()
    {
        $this->services = new ServiceManager();
        $this->services->setService('RoutePluginManager', new RoutePluginManager());
        $this->factory  = new RouterFactory();
    }

    public function testFactoryCanCreateRouterBasedOnConfiguredName()
    {
        $this->services->setService('Config', array(
            'router' => array(
                'router_class' => 'ZendTest\Mvc\Service\TestAsset\Router',
            ),
            'console' => array(
                'router' => array(
                    'router_class' => 'ZendTest\Mvc\Service\TestAsset\Router',
                ),
            ),
        ));

        $router = $this->factory->createService($this->services, 'router', 'Router');
        $this->assertInstanceOf('ZendTest\Mvc\Service\TestAsset\Router', $router);
    }

    public function testFactoryCanCreateRouterWhenOnlyHttpRouterConfigPresent()
    {
        $this->services->setService('Config', array(
            'router' => array(
                'router_class' => 'ZendTest\Mvc\Service\TestAsset\Router',
            ),
        ));

        $router = $this->factory->createService($this->services, 'router', 'Router');
        $this->assertInstanceOf('Zend\Mvc\Router\Console\SimpleRouteStack', $router);
    }
}
