<?php

namespace Zf2Module;

class ModuleLoader implements ModuleResolver
{

    /**
     * @var array An array of module paths to scan
     */
    protected $paths = array();

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
            throw new Exception(sprintf(
                'Invalid path provided; must be a string, received %s',
                gettype($path)
            ));
        }
        $this->paths[] = static::normalizePath($path);
        return $this;
    }

    /**
     * Resolves and loads a module based on name, ensures that it's 
     * Information.php file has been included/required, and returns
     * the full class name of the module's Information class.
     * 
     * @param string $moduleName 
     * @return string
     */
    public function load($moduleName)
    {
    }
}
