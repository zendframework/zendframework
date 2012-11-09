<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ServiceManager
 */

namespace Zend\ServiceManager;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @category  Zend
 * @package   Zend_ServiceManager
 */
trait ServiceLocatorAwareTrait
{
    /**
     * @var \Zend\ServiceManager\ServiceLocator
     */
    protected $service_locator = null;

    /**
     * setServiceLocator
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->service_locator = $serviceLocator;

        return $this;
    }

    /**
     * getServiceLocator
     *
     * @return \Zend\ServiceManager\ServiceLocator
     */
    public function getServiceLocator()
    {
        return $this->service_locator;
    }
}
