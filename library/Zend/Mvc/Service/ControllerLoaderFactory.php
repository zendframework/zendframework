<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace Zend\Mvc\Service;

use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\Di\DiStrictAbstractServiceFactory;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Service
 */
class ControllerLoaderFactory implements FactoryInterface
{
    /**
     * Create the controller loader service
     *
     * Creates and returns an instance of Controller\ControllerManager. The
     * only controllers this manager will allow are those defined in the
     * application configuration's "controllers" array. If a controller is
     * matched, the scoped manager will attempt to load the controller.
     * Finally, it will attempt to inject the controller plugin manager
     * if the controller implements a setPluginManager() method.
     *
     * This plugin manager is _not_ peered against DI, and as such, will
     * not load unknown classes.
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return ControllerManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $controllerLoader = new ControllerManager();
        $controllerLoader->setServiceLocator($serviceLocator);
        $controllerLoader->addPeeringServiceManager($serviceLocator);

        $configuration = $serviceLocator->get('Config');

        if (isset($configuration['di']) && $serviceLocator->has('Di')) {
            $di = $serviceLocator->get('Di');

            $controllerLoader->addAbstractFactory(
                new DiStrictAbstractServiceFactory($di, DiStrictAbstractServiceFactory::USE_SL_BEFORE_DI)
            );
        }

        return $controllerLoader;
    }
}
