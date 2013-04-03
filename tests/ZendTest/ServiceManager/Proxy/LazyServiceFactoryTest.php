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

use Zend\ServiceManager\Proxy\LazyServiceFactory;

/**
 * Tests for {@see \Zend\ServiceManager\Proxy\LazyServiceFactory}
 *
 * @covers \Zend\ServiceManager\Proxy\LazyServiceFactory
 */
class LazyServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ProxyManager\Factory\LazyLoadingValueHolderFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $proxyFactory;

    protected $locator;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        if (!interface_exists('ProxyManager\\Proxy\\ProxyInterface')) {
            $this->markTestSkipped('Please install `ocramius/proxy-manager` to run these tests');
        }

        $this->locator      = $this->getMock('Zend\\ServiceManager\\ServiceLocatorInterface');
        $this->proxyFactory = $this
            ->getMockBuilder('ProxyManager\\Factory\\LazyLoadingValueHolderFactory')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testCreateDelegatorWithRequestedName()
    {
        $instance = new \stdClass();
        $callback = function () {};
        $factory  = new LazyServiceFactory($this->proxyFactory, array('foo' => 'bar'));

        $this
            ->proxyFactory
            ->expects($this->once())
            ->method('createProxy')
            ->with('bar', $callback)
            ->will($this->returnValue($instance));

        $this->assertSame($instance, $factory->createDelegatorWithName($this->locator, 'baz', 'foo', $callback));
    }

    public function testCreateDelegatorWithCanonicalName()
    {
        $instance = new \stdClass();
        $callback = function () {};
        $factory  = new LazyServiceFactory($this->proxyFactory, array('foo' => 'bar'));

        $this
            ->proxyFactory
            ->expects($this->once())
            ->method('createProxy')
            ->with('bar', $callback)
            ->will($this->returnValue($instance));

        $this->assertSame($instance, $factory->createDelegatorWithName($this->locator, 'foo', 'baz', $callback));
    }

    public function testCannotCreateDelegatorWithNoMappedServiceClass()
    {
        $factory = new LazyServiceFactory($this->proxyFactory, array());

        $this->setExpectedException('Zend\\ServiceManager\\Exception\\InvalidServiceNameException');

        $factory->createDelegatorWithName($this->locator, 'foo', 'baz', function () {});
    }
}
