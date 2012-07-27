<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ServiceManager
 */

namespace Zend\Mvc\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\Exception;
use Zend\ServiceManager\Di\DiAbstractServiceFactory;

class DiStrictAbstractServiceFactory extends DiAbstractServiceFactory
{
    /**
     * @var array
     */
    protected $allowedServiceNames = array();

    /**
     * @param array $allowedServiceNames
     */
    public function setAllowedServiceNames(array $allowedServiceNames)
    {
        $this->allowedServiceNames = array_flip(array_values($allowedServiceNames));
    }

    /**
     * @return array
     */
    public function getAllowedServiceNames()
    {
        return $this->allowedServiceNames;
    }

    /**
     * {@inheritDoc}
     *
     * Also prevents instantiation of services that are not in a pre-defined list
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $serviceName, $requestedName)
    {
        if (!isset($this->allowedServiceNames[$requestedName])) {
            throw new Exception\InvalidServiceNameException('Service "' . $requestedName . '" is not whitelisted');
        }

        return parent::createServiceWithName($serviceLocator, $serviceName, $requestedName);
    }

    /**
     * {@inheritDoc}
     *
     * Also prevents instantiation of services that are not in a pre-defined list
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return isset($this->allowedServiceNames[$requestedName])
            && parent::canCreateServiceWithName($serviceLocator, $name, $requestedName);
    }
}
