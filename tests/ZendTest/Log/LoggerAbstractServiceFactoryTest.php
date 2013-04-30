<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */

namespace ZendTest\Log;

use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Service\ServiceManagerConfig;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @group      Zend_Log
 */
class LoggerAbstractServiceFactoryTeset extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceManager;

    /**
     * Set up LoggerAbstractServiceFactory and loggers configuration.
     *
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->serviceManager = new ServiceManager(new ServiceManagerConfig(array(
            'abstract_factories' => array('Zend\Log\LoggerAbstractServiceFactory'),
        )));

        $this->serviceManager->setService('Config', array(
            'log' => array(
                'Application\Frontend' => array(),
                'Application\Backend'  => array(),
            ),
        ));
    }

    /**
     * @return array
     */
    public function providerValidLoggerService()
    {
        return array(
            array('Application\Frontend'),
            array('Application\Backend'),
        );
    }

    /**
     * @return array
     */
    public function providerInvalidLoggerService()
    {
        return array(
            array('Logger\Application\Unknown'),
            array('Logger\Application\Frontend'),
            array('Application\Backend\Logger'),
        );
    }

    /**
     * @param string $service
     * @dataProvider providerValidLoggerService
     */
    public function testValidLoggerService($service)
    {
        $actual = $this->serviceManager->get($service);
        $this->assertInstanceOf('Zend\Log\Logger', $actual);
    }

    /**
     * @param string $service
     * @dataProvider providerInvalidLoggerService
     * @expectedException \Zend\ServiceManager\Exception\ServiceNotFoundException
     */
    public function testInvalidLoggerService($service)
    {
        $actual = $this->serviceManager->get($service);
    }
}
