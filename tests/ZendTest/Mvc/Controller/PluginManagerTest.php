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
use ZendTest\Mvc\Controller\TestAsset\SampleController;
use ZendTest\Mvc\Controller\Plugin\TestAsset\SamplePlugin;
use ZendTest\Mvc\Controller\Plugin\TestAsset\SamplePluginWithConstructor;
use Zend\Mvc\Controller\PluginManager;
use Zend\ServiceManager\ServiceManager;

class PluginManagerTest extends TestCase
{
    public function testPluginManagerThrowsExceptionForMissingPluginInterface()
    {
        $this->setExpectedException('Zend\Mvc\Exception\InvalidPluginException');

        $pluginManager = new PluginManager;
        $pluginManager->setInvokableClass('samplePlugin', 'stdClass');

        $plugin = $pluginManager->get('samplePlugin');
    }

    public function testPluginManagerInjectsControllerInPlugin()
    {
        $controller    = new SampleController;
        $pluginManager = new PluginManager;
        $pluginManager->setInvokableClass('samplePlugin', 'ZendTest\Mvc\Controller\Plugin\TestAsset\SamplePlugin');
        $pluginManager->setController($controller);

        $plugin = $pluginManager->get('samplePlugin');
        $this->assertEquals($controller, $plugin->getController());
    }

    public function testPluginManagerInjectsControllerForExistingPlugin()
    {
        $controller1   = new SampleController;
        $pluginManager = new PluginManager;
        $pluginManager->setInvokableClass('samplePlugin', 'ZendTest\Mvc\Controller\Plugin\TestAsset\SamplePlugin');
        $pluginManager->setController($controller1);

        // Plugin manager registers now instance of SamplePlugin
        $pluginManager->get('samplePlugin');

        $controller2   = new SampleController;
        $pluginManager->setController($controller2);

        $plugin = $pluginManager->get('samplePlugin');
        $this->assertEquals($controller2, $plugin->getController());
    }

    public function testGetWithConstrutor()
    {
        $pluginManager = new PluginManager;
        $pluginManager->setInvokableClass('samplePlugin', 'ZendTest\Mvc\Controller\Plugin\TestAsset\SamplePluginWithConstructor');
        $plugin = $pluginManager->get('samplePlugin');
        $this->assertEquals($plugin->getBar(), 'baz');
    }

    public function testGetWithConstrutorAndOptions()
    {
        $pluginManager = new PluginManager;
        $pluginManager->setInvokableClass('samplePlugin', 'ZendTest\Mvc\Controller\Plugin\TestAsset\SamplePluginWithConstructor');
        $plugin = $pluginManager->get('samplePlugin', 'foo');
        $this->assertEquals($plugin->getBar(), 'foo');
    }

    public function testDefinesFactoryForIdentityPlugin()
    {
        $pluginManager = new PluginManager;
        $this->assertTrue($pluginManager->has('identity'));
    }

    public function testIdentityFactoryCanInjectAuthenticationServiceIfInParentServiceManager()
    {
        $services = new ServiceManager();
        $services->setInvokableClass('Zend\Authentication\AuthenticationService', 'Zend\Authentication\AuthenticationService');
        $pluginManager = new PluginManager;
        $pluginManager->setServiceLocator($services);
        $identity = $pluginManager->get('identity');
        $expected = $services->get('Zend\Authentication\AuthenticationService');
        $this->assertSame($expected, $identity->getAuthenticationService());
    }

    public function testCanCreateByFactory()
    {
        $pluginManager = new PluginManager;
        $pluginManager->setFactory('samplePlugin', 'ZendTest\Mvc\Controller\Plugin\TestAsset\SamplePluginFactory');
        $plugin = $pluginManager->get('samplePlugin');
        $this->assertInstanceOf('\ZendTest\Mvc\Controller\Plugin\TestAsset\SamplePlugin', $plugin);
    }

    public function testCanCreateByFactoryWithConstrutor()
    {
        $pluginManager = new PluginManager;
        $pluginManager->setFactory('samplePlugin', 'ZendTest\Mvc\Controller\Plugin\TestAsset\SamplePluginWithConstructorFactory');
        $plugin = $pluginManager->get('samplePlugin', 'foo');
        $this->assertInstanceOf('\ZendTest\Mvc\Controller\Plugin\TestAsset\SamplePluginWithConstructor', $plugin);
        $this->assertEquals($plugin->getBar(), 'foo');
    }
}
