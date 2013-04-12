<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ServiceManager
 */

namespace ZendTest\ServiceManager\Proxy;

use Zend\ServiceManager\Proxy\LazyServiceFactoryFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * Tests for {@see \Zend\ServiceManager\Proxy\LazyServiceFactoryFactory}
 *
 * @covers \Zend\ServiceManager\Proxy\LazyServiceFactoryFactory
 */
class LazyServiceFactoryFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        if (!interface_exists('ProxyManager\\Proxy\\ProxyInterface')) {
            $this->markTestSkipped('Please install `ocramius/proxy-manager` to run these tests');
        }
    }

    /**
     * @dataProvider invalidConfigProvider
     */
    public function testInvalidConfiguration($config)
    {
        $locator  = $this->getMock('Zend\\ServiceManager\\ServiceLocatorInterface');
        $factory  = new LazyServiceFactoryFactory();

        $locator->expects($this->any())->method('get')->with('Config')->will($this->returnValue($config));
        $this->setExpectedException('Zend\\ServiceManager\\Exception\\InvalidArgumentException');

        $factory->createService($locator);
    }

    public function testAutoGenerateProxyFiles()
    {
        $serviceManager = new ServiceManager();
        $namespace      = 'ZendTestProxy' . uniqid();

        $serviceManager->setService(
            'Config',
            array(
                 'lazy_services' => array(
                     'class_map'         => array('foo' => __CLASS__),
                     'proxies_namespace' => $namespace,
                     'write_proxy_files' => true,
                 ),
            )
        );
        $serviceManager->setFactory('foo-delegator', 'Zend\ServiceManager\Proxy\LazyServiceFactoryFactory');
        $serviceManager->setInvokableClass('foo', __CLASS__);
        $serviceManager->addDelegator('foo', 'foo-delegator');

        /* @var $proxy self|\ProxyManager\Proxy\ValueHolderInterface|\ProxyManager\Proxy\LazyLoadingInterface */
        $proxy = $serviceManager->create('foo');

        $this->assertInstanceOf('ProxyManager\\Proxy\\LazyLoadingInterface', $proxy);
        $this->assertInstanceOf(__CLASS__, $proxy);
        $this->assertSame(
            $namespace . '\__PM__\ZendTest\ServiceManager\Proxy\LazyServiceFactoryFactoryTest',
            get_class($proxy)
        );
        $this->assertFileExists(
            sys_get_temp_dir() . '/' . $namespace . '__PM__ZendTestServiceManagerProxyLazyServiceFactoryFactoryTest.php'
        );
        $this->assertFalse($proxy->isProxyInitialized());
        $this->assertEquals($this->invalidConfigProvider(), $proxy->invalidConfigProvider());
        $this->assertTrue($proxy->isProxyInitialized());
    }

    public function testAutoGenerateAndEvaluateProxies()
    {
        $serviceManager = new ServiceManager();
        $namespace      = 'ZendTestProxy' . uniqid();

        $serviceManager->setService(
            'Config',
            array(
                 'lazy_services' => array(
                     'class_map'         => array('foo' => __CLASS__),
                     'proxies_namespace' => $namespace,
                 ),
            )
        );
        $serviceManager->setFactory('foo-delegator', 'Zend\ServiceManager\Proxy\LazyServiceFactoryFactory');
        $serviceManager->setInvokableClass('foo', __CLASS__);
        $serviceManager->addDelegator('foo', 'foo-delegator');

        /* @var $proxy self|\ProxyManager\Proxy\ValueHolderInterface|\ProxyManager\Proxy\LazyLoadingInterface */
        $proxy = $serviceManager->create('foo');

        $this->assertInstanceOf('ProxyManager\\Proxy\\LazyLoadingInterface', $proxy);
        $this->assertInstanceOf(__CLASS__, $proxy);
        $this->assertSame(
            $namespace . '\__PM__\ZendTest\ServiceManager\Proxy\LazyServiceFactoryFactoryTest',
            get_class($proxy)
        );
        $this->assertFileNotExists(
            sys_get_temp_dir() . '/' . $namespace . '__PM__ZendTestServiceManagerProxyLazyServiceFactoryFactoryTest.php'
        );
        $this->assertFalse($proxy->isProxyInitialized());
        $this->assertEquals($this->invalidConfigProvider(), $proxy->invalidConfigProvider());
        $this->assertTrue($proxy->isProxyInitialized());
    }

    public function testRegistersAutoloader()
    {
        $autoloaders    = spl_autoload_functions();
        $serviceManager = new ServiceManager();
        $namespace      = 'ZendTestProxy' . uniqid();

        $serviceManager->setService(
            'Config',
            array(
                 'lazy_services' => array(
                     'class_map'             => array('foo' => __CLASS__),
                     'proxies_namespace'     => $namespace,
                     'auto_generate_proxies' => false,
                 ),
            )
        );
        $serviceManager->setFactory('foo-delegator', 'Zend\ServiceManager\Proxy\LazyServiceFactoryFactory');
        $serviceManager->create('foo-delegator');

        $currentAutoloaders = spl_autoload_functions();
        $proxyAutoloader    = end($currentAutoloaders);

        $this->assertCount(count($autoloaders) + 1, $currentAutoloaders);
        $this->assertInstanceOf('ProxyManager\\Autoloader\\AutoloaderInterface', $proxyAutoloader);

        spl_autoload_unregister($proxyAutoloader);
    }

    /**
     * Provides invalid configuration
     *
     * @return array
     */
    public function invalidConfigProvider()
    {
        return array(
            array(array()),
            array(array('lazy_services' => array()))
        );
    }
}
