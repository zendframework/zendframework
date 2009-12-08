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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Concrete base class for bootstrap classes
 *
 * Registers and utilizes Zend_Controller_Front by default.
 *
 * @uses       Zend_Application_Bootstrap_Bootstrap
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Bootstrap
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Application_Bootstrap_Bootstrap
    extends Zend_Application_Bootstrap_BootstrapAbstract
{
    /**
     * Default application resource namespace
     * @var string
     */
    protected $_defaultAppNamespace = 'Application';

    /**
     * Application resource autoloader
     * @var Zend_Loader_Autoloader_Resource
     */
    protected $_resourceLoader;

    /**
     * Constructor
     *
     * Ensure FrontController resource is registered
     *
     * @param  Zend_Application|Zend_Application_Bootstrap_Bootstrapper $application
     * @return void
     */
    public function __construct($application)
    {
        parent::__construct($application);

        if ($application->hasOption('resourceloader')) {
            $this->setOptions(array(
                'resourceloader' => $application->getOption('resourceloader')
            ));
        }
        $this->getResourceLoader();

        if (!$this->hasPluginResource('FrontController')) {
            $this->registerPluginResource('FrontController');
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
     * @return void
     * @throws Zend_Application_Bootstrap_Exception
     */
    public function run()
    {
        $front   = $this->getResource('FrontController');
        $default = $front->getDefaultModule();
        if (null === $front->getControllerDirectory($default)) {
            throw new Zend_Application_Bootstrap_Exception(
                'No default controller directory registered with front controller'
            );
        }

        $front->setParam('bootstrap', $this);
        $front->dispatch();
    }

    /**
     * Set module resource loader
     *
     * @param  Zend_Loader_Autoloader_Resource $loader
     * @return Zend_Application_Module_Bootstrap
     */
    public function setResourceLoader(Zend_Loader_Autoloader_Resource $loader)
    {
        $this->_resourceLoader = $loader;
        return $this;
    }

    /**
     * Retrieve module resource loader
     *
     * @return Zend_Loader_Autoloader_Resource
     */
    public function getResourceLoader()
    {
        if (null === $this->_resourceLoader) {
            $r    = new ReflectionClass($this);
            $path = $r->getFileName();
            $this->setResourceLoader(new Zend_Application_Module_Autoloader(array(
                'namespace' => $this->getDefaultAppNamespace(),
                'basePath'  => dirname($path),
            )));
        }
        return $this->_resourceLoader;
    }

    /**
     * Get defaultAppNamespace
     *
     * @return string
     */
    public function getDefaultAppNamespace()
    {
        return $this->_defaultAppNamespace;
    }

    /**
     * Set defaultAppNamespace
     *
     * @param  string
     * @return Zend_Application_Bootstrap_Bootstrap
     */
    public function setDefaultAppNamespace($value)
    {
        $this->_defaultAppNamespace = (string) $value;
        return $this;
    }
}
