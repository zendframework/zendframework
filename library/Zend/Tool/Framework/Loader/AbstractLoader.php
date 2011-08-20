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
 * @package    Zend_Tool
 * @subpackage Framework
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Tool\Framework\Loader;
use Zend\Tool\Framework\Loader,
    Zend\Tool\Framework\RegistryEnabled;

/**
 * @uses       ReflectionClass
 * @uses       \Zend\Tool\Framework\Loader
 * @uses       \Zend\Tool\Framework\Manifest
 * @uses       \Zend\Tool\Framework\Provider
 * @uses       \Zend\Tool\Framework\RegistryEnabled
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractLoader implements Loader, RegistryEnabled
{
    /**
     * @var Zend\Tool\Framework\Repository
     */
    protected $_registry = null;

    /**
     * @var array
     */
    private $_retrievedFiles = array();

    /**
     * @var array
     */
    private $_loadedClasses  = array();

    /**
     * _getFiles
     *
     * @return array Array Of Files
     */
    abstract protected function _getFiles();

    /**
     * setRegistry() - required by the enabled interface to get an instance of
     * the registry
     *
     * @param \Zend\Tool\Framework\Registry $registry
     * @return \Zend\Tool\Framework\Loader\AbstractLoader
     */
    public function setRegistry(\Zend\Tool\Framework\Registry $registry)
    {
        $this->_registry = $registry;
        return $this;
    }

    /**
     * load() - called by the client initialize routine to load files
     *
     */
    public function load()
    {
        $this->_retrievedFiles = $this->getRetrievedFiles();
        $this->_loadedClasses  = array();

        $manifestRepository = $this->_registry->getManifestRepository();
        $providerRepository = $this->_registry->getProviderRepository();

        $loadedClasses = array();

        // loop through files and find the classes declared by loading the file
        foreach ($this->_retrievedFiles as $file) {
            if(is_dir($file)) {
                continue;
            }

            $classesLoadedBefore = get_declared_classes();
            $oldLevel = error_reporting(E_ALL | ~E_STRICT); // remove strict so that other packages wont throw warnings
            // should we lint the files here? i think so
            include_once $file;
            error_reporting($oldLevel); // restore old error level
            $classesLoadedAfter = get_declared_classes();
            $loadedClasses = array_merge($loadedClasses, array_diff($classesLoadedAfter, $classesLoadedBefore));
        }

        // loop through the loaded classes and ensure that
        foreach ($loadedClasses as $loadedClass) {

            // reflect class to see if its something we want to load
            $reflectionClass = new \ReflectionClass($loadedClass);
            if ($reflectionClass->implementsInterface('Zend\\Tool\\Framework\\Manifest\\Interface')
                && !$reflectionClass->isAbstract())
            {
                $manifestRepository->addManifest($reflectionClass->newInstance());
                $this->_loadedClasses[] = $loadedClass;
            }

            if ($reflectionClass->implementsInterface('Zend\\Tool\\Framework\\Provider\\Interface')
                && !$reflectionClass->isAbstract()
                && !$providerRepository->hasProvider($reflectionClass->getName(), false))
            {
                $providerRepository->addProvider($reflectionClass->newInstance());
                $this->_loadedClasses[] = $loadedClass;
            }

        }

        return $this->_loadedClasses;
    }

    /**
     * getRetrievedFiles()
     *
     * @return array Array of Files Retrieved
     */
    public function getRetrievedFiles()
    {
        if ($this->_retrievedFiles == null) {
            $this->_retrievedFiles = $this->_getFiles();
        }

        return $this->_retrievedFiles;
    }

    /**
     * getLoadedClasses()
     *
     * @return array Array of Loaded Classes
     */
    public function getLoadedClasses()
    {
        return $this->_loadedClasses;
    }


}
