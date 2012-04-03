<?php

namespace Zend\Module\Listener;

class ModuleResolverListener extends AbstractListener
{
    /**
     * @param $e
     * @return object
     */
    public function __invoke($e)
    {
        $moduleName = $e->getModuleName();
        $class = $moduleName . '\Module';
        if (!class_exists($class)) {
            return false;
        }
        $module = new $class;
        return $module;
    }
}
