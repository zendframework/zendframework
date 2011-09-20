<?php

namespace Zf2Module;

use SplFileInfo,
    Traversable;

class ModuleLoader implements ModuleResolver
{

    /**
     * @var array An array of module paths to scan
     */
    protected $paths = array();

    /**
     * @var array An array of Module class names of loaded modules
     */
    protected $loadedModules = array();

    /**
     * __construct 
     * 
     * @param array|Traversable $paths 
     */
    public function __construct($paths = null)
    {
        $this->registerPaths($paths);
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
                'Parameter to \\Zf2Module\\ModuleLoader\'s '
                . 'registerPaths methos must be an array or '
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
     * Resolves and loads a module based on name, ensures that it's 
     * Module.php file has been included/required, and returns
     * the full class name of the module's Module class.
     * 
     * @param string $moduleName 
     * @return string The Module class name, which is now loaded
     */
    public function load($moduleName)
    {
        if (!isset($this->loadedModules[$moduleName])) {
            $moduleClass = null;
            foreach ($this->paths as $path) {
                $file = new SplFileInfo($path . $moduleName . '/Module.php');
                if ($file->isReadable()) {
                    require_once $file->getRealPath();
                    $moduleClass = $moduleName . '\Module';
                } else {
                    $file = new SplFileInfo($path . $moduleName);
                    if ($file->isReadable() && $file->isFile()) {
                        require_once $file->getRealPath();
                        if (strstr($moduleName, '.') !== false) {
                            $moduleName = explode('.', $moduleName);
                            $moduleName = array_shift($moduleName);
                        }
                        $moduleClass = $moduleName . '\Module';
                    }
                }
            }
            if (!class_exists($moduleClass)) {
                throw new \Exception(sprintf(
                    'Unable to load module \'%s\' from module path (%s)',
                    $moduleName, implode(':', $this->paths)
                ));
            }
            $this->loadedModules[$moduleName] = $moduleClass;
        }
        return $this->loadedModules[$moduleName];
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
