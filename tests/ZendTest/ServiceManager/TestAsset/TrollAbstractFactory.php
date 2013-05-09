<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\ServiceManager\TestAsset;

use stdClass;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TrollAbstractFactory implements AbstractFactoryInterface
{
    public $inexistingServiceCheckResult = null;

    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        // Check if a non-existing service exists
        $this->inexistingServiceCheckResult = $serviceLocator->has('NonExistingService');

        if ($requestedName === 'SomethingThatCanBeCreated') {
            return true;
        }

        return false;
    }

    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return new stdClass;
    }
}
