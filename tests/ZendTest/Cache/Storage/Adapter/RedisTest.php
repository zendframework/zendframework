<?php

namespace ZendTest\Cache\Storage\Adapter;

use Zend\Cache;
use Redis as RedisResource;

class RedisTest extends CommonAdapterTest
{

    /**
     *
     * @var Cache\Storage\Adapter\RedisOptions
     */
    protected $_options;

    /**
     *
     * @var Cache\Storage\Adapter\Redis
     */
    protected $_storage;

    public function setUp()
    {
        if (!defined('TESTS_ZEND_CACHE_REDIS_ENABLED') || !TESTS_ZEND_CACHE_REDIS_ENABLED) {
            $this->markTestSkipped("Skipped by TestConfiguration (TESTS_ZEND_CACHE_REDIS_ENABLED)");
        }

        if (!extension_loaded('redis')) {
            $this->markTestSkipped("Redis extension is not loaded");
        }

        $this->_options  = new Cache\Storage\Adapter\RedisOptions(array(
            'resource_id' => __CLASS__,
        ));

        if (defined('TESTS_ZEND_CACHE_REDIS_HOST') && defined('TESTS_ZEND_CACHE_REDIS_PORT')) {
            $this->_options->getResourceManager()->setServer(__CLASS__, array(
                TESTS_ZEND_CACHE_REDIS_HOST, TESTS_ZEND_CACHE_REDIS_PORT, 1
            ));
        } elseif (defined('TESTS_ZEND_CACHE_REDIS_HOST')) {
            $this->_options->getResourceManager()->setServer(__CLASS__, array(
                TESTS_ZEND_CACHE_REDIS_HOST
            ));
        }

        if (defined('TESTS_ZEND_CACHE_REDIS_DATABASE')) {
            $this->_options->getResourceManager()->setDatabase(__CLASS__,
                TESTS_ZEND_CACHE_REDIS_DATABASE
            );
        }

        if (defined('TESTS_ZEND_CACHE_REDIS_PASSWORD')) {
            $this->_options->getResourceManager()->setPassword(__CLASS__,
                TESTS_ZEND_CACHE_REDIS_PASSWORD
            );
        }
        $this->_storage = new Cache\Storage\Adapter\Redis();

        $this->_storage->setOptions($this->_options);
        $this->_storage->flush();
        parent::setUp();
    }

    public function tearDown()
    {
        if ($this->_storage) {
            $this->_storage->flush();
        }

        parent::tearDown();
    }

    /* Redis */

    public function testRedisCacheStore()
    {
        $key = 'singleKey';
        //assure that there's nothing under key
        $this->_storage->removeItem($key);
        $this->assertNull($this->_storage->getItem($key));
        $this->_storage->setItem($key, serialize(array('test', array('one', 'two'))));

        $this->assertCount(2, unserialize($this->_storage->getItem($key)), 'Get item should return array of two elements');

        $expectedVals = array(
            'key1' => 'val1',
            'key2' => 'val2',
            'key3' => array('val3', 'val4'),
        );

        $this->_storage->setItems($expectedVals);

        $this->assertCount(
            3,
            $this->_storage->getItems(array_keys($expectedVals)),
                'Multiple set/get items didnt save correct amount of rows'
        );
    }

    public function testRedisSerializer()
    {
        $this->_storage->addPlugin(new \Zend\Cache\Storage\Plugin\Serializer());
        $value = array('test', 'of', 'array');
        $this->_storage->setItem('key', $value);

        $this->assertCount(count($value), $this->_storage->getItem('key'), 'Problem with Redis serialization');
    }

    public function testRedisSetInt()
    {
        $key = 'key';
        $this->assertTrue($this->_storage->setItem($key, 123));
        $this->assertEquals('123', $this->_storage->getItem($key), 'Integer should be cast to string');
    }

    public function testRedisSetDouble()
    {
        $key = 'key';
        $this->assertTrue($this->_storage->setItem($key, 123.12));
        $this->assertEquals('123.12', $this->_storage->getItem($key), 'Integer should be cast to string');
    }

    public function testRedisSetNull()
    {
        $key = 'key';
        $this->assertTrue($this->_storage->setItem($key, null));
        $this->assertEquals('', $this->_storage->getItem($key), 'Null should be cast to string');
    }

    public function testRedisSetBoolean()
    {
        $key = 'key';
        $this->assertTrue($this->_storage->setItem($key, true));
        $this->assertEquals('1', $this->_storage->getItem($key), 'Boolean should be cast to string');
        $this->assertTrue($this->_storage->setItem($key, false));
        $this->assertEquals('', $this->_storage->getItem($key), 'Boolean should be cast to string');
    }

