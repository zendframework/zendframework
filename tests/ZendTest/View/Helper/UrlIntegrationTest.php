<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\Helper;

use Zend\Console\Console;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\Config as ServiceManagerConfig;
use Zend\View\Helper\Url as UrlHelper;

/**
 * url() helper test -- tests integration with MVC
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class UrlIntegrationTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $config = array(
            'router' => array(
                'routes' => array(
                    'test' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/test',
                            'defaults' => array(
                                'controller' => 'Test\Controller\Test',
                            ),
                        ),
                    ),
                ),
            ),
            'console' => array(
                'router' => array(
                    'routes' => array(
                        'test' => array(
                            'type' => 'Simple',
                            'options' => array(
                                'route' => 'test this',
                                'defaults' => array(
                                    'controller' => 'Test\Controller\TestConsole',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );
        $serviceConfig = array(
            'invokables' => array(
                'SharedEventManager' => 'Zend\EventManager\SharedEventManager',
                'DispatchListener'   => 'Zend\Mvc\DispatchListener',
                'RouteListener'      => 'Zend\Mvc\RouteListener',
                'SendResponseListener' => 'Zend\Mvc\SendResponseListener'
            ),
            'factories' => array(
                'Application'             => 'Zend\Mvc\Service\ApplicationFactory',
                'EventManager'            => 'Zend\Mvc\Service\EventManagerFactory',
                'ViewHelperManager'       => 'Zend\Mvc\Service\ViewHelperManagerFactory',
                'Request'                 => 'Zend\Mvc\Service\RequestFactory',
                'Response'                => 'Zend\Mvc\Service\ResponseFactory',
                'Router'                  => 'Zend\Mvc\Service\RouterFactory',
                'ConsoleRouter'           => 'Zend\Mvc\Service\RouterFactory',
                'HttpRouter'              => 'Zend\Mvc\Service\RouterFactory',
                'RoutePluginManager'      => 'Zend\Mvc\Service\RoutePluginManagerFactory',
                'ViewManager'             => 'Zend\Mvc\Service\ViewManagerFactory',
                'ViewResolver'            => 'Zend\Mvc\Service\ViewResolverFactory',
                'ViewTemplateMapResolver' => 'Zend\Mvc\Service\ViewTemplateMapResolverFactory',
                'ViewTemplatePathStack'   => 'Zend\Mvc\Service\ViewTemplatePathStackFactory',
            ),
            'shared' => array(
                'EventManager' => false,
            ),
        );
        $serviceConfig = new ServiceManagerConfig($serviceConfig);

        $this->serviceManager = new ServiceManager($serviceConfig);
        $this->serviceManager->setService('Config', $config);
        $this->serviceManager->setAlias('Configuration', 'Config');
    }

    public function testUrlHelperWorksUnderNormalHttpParadigms()
    {
        Console::overrideIsConsole(false);
        $this->serviceManager->get('Application')->bootstrap();
        $request = $this->serviceManager->get('Request');
        $this->assertInstanceOf('Zend\Http\Request', $request);
        $viewHelpers = $this->serviceManager->get('ViewHelperManager');
        $urlHelper   = $viewHelpers->get('url');
        $test        = $urlHelper('test');
        $this->assertEquals('/test', $test);
    }

    public function testUrlHelperWorksWithForceCanonicalFlag()
    {
        Console::overrideIsConsole(false);
        $this->serviceManager->get('Application')->bootstrap();
        $request = $this->serviceManager->get('Request');
        $this->assertInstanceOf('Zend\Http\Request', $request);
        $router = $this->serviceManager->get('Router');
        $router->setRequestUri($request->getUri());
        $request->setUri('http://example.com/test');
        $viewHelpers = $this->serviceManager->get('ViewHelperManager');
        $urlHelper   = $viewHelpers->get('url');
        $test        = $urlHelper('test', array(), array('force_canonical' => true));
        $this->assertContains('/test', $test);
    }

    public function testUrlHelperUnderConsoleParadigmShouldReturnHttpRoutes()
    {
        Console::overrideIsConsole(true);
        $this->serviceManager->get('Application')->bootstrap();
        $request = $this->serviceManager->get('Request');
        $this->assertInstanceOf('Zend\Console\Request', $request);
        $viewHelpers = $this->serviceManager->get('ViewHelperManager');
        $urlHelper   = $viewHelpers->get('url');
        $test        = $urlHelper('test');
        $this->assertEquals('/test', $test);
    }
}
