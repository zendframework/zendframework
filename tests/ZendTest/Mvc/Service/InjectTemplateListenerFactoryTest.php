<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mvc\Service;

use ArrayObject;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Mvc\Service\InjectTemplateListenerFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * Tests for {@see \Zend\Mvc\Service\InjectTemplateListenerFactory}
 *
 * @covers \Zend\Mvc\Service\InjectTemplateListenerFactory
 */
class InjectTemplateListenerFactoryTest extends TestCase
{
    /**
     * @var InjectTemplateListenerFactory
     */
    private $factory;

    public function setUp()
    {
        $this->factory  = new InjectTemplateListenerFactory();
    }

    public function testFactoryCanCreateInjectTemplateListener()
    {
        $listener = $this->factory->createService($this->buildServiceLocatorWithConfig(array()));

        $this->assertInstanceOf('Zend\Mvc\View\Http\InjectTemplateListener', $listener);
    }

    public function testFactoryCanSetControllerMap()
    {
        $listener = $this->factory->createService($this->buildServiceLocatorWithConfig(array(
            'view_manager' => array(
                'controller_map' => array(
                    'SomeModule' => 'some/module',
                ),
            ),
        )));

        $this->assertEquals('some/module', $listener->mapController("SomeModule"));
    }

    public function testFactoryCanSetControllerMapViaArrayAccessVM()
    {
        $listener = $this->factory->createService($this->buildServiceLocatorWithConfig(array(
            'view_manager' => new ArrayObject(array(
                'controller_map' => array(
                    // must be an array due to type hinting on setControllerMap()
                    'SomeModule' => 'some/module',
                ),
            ))
        )));

        $this->assertEquals('some/module', $listener->mapController("SomeModule"));
    }

    /**
     * @param mixed $config
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Zend\ServiceManager\ServiceLocatorInterface
     */
    private function buildServiceLocatorWithConfig($config)
    {
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');

        $serviceLocator->expects($this->any())->method('get')->with('Config')->will($this->returnValue($config));

        return $serviceLocator;
    }
}
