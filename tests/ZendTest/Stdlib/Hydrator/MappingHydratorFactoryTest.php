<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\Hydrator;

use Zend\Stdlib\Hydrator\MappingHydratorFactory;

class MappingHydratorFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $hydratorManager = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');

        $serviceLocator->expects($this->once())
            ->method('get')
            ->with('HydratorManager')
            ->will($this->returnValue($hydratorManager));

        $factory = new MappingHydratorFactory();
        $this->assertInstanceOf('Zend\Stdlib\Hydrator\MappingHydrator', $factory->createService($serviceLocator));

    }
}
