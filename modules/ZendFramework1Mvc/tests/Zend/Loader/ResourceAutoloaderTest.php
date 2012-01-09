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
 * @package    Zend_Loader
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Loader;

use Zend\Loader\ResourceAutoloader,
    Zend\Config\Config;

/**
 * @category   Zend
 * @package    Zend_Loader
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Loader
 */
class ResourceAutoloaderTest extends \PHPUnit_Framework_TestCase
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

        // Store original include_path
        $this->includePath = get_include_path();

        // initialize 'error' member for tests that utilize error handling
        $this->error = null;

        $this->loader = new ResourceAutoloader(array(
            'namespace' => 'FooBar',
            'basePath'  => realpath(__DIR__ . '/_files/ResourceAutoloader'),
        ));
        $this->loader->register();
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

        // Retore original include_path
        set_include_path($this->includePath);
    }

    public function testAutoloaderInstantiationShouldRaiseExceptionWithoutNamespace()
    {
        $this->setExpectedException('Zend\Loader\Exception\InvalidArgumentException');
        $loader = new ResourceAutoloader(array('basePath' => __DIR__));
    }

    public function testAutoloaderInstantiationShouldRaiseExceptionWithoutBasePath()
    {
        $this->setExpectedException('Zend\Loader\Exception\InvalidArgumentException');
        $loader = new ResourceAutoloader(array('namespace' => 'Foo'));
    }

    public function testAutoloaderInstantiationShouldRaiseExceptionWhenInvalidOptionsTypeProvided()
    {
        $this->setExpectedException('Zend\Loader\Exception\InvalidArgumentException');
        $loader = new ResourceAutoloader('foo');
    }

    public function testAutoloaderConstructorShouldAcceptZendConfigObject()
    {
        $config = new Config(array('namespace' => 'Foo', 'basePath' => __DIR__));
        $loader = new ResourceAutoloader($config);
    }

    public function testAutoloaderShouldAllowRetrievingNamespace()
    {
        $this->assertEquals('FooBar', $this->loader->getNamespace());
    }

    public function testAutoloaderShouldAllowRetrievingBasePath()
    {
        $this->assertEquals(realpath(__DIR__ . '/_files/ResourceAutoloader'), $this->loader->getBasePath());
    }

    public function testNoResourceTypesShouldBeRegisteredByDefault()
    {
        $resourceTypes = $this->loader->getResourceTypes();
        $this->assertTrue(is_array($resourceTypes));
        $this->assertTrue(empty($resourceTypes));
    }

    public function testInitialResourceTypeDefinitionShouldRequireNamespace()
    {
        $this->setExpectedException('Zend\Loader\Exception\MissingResourceNamespaceException');
        $this->loader->addResourceType('foo', 'foo');
    }

    public function testPassingNonStringPathWhenAddingResourceTypeShouldRaiseAnException()
    {
        $this->setExpectedException('Zend\Loader\Exception\InvalidPathException');
        $this->loader->addResourceType('foo', array('foo'), 'Foo');
    }

    public function testAutoloaderShouldAllowAddingArbitraryResourceTypes()
    {
        $this->loader->addResourceType('models', 'models', 'Model');
        $resources = $this->loader->getResourceTypes();
        $this->assertTrue(array_key_exists('models', $resources));
        $this->assertEquals($this->loader->getNamespace() . '\\Model', $resources['models']['namespace']);
        $this->assertContains('/models', $resources['models']['path']);
    }

    public function testAutoloaderShouldAllowAddingArbitraryResourceTypesUsingPrefixes()
    {
        $this->loader->setNamespace(null)
                     ->setPrefix('FooBar');
        $this->loader->addResourceType('models', 'models', 'Model');
        $resources = $this->loader->getResourceTypes();
        $this->assertTrue(array_key_exists('models', $resources));
        $this->assertEquals($this->loader->getPrefix() . '_Model', $resources['models']['namespace']);
        $this->assertContains('/models', $resources['models']['path']);
    }

    public function testAutoloaderShouldAllowAddingResettingResourcePaths()
    {
        $this->loader->addResourceType('models', 'models', 'Model');
        $this->loader->addResourceType('models', 'apis');
        $resources = $this->loader->getResourceTypes();
        $this->assertNotContains('/models', $resources['models']['path']);
        $this->assertContains('/apis', $resources['models']['path']);
    }

    public function testAutoloaderShouldSupportAddingMultipleResourceTypesAtOnce()
    {
        $this->loader->addResourceTypes(array(
            'model' => array('path' => 'models', 'namespace' => 'Model'),
            'form'  => array('path' => 'forms', 'namespace' => 'Form'),
        ));
        $resources = $this->loader->getResourceTypes();
        $this->assertContains('model', array_keys($resources));
        $this->assertContains('form', array_keys($resources));
    }

    public function testAutoloaderShouldSupportAddingMultipleResourceTypesAtOnceUsingPrefixes()
    {
        $this->loader->setNamespace(null)
                     ->setPrefix('FooBar');
        $this->loader->addResourceTypes(array(
            'model' => array('path' => 'models', 'namespace' => 'Model'),
            'form'  => array('path' => 'forms', 'namespace' => 'Form'),
        ));
        $resources = $this->loader->getResourceTypes();
        $this->assertContains('model', array_keys($resources));
        $this->assertContains('form', array_keys($resources));
    }

    public function testAddingMultipleResourceTypesShouldRaiseExceptionWhenReceivingNonArrayItem()
    {
        $this->setExpectedException('Zend\Loader\Exception\InvalidArgumentException', 'expects an array');
        $this->loader->addResourceTypes(array('foo' => 'bar'));
    }

    public function testAddingMultipleResourceTypesShouldRaiseExceptionWhenMissingResourcePath()
    {
        $this->setExpectedException('Zend\Loader\Exception\InvalidArgumentException', 'include a paths');
        $this->loader->addResourceTypes(array('model' => array('namespace' => 'Model')));
    }

    public function testSetResourceTypesShouldOverwriteExistingResourceTypes()
    {
        $this->loader->addResourceTypes(array(
            'model' => array('path' => 'models', 'namespace' => 'Model'),
            'form'  => array('path' => 'forms', 'namespace' => 'Form'),
        ));

        $this->loader->setResourceTypes(array(
            'view'   => array('path' => 'views', 'namespace' => 'View'),
            'layout' => array('path' => 'layouts', 'namespace' => 'Layout'),
        ));

        $resources = $this->loader->getResourceTypes();
        $this->assertNotContains('model', array_keys($resources));
        $this->assertNotContains('form', array_keys($resources));
        $this->assertContains('view', array_keys($resources));
        $this->assertContains('layout', array_keys($resources));
    }

    public function testHasResourceTypeShouldReturnFalseWhenTypeNotDefined()
    {
        $this->assertFalse($this->loader->hasResourceType('model'));
    }

    public function testHasResourceTypeShouldReturnTrueWhenTypeIsDefined()
    {
        $this->loader->addResourceTypes(array(
            'model' => array('path' => 'models', 'namespace' => 'Model'),
        ));
        $this->assertTrue($this->loader->hasResourceType('model'));
    }

    public function testRemoveResourceTypeShouldRemoveResourceFromList()
    {
        $this->loader->addResourceTypes(array(
            'model' => array('path' => 'models', 'namespace' => 'Model'),
            'form'  => array('path' => 'forms', 'namespace' => 'Form'),
        ));
        $this->loader->removeResourceType('form');

        $resources = $this->loader->getResourceTypes();
        $this->assertContains('model', array_keys($resources));
        $this->assertNotContains('form', array_keys($resources));
    }

    public function testAutoloaderShouldAllowSettingDefaultResourceType()
    {
        $this->loader->addResourceTypes(array(
            'model' => array('path' => 'models', 'namespace' => 'Model'),
        ));
        $this->loader->setDefaultResourceType('model');
        $this->assertEquals('model', $this->loader->getDefaultResourceType());
    }

    public function testSettingDefaultResourceTypeToUndefinedTypeShouldHaveNoEffect()
    {
        $this->loader->setDefaultResourceType('model');
        $this->assertNull($this->loader->getDefaultResourceType());
    }

    public function testLoadShouldRaiseExceptionWhenNotTypePassedAndNoDefaultSpecified()
    {
        $this->setExpectedException('Zend\Loader\Exception\InvalidArgumentException', 'No resource type');
        $this->loader->load('Foo');
    }

    public function testLoadShouldRaiseExceptionWhenResourceTypeDoesNotExist()
    {
        $this->setExpectedException('Zend\Loader\Exception\InvalidArgumentException', 'Invalid resource type');
        $this->loader->load('Foo', 'model');
    }

    public function testLoadShouldReturnObjectOfExpectedClass()
    {
        $this->loader->addResourceTypes(array(
            'model' => array('path' => 'models', 'namespace' => 'Model'),
        ));
        $object = $this->loader->load('ZendLoaderAutoloaderResourceTest', 'model');
        $this->assertTrue($object instanceof \FooBar\Model\ZendLoaderAutoloaderResourceTest);
    }

    public function testLoadShouldReturnObjectOfExpectedClassUsingPrefixes()
    {
        $loader = new ResourceAutoloader(array(
            'prefix' => 'FooBar',
            'basePath' => realpath(__DIR__ . '/_files/ResourceAutoloader'),
        ));
        $loader->addResourceTypes(array(
            'model' => array('path' => 'models', 'namespace' => 'Model'),
        ));
        $loader->register();
        $object = $loader->load('ZendLoaderAutoloaderResourcePrefixTest', 'model');
        $this->assertTrue($object instanceof \FooBar_Model_ZendLoaderAutoloaderResourcePrefixTest);
    }

    public function testSuccessiveCallsToLoadSameResourceShouldReturnSameObject()
    {
        $this->loader->addResourceTypes(array(
            'form' => array('path' => 'forms', 'namespace' => 'Form'),
        ));
        $object = $this->loader->load('ZendLoaderAutoloaderResourceTest', 'form');
        $this->assertTrue($object instanceof \FooBar\Form\ZendLoaderAutoloaderResourceTest);
        $test   = $this->loader->load('ZendLoaderAutoloaderResourceTest', 'form');
        $this->assertSame($object, $test);
    }

    public function testAutoloadShouldAllowEmptyNamespacing()
    {
        $loader = new ResourceAutoloader(array(
            'namespace' => '',
            'basePath'  => realpath(__DIR__ . '/_files/ResourceAutoloader'),
        ));
        $loader->addResourceTypes(array(
            'service' => array('path' => 'services', 'namespace' => 'Service'),
        ));
        $loader->register();
        $test = $loader->load('ZendLoaderAutoloaderResourceTest', 'service');
        $this->assertTrue($test instanceof \Service\ZendLoaderAutoloaderResourceTest);
    }

    public function testPassingClassOfDifferentNamespaceToAutoloadShouldReturnFalse()
    {
        $this->assertFalse($this->loader->autoload('Foo\Bar\Baz'));
    }

    public function testPassingClassOfDifferentPrefixToAutoloadShouldReturnFalse()
    {
        $loader = new ResourceAutoloader(array(
            'prefix'   => 'FooBar',
            'basePath' => realpath(__DIR__ . '/_files/ResourceAutoloader'),
        ));
        $this->assertFalse($loader->autoload('Foo_Bar_Baz'));
    }

    public function testPassingClassWithoutBothComponentAndClassSegmentsToAutoloadShouldReturnFalse()
    {
        $this->assertFalse($this->loader->autoload('FooBar\Baz'));
    }

    public function testPassingPrefixedClassWithoutBothComponentAndClassSegmentsToAutoloadShouldReturnFalse()
    {
        $loader = new ResourceAutoloader(array(
            'prefix'   => 'FooBar',
            'basePath' => realpath(__DIR__ . '/_files/ResourceAutoloader'),
        ));
        $this->assertFalse($loader->autoload('FooBar_Baz'));
    }

    public function testPassingClassWithUnmatchedResourceTypeToAutoloadShouldReturnFalse()
    {
        $this->assertFalse($this->loader->autoload('FooBar\Baz\Bat'));
    }

    public function testPassingPrefixedClassWithUnmatchedResourceTypeToAutoloadShouldReturnFalse()
    {
        $loader = new ResourceAutoloader(array(
            'prefix'   => 'FooBar',
            'basePath' => realpath(__DIR__ . '/_files/ResourceAutoloader'),
        ));
        $this->assertFalse($loader->autoload('FooBar_Baz_Bat'));
    }

    public function testMethodOverloadingShouldRaiseExceptionForNonGetterMethodCalls()
    {
        $this->setExpectedException('Zend\Loader\Exception\BadMethodCallException');
        $this->loader->lalalalala();
    }

    public function testMethodOverloadingShouldRaiseExceptionWhenRequestedResourceDoesNotExist()
    {
        $this->setExpectedException('Zend\Loader\Exception\InvalidArgumentException', 'Invalid resource');
        $this->loader->getModel('Foo');
    }

    public function testMethodOverloadingShouldRaiseExceptionWhenNoArgumentPassed()
    {
        $this->setExpectedException('Zend\Loader\Exception\InvalidArgumentException', 'no resourc');
        $this->loader->addResourceTypes(array(
            'model' => array('path' => 'models', 'namespace' => 'Model'),
        ));
        $this->loader->getModel();
    }

    public function testMethodOverloadingShouldReturnObjectOfExpectedType()
    {
        $this->loader->addResourceTypes(array(
            'model' => array('path' => 'models', 'namespace' => 'Model'),
        ));
        $test = $this->loader->getModel('ZendLoaderAutoloaderResourceMethodOverloading');
        $this->assertTrue($test instanceof \FooBar\Model\ZendLoaderAutoloaderResourceMethodOverloading);
    }

    /**
     * @group ZF-7501
     */
    public function testAutoloaderShouldTrimResourceTypePathsForTrailingPathSeparator()
    {
        $this->loader->addResourceType('models', 'models/', 'Model');
        $resources = $this->loader->getResourceTypes();
        $this->assertEquals($this->loader->getBasePath() . '/models', $resources['models']['path']);
    }

    /**
     * @group ZF-6727
     */
    public function testAutoloaderResourceGetClassPath()
    {
        $this->loader->addResourceTypes(array(
            'model' => array('path' => 'models', 'namespace' => 'Model'),
        ));
        $path = $this->loader->getClassPath('FooBar\Model\Class\Model');
        // if true we have // in path
        $this->assertFalse(strpos($path, '//'));
    }

    /**
     * @group ZF-8364
     * @group ZF-6727
     */
    public function testAutoloaderResourceGetClassPathReturnFalse()
    {
        $this->loader->addResourceTypes(array(
            'model' => array('path' => 'models', 'namespace' => 'Model'),
        ));
        $path = $this->loader->autoload('Something\\Totally\\Wrong');
        $this->assertFalse($path);
    }

    /**
     * @group ZF-8364
     * @group ZF-6727
     */
    public function testAutoloaderResourceGetClassPathReturnFalseForPrefixedClass()
    {
        $loader = new ResourceAutoloader(array(
            'prefix'   => 'FooBar',
            'basePath' => realpath(__DIR__ . '/_files/ResourceAutoloader'),
        ));
        $loader->addResourceTypes(array(
            'model' => array('path' => 'models', 'namespace' => 'Model'),
        ));
        $path = $loader->autoload('Something_Totally_Wrong');
        $this->assertFalse($path);
    }
}
