<?php

namespace Zend\Module\Listener;

use Zend\Loader\AutoloaderFactory,
    Zend\Module\Consumer\AutoloaderProvider;

class AutoloaderListener extends AbstractListener
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
