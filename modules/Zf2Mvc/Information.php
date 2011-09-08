<?php

namespace Zf2Mvc;

use Zend\Loader\AutoLoaderFactory;

/**
 * Class detailing metadata about this module
 *
 * Ideas include exposing DI definitions, event handlers, configuration, etc.
 */
class Information
{
    public function init()
    {
        $this->initAutoloader();
    }

    protected function initAutoloader()
    {
        AutoloaderFactory::factory(array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/classmap.php',
            )
        ));
    }
}
