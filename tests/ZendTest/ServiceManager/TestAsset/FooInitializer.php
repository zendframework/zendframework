<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ServiceManager
 */

namespace ZendTest\ServiceManager\TestAsset;

use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FooInitializer implements InitializerInterface
{
    public $sm;

    protected $var;

    public function __construct($var = null)
    {
        if ($var) {
            $this->var = $var;
        }
    }

    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        $this->sm = $serviceLocator;
        if ($this->var) {
            list($key, $value) = each($this->var);
            $instance->{$key} = $value;
        }
    }
}
