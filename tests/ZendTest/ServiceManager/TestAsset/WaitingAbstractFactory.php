<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\ServiceManager\TestAsset;

use stdClass;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class WaitingAbstractFactory implements AbstractFactoryInterface
{
    public $waitingService = null;

    public $canCreateCallCount = 0;

    public $createNullService = false;

    public $throwExceptionWhenCreate = false;

    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $this->canCreateCallCount++;
        return $requestedName === $this->waitingService;
    }

    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if ($this->throwExceptionWhenCreate) {
            throw new FooException('E');
        }
        if ($this->createNullService) {
            return null;
        }
        return new stdClass;
    }
}
