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
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Cache;
use Zend\Cache,
    Zend\Loader\Broker;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cache
 */
class StorageFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        Cache\StorageFactory::resetAdapterBroker();
        Cache\StorageFactory::resetPluginBroker();
    }

    public function tearDown()
    {
        Cache\StorageFactory::resetAdapterBroker();
        Cache\StorageFactory::resetPluginBroker();
    }

    public function testDefaultAdapterBroker()
    {
        $broker = Cache\StorageFactory::getAdapterBroker();
        $this->assertInstanceOf('Zend\Cache\Storage\AdapterBroker', $broker);
    }

    public function testChangeAdapterBroker()
    {
        $broker = new Cache\Storage\AdapterBroker();
        Cache\StorageFactory::setAdapterBroker($broker);
        $this->assertSame($broker, Cache\StorageFactory::getAdapterBroker());
    }

    public function testAdapterFactory()
    {
        $adapter1 = Cache\StorageFactory::adapterFactory('Memory');
        $this->assertInstanceOf('Zend\Cache\Storage\Adapter\Memory', $adapter1);

        $adapter2 = Cache\StorageFactory::adapterFactory('Memory');
        $this->assertInstanceOf('Zend\Cache\Storage\Adapter\Memory', $adapter2);

        $this->assertNotSame($adapter1, $adapter2);
    }

    public function testDefaultPluginBroker()
    {
        $broker = Cache\StorageFactory::getPluginBroker();
        $this->assertInstanceOf('Zend\Cache\Storage\PluginBroker', $broker);
    }

    public function testChangePluginBroker()
    {
        $broker = new Cache\Storage\PluginBroker();
        Cache\StorageFactory::setPluginBroker($broker);
        $this->assertSame($broker, Cache\StorageFactory::getPluginBroker());
    }

    public function testPluginFactory()
    {
        $plugin1 = Cache\StorageFactory::pluginFactory('Serializer');
        $this->assertInstanceOf('Zend\Cache\Storage\Plugin\Serializer', $plugin1);

        $plugin2 = Cache\StorageFactory::pluginFactory('Serializer');
        $this->assertInstanceOf('Zend\Cache\Storage\Plugin\Serializer', $plugin2);

        $this->assertNotSame($plugin1, $plugin2);
    }

    public function testFactoryAdapterAsString()
    {
        $cache = Cache\StorageFactory::factory(array(
            'adapter' => 'Memory',
        ));
        $this->assertInstanceOf('Zend\Cache\Storage\Adapter\Memory', $cache);
    }

    public function testFactoryAdapterAsArray()
    {
        $cache = Cache\StorageFactory::factory(array(
            'adapter' => array(
                'name' => 'Memory',
            )
        ));
        $this->assertInstanceOf('Zend\Cache\Storage\Adapter\Memory', $cache);
    }

    public function testFactoryWithPlugins()
    {
        $adapter = 'Memory';
        $plugins = array('Serializer', 'ClearByFactor');

        $cache = Cache\StorageFactory::factory(array(
            'adapter' => $adapter,
            'plugins' => $plugins,
        ));

        // test adapter
        $this->assertInstanceOf('Zend\Cache\Storage\Adapter\Memory', $cache);

        // test plugin structure
        $i = 0;
        foreach ($cache->getPlugins() as $plugin) {
            $this->assertInstanceOf('Zend\Cache\Storage\Plugin\\' . $plugins[$i++], $plugin);
        }
    }

    public function testFactoryWithPluginsAndOptionsArray()
    {
        $factory = array(
            'adapter' => array(
                 'name' => 'Memory',
                 'options' => array(
                     'ttl' => 123,
                     'namespace' => 'willBeOverwritten'
                 ),
            ),
            'plugins' => array(
                // plugin as a simple string entry
                'Serializer',

                // plugin as name-options pair
                'ClearByFactor' => array(
                    'clearing_factor' => 1,
                ),

                // plugin with full definition
                array(
                    'name'     => 'IgnoreUserAbort',
                    'priority' => 100,
                    'options'  => array(
                        'exit_on_abort' => false,
                    ),
                ),
            ),
            'options' => array(
                'namespace' => 'test',
            )
        );
        $storage = Cache\StorageFactory::factory($factory);

        // test adapter
        $this->assertInstanceOf('Zend\Cache\Storage\Adapter\\' . $factory['adapter']['name'], $storage);
        $this->assertEquals(123, $storage->getOptions()->getTtl());
        $this->assertEquals('test', $storage->getOptions()->getNamespace());

        // test plugin structure
        foreach ($storage->getPlugins() as $i => $plugin) {

            // test plugin options
            $pluginClass = get_class($plugin);
            switch ($pluginClass) {
                case 'Zend\Cache\Storage\Plugin\ClearByFactor':
                    $this->assertSame(
                        $factory['plugins']['ClearByFactor']['clearing_factor'],
                        $plugin->getOptions()->getClearingFactor()
                    );
                    break;

                case 'Zend\Cache\Storage\Plugin\Serializer':
                    break;

                case 'Zend\Cache\Storage\Plugin\IgnoreUserAbort':
                    $this->assertFalse($plugin->getOptions()->getExitOnAbort());
                    break;

                default:
                    $this->fail("Unexpected plugin class '{$pluginClass}'");
            }

        }
    }

}
