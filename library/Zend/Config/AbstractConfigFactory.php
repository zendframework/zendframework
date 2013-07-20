<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Config;

use Zend\ServiceManager;

/**
 * Class AbstractConfigFactory
 */
class AbstractConfigFactory implements ServiceManager\AbstractFactoryInterface
{
    /**
     * @var string
     */
    protected $pattern = '#^(.*)\\\\Config$#i';

    /**
     * Determine if we can create a service with name
     *
     * @param ServiceManager\ServiceLocatorInterface $serviceLocator
     * @param string $name
     * @param string $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceManager\ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if (!preg_match($this->pattern, $requestedName, $matches)) {
            return false;
        }

        $config = $serviceLocator->get('Config');
        if (!isset($config[$matches[1]])) {
            return false;
        }

        return true;
    }

    /**
     * Create service with name
     *
     * @param ServiceManager\ServiceLocatorInterface $serviceLocator
     * @param string $name
     * @param string $requestedName
     * @return mixed
     */
    public function createServiceWithName(ServiceManager\ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        preg_match($this->pattern, $requestedName, $matches);
        $config = $serviceLocator->get('Config');
        return new Config($config[$matches[1]]);
    }
}
