<?php

namespace Zf2Mvc;

use Zend\Loader\AutoLoaderFactory;

/**
 * Class detailing metadata about this module
 *
 * Ideas include exposing DI definitions, event listeners, configuration, etc.
 */
class Module
{
    public function init()
    {
        $this->initAutoloader();
    }

    protected function initAutoloader()
    {
        require __DIR__ . '/autoload_register.php';
    }
}
