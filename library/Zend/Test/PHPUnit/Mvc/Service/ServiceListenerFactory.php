<?php

namespace Zend\Test\PHPUnit\Mvc\Service;

use Zend\Mvc\Service\ServiceListenerFactory as BaseServiceListenerFactory;

class ServiceListenerFactory extends BaseServiceListenerFactory
{
    protected $testServiceConfig = array(
        'invokables' => array(
            'Request'          => 'Zend\Http\PhpEnvironment\Request',
            'Response'         => 'Zend\Http\PhpEnvironment\Response',
            'ViewManager'      => 'Zend\Mvc\View\Http\ViewManager',
        ),
        'factories' => array(
            'Router'           => 'Zend\Test\PHPUnit\Mvc\Service\RouterFactory',
        ),
    );

    public function __construct()
    {
        // merge basee config with specific tests config
        $this->defaultServiceConfig = array_replace_recursive(
            $this->defaultServiceConfig, $this->testServiceConfig
        );

        // delete the factory which has moved
        unset($this->defaultServiceConfig['factories']['Request']);
        unset($this->defaultServiceConfig['factories']['Response']);
        unset($this->defaultServiceConfig['factories']['ViewManager']);
    }
}
