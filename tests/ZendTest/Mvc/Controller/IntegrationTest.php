<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZendTest\Mvc\Controller;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\EventManager\SharedEventManager;
use Zend\Mvc\Controller\ControllerManager;
use Zend\Mvc\Controller\PluginManager;
use Zend\ServiceManager\ServiceManager;

class IntegrationTest extends TestCase
{
    public function setUp()
    {
        $this->plugins      = new PluginManager();
        $this->sharedEvents = new SharedEventManager();
        $this->services     = new ServiceManager();
        $this->services->setService('ControllerPluginManager', $this->plugins);
        $this->services->setService('SharedEventManager', $this->sharedEvents);
        $this->services->setService('Zend\ServiceManager\ServiceLocatorInterface', $this->services);

        $this->controllers = new ControllerManager();
        $this->controllers->setServiceLocator($this->services);
    }

    public function testPluginReceivesCurrentController()
    {
        $this->controllers->setInvokableClass('first', 'ZendTest\Mvc\Controller\TestAsset\SampleController');
        $this->controllers->setInvokableClass('second', 'ZendTest\Mvc\Controller\TestAsset\SampleController');

        $first  = $this->controllers->get('first');
        $second = $this->controllers->get('second');
        $this->assertNotSame($first, $second);

        $plugin1 = $first->plugin('url');
        $this->assertSame($first, $plugin1->getController());

        $plugin2 = $second->plugin('url');
        $this->assertSame($second, $plugin2->getController());

        $this->assertSame($plugin1, $plugin2);
    }
}
