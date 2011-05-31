<?php

namespace Zend\Di\Generator;

class Configuration
{
    const MODE_DISCOVER = null;
    const MODE_DEVELOPMENT = 'development';
    const MODE_PRODUCTION  = 'production';
    
    protected $containerConfigurationPath = null;
    protected $mode = self::MODE_DISCOVER;
    protected $developmentFileStatPath = null;
    protected $managedDirectories = array();
    protected $managedNamespaces = array();
    protected $introspectors = array();
    protected $objectConfigurations = array();
    
    public function fromArray(array $configValues)
    {
        foreach ($configValues as $name => $value) {
            if (method_exists($this, 'set' . $name)) {
                $this->{'set' . $name}($value);
            }
        }
    }
    
	/**
     * @return string $containerConfigurationPath
     */
    public function getContainerConfigurationPath()
    {
        return $this->containerConfigurationPath;
    }

	/**
     * @param string $containerConfigurationPath
     */
    public function setContainerConfigurationPath($containerConfigurationPath)
    {
        $this->containerConfigurationPath = $containerConfigurationPath;
        return $this;
    }

	/**
     * @return string $mode
     */
    public function getMode()
    {
        return $this->mode;
    }

	/**
     * @param string $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

	/**
     * @return string $developmentFileStatPath
     */
    public function getDevelopmentFileStatPath()
    {
        return $this->developmentFileStatPath;
    }

	/**
     * @param string $developmentFileStatPath
     */
    public function setDevelopmentFileStatPath($developmentFileStatPath)
    {
        $this->developmentFileStatPath = $developmentFileStatPath;
    }

	/**
     * @return array $managedDirectories
     */
    public function getManagedDirectories()
    {
        return $this->managedDirectories;
    }

	/**
     * @param array $managedDirectories
     */
    public function setManagedDirectories(array $managedDirectories)
    {
        $this->managedDirectories = $managedDirectories;
    }

	/**
     * @return array $managedNamespaces
     */
    public function getManagedNamespaces()
    {
        return $this->managedNamespaces;
    }

	/**
     * @param array $managedNamespaces
     */
    public function setManagedNamespaces(array $managedNamespaces)
    {
        $this->managedNamespaces = $managedNamespaces;
    }

    public function getIntrospectors()
    {
        return array_keys($this->introspectors);
    }
    
    public function setIntrospectors(array $introspectors)
    {
        $this->introspectors = array();
        foreach ($introspectors as $name => $value) {
            if (is_int($name)) {
                $this->introspectors[$value] = array();
            } elseif (is_string($name)) {
                $this->introspectors[$name] = $value;
            }
        }
    }
    
    public function getIntrospectionConfiguration($name)
    {
        return ((array_key_exists($name, $this->introspectors)) ? $this->introspectors[$name] : array());
    }
    
    public function setObjectConfigurations($objectConfigurations)
    {
        $this->objectConfigurations = $objectConfigurations;
    }
    
    public function getObjectConfigurations()
    {
        return $this->objectConfigurations;
    }
    
}
