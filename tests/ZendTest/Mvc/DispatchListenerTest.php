<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZendTest\Mvc;

use ArrayObject;
use PHPUnit_Framework_TestCase as TestCase;
use stdClass;
use Zend\Config\Config;
use Zend\Http\Request;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\Uri\UriFactory;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage UnitTest
 */
class DispatchListenerTest extends TestCase
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var Application
     */
    protected $application;

    public function setUp()
    {
        $appConfig = array(
            'modules' => array(),
            'module_listener_options' => array(
                'config_cache_enabled' => false,
                'cache_dir'            => 'data/cache',
                'module_paths'         => array(),
            ),
        );
        $config = function ($s) {
            return new Config(array());
        };
        $sm = $this->serviceManager = new ServiceManager(
            new ServiceManagerConfig(array(
                'invokables' => array(
                    'DispatchListener' => 'Zend\Mvc\DispatchListener',
                    'Request'          => 'Zend\Http\PhpEnvironment\Request',
                    'Response'         => 'Zend\Http\PhpEnvironment\Response',
                    'RouteListener'    => 'Zend\Mvc\RouteListener',
                    'ViewManager'      => 'ZendTest\Mvc\TestAsset\MockViewManager',
                    'SendResponseListener' => 'ZendTest\Mvc\TestAsset\MockSendResponseListener'
                ),
                'factories' => array(
                    'ControllerLoader'        => 'Zend\Mvc\Service\ControllerLoaderFactory',
                    'ControllerPluginManager' => 'Zend\Mvc\Service\ControllerPluginManagerFactory',
                    'RoutePluginManager'      => 'Zend\Mvc\Service\RoutePluginManagerFactory',
                    'Application'             => 'Zend\Mvc\Service\ApplicationFactory',
                    'HttpRouter'              => 'Zend\Mvc\Service\RouterFactory',
                    'Config'                  => $config,
                ),
                'aliases' => array(
                    'Router'                 => 'HttpRouter',
                    'Configuration'          => 'Config',
                ),
            ))
        );
        $sm->setService('ApplicationConfig', $appConfig);
        $sm->setFactory('ServiceListener', 'Zend\Mvc\Service\ServiceListenerFactory');
        $sm->setAllowOverride(true);

        $this->application = $sm->get('Application');
    }

    public function setupPathController()
    {
        $request = $this->serviceManager->get('Request');
        $uri     = UriFactory::factory('http://example.local/path');
        $request->setUri($uri);

        $router = $this->serviceManager->get('HttpRouter');
        $route  = Router\Http\Literal::factory(array(
            'route'    => '/path',
            'defaults' => array(
                'controller' => 'path',
            ),
        ));
        $router->addRoute('path', $route);
        $this->application->bootstrap();
    }

    public function testControllerLoaderComposedOfAbstractFactory()
    {
        $this->setupPathController();

        $controllerLoader = $this->serviceManager->get('ControllerLoader');
        $controllerLoader->addAbstractFactory('ZendTest\Mvc\Controller\TestAsset\ControllerLoaderAbstractFactory');

        $log = array();
        $this->application->getEventManager()->attach(MvcEvent::EVENT_DISPATCH_ERROR, function ($e) use (&$log) {
            $log['error'] = $e->getError();
        });

        $this->application->run();

        $event = $this->application->getMvcEvent();
        $dispatchListener = $this->serviceManager->get('DispatchListener');
        $return = $dispatchListener->onDispatch($event);

        $this->assertEmpty($log);
        $this->assertInstanceOf('Zend\Http\PhpEnvironment\Response', $return);
        $this->assertSame(200, $return->getStatusCode());
    }

    public function testUnlocatableControllerLoaderComposedOfAbstractFactory()
    {
        $this->setupPathController();

        $controllerLoader = $this->serviceManager->get('ControllerLoader');
        $controllerLoader->addAbstractFactory('ZendTest\Mvc\Controller\TestAsset\UnlocatableControllerLoaderAbstractFactory');

        $log = array();
        $this->application->getEventManager()->attach(MvcEvent::EVENT_DISPATCH_ERROR, function ($e) use (&$log) {
            $log['error'] = $e->getError();
        });

        $this->application->run();
        $event = $this->application->getMvcEvent();
        $dispatchListener = $this->serviceManager->get('DispatchListener');
        $return = $dispatchListener->onDispatch($event);

        $this->assertArrayHasKey('error', $log);
        $this->assertSame('error-controller-not-found', $log['error']);
    }
}
