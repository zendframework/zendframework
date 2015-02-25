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
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Zend\Mvc\Service\HttpMethodListenerFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @covers Zend\Mvc\Service\HttpMethodListenerFactory
 */
class HttpMethodListenerFactoryTest extends TestCase
{
    /**
     * @var ServiceLocatorInterface|MockObject
     */
    protected $serviceLocator;

    public function setUp()
    {
        $this->serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
    }

    public function testCreateWithDefaults()
    {
        $factory = new HttpMethodListenerFactory();
        $listener = $factory->createService($this->serviceLocator);
        $this->assertTrue($listener->isEnabled());
        $this->assertNotEmpty($listener->getAllowedMethods());
    }

    public function testCreateWithConfig()
    {
        $config['http_methods_listener'] = array(
            'enabled' => false,
            'allowed_methods' => array('FOO', 'BAR')
        );

        $this->serviceLocator->expects($this->atLeastOnce())
            ->method('get')
            ->with('config')
            ->willReturn($config);

        $factory = new HttpMethodListenerFactory();
        $listener = $factory->createService($this->serviceLocator);

        $listenerConfig = $config['http_methods_listener'];

        $this->assertSame($listenerConfig['enabled'], $listener->isEnabled());
        $this->assertSame($listenerConfig['allowed_methods'], $listener->getAllowedMethods());
    }
}
