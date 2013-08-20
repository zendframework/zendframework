<?php
/**
 * AbstractFactoryTest.php
 *
 * @author Chris Raidler <chris@raidler.com>
 * @copyright Copyright 2012 - 2013, raidler dot com
 */
namespace ZendTest\Config;

use Zend\Config\AbstractConfigFactory;
use Zend\Config\Config;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;

/**
 * Class AbstractFactoryTest
 */
class AbstractFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\Mvc\Application
     */
    protected $application;

    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    /**
     * @return void
     */
    public function setUp()
    {
        $config = array(
            'MyModule' => array(
                'foo' => array(
                    'bar'
                )
            )
        );

        $sm = $this->serviceManager = new ServiceManager(
            new ServiceManagerConfig(array(
                'abstract_factories' => array(
                    'Zend\Config\AbstractConfigFactory',
                )
            ))
        );

        $sm->setService('Config', $config);
    }

    public function testCanCreateService()
    {
        $sm = $this->serviceManager;
        $factory = new AbstractConfigFactory();

        $this->assertFalse($factory->canCreateServiceWithName($sm, 'mymodulefail', 'MyModule\Fail'));
        $this->assertTrue($factory->canCreateServiceWithName($sm, 'mymoduleconfig', 'MyModule\Config'));
    }

    /**
     * @depends testCanCreateService
     */
    public function testCreateService()
    {
        $sm = $this->serviceManager;
        $this->assertInstanceOf('Zend\Config\Config', $sm->get('MyModule\Config'));
    }

    /**
     * @depends testCreateService
     */
    public function testServiceManager()
    {
        $sm = $this->serviceManager;
        $this->assertInstanceOf('Zend\Config\Config', $sm->get('MyModule\Config'));
    }
}