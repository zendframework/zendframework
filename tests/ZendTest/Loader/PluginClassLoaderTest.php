<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Loader
 */

namespace ZendTest\Loader;

use Zend\Loader\PluginClassLoader;

/**
 * @category   Zend
 * @package    Loader
 * @subpackage UnitTests
 * @group      Loader
 */
class PluginClassLoaderTest extends \PHPUnit_Framework_TestCase
{
    /** @var PluginClassLoader */
    public $loader;

    public function setUp()
    {
        // Clear any static maps
        PluginClassLoader::addStaticMap(null);
        TestAsset\ExtendedPluginClassLoader::addStaticMap(null);

        // Create a loader instance
        $this->loader = new PluginClassLoader();
    }

    public function testPluginClassLoaderHasNoAssociationsByDefault()
    {
        $plugins = $this->loader->getRegisteredPlugins();
        $this->assertTrue(empty($plugins));
    }

    public function testRegisterPluginRegistersShortNameClassNameAssociation()
    {
        $this->loader->registerPlugin('loader', __CLASS__);
        $plugins = $this->loader->getRegisteredPlugins();
        $this->assertArrayHasKey('loader', $plugins);
        $this->assertEquals(__CLASS__, $plugins['loader']);
    }

    public function testCallingRegisterPluginWithAnExistingPluginNameOverwritesThatMapAssociation()
    {
        $this->testRegisterPluginRegistersShortNameClassNameAssociation();
        $this->loader->registerPlugin('loader', 'Zend\Loader\PluginClassLoader');
        $plugins = $this->loader->getRegisteredPlugins();
        $this->assertArrayHasKey('loader', $plugins);
        $this->assertEquals('Zend\Loader\PluginClassLoader', $plugins['loader']);
    }

    public function testCallingRegisterPluginsWithInvalidStringMapRaisesException()
    {
        $this->setExpectedException('Zend\Loader\Exception\InvalidArgumentException');
        $this->loader->registerPlugins('__foobar__');
    }

    public function testCallingRegisterPluginsWithStringMapResolvingToNonTraversableClassRaisesException()
    {
        $this->setExpectedException('Zend\Loader\Exception\InvalidArgumentException');
        $this->loader->registerPlugins('stdClass');
    }

    public function testCallingRegisterPluginsWithValidStringMapResolvingToTraversableClassRegistersPlugins()
    {
        $this->loader->registerPlugins('ZendTest\Loader\TestAsset\TestPluginMap');
        $pluginMap = new TestAsset\TestPluginMap;
        $this->assertEquals($pluginMap->map, $this->loader->getRegisteredPlugins());
    }

    /**
     * @dataProvider invalidMaps
     */
    public function testCallingRegisterPluginsWithNonArrayNonStringNonTraversableValueRaisesException($arg)
    {
        $this->setExpectedException('Zend\Loader\Exception\InvalidArgumentException');
        $this->loader->registerPlugins($arg);
    }

    public function invalidMaps()
    {
        return array(
            array(null),
            array(true),
            array(1),
            array(1.0),
            array(new \stdClass),
        );
    }

    public function testCallingRegisterPluginsWithArrayRegistersMap()
    {
        $map = array('test' => __CLASS__);
        $this->loader->registerPlugins($map);
        $test = $this->loader->getRegisteredPlugins();
        $this->assertEquals($map, $test);
    }

    public function testCallingRegisterPluginsWithTraversableObjectRegistersMap()
    {
        $map = new TestAsset\TestPluginMap();
        $this->loader->registerPlugins($map);
        $test = $this->loader->getRegisteredPlugins();
        $this->assertEquals($map->map, $test);
    }

    public function testUnregisterPluginRemovesPluginFromMap()
    {
        $map = new TestAsset\TestPluginMap();
        $this->loader->registerPlugins($map);

        $this->loader->unregisterPlugin('test');

        $test = $this->loader->getRegisteredPlugins();
        $this->assertFalse(array_key_exists('test', $test));
    }

    public function testIsLoadedReturnsFalseIfPluginIsNotInMap()
    {
        $this->assertFalse($this->loader->isLoaded('test'));
    }

    public function testIsLoadedReturnsTrueIfPluginIsInMap()
    {
        $this->loader->registerPlugin('test', __CLASS__);
        $this->assertTrue($this->loader->isLoaded('test'));
    }

    public function testGetClassNameReturnsFalseIfPluginIsNotInMap()
    {
        $this->assertFalse($this->loader->getClassName('test'));
    }

    public function testGetClassNameReturnsClassNameIfPluginIsInMap()
    {
        $this->loader->registerPlugin('test', __CLASS__);
        $this->assertEquals(__CLASS__, $this->loader->getClassName('test'));
    }

