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
 * @subpackage Bootstrap
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Application;

use Zend\Loader\ResourceAutoloader,
    Zend\Application\Module\Autoloader as ModuleAutoloader;

/**
 * Concrete base class for bootstrap classes
 *
 * Registers and utilizes Zend_Controller_Front by default.
 *
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Bootstrap
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Bootstrap extends AbstractBootstrap
{
    /**
     * Application resource namespace
     * @var false|string
     */
    protected $_appNamespace = false;

    /**
     * Application resource autoloader
     * @var \Zend\Loader\ResourceAutoloader
     */
    protected $_resourceLoader;

    /**
     * Constructor
     *
     * Ensure frontcontroller resource is registered
     *
     * @param  Zend_Application|\Zend\Application\Bootstrapper $application
     * @return void
     */
    public function __construct($application)
    {
        $this->setApplication($application);
        $options = $application->getOptions();

        if ($application->hasOption('resourceloader')) {
            $options['resourceloader'] = $application->getOption('resourceloader');
        }
        if ($application->hasOption('resource_broker')) {
            $options['broker'] = $application->getOption('resource_broker');
        }

        $this->setOptions($options);

        $this->getResourceLoader();

        if (!$this->hasResource('frontcontroller')) {
            $broker = $this->getBroker();
            if (!$broker->hasPlugin('frontcontroller')) {
                $broker->registerSpec('frontcontroller');
            }
        }
    }

    /**
     * Run the application
     *
     * Checks to see that we have a default controller directory. If not, an
     * exception is thrown.
     *
     * If so, it registers the bootstrap with the 'bootstrap' parameter of
     * the front controller, and dispatches the front controller.
     *
     * @return mixed
     * @throws \Zend\Application\Exception\RuntimeException
     */
    public function run()
    {
        $front   = $this->getResource('frontcontroller');
        $default = $front->getDefaultModule();
        if (null === $front->getControllerDirectory($default)) {
            throw new Exception\RuntimeException(
                'No default controller directory registered with front controller'
            );
        }

        $front->setParam('bootstrap', $this);
        $response = $front->dispatch();
        if ($front->returnResponse()) {
            return $response;
        }
    }

    /**
     * Set module resource loader
     *
     * @param  \Zend\Loader\ResourceAutoloader $loader
     * @return \Zend\Application\Module\Bootstrap
     */
    public function setResourceLoader(ResourceAutoloader $loader)
    {
        $this->_resourceLoader = $loader;
        return $this;
    }

    /**
     * Retrieve module resource loader
     *
     * @return \Zend\Loader\ResourceAutoloader
     */
    public function getResourceLoader()
    {
        if ((null === $this->_resourceLoader)
            && (false !== ($namespace = $this->getAppNamespace()))
        ) {
            $r    = new \ReflectionClass($this);
            $path = $r->getFileName();
            $autoloader = new ModuleAutoloader(array(
                'namespace' => $namespace,
                'basePath'  => dirname($path),
            ));
            $autoloader->register();
            $this->setResourceLoader($autoloader);
        }
        return $this->_resourceLoader;
    }

    /**
     * Get application namespace (used for module autoloading)
     *
     * @return string
     */
    public function getAppNamespace()
    {
        return $this->_appNamespace;
    }

    /**
     * Set application namespace (for module autoloading)
     *
     * @param  string
     * @return \Zend\Application\Bootstrap
     */
    public function setAppNamespace($value)
    {
        $this->_appNamespace = (string) $value;
        return $this;
    }
}
