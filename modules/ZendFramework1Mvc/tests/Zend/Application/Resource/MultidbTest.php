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

namespace ZendTest\Application\Resource;

use Zend\Application\Resource\MultiDb as MultidbResource,
    Zend\Application,
    Zend\Controller\Front as FrontController,
    Zend\Db\Table\Table as DBTable,
    Zend\Cache\Cache;

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application
 */
class MultidbResourceTest extends \PHPUnit_Framework_TestCase
{
    protected $_dbOptions = array(
        'db1' => array(
            'adapter'  => 'PdoMysql',
            'dbname'   => 'db1',
            'password' => 'XXXX',
            'username' => 'webuser',
        ),
        'db2' => array(
            'adapter'  => 'Pdo\Sqlite', 
            'dbname'   => 'db2', 
            'password' => 'notthatpublic', 
            'username' => 'dba',
        )
    );
    
    public function setUp()
    {
        // Store original autoloaders
        $this->loaders = spl_autoload_functions();
        if (!is_array($this->loaders)) {
            // spl_autoload_functions does not return empty array when no
            // autoloaders registered...
            $this->loaders = array();
        }

        $this->application = new Application\Application('testing');
        $this->bootstrap = new Application\Bootstrap($this->application);

        FrontController::getInstance()->resetInstance();
    }

    public function tearDown()
    {
        DBTable::setDefaultAdapter(null);
        DBTable::setDefaultMetadataCache();
        // Restore original autoloaders
        $loaders = spl_autoload_functions();
        foreach ($loaders as $loader) {
            spl_autoload_unregister($loader);
        }

        foreach ($this->loaders as $loader) {
            spl_autoload_register($loader);
        }
    }

    public function testInitializationInitializesResourcePluginObject()
    {
        $resource = new MultidbResource(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions($this->_dbOptions);
        $res = $resource->init();
        $this->assertTrue($res instanceof MultidbResource);
    }

    public function testDbsAreSetupCorrectlyObject()
    {
        $resource = new MultidbResource(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions($this->_dbOptions);
        $res = $resource->init();
        $this->assertTrue($res->getDb('db1') instanceof \Zend\Db\Adapter\PdoMysql);
        $this->assertTrue($res->getDb('db2') instanceof \Zend\Db\Adapter\Pdo\Sqlite);
    }

    public function testGetDefaultIsSetAndReturnedObject()
    {
        $options = $this->_dbOptions;
        $options['db2']['default'] = true;
        
        $resource = new MultidbResource(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions($options);
        $res = $resource->init();
        $this->assertTrue($res->getDb() instanceof \Zend\Db\Adapter\Pdo\Sqlite);
        $this->assertTrue($res->isDefault($res->getDb('db2')));
        $this->assertTrue($res->isDefault('db2'));

        $options = $this->_dbOptions;
        $options['db2']['isDefaultTableAdapter'] = true;
        
        $resource = new MultidbResource(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions($options);
        $res = $resource->init();
        $this->assertTrue($res->getDb() instanceof \Zend\Db\Adapter\Pdo\Sqlite);
        $this->assertTrue($res->isDefault($res->getDb('db2')));
        $this->assertTrue($res->isDefault('db2'));
        $this->assertTrue(DBTable::getDefaultAdapter() instanceof \Zend\Db\Adapter\Pdo\Sqlite);
    }

    public function testGetDefaultRandomWhenNoDefaultWasSetObject()
    {
        $resource = new MultidbResource(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions($this->_dbOptions);
        $res = $resource->init();
        $this->assertTrue($res->getDefaultDb() instanceof \Zend\Db\Adapter\PdoMysql);
        $this->assertTrue($res->getDefaultDb(true) instanceof \Zend\Db\Adapter\PdoMysql);
        $this->assertNull($res->getDefaultDb(false));
    }

    public function testGetDbWithFaultyDbNameThrowsException()
    {
        $resource = new MultidbResource(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions($this->_dbOptions);
        $res = $resource->init();

        $this->setExpectedException('Zend\Application\Resource\Exception\InitializationException', 'A DB adapter was tried to retrieve, but was not configured');
        $res->getDb('foobar');
    }

    /**
     * @group ZF-9131
     */
    public function testParamDefaultAndAdapterAreNotPassedOnAsParameter()
    {
        $resource = new MultidbResource(array());
        $resource->setBootstrap($this->bootstrap);
        $options = $this->_dbOptions;
        $options['db2']['isDefaultTableAdapter'] = true;
        $resource->setOptions($options);
        $res = $resource->init();

        $expected = array(
            'dbname'         => 'db2',
            'password'       => 'notthatpublic',
            'username'       => 'dba',
            'charset'        => null,
            'persistent'     => false,
            'options'        => array(
                'caseFolding'          => 0, 
                'autoQuoteIdentifiers' => true,
            ),
            'driver_options' => array());
        $this->assertEquals($expected, $res->getDb('db2')->getConfig());

        $options = $this->_dbOptions;
        $options['db2']['default'] = true;
        $resource->setOptions($options);
        $res = $resource->init();
        $this->assertEquals($expected, $res->getDb('db2')->getConfig());
    }

    /**
     * @group ZF-10049
     */
    public function testSetDefaultMetadataCache()
    {
        $cache = Cache::factory('Core', 'BlackHole', array(
            'lifetime' => 120,
            'automatic_serialization' => true
        ));

        $options = $this->_dbOptions;
        $options['defaultMetadataCache'] = $cache;
        $resource = new MultidbResource($options);
        $resource->init();
        $this->assertInstanceOf('Zend\Cache\Frontend\Core', DBTable::getDefaultMetadataCache());
    }

    /**
     * @group ZF-10049
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
                    'name' => 'Black Hole'
                )
            )
        );
        $this->bootstrap->getBroker()->registerSpec('cachemanager', $configCache);

        $options = $this->_dbOptions;
        $options['defaultMetadataCache'] = 'database';
        $resource = new MultidbResource($options);
        $resource->setBootstrap($this->bootstrap);
        $resource->init();
        $this->assertInstanceOf('Zend\Cache\Frontend\Core', DBTable::getDefaultMetadataCache());
    }
}
