<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\View\Helper;

use Zend\Console\Console;
use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\Mvc\Service\ServiceListenerFactory;

/**
 * url() helper test -- tests integration with MVC
 *
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

        $serviceConfig = $this->readAttribute(new ServiceListenerFactory, 'defaultServiceConfig');

        $this->serviceManager = new ServiceManager(new ServiceManagerConfig($serviceConfig));
        $this->serviceManager
            ->setAllowOverride(true)
            ->setService('Config', $config)
            ->setAlias('Configuration', 'Config')
            ->setAllowOverride(false);
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
