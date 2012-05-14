<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Service
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Mvc\Service;

use Zend\Di\Configuration as DiConfiguration;
use Zend\Di\Di;
use Zend\ServiceManager\Di\DiAbstractServiceFactory;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Service
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DiFactory implements FactoryInterface
{
    /**
     * Create and return abstract factory seeded by dependency injector
     *
     * Creates and returns an abstract factory seeded by the dependency 
     * injector. If the "di" key of the configuration service is set, that
     * sub-array is passed to a DiConfiguration object and used to configure
     * the DI instance. The DI instance is then used to seed the 
     * DiAbstractServiceFactory, which is then registered with the service
     * manager.
     * 
     * @param  ServiceLocatorInterface $serviceLocator 
     * @return Di
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $di     = new Di();

        $config = $serviceLocator->get('Configuration');
        if (isset($config['di'])) {
            $di->configure(new DiConfiguration($config['di']));
        }

        if ($serviceLocator instanceof ServiceManager) {
            // register as abstract factory as well:
            $serviceLocator->addAbstractFactory(
                new DiAbstractServiceFactory($di, DiAbstractServiceFactory::USE_SL_BEFORE_DI)
            );
        }

        return $di;
    }
}
