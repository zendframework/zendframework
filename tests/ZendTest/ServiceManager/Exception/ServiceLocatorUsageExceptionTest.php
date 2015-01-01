<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\ServiceManager\Exception;

use Exception;
use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\Exception\ServiceLocatorUsageException;

/**
 * Tests for {@see \Zend\ServiceManager\Exception\ServiceLocatorUsageException}
 *
 * @covers \Zend\ServiceManager\Exception\ServiceLocatorUsageException
 */
class ServiceLocatorUsageExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testFromInvalidPluginManagerRequestedServiceName()
    {
        /* @var $pluginManager \Zend\ServiceManager\AbstractPluginManager */
        $pluginManager     = $this->getMockForAbstractClass('Zend\ServiceManager\AbstractPluginManager');
        /* @var $serviceLocator \Zend\ServiceManager\ServiceLocatorInterface */
        $serviceLocator    = $this->getMockForAbstractClass('Zend\ServiceManager\ServiceLocatorInterface');
        $previousException = new Exception();

        $exception = ServiceLocatorUsageException::fromInvalidPluginManagerRequestedServiceName(
            $pluginManager,
            $serviceLocator,
            'the-service',
            $previousException
        );

        $this->assertInstanceOf('Zend\ServiceManager\Exception\ServiceLocatorUsageException', $exception);
        $this->assertInstanceOf(
            'Zend\ServiceManager\Exception\ServiceNotFoundException',
            $exception,
            'Must be a ServiceNotFoundException for BC compatibility with older try-catch logic'
        );
        $this->assertSame($previousException, $exception->getPrevious());

        $expectedMessageFormat = <<<'MESSAGE'
Service "the-service" has been requested to plugin manager of type "%a", but couldn't be retrieved.
A previous exception of type "Exception" has been raised in the process.
By the way, a service with the name "the-service" has been found in the parent service locator "%a": did you forget to use $parentLocator = $serviceLocator->getServiceLocator() in your factory code?
MESSAGE;

        $this->assertStringMatchesFormat($expectedMessageFormat, $exception->getMessage());
    }
}
