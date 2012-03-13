<?php

namespace ListenerTestModule;

use Zend\Module\Consumer\AutoloaderProvider,
    Zend\Module\Consumer\LocatorRegistered;

class Module implements AutoloaderProvider, LocatorRegistered
{
    public $initCalled = false;
    public $getConfigCalled = false;
    public $getAutoloaderConfigCalled = false;

    public function init($moduleManager = null)
    {
        $this->initCalled = true;
    }

    public function getConfig()
    {
        $this->getConfigCalled = true;
        return array(
            'listener' => 'test'
        );
    }

    public function getAutoloaderConfig()
    {
        $this->getAutoloaderConfigCalled = true;
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    'Foo' => __DIR__ . '/src/Foo',
                ),
            ),
        );
    }
}
