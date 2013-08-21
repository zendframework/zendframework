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
use Zend\ServiceManager;

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

        $sm->setService('Config', $config);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidPattern()
    {
        $factory = new AbstractConfigFactory();
        $factory->addPattern(new \stdClass());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidPatternIterator()
    {
        $factory = new AbstractConfigFactory();
        $factory->addPatterns('invalid');
    }

    public function testPatterns()
    {
        $factory = new AbstractConfigFactory();
        $defaults = $factory->getPatterns();

        // Tests that the accessor returns an array
        $this->assertInternalType('array', $defaults);
        $this->assertGreaterThan(0, count($defaults));

        // Tests adding a single pattern
        $this->assertSame($factory, $factory->addPattern('#foobarone#i'));
        $this->assertEquals(count($defaults) + 1, $factory->getPatterns());

        // Tests adding multiple patterns at once
        $patterns = $factory->getPatterns();
        $this->assertSame($factory, $factory->addPatterns(array('#foobartwo#i', '#foobarthree#i')));
        $this->assertEquals(count($patterns + 2), count($factory->getPatterns()));

        // Tests whether the latest added pattern is the first in stack
        $patterns = $factory->getPatterns();
        $this->assertSame('#foobarthree#i', $patterns[0]);
    }

    /**
     * @dataProvider provideServiceLocator
     */
    public function testCanCreateService($serviceLocator)
    {
        $factory = new AbstractConfigFactory();

        $this->assertFalse($factory->canCreateServiceWithName($serviceLocator, 'mymodulefail', 'MyModule\Fail'));
        $this->assertTrue($factory->canCreateServiceWithName($serviceLocator, 'mymoduleconfig', 'MyModule\Config'));
    }

    /**
     * @depends testCanCreateService
     * @dataProvider provideServiceLocator
     */
    public function testCreateService($serviceLocator)
    {
        $this->assertInstanceOf('Zend\Config\Config', $serviceLocator->get('MyModule\Config'));
        $this->assertInstanceOf('Zend\Config\Config', $serviceLocator->get('MyModule_Config'));
        $this->assertInstanceOf('Zend\Config\Config', $serviceLocator->get('Config.MyModule'));
        $this->assertInstanceOf('Zend\Config\Config', $serviceLocator->get('phly-blog.config'));
        $this->assertInstanceOf('Zend\Config\Config', $serviceLocator->get('phly-blog-config'));
        $this->assertInstanceOf('Zend\Config\Config', $serviceLocator->get('config-phly-blog'));
    }

    /**
     * @return ServiceManager\ServiceLocatorInterface
     */
    public function provideServiceLocator()
    {
        return $this->serviceManager;
    }
}