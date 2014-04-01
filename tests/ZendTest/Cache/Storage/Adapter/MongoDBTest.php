<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Cache\Storage\Adapter;

use MongoClient;
use Zend\Cache\Storage\Adapter\MongoDB;
use Zend\Cache\Storage\Adapter\MongoDBOptions;

class MongoDBTest extends CommonAdapterTest
{
    public function setUp()
    {
        if (!extension_loaded('mongo')) {
            $this->markTestSkipped("Mongo extension is not loaded");
        }

        $optionsArray = array(
            'collection' => TESTS_ZEND_CACHE_MONGODB_COLLECTION,
            'connectString' => TESTS_ZEND_CACHE_MONGODB_CONNECTSTRING,
            'database' => TESTS_ZEND_CACHE_MONGODB_DATABASE,
        );

        $this->_options = new MongoDBOptions($optionsArray);

        $this->_storage = new MongoDB();
        $this->_storage->setOptions($this->_options);

        parent::setUp();
    }

    public function tearDown()
    {
        $mongo = new MongoClient(TESTS_ZEND_CACHE_MONGODB_CONNECTSTRING);

        $database = TESTS_ZEND_CACHE_MONGODB_DATABASE;
        $collection = TESTS_ZEND_CACHE_MONGODB_COLLECTION;

        $collection = $mongo->selectCollection($database, $collection);
        $collection->drop();
    }

    public function testSetOptionsNotMongoDBOptions()
    {
        $options = array(
            'collection' => TESTS_ZEND_CACHE_MONGODB_COLLECTION,
            'connectString' => TESTS_ZEND_CACHE_MONGODB_CONNECTSTRING,
            'database' => TESTS_ZEND_CACHE_MONGODB_DATABASE,
        );

        $this->_storage->setOptions($options);

        $this->assertInstanceOf('\Zend\Cache\Storage\Adapter\MongoDBOptions', $this->_storage->getOptions());
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
}
