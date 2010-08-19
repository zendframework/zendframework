<?php
namespace Zend\View;

use Zend\Loader\ShortNameLocater;

class HelperBroker
{
    protected $classLoader;
    protected $helpers = array();

    /**
     * Load and return a helper instance
     * 
     * @param  string $helper 
     * @param  array $options Options to pass to the helper constructor
     * @return Helper
     * @throws Exception if helper not found
     */
    public function load($helper, array $options = null)
    {
        $helperName = strtolower($helper);
        if (isset($this->helpers[$helperName])) {
            return $this->helpers[$helperName];
        }

        $class = $this->getClassLoader()->load($helper);
        if (empty($class)) {
            throw new Exception('Unable to locate class associated with "' . $helperName . '"' /*'"; available plugins: ' . var_export($this->getClassLoader()->getRegisteredPlugins(), 1)*/);
        }

        if (empty($options)) {
            $instance = new $class();
        } else {
            $r = new \ReflectionClass($class);
            $instance = $r->newInstanceArgs($options);
        }

        if (!$instance instanceof Helper) {
            throw new InvalidHelperException('View helpers must implement Zend\View\Helper');
        }

        $this->helpers[$helperName] = $instance;
        return $instance;
    }

    /**
     * Set class loader to use when resolving helper names to class names
     * 
     * @param  ShortNameLocater $loader 
     * @return HelperBroker
     */
    public function setClassLoader(ShortNameLocater $loader)
    {
        $this->classLoader = $loader;
        return $this;
    }

    /**
     * Retrieve the class loader
     *
     * Lazy-loads an instance of the HelperLoader if no loader is registered.
     * 
     * @return ShortNameLocater
     */
    public function getClassLoader()
    {
        if (null === $this->classLoader) {
            $this->setClassLoader(new HelperLoader());
        }
        return $this->classLoader;
    }
}
