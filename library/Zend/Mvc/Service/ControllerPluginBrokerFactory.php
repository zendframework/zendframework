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

use Zend\Mvc\Controller\PluginBroker as ControllerPluginBroker;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Service
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ControllerPluginBrokerFactory implements FactoryInterface
{
    /**
     * Create and return the MVC controller plugin broker
     * 
     * @param  ServiceLocatorInterface $serviceLocator 
     * @return ControllerPluginBroker
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $broker = new ControllerPluginBroker();
        $broker->setClassLoader($serviceLocator->get('ControllerPluginLoader'));
        $broker->setServiceLocator($serviceLocator);
        return $broker;
    }
}
