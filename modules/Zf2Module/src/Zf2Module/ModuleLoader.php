<?php

namespace Zf2Module;

use SplFileInfo;

class ModuleLoader implements ModuleResolver
{

    /**
     * @var array An array of module paths to scan
     */
    protected $paths = array();

    /**
     * @var array An array of Module class names of loaded modules
     */
    protected $modules = array();

    /**
     * registerPaths 
     * 
     * @param array $paths 
     * @return ModuleLoader
     */
    public function registerPaths(array $paths)
    {
        foreach ($paths as $path) {
            $this->registerPath($path);
        } 
        return $this;
    }

    /**
     * registerPath 
     * 
     * @param mixed $path 
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
     * @return string
     */
    public function load($moduleName)
    {
        if (!isset($this->modules[$moduleName])) {
            $this->modules[$moduleName] = $this->resolveModule($moduleName);
        }
        return $this->modules[$moduleName];
    }

    protected function resolveModule($moduleName)
    {
        foreach ($this->paths as $path) {
            $file = new SplFileInfo($path . $moduleName . '/Module.php');
            if ($file->isReadable()) {
                require_once $file->getRealPath();
                return $moduleName . '\Module';
            }
        }
        throw new \Exception(sprintf(
            'Unable to load module \'%s\' from module path (%s)',
            $moduleName, implode(':', $this->paths)
        ));
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
