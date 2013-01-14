<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZendTest\Mvc\Controller\TestAsset;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ControllerLoaderAbstractFactory implements AbstractFactoryInterface
{
    protected $classmap = array(
        'path' => 'ZendTest\Mvc\TestAsset\PathController',
    );

    public function canCreateServiceWithName(ServiceLocatorInterface $sl, $cName, $rName)
    {
        $classname = $this->classmap[$cName];
        return class_exists($classname);
    }

    public function createServiceWithName(ServiceLocatorInterface $sl, $cName, $rName)
    {
        $classname = $this->classmap[$cName];
        $controller = new $classname;
        return $controller;
    }
}
