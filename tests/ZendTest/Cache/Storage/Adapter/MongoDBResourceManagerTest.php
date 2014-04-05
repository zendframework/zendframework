<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Cache\Storage\Adapter;

use DateTime;
use Zend\Cache\Exception;
use Zend\Cache\Storage\Adapter\MongoDBResourceManager;
use Zend\Config\Config;

/**
 * @group      Zend_Cache
 */
class MongoDBResourceManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $object;

    public function setUp()
    {
        if (!defined('TESTS_ZEND_CACHE_MONGODB_ENABLED') || !TESTS_ZEND_CACHE_MONGODB_ENABLED) {
            $this->markTestSkipped("Skipped by TestConfiguration (TESTS_ZEND_CACHE_MONGODB_ENABLED)");
        }

        if (!extension_loaded('mongo') || !class_exists('\Mongo') || !class_exists('\MongoClient')) {
            // Allow tests to run if Mongo extension is loaded, or we have a polyfill in place
            $this->markTestSkipped("Mongo extension is not loaded");
        }

        $this->object = new MongoDBResourceManager();
    }

    public function testSetResourceAlreadyCreated()
    {
        $this->assertAttributeEmpty('resources', $this->object);

        $id = 'foo';

        $clientClass = (version_compare(phpversion('mongo'), '1.3.0', '<')) ? '\Mongo' : '\MongoClient';
        $client = new $clientClass(TESTS_ZEND_CACHE_MONGODB_CONNECTSTRING);
        $resource = $client->selectCollection(TESTS_ZEND_CACHE_MONGODB_DATABASE, TESTS_ZEND_CACHE_MONGODB_COLLECTION);

        $this->object->setResource($id, $resource);

        $expected = array($id => array('initialized' => true, 'resource' => $resource));

        $this->assertAttributeSame($expected, 'resources', $this->object);
    }

    public function testSetResourceArray()
    {
        $this->assertAttributeEmpty('resources', $this->object);

        $id = 'foo';
        $array = array('foo' => 'bar');

        $this->object->setResource($id, $array);

        $expected = array(
            $id => array(
                'collection' => 'cache',
                'database' => 'zend',
                'driverOptions' => array(),
                'foo' => 'bar',
                'initialized' => false,
                'options' => array(
                    'fsync' => false,
                    'journal' => true,
                ),
                'server' => 'mongodb://localhost:27017',
            )
        );

        $this->assertAttributeEquals($expected, 'resources', $this->object);
    }

    public function testSetResourceTraversible()
    {
        $this->assertAttributeEmpty('resources', $this->object);

        $id = 'foo';
        $config = new Config(array('foo' => 'bar'));

        $this->object->setResource($id, $config);

        $expected = array(
            $id => array(
                'collection' => 'cache',
                'database' => 'zend',
                'driverOptions' => array(),
                'foo' => 'bar',
                'initialized' => false,
                'options' => array(
                    'fsync' => false,
                    'journal' => true,
                ),
                'server' => 'mongodb://localhost:27017',
            )
        );

        $this->assertAttributeEquals($expected, 'resources', $this->object);
    }

    public function testSetResourceThrowsException()
    {
        $id = 'foo';
        $resource = new \stdClass();

        try {
            $this->object->setResource($id, $resource);
        } catch (Exception\InvalidArgumentException $e) {
            $this->addToAssertionCount(1);
            return;
        }

        $this->fail('Exception not thrown');
    }

    public function testHasResourceEmpty()
    {
        $id = 'foo';

        $this->assertFalse($this->object->hasResource($id));
    }

    public function testHasResourceSet()
    {
        $id = 'foo';
        $config = new \Zend\Config\Config(array('foo' => 'bar'));

        $this->object->setResource($id, $config);

        $this->assertTrue($this->object->hasResource($id));
    }

    public function testGetResourceNotSet()
    {
        $id = 'foo';

        $this->assertFalse($this->object->hasResource($id));

        try {
            $this->object->getResource($id);
        } catch (Exception\RuntimeException $e) {
            $this->addToAssertionCount(1);
            return;
        }

        $this->fail('Exception not thrown');
    }

    public function testGetResourceInitialized()
    {
        $id = 'foo';

        $clientClass = (version_compare(phpversion('mongo'), '1.3.0', '<')) ? '\Mongo' : '\MongoClient';
        $client = new $clientClass(TESTS_ZEND_CACHE_MONGODB_CONNECTSTRING);
        $resource = $client->selectCollection(TESTS_ZEND_CACHE_MONGODB_DATABASE, TESTS_ZEND_CACHE_MONGODB_COLLECTION);

        $this->object->setResource($id, $resource);

        $this->assertSame($resource, $this->object->getResource($id));
    }

    public function testGetResourceNewResource()
    {
        $id = 'foo';
        $resource = array();

        $this->object->setResource($id, $resource);

        $this->assertInstanceOf('\MongoCollection', $this->object->getResource($id));
    }

    public function testGetResourceNewResourceThrowsException()
    {
        $id = 'foo';
        $resource = array('server' => 'mongodb://example.com', 'options' => array('connectTimeoutMS' => 5));

        $this->object->setResource($id, $resource);

        try {
            $this->object->getResource($id);
        } catch (Exception\RuntimeException $e) {
            $this->addToAssertionCount(1);
            return;
        }

        $this->fail('Exception not thrown');
    }

    public function testSetLibOptionsNoResource()
    {
        $this->assertAttributeEmpty('resources', $this->object);

        $id = 'foo';
        $libOptions = array(
            'server' => 'bar'
        );

        $this->object->setLibOptions($id, $libOptions);

        $expected = array(
            $id => array(
                'collection' => 'cache',
                'database' => 'zend',
                'driverOptions' => array(),
                'initialized' => false,
                'options' => array(
                    'fsync' => false,
                    'journal' => true,
                ),
                'server' => 'bar',
            )
        );

        $this->assertAttributeEquals($expected, 'resources', $this->object);
    }

    public function testSetLibOptionsResourceExists()
    {
        $id = 'foo';
        $clientClass = (version_compare(phpversion('mongo'), '1.3.0', '<')) ? '\Mongo' : '\MongoClient';
        $client = new $clientClass(TESTS_ZEND_CACHE_MONGODB_CONNECTSTRING);
        $resource = $client->selectCollection(TESTS_ZEND_CACHE_MONGODB_DATABASE, TESTS_ZEND_CACHE_MONGODB_COLLECTION);

        $this->object->setResource($id, $resource);

        $libOptions = array(
            'server' => 'bar'
        );

        $this->object->setLibOptions($id, $libOptions);

        $expected = array(
            $id => array(
                'initialized' => false,
                'server' => 'bar',
            )
        );

        $this->assertAttributeEquals($expected, 'resources', $this->object);
    }

    public function testCreateMongoDate()
    {
        $this->assertInstanceOf('\MongoDate', $this->object->createMongoDate());
    }

    public function testCreateMongoDateWithDateTime()
    {
        $date = new DateTime();

        $return = $this->object->createMongoDate($date);

        $this->assertEquals($date->getTimestamp(), $return->sec);
    }
}
