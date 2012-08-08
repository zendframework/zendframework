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

use Zend\ServiceManager\Di\DiServiceInitializer;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Service
 */
class DiServiceInitializerFactory implements FactoryInterface
{
    /**
     * Class responsible for instantiating a DiStrictAbstractServiceFactory
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return DiStrictAbstractServiceFactory
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new DiServiceInitializer($serviceLocator->get('Di'), $serviceLocator);
    }
}
