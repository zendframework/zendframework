<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Session\Service;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;

/**
 * Session container abstract service factory.
 *
 * Allows creating Container instances, using the Zend\Service\ManagerInterface
 * if present. Containers are named in a "session_containers" array in the
 * Config service:
 *
 * <code>
 * return array(
 *     'session_containers' => array(
 *         'auth',
 *         'user',
 *         'captcha',
 *     ),
 * );
 * </code>
 *
 * Services use the prefix "SessionContainer\\":
 *
 * <code>
 * $container = $services->get('SessionContainer\captcha');
 * </code>
 */
class ContainerAbstractFactory implements AbstractFactoryInterface
{
    /**
     * Cached container configuration
     *
     * @var array
     */
    protected $config;

    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @param  string $name
     * @param  string $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $this->getConfig($serviceLocator);
        if (!$config) {
            return false;
        }

        $containerName = $this->normalizeContainerName($requestedName);
        if (!$containerName) {
            return false;
        }

        return array_key_exists($containerName, $config);
    }

    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @param  string $name
     * @param  string $requestedName
     * @return Container
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $containerName = substr($requestedName, 17);
        $manager       = null;

        if ($serviceLocator->has('Zend\Session\ManagerInterface')) {
            $manager = $serviceLocator->get('Zend\Session\ManagerInterface');
        }

        return new Container($containerName, $manager);
    }

    /**
     * Retrieve config from service locator, and cache for later
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return false|array
     */
    protected function getConfig(ServiceLocatorInterface $serviceLocator)
    {
        if (null !== $this->config) {
            return $this->config;
        }

        if (!$serviceLocator->has('Config')) {
            $this->config = array();
            return false;
        }

        $config = $serviceLocator->get('Config');
        if (!isset($config['session_containers']) || !is_array($config['session_containers'])) {
            $this->config = array();
            return false;
        }

        $config = $config['session_containers'];
        $config = array_flip($config);

        $this->config = array_change_key_case($config);
        return $this->config;
    }

    /**
     * Normalize the container name in order to perform a lookup
     *
     * Strips off the "SessionContainer\" prefix, and lowercases the name.
     *
     * @param  string $name
     * @return string
     */
    protected function normalizeContainerName($name)
    {
        $containerName = strtolower($name);
        if (18 > strlen($containerName)
            || ('sessioncontainer\\' !== substr($containerName, 0, 17))
        ) {
            return false;
        }

        return substr($containerName, 17);
    }
}
