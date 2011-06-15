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
    Zend\Tool\Framework\Exception,
    Zend\Tool\Framework\RegistryEnabled,
    Zend\Loader\StandardAutoloader;

/**
 * @uses       ReflectionClass
 * @uses       \Zend\Loader
 * @uses       \Zend\Tool\Framework\Loader
 * @uses       \Zend\Tool\Framework\Manifest
 * @uses       \Zend\Tool\Framework\Provider
 * @uses       \Zend\Tool\Framework\RegistryEnabled
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class BasicLoader implements Loader, RegistryEnabled
{
    /**
     * @var Zend\Tool\Framework\Repository\Interface
     */
    protected $_registry = null;

    protected $loader;

    /**
     * @var array
     */
    protected $_classesToLoad = array();
    
    public function __construct($options = array())
    {
        if ($options) {
            $this->setOptions($options);
        }

        // use for resolving classes not handled by autoloading
        $this->loader = new StandardAutoloader();
        $this->loader->setFallbackAutoloader(true);
    }
    
    public function setOptions(Array $options)
    {
        foreach ($options as $optionName => $optionValue) {
            $setMethod = 'set' . $optionName;
            if (method_exists($this, $setMethod)) {
                $this->{$setMethod}($optionValue);
            }
        }
    }
    
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
     * @param  array $classesToLoad
     * @return \Zend\Tool\Framework\Loader\AbstractLoader
     */
    public function setClassesToLoad(array $classesToLoad)
    {
        $this->_classesToLoad = $classesToLoad;
        return $this;
    }
    
    public function load()
    {
        $manifestRegistry = $this->_registry->getManifestRepository();
        $providerRegistry = $this->_registry->getProviderRepository();
        
        $loadedClasses = array();
        
        // loop through the loaded classes and ensure that
        foreach ($this->_classesToLoad as $class) {
            
            if (!class_exists($class)
                && !$this->loader->autoload($class)
            ) {
                throw new Exception\RuntimeException(sprintf('Unable to resolve class "%s"', $class));
            }

            // reflect class to see if its something we want to load
            $reflectionClass = new \ReflectionClass($class);
            if ($this->_isManifestImplementation($reflectionClass)) {
                $manifestRegistry->addManifest($reflectionClass->newInstance());
                $loadedClasses[] = $class;
            }

            if ($this->_isProviderImplementation($reflectionClass)) {
                $providerRegistry->addProvider($reflectionClass->newInstance());
                $loadedClasses[] = $class;
            }

        }

        return $loadedClasses;
    }

    /**
     * @param  ReflectionClass $reflectionClass
     * @return bool
     */
    private function _isManifestImplementation($reflectionClass)
    {
        return (
            $reflectionClass->implementsInterface('Zend\Tool\Framework\Manifest')
                && !$reflectionClass->isAbstract()
        );
    }

    /**
     * @param  ReflectionClass $reflectionClass
     * @return bool
     */
    private function _isProviderImplementation($reflectionClass)
    {
        $providerRegistry = $this->_registry->getProviderRepository();

        return (
            $reflectionClass->implementsInterface('Zend\Tool\Framework\Provider')
                && !$reflectionClass->isAbstract()
                && !$providerRegistry->hasProvider($reflectionClass->getName(), false)
        );
    }
    
}
