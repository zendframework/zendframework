<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Cache\Storage\Adapter;

use Zend\Cache\Storage\Adapter\MongoDBOptions;

/**
 * @group      Zend_Cache
 */
class MongoDBOptionsTest extends \PHPUnit_Framework_TestCase
{
    protected $object;

    public function setUp()
    {
        $this->object = new MongoDBOptions();
    }

    public function testSetConnectString()
    {
        $this->assertAttributeEquals(null, 'connectString', $this->object);

        $connectString = 'foo';

        $this->object->setConnectString($connectString);

        $this->assertAttributeEquals($connectString, 'connectString', $this->object);
    }

    public function testGetConnectString()
    {
        $this->assertNull($this->object->getConnectString());

        $connectString = 'foo';

        $this->object->setConnectString($connectString);

        $this->assertEquals($connectString, $this->object->getConnectString());
    }

    public function testSetCollection()
    {
        $this->assertAttributeEquals(null, 'collection', $this->object);

        $collection = 'foo';

        $this->object->setCollection($collection);

        $this->assertAttributeEquals($collection, 'collection', $this->object);
    }

    public function testGet()
    {
        $this->assertNull($this->object->getCollection());

        $expected = 'foo';

        $this->object->setCollection($expected);

        $this->assertSame($expected, $this->object->getCollection());
    }

    public function testSetDatabase()
    {
        $this->assertAttributeEmpty('database', $this->object);

        $expected = 'foo';

        $this->object->setDatabase($expected);

        $this->assertAttributeEquals($expected, 'database', $this->object);
    }

    public function testGetDatabase()
    {
        $this->assertNull($this->object->getDatabase());

        $expected = 'foo';

        $this->object->setDatabase($expected);

        $this->assertSame($expected, $this->object->getDatabase());
    }
}
