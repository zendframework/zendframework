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
 * @package    Zend_Application
 * @subpackage Module
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Application\Module;

use Zend\Application\Bootstrap as ApplicationBootstrap,
    Zend\Loader\Broker,
    Zend\Loader\LazyLoadingBroker;

/**
 * Base bootstrap class for modules
 *
 * @uses       \Zend\Loader\ResourceAutoloader
 * @uses       \Zend\Application\Bootstrap
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Module
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Bootstrap
    extends ApplicationBootstrap
{
    /**
     * Set this explicitly to reduce impact of determining module name
     * @var string
     */
    protected $_moduleName;

    /**
     * Constructor
     *
     * @param  Zend_Application|\Zend\Application\Bootstrapper $application
     * @return void
     */
    public function __construct($application)
    {
        $this->setApplication($application);
        $front = false;

        // Use same plugin broker as parent bootstrap
        if ($application instanceof \Zend\Application\ResourceBootstrapper) {
            $this->setBroker($application->getBroker());
            if ($application->hasResource('frontcontroller')) {
                $front = $application->getResource('frontcontroller');
            }
        }

        $key = strtolower($this->getModuleName());
        if ($application->hasOption($key)) {
            // Don't run via setOptions() to prevent duplicate initialization
            $this->setOptions($application->getOption($key));
        }

        if ($application->hasOption('resourceloader')) {
            $this->setOptions(array(
                'resourceloader' => $application->getOption('resourceloader')
            ));
        }
        $this->initResourceLoader();

        $broker = $this->getBroker();
        if ($broker->hasPlugin('modules')) {
            if ($broker instanceof Broker) {
                $broker->unregister('modules');
            }
            if ($broker instanceof LazyLoadingBroker) {
                $broker->unregisterSpec('modules');
            }
        }

        // ZF-6545: ensure front controller resource is loaded
        if ($front) {
            $this->getContainer()->frontcontroller = $front;
            return;
        }

        if (!$broker->hasPlugin('frontcontroller')) {
            $broker->registerSpec('frontcontroller');
        }
    }

    /**
     * Ensure resource loader is loaded
     *
     * @return void
     */
    public function initResourceLoader()
    {
        $this->getResourceLoader();
    }

    /**
     * Get default application namespace
     *
     * Proxies to {@link getModuleName()}, and returns the current module 
     * name
     * 
     * @return string
     */
    public function getAppNamespace()
    {
        return $this->getModuleName();
    }

    /**
     * Retrieve module name
     *
     * @return string
     */
    public function getModuleName()
    {
        if (empty($this->_moduleName)) {
            $class = get_class($this);
            if (preg_match('/^([a-z][a-z0-9]*)\\\\/i', $class, $matches)) {
                $prefix = $matches[1];
            } else {
                $prefix = $class;
            }
            $this->_moduleName = $prefix;
        }
        return $this->_moduleName;
    }
}