    public function testGetCapabilitiesTtl()
    {
        $host = defined('TESTS_ZEND_CACHE_REDIS_HOST') ? TESTS_ZEND_CACHE_REDIS_HOST : '127.0.0.1';
        $port = defined('TESTS_ZEND_CACHE_REDIS_PORT') ? TESTS_ZEND_CACHE_REDIS_PORT : 6379;
        $redisResource = new RedisResource();
        $redisResource->connect($host, $port);
        $info = $redisResource->info();
        $mayorVersion = (int)$info['redis_version'];

        $this->assertEquals($mayorVersion, $this->_options->getResourceManager()->getMayorVersion($this->_options->getResourceId()));

        $capabilities = $this->_storage->getCapabilities();
        if ($mayorVersion < 2) {
            $this->assertEquals(0, $capabilities->getMinTtl(), 'Redis version < 2.0.0 does not support key expiration');
        } else {
            $this->assertEquals(1, $capabilities->getMinTtl(), 'Redis version > 2.0.0 supports key expiration');
        }
    }

    /* ResourceManager */

    public function testSocketConnection()
    {
        $socket = '/tmp/redis.sock';
        $this->_options->getResourceManager()->setServer($this->_options->getResourceId(), $socket);
        $normalized = $this->_options->getResourceManager()->getServer($this->_options->getResourceId());
        $this->assertEquals($socket, $normalized['host'], 'Host should equal to socket {$socket}');

        $this->_storage = null;
    }

    public function testGetSetDatabase()
    {
        $this->assertTrue($this->_storage->setItem('key', 'val'));
        $this->assertEquals('val', $this->_storage->getItem('key'));

        $databaseNumber = 1;
        $resourceManager = $this->_options->getResourceManager();
        $resourceManager->setDatabase($this->_options->getResourceId(), $databaseNumber);
        $this->assertNull($this->_storage->getItem('key'), 'No value should be found because set was done on different database than get');
        $this->assertEquals($databaseNumber, $resourceManager->getDatabase($this->_options->getResourceId()), 'Incorrect database was returned');
    }

    public function testGetSetPassword()
    {
        $pass = 'super secret';
        $this->_options->getResourceManager()->setPassword($this->_options->getResourceId(), $pass);
        $this->assertEquals(
            $pass,
            $this->_options->getResourceManager()->getPassword($this->_options->getResourceId()),
            'Password was not correctly set'
        );
    }

    /* RedisOptions */

    public function testGetSetNamespace()
    {
        $namespace = 'testNamespace';
        $this->_options->setNamespace($namespace);
        $this->assertEquals($namespace, $this->_options->getNamespace(), 'Namespace was not set correctly');
    }

    public function testGetSetNamespaceSeparator()
    {
        $separator = '/';
        $this->_options->setNamespaceSeparator($separator);
        $this->assertEquals($separator, $this->_options->getNamespaceSeparator(), 'Separator was not set correctly');
    }

    public function testGetSetResourceManager()
    {
        $resourceManager = new \Zend\Cache\Storage\Adapter\RedisResourceManager();
        $options = new \Zend\Cache\Storage\Adapter\RedisOptions();
        $options->setResourceManager($resourceManager);
        $this->assertInstanceOf(
            'Zend\\Cache\\Storage\\Adapter\\RedisResourceManager',
            $options->getResourceManager(),
            'Wrong resource manager retuned, it should of type RedisResourceManager'
        );

        $this->assertEquals($resourceManager, $options->getResourceManager());
    }

    public function testGetSetResourceId()
    {
        $resourceId = '1';
        $options = new \Zend\Cache\Storage\Adapter\RedisOptions();
        $options->setResourceId($resourceId);
        $this->assertEquals($resourceId, $options->getResourceId(), 'Resource id was not set correctly');
    }

    public function testGetSetPersistentId()
    {
        $persistentId = '1';
        $this->_options->setPersistentId($persistentId);
        $this->assertEquals($persistentId, $this->_options->getPersistentId(), 'Persistent id was not set correctly');
    }

    public function testOptionsGetSetLibOptions()
    {
        $options = array('serializer', RedisResource::SERIALIZER_PHP);
        $this->_options->setLibOptions($options);
        $this->assertEquals($options, $this->_options->getLibOptions(), 'Lib Options were not set correctly through RedisOptions');
    }

    public function testGetSetServer()
    {
        $server = array(
            'host' => '127.0.0.1',
            'port' => 6379,
            'timeout' => 0,
        );
        $this->_options->setServer($server);
        $this->assertEquals($server, $this->_options->getServer(), 'Server was not set correctly through RedisOptions');
    }

    public function testOptionsGetSetDatabase()
    {
        $database = 1;
        $this->_options->setDatabase($database);
        $this->assertEquals($database, $this->_options->getDatabase(), 'Database not set correctly using RedisOptions');
    }

}
