<?php

namespace Bar;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'BarObject' => function($sm) {
                    $foo      = $sm->get('FooObject');
                    $foo->bar = 'baz';
                    return $foo;
                }
            ),
        );
    }
}
