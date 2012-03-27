<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Application;

require_once __DIR__ . '/TestAsset/Zf7696Bootstrap.php';

use Zend\Loader\ResourceAutoloader,
    Zend\Registry,
    Zend\Application,
    Zend\Application\ResourceBroker,
    Zend\Application\Resource\AbstractResource;

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application
 */
class AbstractBootstrapTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->application = new Application\Application('testing');
        $this->error = false;
    }

    public function tearDown()
    {
    }

    public function handleError($errno, $errstr)
    {
        $this->error = $errstr;
        return true;
    }

    public function testConstructorShouldPopulateApplication()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $this->assertSame($this->application, $bootstrap->getApplication());
    }

    public function testConstructorShouldPopulateOptionsFromApplicationObject()
    {
        $options = array(
            'foo' => 'bar',
            'bar' => 'baz',
        );
        $this->application->setOptions($options);
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $this->assertSame($options, $bootstrap->getOptions());
    }

    public function testConstructorShouldAllowPassingAnotherBootstrapObject()
    {
        $bootstrap1 = new TestAsset\ZfAppBootstrap($this->application);
        $bootstrap2 = new TestAsset\ZfAppBootstrap($bootstrap1);
        $this->assertSame($bootstrap1, $bootstrap2->getApplication());
    }

    public function testConstructorShouldRaiseExceptionForInvalidApplicationArgument()
    {
        $this->setExpectedException('Zend\Application\Exception\InvalidArgumentException');
        $bootstrap = new TestAsset\ZfAppBootstrap(new \stdClass);
    }

    public function testSettingOptionsShouldProxyToInternalSetters()
    {
        $options = array(
            'arbitrary' => 'foo',
        );
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $bootstrap->setOptions($options);
        $this->assertEquals('foo', $bootstrap->getArbitrary());
    }

    /**
     * @group ZF-6459
     */
    public function testCallingSetOptionsMultipleTimesShouldMergeOptionsRecursively()
    {
        $options = array(
            'deep' => array(
                'foo' => 'bar',
                'bar' => 'baz',
            ),
        );
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $bootstrap->setOptions($options);
        $options2 = array(
            'deep' => array(
                'bar' => 'bat',
                'baz' => 'foo',
            ),
        );
        $bootstrap->setOptions($options2);
        $expected = $bootstrap->mergeOptions($options, $options2);
        $test     = $bootstrap->getOptions();
        $this->assertEquals($expected, $test);
    }

    public function testResourcesOptionKeyShouldRegisterBootstrapPluginResources()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $bootstrap->setOptions(array(
            'resources' => array(
                'view' => array(),
            ),
        ));
        $this->assertTrue($bootstrap->getBroker()->hasPlugin('view'));
    }

    public function testHasOptionShouldReturnFalseWhenOptionUnavailable()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $this->assertFalse($bootstrap->hasOption('foo'));
    }

    public function testHasOptionShouldReturnTrueWhenOptionPresent()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $bootstrap->setOptions(array('foo' => 'bar'));
        $this->assertTrue($bootstrap->hasOption('foo'));
    }

    public function testGetOptionShouldReturnNullWhenOptionUnavailable()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $this->assertNull($bootstrap->getOption('foo'));
    }

    public function testGetOptionShouldReturnOptionValue()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $bootstrap->setOptions(array('foo' => 'bar'));
        $this->assertEquals('bar', $bootstrap->getOption('foo'));
    }

    public function testInternalIntializersShouldBeRegisteredAsClassResources()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $test      = $bootstrap->getClassResources();
        $resources = array('foo' => '_initFoo', 'bar' => '_initBar', 'barbaz' => '_initBarbaz');
        $this->assertEquals($resources, $test);
    }

    public function testInternalInitializersShouldRegisterResourceNames()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $test      = $bootstrap->getClassResourceNames();
        $resources = array('foo', 'bar', 'barbaz');
        $this->assertEquals($resources, $test);
    }

    public function testShouldAllowRegisteringConcretePluginResources()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $resource  = new Application\Resource\View();
        $bootstrap->getBroker()->register('view', $resource);
        $test = $bootstrap->getBroker()->load('view');
        $this->assertSame($resource, $test);
    }

    public function testRegisteringSecondPluginResourceOfSameTypeShouldOverwrite()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $resource1 = new Application\Resource\View();
        $resource2 = new Application\Resource\View();
        $broker    = $bootstrap->getBroker();
        $broker->register('view', $resource1)
               ->register('view', $resource2);
        $test = $broker->load('view');
        $this->assertSame($resource2, $test);
    }

    public function testShouldAllowSettingAlternativePluginBrokerInstance()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $broker    = new ResourceBroker();
        $bootstrap->setBroker($broker);
        $this->assertSame($broker, $bootstrap->getBroker());
    }

    public function testEnvironmentShouldMatchApplicationEnvironment()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $this->assertSame($this->application->getEnvironment(), $bootstrap->getEnvironment());
    }

    public function testBootstrappingShouldOnlyExecuteEachInitializerOnce()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $bootstrap->bootstrap('foo');
        $bootstrap->bootstrap('foo');
        $this->assertEquals(1, $bootstrap->fooExecuted);
    }

    /**
     * @group ZF-7955
     */
    public function testBootstrappingIsCaseInsensitive()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $bootstrap->bootstrap('Foo');
        $bootstrap->bootstrap('Foo');
        $bootstrap->bootstrap('foo');
        $bootstrap->bootstrap('foo');
        $this->assertEquals(1, $bootstrap->fooExecuted);
    }

    public function testBootstrappingShouldFavorInternalResourcesOverPlugins()
    {
        require_once __DIR__ . '/TestAsset/resources/Foo.php';
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $broker    = $bootstrap->getBroker();
        $broker->getClassLoader()->registerPlugin('foo', 'ZendTest\Application\TestAsset\Resource\Foo');
        $broker->registerSpec('foo');
        $bootstrap->bootstrap('foo');
        $this->assertFalse($bootstrap->executedFooResource);
    }

    public function testBootstrappingShouldAllowPassingAnArrayOfResources()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $bootstrap->bootstrap(array('foo', 'bar'));
        $this->assertEquals(1, $bootstrap->fooExecuted);
        $this->assertEquals(1, $bootstrap->barExecuted);
    }

    public function testPassingNoValuesToBootstrapExecutesAllResources()
    {
        require_once __DIR__ . '/TestAsset/resources/Foobar.php';
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $broker    = $bootstrap->getBroker();
        $broker->getClassLoader()->registerPlugin('foobar', 'ZendTest\Application\TestAsset\Resource\Foobar');
        $broker->registerSpec('foobar');
        set_error_handler(array($this, 'handleError'), E_WARNING);
        $bootstrap->bootstrap();
        restore_error_handler();
        $this->assertEquals(1, $bootstrap->fooExecuted);
        $this->assertEquals(1, $bootstrap->barExecuted);
        $this->assertTrue($bootstrap->executedFoobarResource);
    }

    public function testPassingInvalidResourceArgumentToBootstrapShouldThrowException()
    {
        $this->setExpectedException('Zend\Application\Exception\InvalidArgumentException');
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $bootstrap->bootstrap(new \stdClass);
    }

    public function testPassingUnknownResourceToBootstrapShouldThrowException()
    {
        $this->setExpectedException('Zend\Application\Exception\InvalidArgumentException');
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $bootstrap->bootstrap('bazbat');
    }

    public function testCallShouldOverloadToBootstrap()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $bootstrap->bootstrapFoo();
        $this->assertEquals(1, $bootstrap->fooExecuted);
    }

    public function testCallShouldThrowExceptionForInvalidMethodCall()
    {
        $this->setExpectedException('Zend\Application\Exception\BadMethodCallException');
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $bootstrap->initFoo();
    }

    public function testDependencyTrackingShouldDetectCircularDependencies()
    {
        $this->setExpectedException('Zend\Application\Exception\RuntimeException');
        $bootstrap = new TestAsset\BootstrapBaseCircularDependency($this->application);
        $bootstrap->bootstrap();
    }

    public function testContainerShouldBeRegistryInstanceByDefault()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $container = $bootstrap->getContainer();
        $this->assertTrue($container instanceof Registry);
    }

    public function testContainerShouldAggregateReturnValuesFromClassResources()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $bootstrap->bootstrap('barbaz');
        $container = $bootstrap->getContainer();
        $this->assertEquals('Baz', $container->barbaz->baz);
    }

    public function testContainerShouldAggregateReturnValuesFromPluginResources()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $broker    = $bootstrap->getBroker();
        require_once __DIR__ . '/TestAsset/resources/Baz.php';
        $broker->getClassLoader()->registerPlugin('baz', 'ZendTest\Application\TestAsset\Resource\Baz');
        $broker->registerSpec('baz');
        set_error_handler(array($this, 'handleError'), E_WARNING);
        $bootstrap->bootstrap('baz');
        restore_error_handler();
        $container = $bootstrap->getContainer();
        $this->assertEquals('Baz', $container->baz->baz);
    }

    public function testClassResourcesShouldBeAvailableFollowingBootstrapping()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $bootstrap->bootstrap('barbaz');
        $this->assertTrue($bootstrap->hasResource('barbaz'));

        $resource = $bootstrap->getResource('barbaz');
        $this->assertEquals('Baz', $resource->baz);
    }

    public function testPluginResourcesShouldBeAvailableFollowingBootstrapping()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $broker    = $bootstrap->getBroker();
        require_once __DIR__ . '/TestAsset/resources/Baz.php';
        $broker->getClassLoader()->registerPlugin('baz', 'ZendTest\Application\TestAsset\Resource\Baz');
        $broker->registerSpec('baz');
        $bootstrap->bootstrap('baz');

        $this->assertTrue($bootstrap->hasResource('baz'));
        $resource = $bootstrap->getResource('baz');
        $this->assertEquals('Baz', $resource->baz);
    }

    public function testMagicMethodsForPluginResources()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $broker    = $bootstrap->getBroker();
        require_once __DIR__ . '/TestAsset/resources/Baz.php';
        $broker->getClassLoader()->registerPlugin('baz', 'ZendTest\Application\TestAsset\Resource\Baz');
        $broker->registerSpec('baz');
        $bootstrap->bootstrap('baz');

        $this->assertTrue(isset($bootstrap->baz));
        $resource = $bootstrap->baz;
        $this->assertEquals('Baz', $resource->baz);
    }

    /**
     * @group ZF-6543
     * @group disable
     */
    public function testPassingPluginResourcesByFullClassNameWithMatchingPluginPathShouldRegisterAsShortName()
    {
        $this->application->setOptions(array(
            'resources' => array(
                'ZendTest\Application\TestAsset\Resource\View' => array(),
            ),
            'pluginPaths' => array(
                'ZendTest\Application\TestAsset\Resource' => __DIR__ . '/TestAsset/Resource',
            ),
        ));
        set_error_handler(array($this, 'handleError'), E_WARNING);
        $bootstrap = new Application\Bootstrap($this->application);
        $this->assertTrue($bootstrap->getPlugin()->hasPlugin('View'), var_export(array_keys($bootstrap->getBroker()->getRegisteredPlugins()), 1));
        restore_error_handler();
    }

    /**
     * @group ZF-6543
     * @group disable
     */
    public function testPassingFullViewClassNameNotMatchingARegisteredPrefixShouldRegisterAsTheClassName()
    {
        $this->application->setOptions(array(
            'resources' => array(
                'ZendTest\Application\TestAsset\Resource\View' => array(),
            ),
        ));
        set_error_handler(array($this, 'handleError'));
        $bootstrap = new Application\Bootstrap($this->application);
        $this->assertTrue($bootstrap->getPlugin()->hasPlugin('ZendTest\\Application\\TestAsset\\Resource\\View'));
        restore_error_handler();
    }

    /**
     * @group ZF-6543
     * @group disable
     */
    public function testPassingFullViewClassNameNotMatchingARegisteredPrefixShouldReturnAppropriateResource()
    {
        $this->application->setOptions(array(
            'resources' => array(
                'ZendTest\Application\TestAsset\Resource\View' => array(),
            ),
        ));
        set_error_handler(array($this, 'handleError'));
        $bootstrap = new Application\Bootstrap($this->application);
        $bootstrap->bootstrap('ZendTest\Application\TestAsset\Resource\View');
        $resource = $bootstrap->getResource('ZendTest\Application\TestAsset\Resource\View');
        $this->assertTrue($resource instanceof \ZendTest\Application\TestAsset\Resource\View,
            var_export(array_keys($bootstrap->getBroker()->getRegisteredPlugins()), 1));
        restore_error_handler();
    }

    /**
     * @group ZF-6543
     * @group disable
     */
    public function testCanMixAndMatchPluginResourcesAndFullClassNames()
    {
        $this->application->setOptions(array(
            'resources' => array(
                'ZendTest\Application\TestAsset\Resource\View' => array(),
                'view' => array(),
            ),
        ));
        set_error_handler(array($this, 'handleError'));
        $bootstrap = new Application\Bootstrap($this->application);
        $bootstrap->bootstrap('ZendTest\Application\TestAsset\Resource\View');
        $resource1 = $bootstrap->getResource('ZendTest\Application\TestAsset\Resource\View');
        $bootstrap->bootstrap('view');
        $resource2 = $bootstrap->getResource('view');
        $this->assertNotSame($resource1, $resource2);
        $this->assertTrue($resource1 instanceof \ZendTest\Application\TestAsset\Resource\View,
            var_export(array_keys($bootstrap->getBroker()->getRegisteredPlugins()), 1));
        $this->assertTrue($resource2 instanceof \Zend\View\View);
        restore_error_handler();
    }

    /**
     * @group ZF-6543
     * @group disable
     */
    public function testPluginClassesDefiningExplicitTypeWillBeRegisteredWithThatValue()
    {
        $this->application->setOptions(array(
            'resources' => array(
                'ZendTest\Application\Layout' => array(),
                'layout' => array(),
            ),
        ));
        set_error_handler(array($this, 'handleError'));
        $bootstrap = new Application\Bootstrap($this->application);
        $bootstrap->bootstrap('BootstrapAbstractTestLayout');
        $resource1 = $bootstrap->getResource('BootstrapAbstractTestLayout');
        $bootstrap->bootstrap('layout');
        $resource2 = $bootstrap->getResource('layout');
        $this->assertNotSame($resource1, $resource2);
        $this->assertTrue($resource1 instanceof Layout, var_export(array_keys($bootstrap->getBroker()->getRegisteredPlugins()), 1));
        $this->assertTrue($resource2 instanceof \Zend\Layout\Layout);
        restore_error_handler();
    }

    /**
     * @group ZF-6471
     * @group disable
     */
    public function testBootstrapShouldPassItselfToResourcePluginConstructor()
    {
        $this->application->setOptions(array(
            'pluginPaths' => array(
                'ZendTest\Application' => __DIR__,
            ),
            'resources' => array(
                'Foo' => array(),
            ),
        ));
        $bootstrap = new Application\Bootstrap($this->application);
        $resource = $bootstrap->getBroker()->getPlugin('foo');
        $this->assertTrue($resource->bootstrapSetInConstructor, var_export(get_object_vars($resource), 1));
    }

    /**
     * @group ZF-6591
     * @group disable
     */
    public function testRequestingPluginsByShortNameShouldNotRaiseFatalErrors()
    {
        $this->autoloader->setFallbackAutoloader(true)
                         ->suppressNotFoundWarnings(false);
        $this->application->setOptions(array(
            'resources' => array(
                'FrontController' => array(),
            ),
        ));
        set_error_handler(array($this, 'handleError'));
        $bootstrap = new Application\Bootstrap($this->application);
        $resource = $bootstrap->getBroker()->getPlugin('FrontController');
        restore_error_handler();
        $this->assertTrue(false === $this->error, $this->error);
    }

    /**
     * @group ZF-7550
     * @group disable
     */
    public function testRequestingPluginsByAutoloadableClassNameShouldNotRaiseFatalErrors()
    {
        // Using namesapce 'zabt' to prevent conflict with Zend namespace
        $rl = new ResourceAutoloader(array(
            'namespace' => 'Zabt',
            'basePath'  => __DIR__ . '/TestAsset',
        ));
        $rl->addResourceType('resources', 'resources', 'Resource');
        $options = array(
            'resources' => array(
                'Zabt\Resource\Autoloaded' => array('bar' => 'baz')
            ),
        );
        set_error_handler(array($this, 'handleError'));
        $this->application->setOptions($options);
        $bootstrap = new Application\Bootstrap($this->application);
        $bootstrap->bootstrap();
        restore_error_handler();
    }

    /**
     * @group ZF-7690
     */
    public function testCallingSetOptionsMultipleTimesShouldUpdateOptionKeys()
    {
        $this->application->setOptions(array(
            'resources' => array(
                'layout' => array(),
            ),
        ));
        $bootstrap = new OptionKeys($this->application);
        $bootstrap->setOptions(array(
            'values' => array(
                'Foo' => __DIR__,
            ),
        ));
        $expected = array('resources', 'values');
        $actual   = $bootstrap->getOptionKeys();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group ZF-9110
     */
    public function testPassingSameBootstrapAsApplicationShouldNotCauseRecursion()
    {
        $this->setExpectedException('Zend\Application\Exception\InvalidArgumentException');
        $bootstrap = new Application\Bootstrap($this->application);
        $bootstrap->setApplication($bootstrap);
    }

    /**
     * @group ZF-7696
     * @group disable
     */
    public function testUsingFallbackAutoloaderWithModulesShouldNotResultInFrontcontrollerNotFoundWarning()
    {
        $this->autoloader->setFallbackAutoloader(true);
        $options = array(
            'Resources' => array(
                'modules' => array(),
            ),
        );
        $this->application->setOptions($options);
        $bootstrap = new \Zf7696Bootstrap($this->application);
        $bootstrap->bootstrap(array('modules'));
    }

    /**
     * @group ZF-10199
     */
    public function testHasOptionShouldTreatOptionKeysAsCaseInsensitive()
    {
        $application = $this->application;
        $application->setOptions(array(
            'fooBar' => 'baz',
        ));
        $this->assertTrue($application->getBootstrap()->hasOption('FooBar'));
    }

    /**
     * @group ZF-10199
     */
    public function testGetOptionShouldTreatOptionKeysAsCaseInsensitive()
    {
        $application = $this->application;
        $application->setOptions(array(
            'fooBar' => 'baz',
        ));
        $this->assertEquals('baz', $application->getBootstrap()->getOption('FooBar'));
    }

    public function testCanSpecifyBrokerClassAsOptionString()
    {
        $application = $this->application;
        $application->setOptions(array(
            'broker' => 'ZendTest\Application\TestAsset\ResourceBroker',
        ));
        $bootstrap = $application->getBootstrap();
        $broker    = $bootstrap->getBroker();
        $this->assertInstanceOf('ZendTest\Application\TestAsset\ResourceBroker', $broker);
    }

    public function testCanPassConcreteBrokerViaOptions()
    {
        $broker      = new TestAsset\ResourceBroker();
        $application = $this->application;
        $application->setOptions(array(
            'broker' => $broker,
        ));
        $bootstrap = $application->getBootstrap();
        $this->assertSame($broker, $bootstrap->getBroker());
    }

    public function testCanPassArrayWithClassAndOptionsDescribingBrokerViaOptions()
    {
        $application = $this->application;
        $application->setOptions(array(
            'broker' => array(
                'class'   => 'ZendTest\Application\TestAsset\ResourceBroker',
                'options' => array('foo' => 'bar'),
            ),
        ));
        $bootstrap = $application->getBootstrap();
        $broker    = $bootstrap->getBroker();
        $this->assertInstanceOf('ZendTest\Application\TestAsset\ResourceBroker', $broker);
        $this->assertEquals(array('foo' => 'bar'), $broker->options);
    }
    
    /**
     * @group ZF2-30
     */
    public function testMultipleApplicationResourcesInitialization()
    {
        define('APPLICATION_PATH_ZF2_30', __DIR__);
        $application = new Application\Application('testing', __DIR__.'/TestAsset/Zf2-30.ini');        
        $application->bootstrap();      
        $loadedResource = $application->getBootstrap()->getBroker()->load('zf30');  
        $this->assertFalse(($loadedResource->getInitCount() > 1), 'Resource Zf30 initilized '.$loadedResource->getInitCount().' times');
    }
    
    /**
     * @group ZF2-36
     */
    public function testMultipleBrokersInitialization()
    {
        $application = new Application\Application('testing', __DIR__ . '/TestAsset/Zf2-36.ini');        
        $application->bootstrap();      
        $broker1 = $application->getBootstrap()->getBroker();
        $application->getBootstrap()
                    ->setOptions(array('test' => true));
        $broker2 = $application->getBootstrap()->getBroker();
        $this->assertFalse(($broker1 !== $broker2), 'Application broker initialized second time');
    }
    
    /**
     * @group ZF2-38
     */
    public function testContinueResourceExecutingByModulesResource()
    {
        define('APPLICATION_PATH_ZF2_38', __DIR__);
        $application = new Application\Application('testing', __DIR__ . '/TestAsset/Zf2-38.ini');        
        $application->bootstrap();      
        $broker = $application->getBootstrap()->getBroker();
        $modulesInitTitme = $broker->load('zf38modules')->getInitTime();
        $zf38InitTitme = $broker->load('zf38')->getInitTime();
        $this->assertFalse(($modulesInitTitme > $zf38InitTitme), 'Modules execute resources before end of their bootstraps');
    }
}

class Layout extends AbstractResource
{
    public $_explicitType = 'BootstrapAbstractTestLayout';
    public $bootstrapSetInConstructor = false;

    public function __construct($options = null)
    {
        parent::__construct($options);
        if (null !== $this->getBootstrap()) {
            $this->bootstrapSetInConstructor = true;
        }
    }

    public function init()
    {
        return $this;
    }
}

class Foo extends AbstractResource
{
    public $bootstrapSetInConstructor = false;

    public function __construct($options = null)
    {
        parent::__construct($options);
        if (null !== $this->getBootstrap()) {
            $this->bootstrapSetInConstructor = true;
        }
    }

    public function init()
    {
        return $this;
    }
}

class OptionKeys extends Application\Bootstrap
{
    public function getOptionKeys()
    {
        return $this->_optionKeys;
    }
}
