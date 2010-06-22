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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Application;

/**
 * @uses       \Zend\Loader\Autoloader
 * @category   Zend
 * @package    Zend_Application
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Application
{
    /**
     * Autoloader to use
     *
     * @var \Zend\Loader\Autoloader
     */
    protected $_autoloader;

    /**
     * Bootstrap
     *
     * @var \Zend\Application\AbstractBootstrap
     */
    protected $_bootstrap;

    /**
     * Application environment
     *
     * @var string
     */
    protected $_environment;

    /**
     * Flattened (lowercase) option keys
     *
     * @var array
     */
    protected $_optionKeys = array();

    /**
     * Options for Zend_Application
     *
     * @var array
     */
    protected $_options = array();

    /**
     * Constructor
     *
     * Initialize application. Potentially initializes include_paths, PHP
     * settings, and bootstrap class.
     *
     * @param  string                   $environment
     * @param  string|array|\Zend\Config\Config $options String path to configuration file, or array/\Zend\Config\Config of configuration options
     * @throws \Zend\Application\Exception When invalid options are provided
     * @return void
     */
    public function __construct($environment, $options = null)
    {
        $this->_environment = (string) $environment;

        require_once 'Zend/Loader/Autoloader.php';
        $this->_autoloader = \Zend\Loader\Autoloader::getInstance();

        if (null !== $options) {
            if (is_string($options)) {
                $options = $this->_loadConfig($options);
            } elseif ($options instanceof \Zend\Config\Config) {
                $options = $options->toArray();
            } elseif (!is_array($options)) {
                throw new Exception('Invalid options provided; must be location of config file, a config object, or an array');
            }

            $this->setOptions($options);
        }
    }

    /**
     * Retrieve current environment
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->_environment;
    }

    /**
     * Retrieve autoloader instance
     *
     * @return \Zend\Loader\Autoloader
     */
    public function getAutoloader()
    {
        return $this->_autoloader;
    }

    /**
     * Set application options
     *
     * @param  array $options
     * @throws \Zend\Application\Exception When no bootstrap path is provided
     * @throws \Zend\Application\Exception When invalid bootstrap information are provided
     * @return \Zend\Application\Application
     */
    public function setOptions(array $options)
    {
        if (!empty($options['config'])) {
            if (is_array($options['config'])) {
                $_options = array();
                foreach ($options['config'] as $tmp) {
                    $_options = $this->mergeOptions($_options, $this->_loadConfig($tmp));
                }
                $options = $this->mergeOptions($_options, $options);
            } else {
                $options = $this->mergeOptions($this->_loadConfig($options['config']), $options);
            }
        }

        $this->_options = $options;

        $options = array_change_key_case($options, CASE_LOWER);

        $this->_optionKeys = array_keys($options);

        if (!empty($options['phpsettings'])) {
            $this->setPhpSettings($options['phpsettings']);
        }

        if (!empty($options['includepaths'])) {
            $this->setIncludePaths($options['includepaths']);
        }

        if (!empty($options['autoloadernamespaces'])) {
            $this->setAutoloaderNamespaces($options['autoloadernamespaces']);
        }

        if (!empty($options['autoloaderzfpath'])) {
            $autoloader = $this->getAutoloader();
            if (method_exists($autoloader, 'setZfPath')) {
                $zfPath    = $options['autoloaderzfpath'];
                $zfVersion = !empty($options['autoloaderzfversion'])
                           ? $options['autoloaderzfversion']
                           : 'latest';
                $autoloader->setZfPath($zfPath, $zfVersion);
            }
        }

        if (!empty($options['bootstrap'])) {
            $bootstrap = $options['bootstrap'];

            if (is_string($bootstrap)) {
                $this->setBootstrap($bootstrap);
            } elseif (is_array($bootstrap)) {
                if (empty($bootstrap['path'])) {
                    throw new Exception('No bootstrap path provided');
                }

                $path  = $bootstrap['path'];
                $class = null;

                if (!empty($bootstrap['class'])) {
                    $class = $bootstrap['class'];
                }

                $this->setBootstrap($path, $class);
            } else {
                throw new Exception('Invalid bootstrap information provided');
            }
        }

        return $this;
    }

    /**
     * Retrieve application options (for caching)
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Is an option present?
     *
     * @param  string $key
     * @return bool
     */
    public function hasOption($key)
    {
        return in_array(strtolower($key), $this->_optionKeys);
    }

    /**
     * Retrieve a single option
     *
     * @param  string $key
     * @return mixed
     */
    public function getOption($key)
    {
        if ($this->hasOption($key)) {
            $options = $this->getOptions();
            $options = array_change_key_case($options, CASE_LOWER);
            return $options[strtolower($key)];
        }
        return null;
    }

    /**
     * Merge options recursively
     *
     * @param  array $array1
     * @param  mixed $array2
     * @return array
     */
    public function mergeOptions(array $array1, $array2 = null)
    {
        if (is_array($array2)) {
            foreach ($array2 as $key => $val) {
                if (is_array($array2[$key])) {
                    $array1[$key] = (array_key_exists($key, $array1) && is_array($array1[$key]))
                                  ? $this->mergeOptions($array1[$key], $array2[$key])
                                  : $array2[$key];
                } else {
                    $array1[$key] = $val;
                }
            }
        }
        return $array1;
    }

    /**
     * Set PHP configuration settings
     *
     * @param  array $settings
     * @param  string $prefix Key prefix to prepend to array values (used to map . separated INI values)
     * @return \Zend\Application\Application
     */
    public function setPhpSettings(array $settings, $prefix = '')
    {
        foreach ($settings as $key => $value) {
            $key = empty($prefix) ? $key : $prefix . $key;
            if (is_scalar($value)) {
                ini_set($key, $value);
            } elseif (is_array($value)) {
                $this->setPhpSettings($value, $key . '.');
            }
        }

        return $this;
    }

    /**
     * Set include path
     *
     * @param  array $paths
     * @return \Zend\Application\Application
     */
    public function setIncludePaths(array $paths)
    {
        $path = implode(PATH_SEPARATOR, $paths);
        set_include_path($path . PATH_SEPARATOR . get_include_path());
        return $this;
    }

    /**
     * Set autoloader namespaces
     *
     * @param  array $namespaces
     * @return \Zend\Application\Application
     */
    public function setAutoloaderNamespaces(array $namespaces)
    {
        $autoloader = $this->getAutoloader();

        foreach ($namespaces as $namespace) {
            $autoloader->registerNamespace($namespace);
        }

        return $this;
    }

    /**
     * Set bootstrap path/class
     *
     * @param  string $path
     * @param  string $class
     * @return \Zend\Application\Application
     */
    public function setBootstrap($path, $class = null)
    {
        // setOptions() can potentially send a null value; specify default
        // here
        if (null === $class) {
            $class = 'Bootstrap';
        }

        if (!class_exists($class, false)) {
            require_once $path;
            if (!class_exists($class, false)) {
                throw new Exception('Bootstrap class not found');
            }
        }
        $this->_bootstrap = new $class($this);

        if (!$this->_bootstrap instanceof Bootstrapper) {
            throw new Exception('Bootstrap class does not implement Zend\\Application\\Bootstrapper');
        }

        return $this;
    }

    /**
     * Get bootstrap object
     *
     * @return \Zend\Application\AbstractBootstrap
     */
    public function getBootstrap()
    {
        if (null === $this->_bootstrap) {
            $this->_bootstrap = new Bootstrap($this);
        }
        return $this->_bootstrap;
    }

    /**
     * Bootstrap application
     *
     * @param  null|string|array $resource
     * @return \Zend\Application\Application
     */
    public function bootstrap($resource = null)
    {
        $this->getBootstrap()->bootstrap($resource);
        return $this;
    }

    /**
     * Run the application
     *
     * @return void
     */
    public function run()
    {
        $this->getBootstrap()->run();
    }

    /**
     * Load configuration file of options
     *
     * @param  string $file
     * @throws \Zend\Application\Exception When invalid configuration file is provided
     * @return array
     */
    protected function _loadConfig($file)
    {
        $environment = $this->getEnvironment();
        $suffix      = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        switch ($suffix) {
            case 'ini':
                $config = new \Zend\Config\Ini($file, $environment);
                break;

            case 'xml':
                $config = new \Zend\Config\Xml($file, $environment);
                break;

            case 'php':
            case 'inc':
                $config = include $file;
                if (!is_array($config)) {
                    throw new Exception('Invalid configuration file provided; PHP file does not return array value');
                }
                return $config;
                break;

            default:
                throw new Exception('Invalid configuration file provided; unknown config type');
        }

        return $config->toArray();
    }
}
