<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @author    Chris Raidler <chris@raidler.com>
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Config;

use Zend\Config\AbstractConfigFactory;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager;

/**
 * Class AbstractConfigFactoryTest
 */
class AbstractConfigFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $config;

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
        $this->config = array(
            'MyModule' => array(
                'foo' => array(
                    'bar'
                )
            ),
            'phly-blog' => array(
                'foo' => array(
                    'bar'
                )
            )
        );

        $sm = $this->serviceManager = new ServiceManager\ServiceManager(
            new ServiceManagerConfig(array(
            'abstract_factories' => array(
                'Zend\Config\AbstractConfigFactory',
            )
            ))
        );

        $sm->setService('Config', $this->config);
    }

    /**
     * @expectedException InvalidArgumentException
     * @return void
     */
    public function testInvalidPattern()
    {
        $factory = new AbstractConfigFactory();
        $factory->addPattern(new \stdClass());
    }

    /**
     * @expectedException InvalidArgumentException
     * @return void
     */
    public function testInvalidPatternIterator()
    {
        $factory = new AbstractConfigFactory();
        $factory->addPatterns('invalid');
    }

    /**
     * @return void
     */
    public function testPatterns()
    {
        $factory = new AbstractConfigFactory();
        $defaults = $factory->getPatterns();

        // Tests that the accessor returns an array
        $this->assertInternalType('array', $defaults);
        $this->assertGreaterThan(0, count($defaults));

        // Tests adding a single pattern
        $this->assertSame($factory, $factory->addPattern('#foobarone#i'));
        $this->assertCount(count($defaults) + 1, $factory->getPatterns());

        // Tests adding multiple patterns at once
        $patterns = $factory->getPatterns();
        $this->assertSame($factory, $factory->addPatterns(array('#foobartwo#i', '#foobarthree#i')));
        $this->assertCount(count($patterns) + 2, $factory->getPatterns());

        // Tests whether the latest added pattern is the first in stack
        $patterns = $factory->getPatterns();
        $this->assertSame('#foobarthree#i', $patterns[0]);
    }

    /**
     * @return void
     */
    public function testCanCreateService()
    {
        $factory = new AbstractConfigFactory();
        $serviceLocator = $this->serviceManager;

        $this->assertFalse($factory->canCreateServiceWithName($serviceLocator, 'mymodulefail', 'MyModule\Fail'));
        $this->assertTrue($factory->canCreateServiceWithName($serviceLocator, 'mymoduleconfig', 'MyModule\Config'));
    }

    /**
     * @depends testCanCreateService
     * @return void
     */
    public function testCreateService()
    {
        $serviceLocator = $this->serviceManager;
        $this->assertInternalType('array', $serviceLocator->get('MyModule\Config'));
        $this->assertInternalType('array', $serviceLocator->get('MyModule_Config'));
        $this->assertInternalType('array', $serviceLocator->get('Config.MyModule'));
        $this->assertInternalType('array', $serviceLocator->get('phly-blog.config'));
        $this->assertInternalType('array', $serviceLocator->get('phly-blog-config'));
        $this->assertInternalType('array', $serviceLocator->get('config-phly-blog'));
    }

    /**
     * @depends testCreateService
     * @return void
     */
    public function testCreateServiceWithRequestedConfigKey()
    {
        $serviceLocator = $this->serviceManager;
        $this->assertSame($this->config['MyModule'], $serviceLocator->get('MyModule\Config'));
        $this->assertSame($this->config['phly-blog'], $serviceLocator->get('phly-blog-config'));
    }
}
