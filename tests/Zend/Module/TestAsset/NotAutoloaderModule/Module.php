<?php

namespace NotAutoloaderModule;

class Module
{
    public $getAutoloaderConfigCalled = false;

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
