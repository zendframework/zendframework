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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Application_Bootstrap_BootstrapAbstractTest::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * Zend_Loader_Autoloader
 */
require_once 'Zend/Loader/Autoloader.php';

/**
 * Zend_Application_Resource_ResourceAbstract
 */
require_once 'Zend/Application/Resource/ResourceAbstract.php';

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application
 */
class Zend_Application_Bootstrap_BootstrapAbstractTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        // Store original autoloaders
        $this->loaders = spl_autoload_functions();
        if (!is_array($this->loaders)) {
            // spl_autoload_functions does not return empty array when no
            // autoloaders registered...
            $this->loaders = array();
        }

        Zend_Loader_Autoloader::resetInstance();
        $this->autoloader = Zend_Loader_Autoloader::getInstance();

        $this->application = new Zend_Application('testing');
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
        Zend_Loader_Autoloader::resetInstance();
    }

    public function handleError($errno, $errstr)
    {
        $this->error = $errstr;
        return true;
    }

    public function testConstructorShouldPopulateApplication()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $this->assertSame($this->application, $bootstrap->getApplication());
    }

    public function testConstructorShouldPopulateOptionsFromApplicationObject()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $options = array(
            'foo' => 'bar',
            'bar' => 'baz',
        );
        $this->application->setOptions($options);
        $bootstrap = new ZfAppBootstrap($this->application);
        $this->assertSame($options, $bootstrap->getOptions());
    }

    public function testConstructorShouldAllowPassingAnotherBootstrapObject()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap1 = new ZfAppBootstrap($this->application);
        $bootstrap2 = new ZfAppBootstrap($bootstrap1);
        $this->assertSame($bootstrap1, $bootstrap2->getApplication());
    }

    /**
     * @expectedException Zend_Application_Bootstrap_Exception
     */
    public function testConstructorShouldRaiseExceptionForInvalidApplicationArgument()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap(new stdClass);
    }

    public function testSettingOptionsShouldProxyToInternalSetters()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $options = array(
            'arbitrary' => 'foo',
        );
        $bootstrap = new ZfAppBootstrap($this->application);
        $bootstrap->setOptions($options);
        $this->assertEquals('foo', $bootstrap->getArbitrary());
    }

    /**
     * @group ZF-6459
     */
    public function testCallingSetOptionsMultipleTimesShouldMergeOptionsRecursively()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $options = array(
            'deep' => array(
                'foo' => 'bar',
                'bar' => 'baz',
            ),
        );
        $bootstrap = new ZfAppBootstrap($this->application);
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
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
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
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $bootstrap->setOptions(array(
            'resources' => array(
                'view' => array(
                    'basePath' => dirname(__FILE__) . '/../_files/views/scripts',
                ),
            ),
        ));
        $this->assertTrue($bootstrap->hasPluginResource('view'));
    }

    public function testHasOptionShouldReturnFalseWhenOptionUnavailable()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $this->assertFalse($bootstrap->hasOption('foo'));
    }

    public function testHasOptionShouldReturnTrueWhenOptionPresent()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $bootstrap->setOptions(array('foo' => 'bar'));
        $this->assertTrue($bootstrap->hasOption('foo'));
    }

    public function testGetOptionShouldReturnNullWhenOptionUnavailable()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $this->assertNull($bootstrap->getOption('foo'));
    }

    public function testGetOptionShouldReturnOptionValue()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $bootstrap->setOptions(array('foo' => 'bar'));
        $this->assertEquals('bar', $bootstrap->getOption('foo'));
    }

    public function testInternalIntializersShouldBeRegisteredAsClassResources()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $test      = $bootstrap->getClassResources();
        $resources = array('foo' => '_initFoo', 'bar' => '_initBar', 'barbaz' => '_initBarbaz');
        $this->assertEquals($resources, $test);
    }

    public function testInternalInitializersShouldRegisterResourceNames()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $test      = $bootstrap->getClassResourceNames();
        $resources = array('foo', 'bar', 'barbaz');
        $this->assertEquals($resources, $test);
    }

    /**
     * @expectedException Zend_Application_Bootstrap_Exception
     */
    public function testRegisterPluginResourceShouldThrowExceptionForInvalidResourceType()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $bootstrap->registerPluginResource(array());
    }

    public function testShouldAllowRegisteringConcretePluginResources()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $resource  = new Zend_Application_Resource_View();
        $bootstrap->registerPluginResource($resource);
        $test = $bootstrap->getPluginResource('view');
        $this->assertSame($resource, $test);
    }

    public function testRegisteringSecondPluginResourceOfSameTypeShouldOverwrite()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $resource1  = new Zend_Application_Resource_View();
        $resource2  = new Zend_Application_Resource_View();
        $bootstrap->registerPluginResource($resource1)
                  ->registerPluginResource($resource2);
        $test = $bootstrap->getPluginResource('view');
        $this->assertSame($resource2, $test);
    }

    public function testShouldAllowRegisteringPluginResourceUsingNameOnly()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $bootstrap->registerPluginResource('view');
        $test = $bootstrap->getPluginResource('view');
        $this->assertEquals('Zend_Application_Resource_View', get_class($test));
    }

    public function testShouldAllowUnregisteringPluginResourcesUsingConcreteInstance()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $resource  = new Zend_Application_Resource_View();
        $bootstrap->registerPluginResource($resource);
        $bootstrap->unregisterPluginResource($resource);
        $this->assertFalse($bootstrap->hasPluginResource('view'));
    }

    /**
     * @expectedException Zend_Application_Bootstrap_Exception
     */
    public function testAttemptingToUnregisterPluginResourcesUsingInvalidResourceTypeShouldThrowException()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $bootstrap->registerPluginResource('view');
        $bootstrap->unregisterPluginResource(array());
    }

    public function testShouldAllowUnregisteringPluginResourcesByName()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $bootstrap->registerPluginResource('view');
        $bootstrap->unregisterPluginResource('view');
        $this->assertFalse($bootstrap->hasPluginResource('view'));
    }

    public function testRetrievingNonExistentPluginResourceShouldReturnNull()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $this->assertNull($bootstrap->getPluginResource('view'));
    }

    public function testRetrievingPluginResourcesShouldRetrieveConcreteInstances()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $bootstrap->registerPluginResource('view');
        $test = $bootstrap->getPluginResources();
        foreach ($test as $type => $resource) {
            $this->assertTrue($resource instanceof Zend_Application_Resource_Resource);
        }
    }

    public function testShouldAllowRetrievingOnlyPluginResourceNames()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $bootstrap->registerPluginResource('view');
        $test = $bootstrap->getPluginResourceNames();
        $this->assertEquals(array('view'), $test);
    }

    public function testShouldAllowSettingAlternativePluginLoaderInstance()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $loader    = new Zend_Loader_PluginLoader();
        $bootstrap->setPluginLoader($loader);
        $this->assertSame($loader, $bootstrap->getPluginLoader());
    }

    public function testDefaultPluginLoaderShouldRegisterPrefixPathForResources()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $loader = $bootstrap->getPluginLoader();
        $paths  = $loader->getPaths('Zend_Application_Resource');
        $this->assertFalse(empty($paths));
    }

    public function testEnvironmentShouldMatchApplicationEnvironment()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $this->assertSame($this->application->getEnvironment(), $bootstrap->getEnvironment());
    }

    public function testBootstrappingShouldOnlyExecuteEachInitializerOnce()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $bootstrap->bootstrap('foo');
        $bootstrap->bootstrap('foo');
        $this->assertEquals(1, $bootstrap->fooExecuted);
    }

    public function testBootstrappingShouldFavorInternalResourcesOverPlugins()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $bootstrap->getPluginLoader()->addPrefixPath('Zend_Application_BootstrapTest_Resource', dirname(__FILE__) . '/../_files/resources');
        $bootstrap->bootstrap('foo');
        $this->assertFalse($bootstrap->executedFooResource);
    }

    public function testBootstrappingShouldAllowPassingAnArrayOfResources()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $bootstrap->bootstrap(array('foo', 'bar'));
        $this->assertEquals(1, $bootstrap->fooExecuted);
        $this->assertEquals(1, $bootstrap->barExecuted);
    }

    public function testPassingNoValuesToBootstrapExecutesAllResources()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $bootstrap->getPluginLoader()->addPrefixPath('Zend_Application_BootstrapTest_Resource', dirname(__FILE__) . '/../_files/resources');
        $bootstrap->registerPluginResource('foobar');
        $bootstrap->bootstrap();
        $this->assertEquals(1, $bootstrap->fooExecuted);
        $this->assertEquals(1, $bootstrap->barExecuted);
        $this->assertTrue($bootstrap->executedFoobarResource);
    }

    /**
     * @expectedException Zend_Application_Bootstrap_Exception
     */
    public function testPassingInvalidResourceArgumentToBootstrapShouldThrowException()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $bootstrap->bootstrap(new stdClass);
    }

    /**
     * @expectedException Zend_Application_Bootstrap_Exception
     */
    public function testPassingUnknownResourceToBootstrapShouldThrowException()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $bootstrap->bootstrap('bazbat');
    }

    public function testCallShouldOverloadToBootstrap()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $bootstrap->bootstrapFoo();
        $this->assertEquals(1, $bootstrap->fooExecuted);
    }

    /**
     * @expectedException Zend_Application_Bootstrap_Exception
     */
    public function testCallShouldThrowExceptionForInvalidMethodCall()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $bootstrap->initFoo();
    }

    /**
     * @expectedException Zend_Application_Bootstrap_Exception
     */
    public function testDependencyTrackingShouldDetectCircularDependencies()
    {
        require_once dirname(__FILE__) . '/../_files/BootstrapBaseCircularDependency.php';
        $bootstrap = new BootstrapBaseCircularDependency($this->application);
        $bootstrap->bootstrap();
    }

    public function testContainerShouldBeRegistryInstanceByDefault()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $container = $bootstrap->getContainer();
        $this->assertTrue($container instanceof Zend_Registry);
    }

    public function testContainerShouldAggregateReturnValuesFromClassResources()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $bootstrap->bootstrap('barbaz');
        $container = $bootstrap->getContainer();
        $this->assertEquals('Baz', $container->barbaz->baz);
    }

    public function testContainerShouldAggregateReturnValuesFromPluginResources()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $bootstrap->getPluginLoader()->addPrefixPath('Zend_Application_BootstrapTest_Resource', dirname(__FILE__) . '/../_files/resources');
        $bootstrap->registerPluginResource('baz');
        $bootstrap->bootstrap('baz');
        $container = $bootstrap->getContainer();
        $this->assertEquals('Baz', $container->baz->baz);
    }

    public function testClassResourcesShouldBeAvailableFollowingBootstrapping()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $bootstrap->bootstrap('barbaz');
        $this->assertTrue($bootstrap->hasResource('barbaz'));

        $resource = $bootstrap->getResource('barbaz');
        $this->assertEquals('Baz', $resource->baz);
    }

    public function testPluginResourcesShouldBeAvailableFollowingBootstrapping()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $bootstrap->getPluginLoader()->addPrefixPath('Zend_Application_BootstrapTest_Resource', dirname(__FILE__) . '/../_files/resources');
        $bootstrap->registerPluginResource('baz');
        $bootstrap->bootstrap('baz');

        $this->assertTrue($bootstrap->hasResource('baz'));
        $resource = $bootstrap->getResource('baz');
        $this->assertEquals('Baz', $resource->baz);
    }
    
    public function testMagicMethodsForPluginResources()
    {
        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $bootstrap = new ZfAppBootstrap($this->application);
        $bootstrap->getPluginLoader()->addPrefixPath('Zend_Application_BootstrapTest_Resource', dirname(__FILE__) . '/../_files/resources');
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
                'Zend_Application_Bootstrap_BootstrapAbstractTest_View' => array(),
            ),
            'pluginPaths' => array(
                'Zend_Application_Bootstrap_BootstrapAbstractTest' => dirname(__FILE__),
            ),
        ));
        $bootstrap = new Zend_Application_Bootstrap_Bootstrap($this->application);
        $this->assertTrue($bootstrap->hasPluginResource('View'), var_export(array_keys($bootstrap->getPluginResources()), 1));
    }

    /**
     * @group ZF-6543
     */
    public function testPassingFullViewClassNameNotMatchingARegisteredPrefixShouldRegisterAsTheClassName()
    {
        $this->application->setOptions(array(
            'resources' => array(
                'Zend_Application_Bootstrap_BootstrapAbstractTest_View' => array(),
            ),
        ));
        $bootstrap = new Zend_Application_Bootstrap_Bootstrap($this->application);
        $this->assertTrue($bootstrap->hasPluginResource('Zend_Application_Bootstrap_BootstrapAbstractTest_View'));
    }

    /**
     * @group ZF-6543
     */
    public function testPassingFullViewClassNameNotMatchingARegisteredPrefixShouldReturnAppropriateResource()
    {
        $this->application->setOptions(array(
            'resources' => array(
                'Zend_Application_Bootstrap_BootstrapAbstractTest_View' => array(),
            ),
        ));
        $bootstrap = new Zend_Application_Bootstrap_Bootstrap($this->application);
        $bootstrap->bootstrap('Zend_Application_Bootstrap_BootstrapAbstractTest_View');
        $resource = $bootstrap->getResource('Zend_Application_Bootstrap_BootstrapAbstractTest_View');
        $this->assertTrue($resource instanceof Zend_Application_Bootstrap_BootstrapAbstractTest_View, var_export(array_keys($bootstrap->getPluginResources()), 1));
    }

    /**
     * @group ZF-6543
     */
    public function testCanMixAndMatchPluginResourcesAndFullClassNames()
    {
        $this->application->setOptions(array(
            'resources' => array(
                'Zend_Application_Bootstrap_BootstrapAbstractTest_View' => array(),
                'view' => array(),
            ),
        ));
        $bootstrap = new Zend_Application_Bootstrap_Bootstrap($this->application);
        $bootstrap->bootstrap('Zend_Application_Bootstrap_BootstrapAbstractTest_View');
        $resource1 = $bootstrap->getResource('Zend_Application_Bootstrap_BootstrapAbstractTest_View');
        $bootstrap->bootstrap('view');
        $resource2 = $bootstrap->getResource('view');
        $this->assertNotSame($resource1, $resource2);
        $this->assertTrue($resource1 instanceof Zend_Application_Bootstrap_BootstrapAbstractTest_View, var_export(array_keys($bootstrap->getPluginResources()), 1));
        $this->assertTrue($resource2 instanceof Zend_View);
    }

    /**
     * @group ZF-6543
     */
    public function testPluginClassesDefiningExplicitTypeWillBeRegisteredWithThatValue()
    {
        $this->application->setOptions(array(
            'resources' => array(
                'Zend_Application_Bootstrap_BootstrapAbstractTest_Layout' => array(),
                'layout' => array(),
            ),
        ));
        $bootstrap = new Zend_Application_Bootstrap_Bootstrap($this->application);
        $bootstrap->bootstrap('BootstrapAbstractTestLayout');
        $resource1 = $bootstrap->getResource('BootstrapAbstractTestLayout');
        $bootstrap->bootstrap('layout');
        $resource2 = $bootstrap->getResource('layout');
        $this->assertNotSame($resource1, $resource2);
        $this->assertTrue($resource1 instanceof Zend_Application_Bootstrap_BootstrapAbstractTest_Layout, var_export(array_keys($bootstrap->getPluginResources()), 1));
        $this->assertTrue($resource2 instanceof Zend_Layout);
    }

    /**
     * @group ZF-6471
     */
    public function testBootstrapShouldPassItselfToResourcePluginConstructor()
    {
        $this->application->setOptions(array(
            'pluginPaths' => array(
                'Zend_Application_Bootstrap_BootstrapAbstractTest' => dirname(__FILE__),
            ),
            'resources' => array(
                'Foo' => array(),
            ),
        ));
        $bootstrap = new Zend_Application_Bootstrap_Bootstrap($this->application);
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
        $bootstrap = new Zend_Application_Bootstrap_Bootstrap($this->application);
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
        $rl = new Zend_Loader_Autoloader_Resource(array(
            'namespace' => 'Zabt',
            'basePath'  => dirname(__FILE__) . '/../_files',
        ));
        $rl->addResourceType('resources', 'resources', 'Resource');
        $options = array(
            'resources' => array(
                'Zabt_Resource_Autoloaded' => array('bar' => 'baz')
            ),
        );
        $this->application->setOptions($options);
        $bootstrap = new Zend_Application_Bootstrap_Bootstrap($this->application);
        $bootstrap->bootstrap();
    }
}

class Zend_Application_Bootstrap_BootstrapAbstractTest_View
    extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        return $this;
    }
}

class Zend_Application_Bootstrap_BootstrapAbstractTest_Layout
    extends Zend_Application_Resource_ResourceAbstract
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

class Zend_Application_Bootstrap_BootstrapAbstractTest_Foo
    extends Zend_Application_Resource_ResourceAbstract
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

if (PHPUnit_MAIN_METHOD == 'Zend_Application_Bootstrap_BootstrapAbstractTest::main') {
    Zend_Application_Bootstrap_BootstrapAbstractTest::main();
}