    public function testLoadReturnsFalseIfPluginIsNotInMap()
    {
        $this->assertFalse($this->loader->load('test'));
    }

    public function testLoadReturnsClassNameIfPluginIsInMap()
    {
        $this->loader->registerPlugin('test', __CLASS__);
        $this->assertEquals(__CLASS__, $this->loader->load('test'));
    }

    public function testIteratingLoaderIteratesPluginMap()
    {
        $map = new TestAsset\TestPluginMap();
        $this->loader->registerPlugins($map);
        $test = array();
        foreach ($this->loader as $name => $class) {
            $test[$name] = $class;
        }

        $this->assertEquals($map->map, $test);
    }

    public function testPluginRegistrationIsCaseInsensitive()
    {
        $map = array(
            'foo' => __CLASS__,
            'FOO' => __NAMESPACE__ . '\TestAsset\TestPluginMap',
        );
        $this->loader->registerPlugins($map);
        $this->assertEquals($map['FOO'], $this->loader->getClassName('foo'));
    }

    public function testAddingStaticMapDoesNotAffectExistingInstances()
    {
        PluginClassLoader::addStaticMap(array(
            'test' => __CLASS__,
        ));
        $this->assertFalse($this->loader->getClassName('test'));
    }

    public function testAllowsSettingStaticMapForSeedingInstance()
    {
        PluginClassLoader::addStaticMap(array(
            'test' => __CLASS__,
        ));
        $loader = new PluginClassLoader();
        $this->assertEquals(__CLASS__, $loader->getClassName('test'));
    }

    public function testPassingNullToStaticMapClearsMap()
    {
        $this->testAllowsSettingStaticMapForSeedingInstance();
        PluginClassLoader::addStaticMap(null);
        $loader = new PluginClassLoader();
        $this->assertFalse($loader->getClassName('test'));
    }

    public function testAllowsPassingTraversableObjectToStaticMap()
    {
        $map = new \ArrayObject(array(
            'test' => __CLASS__,
        ));
        PluginClassLoader::addStaticMap($map);
        $loader = new PluginClassLoader();
        $this->assertEquals(__CLASS__, $loader->getClassName('test'));
    }

    public function testMultipleCallsToAddStaticMapMergeMap()
    {
        PluginClassLoader::addStaticMap(array(
            'test' => __CLASS__,
        ));
        PluginClassLoader::addStaticMap(array(
            'loader' => 'Zend\Loader\PluginClassLoader',
        ));
        $loader = new PluginClassLoader();
        $this->assertEquals(__CLASS__, $loader->getClassName('test'));
        $this->assertEquals('Zend\Loader\PluginClassLoader', $loader->getClassName('loader'));
    }

    public function testStaticMapUsesLateStaticBinding()
    {
        TestAsset\ExtendedPluginClassLoader::addStaticMap(array('test' => __CLASS__));
        $loader = new PluginClassLoader();
        $this->assertFalse($loader->getClassName('test'));
        $loader = new TestAsset\ExtendedPluginClassLoader();
        $this->assertEquals(__CLASS__, $loader->getClassName('test'));
    }

    public function testMapPrecedenceIsExplicitTrumpsConstructorTrumpsStaticTrumpsInternal()
    {
        $loader = new TestAsset\ExtendedPluginClassLoader();
        $this->assertEquals('Zend\Loader\PluginClassLoader', $loader->getClassName('loader'));

        TestAsset\ExtendedPluginClassLoader::addStaticMap(array('loader' => __CLASS__));
        $loader = new TestAsset\ExtendedPluginClassLoader();
        $this->assertEquals(__CLASS__, $loader->getClassName('loader'));

        $loader = new TestAsset\ExtendedPluginClassLoader(array('loader' => 'ZendTest\Loader\TestAsset\ExtendedPluginClassLoader'));
        $this->assertEquals('ZendTest\Loader\TestAsset\ExtendedPluginClassLoader', $loader->getClassName('loader'));

        $loader->registerPlugin('loader', __CLASS__);
        $this->assertEquals(__CLASS__, $loader->getClassName('loader'));
    }

    public function testRegisterPluginsCanAcceptArrayElementWithClassNameProvidingAMap()
    {
        $pluginMap = new TestAsset\TestPluginMap;
        $this->loader->registerPlugins(array('ZendTest\Loader\TestAsset\TestPluginMap'));
        $this->assertEquals($pluginMap->map, $this->loader->getRegisteredPlugins());
    }

    public function testRegisterPluginsCanAcceptArrayElementWithObjectProvidingAMap()
    {
        $pluginMap = new TestAsset\TestPluginMap;
        $this->loader->registerPlugins(array($pluginMap));
        $this->assertEquals($pluginMap->map, $this->loader->getRegisteredPlugins());
    }
}
