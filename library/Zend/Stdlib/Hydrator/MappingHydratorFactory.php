<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib\Hydrator;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MappingHydratorFactory implements FactoryInterface
{
    /**
     * Creates MappingHydrator
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return MappingHydrator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var ServiceLocatorInterface $hydratorManager */
        $hydratorManager = $serviceLocator->get('HydratorManager');

        return new MappingHydrator($hydratorManager);
    }
}
