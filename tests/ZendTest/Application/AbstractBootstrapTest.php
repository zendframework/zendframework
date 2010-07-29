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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

namespace ZendTest\Application;

require_once __DIR__ . '/TestAsset/Zf7696Bootstrap.php';

use Zend\Loader\Autoloader,
    Zend\Loader\ResourceAutoloader,
    Zend\Loader\PluginLoader,
    Zend\Registry,
    Zend\Application,
    Zend\Application\Resource\AbstractResource;

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application
 */
class AbstractBootstrapTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // Store original autoloaders
        $this->loaders = spl_autoload_functions();
        if (!is_array($this->loaders)) {
            // spl_autoload_functions does not return empty array when no
            // autoloaders registered...
            $this->loaders = array();
        }

        Autoloader::resetInstance();
        $this->autoloader = Autoloader::getInstance();
        $this->autoloader->unshiftAutoloader('ZendTest_Autoloader', 'ZendTest');

        $this->application = new Application\Application('testing');
        $this->error = false;
    }

    public function tearDown()
    {
        // Restore original autoloaders
        $loaders = spl_autoload_functions();
        foreach ($loaders as $loader) {
            spl_autoload_unregister($loader);
        }

        foreach ($this->loaders as $loader) {
            spl_autoload_register($loader);
        }

        // Reset autoloader instance so it doesn't affect other tests
        Autoloader::resetInstance();
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
        $this->setExpectedException('Zend\\Application\\BootstrapException');
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

    public function testPluginPathsOptionKeyShouldAddPrefixPathsToPluginLoader()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $bootstrap->setOptions(array(
            'pluginPaths' => array(
                'Foo' => 'foo/bar/path/',
            ),
        ));
        $loader = $bootstrap->getPluginLoader();
        $paths = $loader->getPaths('Foo');
        $this->assertTrue(is_array($paths));
    }

    public function testResourcesOptionKeyShouldRegisterBootstrapPluginResources()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $bootstrap->setOptions(array(
            'resources' => array(
                'view' => array(
                    'basePath' => __DIR__ . '/TestAsset/views/scripts',
                ),
            ),
        ));
        $this->assertTrue($bootstrap->hasPluginResource('view'));
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

    public function testRegisterPluginResourceShouldThrowExceptionForInvalidResourceType()
    {
        $this->setExpectedException('Zend\Application\BootstrapException');
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $bootstrap->registerPluginResource(array());
    }

    public function testShouldAllowRegisteringConcretePluginResources()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $resource  = new Application\Resource\View();
        $bootstrap->registerPluginResource($resource);
        $test = $bootstrap->getPluginResource('view');
        $this->assertSame($resource, $test);
    }

    public function testRegisteringSecondPluginResourceOfSameTypeShouldOverwrite()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $resource1  = new Application\Resource\View();
        $resource2  = new Application\Resource\View();
        $bootstrap->registerPluginResource($resource1)
                  ->registerPluginResource($resource2);
        $test = $bootstrap->getPluginResource('view');
        $this->assertSame($resource2, $test);
    }

    public function testShouldAllowRegisteringPluginResourceUsingNameOnly()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $bootstrap->registerPluginResource('view');
        $test = $bootstrap->getPluginResource('view');
        $this->assertEquals('Zend\Application\Resource\View', get_class($test));
    }

    public function testShouldAllowUnregisteringPluginResourcesUsingConcreteInstance()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $resource  = new Application\Resource\View();
        $bootstrap->registerPluginResource($resource);
        $bootstrap->unregisterPluginResource($resource);
        $this->assertFalse($bootstrap->hasPluginResource('view'));
    }

    public function testAttemptingToUnregisterPluginResourcesUsingInvalidResourceTypeShouldThrowException()
    {
        $this->setExpectedException('Zend\Application\BootstrapException');
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $bootstrap->registerPluginResource('view');
        $bootstrap->unregisterPluginResource(array());
    }

    public function testShouldAllowUnregisteringPluginResourcesByName()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $bootstrap->registerPluginResource('view');
        $bootstrap->unregisterPluginResource('view');
        $this->assertFalse($bootstrap->hasPluginResource('view'));
    }

    public function testRetrievingNonExistentPluginResourceShouldReturnNull()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $this->assertNull($bootstrap->getPluginResource('view'));
    }

    public function testRetrievingPluginResourcesShouldRetrieveConcreteInstances()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $bootstrap->registerPluginResource('view');
        $test = $bootstrap->getPluginResources();
        foreach ($test as $type => $resource) {
            $this->assertTrue($resource instanceof Application\Resource);
        }
    }

    public function testShouldAllowRetrievingOnlyPluginResourceNames()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $bootstrap->registerPluginResource('view');
        $test = $bootstrap->getPluginResourceNames();
        $this->assertEquals(array('view'), $test);
    }

    public function testShouldAllowSettingAlternativePluginLoaderInstance()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $loader    = new PluginLoader();
        $bootstrap->setPluginLoader($loader);
        $this->assertSame($loader, $bootstrap->getPluginLoader());
    }

    public function testDefaultPluginLoaderShouldRegisterPrefixPathForResources()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $loader = $bootstrap->getPluginLoader();
        $paths  = $loader->getPaths('Zend\Application\Resource');
        $this->assertFalse(empty($paths));
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
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $bootstrap->getPluginLoader()->addPrefixPath('ZendTest\\Application\\TestAsset\\Resource', __DIR__ . '/_files/resources');
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
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $bootstrap->getPluginLoader()->addPrefixPath('ZendTest\\Application\\TestAsset\\Resource', __DIR__ . '/TestAsset/resources');
        $bootstrap->registerPluginResource('foobar');
        set_error_handler(array($this, 'handleError'), E_WARNING);
        $bootstrap->bootstrap();
        restore_error_handler();
        $this->assertEquals(1, $bootstrap->fooExecuted);
        $this->assertEquals(1, $bootstrap->barExecuted);
        $this->assertTrue($bootstrap->executedFoobarResource);
    }

    public function testPassingInvalidResourceArgumentToBootstrapShouldThrowException()
    {
        $this->setExpectedException('Zend\Application\BootstrapException');
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $bootstrap->bootstrap(new \stdClass);
    }

    public function testPassingUnknownResourceToBootstrapShouldThrowException()
    {
        $this->setExpectedException('Zend\Application\BootstrapException');
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
        $this->setExpectedException('Zend\Application\BootstrapException');
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $bootstrap->initFoo();
    }

    public function testDependencyTrackingShouldDetectCircularDependencies()
    {
        $this->setExpectedException('Zend\Application\BootstrapException');
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
        $bootstrap->getPluginLoader()->addPrefixPath('ZendTest\Application\TestAsset\Resource', __DIR__ . '/TestAsset/resources');
        $bootstrap->registerPluginResource('baz');
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
        $bootstrap->getPluginLoader()->addPrefixPath('ZendTest\Application\TestAsset\Resource', __DIR__ . '/TestAsset/resources');
        $bootstrap->registerPluginResource('baz');
        $bootstrap->bootstrap('baz');

        $this->assertTrue($bootstrap->hasResource('baz'));
        $resource = $bootstrap->getResource('baz');
        $this->assertEquals('Baz', $resource->baz);
    }

    public function testMagicMethodsForPluginResources()
    {
        $bootstrap = new TestAsset\ZfAppBootstrap($this->application);
        $bootstrap->getPluginLoader()->addPrefixPath('ZendTest\Application\TestAsset\Resource', __DIR__ . '/TestAsset/resources');
        $bootstrap->registerPluginResource('baz');
        $bootstrap->bootstrap('baz');

        $this->assertTrue(isset($bootstrap->baz));
        $resource = $bootstrap->baz;
        $this->assertEquals('Baz', $resource->baz);
    }

    /**
     * @group ZF-6543
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
        $this->assertTrue($bootstrap->hasPluginResource('View'), var_export(array_keys($bootstrap->getPluginResources()), 1));
        restore_error_handler();
    }

    /**
     * @group ZF-6543
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
        $this->assertTrue($bootstrap->hasPluginResource('ZendTest\\Application\\TestAsset\\Resource\\View'));
        restore_error_handler();
    }

    /**
     * @group ZF-6543
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
            var_export(array_keys($bootstrap->getPluginResources()), 1));
        restore_error_handler();
    }

    /**
     * @group ZF-6543
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
            var_export(array_keys($bootstrap->getPluginResources()), 1));
        $this->assertTrue($resource2 instanceof \Zend\View\View);
        restore_error_handler();
    }

    /**
     * @group ZF-6543
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
        $this->assertTrue($resource1 instanceof Layout, var_export(array_keys($bootstrap->getPluginResources()), 1));
        $this->assertTrue($resource2 instanceof \Zend\Layout\Layout);
        restore_error_handler();
    }

    /**
     * @group ZF-6471
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
        $resource = $bootstrap->getPluginResource('foo');
        $this->assertTrue($resource->bootstrapSetInConstructor, var_export(get_object_vars($resource), 1));
    }

    /**
     * @group ZF-6591
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
        $resource = $bootstrap->getPluginResource('FrontController');
        restore_error_handler();
        $this->assertTrue(false === $this->error, $this->error);
    }

    /**
     * @group ZF-7550
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
            'pluginPaths' => array(
                'Foo' => __DIR__,
            ),
        ));
        $expected = array('resources', 'pluginpaths');
        $actual   = $bootstrap->getOptionKeys();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group ZF-9110
     */
    public function testPassingSameBootstrapAsApplicationShouldNotCauseRecursion()
    {
        $this->setExpectedException('Zend\Application\BootstrapException');
        $bootstrap = new Application\Bootstrap($this->application);
        $bootstrap->setApplication($bootstrap);
    }
    
    /**
     * @group ZF-7696
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
