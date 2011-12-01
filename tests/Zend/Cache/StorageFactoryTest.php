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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

namespace ZendTest\Cache;
use Zend\Cache,
    Zend\Loader\Broker;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
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
        $cache = Cache\StorageFactory::adapterFactory('Memory');
        $this->assertInstanceOf('Zend\Cache\Storage\Adapter\Memory', $cache);
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
        $cache = Cache\StorageFactory::pluginFactory('IgnoreUserAbort', array(
            'adapter' => Cache\StorageFactory::adapterFactory('Memory'),
        ));
        $this->assertInstanceOf('Zend\Cache\Storage\Plugin\IgnoreUserAbort', $cache);
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
        $plugins = array('Serialize', 'IgnoreUserAbort');

        $cache = Cache\StorageFactory::factory(array(
            'adapter' => $adapter,
            'plugins' => $plugins,
        ));

        // test returned class
        $this->assertInstanceOf('Zend\Cache\Storage\Adapter', $cache);

        // test plugin structure
        $i = 0;
        $plugin = $cache;
        do {
            $this->assertInstanceOf('Zend\Cache\Storage\Plugin\\' . $plugins[$i], $plugin);

            $plugin = $plugin->getStorage();
            $i++;
        } while ($plugin instanceof Cache\Storage\Plugin);

        // test adapter
        $this->assertInstanceOf('Zend\Cache\Storage\Adapter\Memory', $plugin);
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
                'Serialize' => array(),
                'IgnoreUserAbort' => array(
                    'exit_on_abort' => true,
                ),
            ),
            'options' => array(
                'namespace' => 'test',
            )
        );
        $storage = Cache\StorageFactory::factory($factory);

        // test plugin structure
        $i = 0;
        do {
            $this->assertInstanceOf('Zend\\Cache\Storage\Plugin', $storage);

            // test plugin options
            switch (get_class($storage)) {
                case 'Zend\Cache\Storage\Plugin\IgnoreUserAbort':
                    $this->assertSame($factory['plugins']['IgnoreUserAbort']['exit_on_abort'], $storage->getExitOnAbort());
                    break;
            }

            $storage = $storage->getStorage();
            $i++;
        } while ($storage instanceof Cache\Storage\Plugin);

        // test adapter
        $this->assertInstanceOf('Zend\Cache\Storage\Adapter\\' . $factory['adapter']['name'], $storage);

        // test adapter options
        $this->assertEquals(123, $storage->getTtl());
        $this->assertEquals('test', $storage->getNamespace());
    }

}
