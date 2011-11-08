<?php

namespace Zend\Module\Listener;

use Zend\Loader\AutoloaderFactory,
    Consumer\AutoloaderProvider;

class AutoloaderTrigger
{
    public function __invoke($e)
    {
        $module = $e->getParam('module');
        if (!$module instanceof AutoloaderProvider) {
            return;
        }
        $autoloaderConfig = $module->getAutoloaderConfig();
        AutoloaderFactory::factory($autoloaderConfig);
    }
}
