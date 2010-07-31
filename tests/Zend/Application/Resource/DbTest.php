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

namespace ZendTest\Application\Resource;

use Zend\Loader\Autoloader,
    Zend\Application\Application,
    Zend\Application\Resource\Db as DbResource,
    ZendTest\Application\TestAsset\ZfAppBootstrap;

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application
 */
class DbTest extends \PHPUnit_Framework_TestCase
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

        $this->application = new Application('testing');

        $this->bootstrap = new ZfAppBootstrap($this->application);
    }

    public function tearDown()
    {
    	\Zend\Db\Table\AbstractTable::setDefaultMetadataCache();

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

    public function testAdapterIsNullByDefault()
    {
        $resource = new DbResource();
        $this->assertNull($resource->getAdapter());
    }

    public function testDbIsNullByDefault()
    {
        $resource = new DbResource();
        $this->assertNull($resource->getDbAdapter());
    }

    public function testParamsAreEmptyByDefault()
    {
        $resource = new DbResource();
        $params = $resource->getParams();
        $this->assertTrue(empty($params));
    }

    public function testIsDefaultTableAdapter()
    {
        $resource = new DbResource();
        $this->assertTrue($resource->isDefaultTableAdapter());
    }

    public function testPassingDatabaseConfigurationSetsObjectState()
    {
        $config = array(
            'adapter' => 'Pdo\\Sqlite',
            'params'  => array(
                'dbname' => ':memory:',
            ),
            'isDefaultTableAdapter' => false,
        );
        $resource = new DbResource($config);
        $this->assertFalse($resource->isDefaultTableAdapter());
        $this->assertEquals($config['adapter'], $resource->getAdapter());
        $this->assertEquals($config['params'], $resource->getParams());
    }

    public function testInitShouldInitializeDbAdapter()
    {
        $config = array(
            'adapter' => 'Pdo\\Sqlite',
            'params'  => array(
                'dbname' => ':memory:',
            ),
            'isDefaultTableAdapter' => false,
        );
        $resource = new DbResource($config);
        $resource->init();
        $db = $resource->getDbAdapter();
        $this->assertTrue($db instanceof \Zend\Db\Adapter\Pdo\Sqlite);
    }

    /**
     * @group ZF-10033
     */
    public function testSetDefaultMetadataCache()
    {
        $cache = \Zend\Cache\Cache::factory('Core', 'BlackHole', array(
            'lifetime' => 120,
            'automatic_serialization' => true
        ));

        $config = array(
            'adapter' => 'Pdo\Sqlite',
            'params'  => array(
                'dbname' => ':memory:',
            ),
            'defaultMetadataCache' => $cache,
        );
        $resource = new DbResource($config);
        $resource->init();
        $this->assertType('Zend\Cache\Frontend', \Zend\Db\Table\AbstractTable::getDefaultMetadataCache());
    }

    /**
     * @group ZF-10033
     */
    public function testSetDefaultMetadataCacheFromCacheManager()
    {
        $configCache = array(
            'database' => array(
                'frontend' => array(
                    'name' => 'Core',
                    'options' => array(
                        'lifetime' => 120,
                        'automatic_serialization' => true
                    )
                ),
                'backend' => array(
                    'name' => 'BlackHole'
                )
            )
        );
        $this->bootstrap->registerPluginResource('cachemanager', $configCache);

        $config = array(
            'bootstrap' => $this->bootstrap,
            'adapter' => 'Pdo\Sqlite',
            'params'  => array(
                'dbname' => ':memory:',
            ),
            'defaultMetadataCache' => 'database',
        );
        $resource = new DbResource($config);
        $resource->init();
        $this->assertType('Zend\Cache\Frontend', \Zend\Db\Table\AbstractTable::getDefaultMetadataCache());
    }
}
