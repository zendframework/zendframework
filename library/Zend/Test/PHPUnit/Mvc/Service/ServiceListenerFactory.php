<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Zend\Test\PHPUnit\Mvc\Service;

use Zend\Mvc\Service\ServiceListenerFactory as BaseServiceListenerFactory;

class ServiceListenerFactory extends BaseServiceListenerFactory
{
    /**
     * Create default service configuration
     */
    public function __construct()
    {
        // merge basee config with specific tests config
        $this->defaultServiceConfig = array_replace_recursive(
            $this->defaultServiceConfig,
            array('factories' => array(
                'Request' => function($sm) {
                    return new \Zend\Http\PhpEnvironment\Request();
                },
                'Response' => function($sm) {
                    return new \Zend\Http\PhpEnvironment\Response();
                },
                'Router' => 'Zend\Test\PHPUnit\Mvc\Service\RouterFactory',
                'ViewManager' => function($sm) {
                    return new \Zend\Mvc\View\Http\ViewManager();
                },
            ))
        );
    }
}
