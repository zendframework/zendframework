<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mvc\View\Console;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Console\Response as ConsoleResponse;
use Zend\EventManager\EventManager;
use Zend\EventManager\SharedEventManager;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Service\ConsoleViewManagerFactory;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\Mvc\View\Console\ViewManager;
use Zend\ServiceManager\ServiceManager;
use Zend\Console\Request as ConsoleRequest;

class ViewManagerTest extends TestCase
{

    /**
     * @var ViewManager
     */
    protected $view_manager;

    /**
     * @var ServiceManager
     */
    protected $services;

    /**
     * @var ServiceManagerConfig
     */
    protected $config;

    public function setUp()
    {
        $this->config = new ServiceManagerConfig();
        $this->services = new ServiceManager();
        $this->view_manager = new ViewManager();
        $this->factory = new ConsoleViewManagerFactory();
    }

    /**
     * @return array
     */
    public function viewManagerConfiguration()
    {
        return array(
            'standard' => array(
                array(
                    'view_manager' => array(
                        'display_exceptions' => false,
                        'display_not_found_reason' => false,
                    ),
                )
            ),
            'with-console' => array(
                array(
                    'view_manager' => array(
                        'display_exceptions' => true,
                        'display_not_found_reason' => true
                    ),
                    'console' => array(
                        'view_manager' => array(
                            'display_exceptions' => false,
                            'display_not_found_reason' => false,
                        )
                    )
                )
            ),
            'without-console' => array(
                array(
                    'view_manager' => array(
                        'display_exceptions' => false,
                        'display_not_found_reason' => false
                    ),
                )
            ),
            'console-only' => array(
                array(
                    'console' => array(
                        'view_manager' => array(
                            'display_exceptions' => false,
                            'display_not_found_reason' => false
                        )
                    ),
                )
            ),
            'mixed' => array(
                array(
                    'view_manager' => array(
                        'display_exceptions' => false,
                    ),
                    'console' => array(
                        'view_manager' => array(
                            'display_not_found_reason' => false
                        )
                    )
                )
            ),
            'mixed-opposite-order' => array(
                array(
                    'view_manager' => array(
                        'display_not_found_reason' => false,
                    ),
                    'console' => array(
                        'view_manager' => array(
                            'display_exceptions' => false
                        )
                    )
                )
            )
        );
    }

    /**
     * @dataProvider viewManagerConfiguration
     * @param array $config
     * @group 6866
     */
    public function testConsoleKeyWillOverrideDisplayExceptionAndDisplayNotFoundReason($config)
    {
        $eventManager = new EventManager();
        $eventManager->setSharedManager(new SharedEventManager());

        $this->services->setService('Config', $config);
        $this->services->setService('Request', new ConsoleRequest());
        $this->services->setService('EventManager',$eventManager);
        $this->services->setService('Response', new ConsoleResponse());

        /** @var $manager ViewManager */
        $manager = $this->factory->createService($this->services);

        $application = new Application($config, $this->services);

        $event = new MvcEvent();
        $event->setApplication($application);
        $manager->onBootstrap($event);

        $this->assertFalse($manager->getExceptionStrategy()->displayExceptions());
        $this->assertFalse($manager->getRouteNotFoundStrategy()->displayNotFoundReason());
    }

    /**
     * @group 6866
     */
    public function testConsoleDisplayExceptionIsTrue()
    {
        $eventManager = new EventManager();
        $eventManager->setSharedManager(new SharedEventManager());

        $this->services->setService('Config', array());
        $this->services->setService('Request', new ConsoleRequest());
        $this->services->setService('EventManager',$eventManager);
        $this->services->setService('Response', new ConsoleResponse());

        /** @var $manager ViewManager */
        $manager = $this->factory->createService($this->services);

        $application = new Application(array(), $this->services);

        $event = new MvcEvent();
        $event->setApplication($application);
        $manager->onBootstrap($event);

        $this->assertTrue($manager->getExceptionStrategy()->displayExceptions());
        $this->assertTrue($manager->getRouteNotFoundStrategy()->displayNotFoundReason());
    }
}
