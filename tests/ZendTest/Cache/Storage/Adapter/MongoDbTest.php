<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Cache\Storage\Adapter;

use Zend\Cache\Storage\Adapter\MongoDb;
use Zend\Cache\Storage\Adapter\MongoDbOptions;

/**
 * @group      Zend_Cache
 */
class MongoDbTest extends CommonAdapterTest
{
    public function setUp()
    {
        if (!defined('TESTS_ZEND_CACHE_MONGODB_ENABLED') || !TESTS_ZEND_CACHE_MONGODB_ENABLED) {
            $this->markTestSkipped("Skipped by TestConfiguration (TESTS_ZEND_CACHE_MONGODB_ENABLED)");
        }

        if (!extension_loaded('mongo') || !class_exists('\Mongo') || !class_exists('\MongoClient')) {
            // Allow tests to run if Mongo extension is loaded, or we have a polyfill in place
            $this->markTestSkipped("Mongo extension is not loaded");
        }

        $this->_options = new MongoDbOptions(array(
            'server'     => TESTS_ZEND_CACHE_MONGODB_CONNECTSTRING,
            'database'   => TESTS_ZEND_CACHE_MONGODB_DATABASE,
            'collection' => TESTS_ZEND_CACHE_MONGODB_COLLECTION,
        ));

        $this->_storage = new MongoDb();
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

    public function testSetOptionsNotMongoDbOptions()
    {
        $this->_storage->setOptions(array(
            'server'     => TESTS_ZEND_CACHE_MONGODB_CONNECTSTRING,
            'database'   => TESTS_ZEND_CACHE_MONGODB_DATABASE,
            'collection' => TESTS_ZEND_CACHE_MONGODB_COLLECTION,
        ));

        $this->assertInstanceOf('\Zend\Cache\Storage\Adapter\MongoDbOptions', $this->_storage->getOptions());
    }

    public function testCachedItemsExpire()
    {
        $ttl = 2;
        $key = 'foo';
        $value = 'bar';

        $this->_storage->getOptions()->setTtl($ttl);

        $this->_storage->setItem($key, $value);

        // wait for the cached item to expire
        sleep($ttl * 2);

        $this->assertNull($this->_storage->getItem($key));
    }

    public function testFlush()
    {
        $key1 = 'foo';
        $key2 = 'key';
        $value1 = 'bar';
        $value2 = 'value';

        $this->assertEquals(array(), $this->_storage->setItems(array(
            $key1 => $value1,
            $key2 => $value2,
        )));

        $this->assertTrue($this->_storage->flush());

        $this->assertEquals(array(), $this->_storage->hasItems(array($key1, $key2)));
    }
}
