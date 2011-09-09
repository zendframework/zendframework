<?php

namespace Zf2Module;

interface ModuleResolver
{
    /**
     * registerPaths 
     * 
     * @param array $paths 
     * @return void
     */
    public function registerPaths(array $paths);

    /**
     * registerPath 
     * 
     * @param string $path 
     * @return void
     */
    public function registerPath($path);

    /**
     * Resolves and loads a module based on name, ensures that it's 
     * Module.php file has been included/required, and returns
     * the full class name of the module's Module class.
     * 
     * @param string $moduleName 
     * @return string
     */
    public function load($moduleName);
}
