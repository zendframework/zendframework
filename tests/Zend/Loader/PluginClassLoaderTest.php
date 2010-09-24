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
 * @package    Loader
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Loader;

use Zend\Loader\PluginClassLoader;

/**
 * @category   Zend
 * @package    Loader
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Loader
 */
class PluginClassLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
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
}
