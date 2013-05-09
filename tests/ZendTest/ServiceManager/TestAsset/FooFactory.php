<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\ServiceManager\TestAsset;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FooFactory implements FactoryInterface, MutableCreationOptionsInterface
{
    protected $creationOptions;

    public function __construct(array $creationOptions = array())
    {
        $this->creationOptions = $creationOptions;
    }

    public function setCreationOptions(array $creationOptions)
    {
        $this->creationOptions = $creationOptions;
    }

    public function getCreationOptions()
    {
        return $this->creationOptions;
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new Foo;
    }
}
