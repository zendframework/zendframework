<?php

namespace Zend\Loader;

use SplFileInfo,
    Zend\Loader\SplAutoloader, // @TODO: Remove once ported to the _real_ Zend\Loader namespace
    Traversable;

class ModuleAutoloader implements SplAutoloader
{
    /**
     * @var array An array of module paths to scan
     */
    protected $paths = array();

    /**
     * Constructor
     *
     * Allow configuration of the autoloader via the constructor.
     * 
     * @param  null|array|Traversable $options 
     * @return void
     */
    public function __construct($options = null)
    {
        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    /**
     * Configure the autoloader
     *
     * In most cases, $options should be either an associative array or 
     * Traversable object.
     * 
     * @param  array|Traversable $options 
     * @return SplAutoloader
     */
    public function setOptions($options)
    {
        $this->registerPaths($options);
    }

    /**
     * Autoload a class
     *
     * @param   $class
     * @return  mixed
     *          False [if unable to load $class]
     *          get_class($class) [if $class is successfully loaded]
     */
    public function autoload($class)
    {
        // Limit scope of this autoloader
        if (substr($class, -7) !== '\Module') {
            return false;
        }
        $moduleClassPath = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

        foreach ($this->paths as $path) {
            $file = new SplFileInfo($path . $moduleClassPath);
            if ($file->isReadable()) {
                // Found directory with Module.php in it
                require_once $file->getRealPath();
                return $class;
            } 
            // No directory with Module.php, searching for phars
            $moduleName = substr($class, 0, strpos($class, '\\'));
            $matches = glob($path . $moduleName . '.*phar*');
            if (count($matches) == 0) {
                continue;
            }
            foreach ($matches as $phar) {
                $file = new SplFileInfo($phar);
                if ($file->isReadable() && $file->isFile()) {
                    require_once $file->getRealPath();
                    return $class;
                }
            }
        }
        return false;
    }

    /**
     * Register the autoloader with spl_autoload registry
     * 
     * @return void
     */
    public function register()
    {
        spl_autoload_register(array($this, 'autoload'));
    }

    /**
     * registerPaths 
     * 
     * @param array|Traversable $paths 
     * @return ModuleLoader
     */
    public function registerPaths($paths)
    {
        if (is_array($paths) || $paths instanceof Traversable) {
            foreach ($paths as $path) {
                $this->registerPath($path);
            } 
        } else {
            throw new \InvalidArgumentException(
                'Parameter to \\Zend\\Loader\\ModuleAutoloader\'s '
                . 'registerPaths method must be an array or '
                . 'implement the \\Traversable interface'
            );
        }
        return $this;
    }

    /**
     * registerPath 
     * 
     * @param string $path 
     * @return ModuleLoader
     */
    public function registerPath($path)
    {
        if (!is_string($path)) {
            throw new \Exception(sprintf(
                'Invalid path provided; must be a string, received %s',
                gettype($path)
            ));
        }
        $this->paths[] = static::normalizePath($path);
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
        $path .= DIRECTORY_SEPARATOR;
        return $path;
    }
}
