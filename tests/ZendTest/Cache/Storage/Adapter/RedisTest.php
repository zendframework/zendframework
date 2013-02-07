<?php

namespace ZendTest\Cache\Storage\Adapter;

use Zend\Cache;

class RedisTest extends CommonAdapterTest
{

    protected $redisOptions;

    protected $redis;

    public function setUp()
    {
        $this->redisOptions = new Cache\Storage\Adapter\RedisOptions(
            array(
                'host' => 'ftdb',
                'port' => 6379,
                'timeout' => 1,
                'database' => 4,
                'password' => 'Iireew8aiphuNg7jeebu',
            )
        );

        $this->redisStorage = new Cache\Storage\Adapter\Redis($this->redisOptions);
        parent::setUp();
    }

    public function testRedisCacheStore()
    {
        $key = 'singleKey';
        //assure that there's nothing under key
        $this->redisStorage->removeItem($key);
        $this->assertFalse($this->redisStorage->getItem($key));
        $this->redisStorage->setItem($key, serialize(array('test', array('one', 'two'))));

        $this->assertCount(2, unserialize($this->redisStorage->getItem($key)), 'Get item should return array of two elements');

        $expectedVals = array(
            'key1' => 'val1',
            'key2' => 'val2',
            'key3' => array('val3', 'val4'),
        );

        $this->redisStorage->setItems($expectedVals);

        $this->assertCount(
            3,
            $this->redisStorage->getItems(array_keys($expectedVals)),
                'Multiple set/get items didnt save correct amount of rows'
        );
    }

    public function testRedisRemoveItem()
    {
        $key = 'newKey';
        $this->redisStorage->setItem($key, 'test value');

        $this->assertEquals('test value', $this->redisStorage->getItem($key), 'Value should be stored in redis');

        $this->redisStorage->removeItem($key);

        $this->assertFalse($this->redisStorage->getItem($key), 'Item should be deleted from redis but its still available');
    }

    public function testRedisHasItem()
    {
        $key = 'newKey';
        $this->redisStorage->setItem($key, 'test val');

        $this->assertTrue($this->redisStorage->hasItem($key), 'Item should be saved into redis, but check item doesnt detect it');
    }

    public function testMultiGetAndMultiSet()
    {
        $save = array(
            'key1' => 'aaa',
            'key2' => 'bbb',
            'key3' => 'ccc',
        );

        $this->redisStorage->setItems($save);

        foreach ($save as $key => $value) {
            $this->assertEquals($value, $this->redisStorage->getItem($key), 'Multi save didn\'t work, one of the keys wasnt found in redis');
        }
    }

    public function testRedisSerializer()
    {
        $this->redisStorage->addPlugin(new \Zend\Cache\Storage\Plugin\Serializer());
        $value = array('test', 'of', 'array');
        $this->redisStorage->setItem('key', $value);

        $this->assertCount(count($value), $this->redisStorage->getItem('key'), 'Redis didn\'t save correctly array value');
    }

    public function testFlushingOfDatabase()
    {
        $key = 'newKey';
        $this->redisStorage->setItem($key, 'test val');

        $this->assertEquals('test val', $this->redisStorage->getItem($key), 'Value wasn\'t saved into cache');

        $this->redisStorage->flush();

        $this->assertFalse($this->redisStorage->getItem($key), 'Database wasn\'t flushed');
    }

    public function tearDown()
    {
        $this->redisStorage->flush();
    }

}
