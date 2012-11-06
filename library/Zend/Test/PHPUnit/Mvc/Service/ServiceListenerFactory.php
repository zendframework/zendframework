<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Test
 */
namespace Zend\Test\PHPUnit\Mvc\Service;

use Zend\Mvc\Service\ServiceListenerFactory as BaseServiceListenerFactory;

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage PHPUnit
 */
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
