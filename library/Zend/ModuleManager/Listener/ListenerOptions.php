<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ModuleManager
 */

namespace Zend\ModuleManager\Listener;

use Traversable;
use Zend\Stdlib\AbstractOptions;

/**
 * Listener options
 *
 * @category   Zend
 * @package    Zend_ModuleManager
 * @subpackage Listener
 */
class ListenerOptions extends AbstractOptions
{
    /**
     * @var array
     */
    protected $modulePaths = array();

    /**
     * @var array
     */
    protected $configGlobPaths = array();

    /**
     * @var array
     */
    protected $configStaticPaths = array();

    /**
     * @var array
     */
    protected $extraConfig = array();

    /**
     * @var bool
     */
    protected $configCacheEnabled = false;

    /**
     * @var string
     */
    protected $configCacheKey;

    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * Get an array of paths where modules reside
     *
     * @return array
     */
    public function getModulePaths()
    {
        return $this->modulePaths;
    }

    /**
     * Set an array of paths where modules reside
     *
     * @param  array|Traversable $modulePaths
     * @throws Exception\InvalidArgumentException
     * @return ListenerOptions
     */
    public function setModulePaths($modulePaths)
    {
        if (!is_array($modulePaths) && !$modulePaths instanceof Traversable) {
            throw new Exception\InvalidArgumentException(
                sprintf('Argument passed to %s::%s() must be an array, '
                . 'implement the \Traversable interface, or be an '
                . 'instance of Zend\Config\Config. %s given.',
                __CLASS__, __METHOD__, gettype($modulePaths))
            );
        }
        $this->modulePaths = $modulePaths;
        return $this;
    }

    /**
     * Get the glob patterns to load additional config files
     *
     * @return array
     */
    public function getConfigGlobPaths()
    {
        return $this->configGlobPaths;
    }

    /**
     * Get the static paths to load additional config files
     *
     * @return array
     */
    public function getConfigStaticPaths()
    {
        return $this->configStaticPaths;
    }

    /**
     * Set the glob patterns to use for loading additional config files
     *
     * @param array|Traversable $configGlobPaths
     * @throws Exception\InvalidArgumentException
     * @return ListenerOptions
     */
    public function setConfigGlobPaths($configGlobPaths)
    {
        if (!is_array($configGlobPaths) && !$configGlobPaths instanceof Traversable) {
            throw new Exception\InvalidArgumentException(
                sprintf('Argument passed to %s::%s() must be an array, '
                . 'implement the \Traversable interface, or be an '
                . 'instance of Zend\Config\Config. %s given.',
                __CLASS__, __METHOD__, gettype($configGlobPaths))
            );
        }
        $this->configGlobPaths = $configGlobPaths;
        return $this;
    }

    /**
     * Set the static paths to use for loading additional config files
     *
     * @param array|Traversable $configStaticPaths
     * @throws Exception\InvalidArgumentException
     * @return ListenerOptions
     */
    public function setConfigStaticPaths($configStaticPaths)
    {
        if (!is_array($configStaticPaths) && !$configStaticPaths instanceof Traversable) {
            throw new Exception\InvalidArgumentException(
                sprintf('Argument passed to %s::%s() must be an array, '
                . 'implement the \Traversable interface, or be an '
                . 'instance of Zend\Config\Config. %s given.',
                __CLASS__, __METHOD__, gettype($configStaticPaths))
            );
        }
        $this->configStaticPaths = $configStaticPaths;
        return $this;
    }

    /**
     * Get any extra config to merge in.
     *
     * @return array|Traversable
     */
    public function getExtraConfig()
    {
        return $this->extraConfig;
    }

    /**
     * Add some extra config array to the main config. This is mainly useful
     * for unit testing purposes.
     *
     * @param array|Traversable $extraConfig
     * @throws Exception\InvalidArgumentException
     * @return ListenerOptions
     */
    public function setExtraConfig($extraConfig)
    {
        if (!is_array($extraConfig) && !$extraConfig instanceof Traversable) {
            throw new Exception\InvalidArgumentException(
                sprintf('Argument passed to %s::%s() must be an array, '
                . 'implement the \Traversable interface, or be an '
                . 'instance of Zend\Config\Config. %s given.',
                __CLASS__, __METHOD__, gettype($extraConfig))
            );
        }
        $this->extraConfig = $extraConfig;
        return $this;
    }

    /**
     * Check if the config cache is enabled
     *
     * @return bool
     */
    public function getConfigCacheEnabled()
    {
        return $this->configCacheEnabled;
    }

    /**
     * Set if the config cache should be enabled or not
     *
     * @param  bool $enabled
     * @return ListenerOptions
     */
    public function setConfigCacheEnabled($enabled)
    {
        $this->configCacheEnabled = (bool) $enabled;
        return $this;
    }

    /**
     * Get key used to create the cache file name
     *
     * @return string
     */
    public function getConfigCacheKey()
    {
        return (string) $this->configCacheKey;
    }

    /**
     * Set key used to create the cache file name
     *
     * @param  string $configCacheKey the value to be set
     * @return ListenerOptions
     */
    public function setConfigCacheKey($configCacheKey)
    {
        $this->configCacheKey = $configCacheKey;
        return $this;
    }

    /**
     * Get the path to the config cache
     *
     * Should this be an option, or should the dir option include the
     * filename, or should it simply remain hard-coded? Thoughts?
     *
     * @return string
     */
    public function getConfigCacheFile()
    {
        return $this->getCacheDir() . '/module-config-cache.' . $this->getConfigCacheKey().'.php';
    }

    /**
     * Get the path where cache file(s) are stored
     *
     * @return string
     */
    public function getCacheDir()
    {
        return $this->cacheDir;
    }

    /**
     * Set the path where cache files can be stored
     *
     * @param  string $cacheDir the value to be set
     * @return ListenerOptions
     */
    public function setCacheDir($cacheDir)
    {
        if (null === $cacheDir) {
            $this->cacheDir = $cacheDir;
        } else {
            $this->cacheDir = static::normalizePath($cacheDir);
        }
        return $this;
    }

    /**
     * Normalize a path for insertion in the stack
     *
     * @param  string $path
     * @return string
     */
    public static function normalizePath($path)
    {
        $path = rtrim($path, '/');
        $path = rtrim($path, '\\');
        return $path;
    }
}
