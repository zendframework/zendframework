<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Cache\Storage\Adapter;

use Zend\Cache\Storage\Adapter\MongoDbOptions;
use Zend\Cache\Storage\Adapter\MongoDbResourceManager;

/**
 * @group      Zend_Cache
 */
class MongoDbOptionsTest extends \PHPUnit_Framework_TestCase
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

        $this->object = new MongoDbOptions();
    }

    public function testSetNamespaceSeparator()
    {
        $this->assertAttributeEquals(':', 'namespaceSeparator', $this->object);

        $namespaceSeparator = '_';

        $this->object->setNamespaceSeparator($namespaceSeparator);

        $this->assertAttributeEquals($namespaceSeparator, 'namespaceSeparator', $this->object);
    }

    public function testGetNamespaceSeparator()
    {
        $this->assertEquals(':', $this->object->getNamespaceSeparator());

        $namespaceSeparator = '_';

        $this->object->setNamespaceSeparator($namespaceSeparator);

        $this->assertEquals($namespaceSeparator, $this->object->getNamespaceSeparator());
    }

    public function testSetResourceManager()
    {
        $this->assertAttributeEquals(null, 'resourceManager', $this->object);

        $resourceManager = new MongoDbResourceManager();

        $this->object->setResourceManager($resourceManager);

        $this->assertAttributeSame($resourceManager, 'resourceManager', $this->object);
    }

    public function testGetResourceManager()
    {
        $this->assertInstanceOf(
            '\Zend\Cache\Storage\Adapter\MongoDbResourceManager', $this->object->getResourceManager()
        );

        $resourceManager = new MongoDbResourceManager();

        $this->object->setResourceManager($resourceManager);

        $this->assertSame($resourceManager, $this->object->getResourceManager());
    }

    public function testSetResourceId()
    {
        $this->assertAttributeEquals('default', 'resourceId', $this->object);

        $resourceId = 'foo';

        $this->object->setResourceId($resourceId);

        $this->assertAttributeEquals($resourceId, 'resourceId', $this->object);
    }

    public function testGetResourceId()
    {
        $this->assertEquals('default', $this->object->getResourceId());

        $resourceId = 'foo';

        $this->object->setResourceId($resourceId);

        $this->assertEquals($resourceId, $this->object->getResourceId());
    }

    public function testSetServer()
    {
        $resourceManager = new MongoDbResourceManager();
        $this->object->setResourceManager($resourceManager);

        $resourceId = $this->object->getResourceId();
        $server     = 'mongodb://test:1234';

        $this->assertFalse($this->object->getResourceManager()->hasResource($resourceId));

        $this->object->setServer($server);
        $this->assertSame($server, $this->object->getResourceManager()->getServer($resourceId));
    }
}
