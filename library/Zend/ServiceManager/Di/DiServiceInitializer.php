<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ServiceManager
 */

namespace Zend\ServiceManager\Di;

use Zend\ServiceManager\InitializerInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\Exception,
    Zend\Di\Di,
    Zend\Di\Exception\ClassNotFoundException as DiClassNotFoundException;

class DiServiceInitializer extends Di implements InitializerInterface
{
    /**
     * @var Di
     */
    protected $di = null;

    /**
     * @var DiInstanceManagerProxy
     */
    protected $diInstanceManagerProxy = null;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator = null;

    /**
     * @param \Zend\Di\Di $di
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @param null|DiInstanceManagerProxy $diImProxy
     */
    public function __construct(Di $di, ServiceLocatorInterface $serviceLocator, DiInstanceManagerProxy $diImProxy = null)
    {
        $this->di = $di;
        $this->serviceLocator = $serviceLocator;
        $this->diInstanceManagerProxy = ($diImProxy) ?: new DiInstanceManagerProxy($di->instanceManager(), $serviceLocator);
    }

    /**
     * @param $instance
     */
    public function initialize($instance)
    {
        $instanceManager = $this->di->instanceManager;
        $this->di->instanceManager = $this->diInstanceManagerProxy;
        try {
            $this->di->injectDependencies($instance);
            $this->di->instanceManager = $instanceManager;
        } catch (\Exception $e) {
            $this->di->instanceManager = $instanceManager;
            throw $e;
        }
    }

}
