<?php

namespace Zf2Module;

class ModuleCollection
{
    /**
     * @var ModuleResolver
     */
    protected $loader;

    /**
     * getLoader 
     * 
     * @return ModuleResolver
     */
    public function getLoader()
    {
        if (!$this->loader instanceof ModuleResolver) {
            $this->setLoader(new ModuleLoader);
        }
        return $this->loader;
    }

    /**
     * setLoader 
     * 
     * @param ModuleResolver $loader 
     * @return ModuleCollection
     */
    public function setLoader(ModuleResolver $loader)
    {
        $this->loader = $loader;
        return $this;
    }
}
