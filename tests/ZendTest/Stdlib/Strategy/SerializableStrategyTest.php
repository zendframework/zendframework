<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Stdlib
 */

namespace ZendTest\Stdlib\Strategy;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Stdlib\Hydrator\Strategy\SerializableStrategy;
use Zend\Serializer\Serializer;

class SerializableStrategyTest extends TestCase
{
    public function testCannotUseBadArgumentSerilizer()
    {
        $this->setExpectedException('Zend\Stdlib\Exception\InvalidArgumentException');
        $serializerStrategy = new SerializableStrategy(false);
    }

    public function testUseBadSerilizerObject()
    {
        $serializer = Serializer::factory('phpserialize');
        $serializerStrategy = new SerializableStrategy($serializer);
        $this->assertEquals($serializer, $serializerStrategy->getSerializer());
    }

    public function testUseBadSerilizerString()
    {
        $serializerStrategy = new SerializableStrategy('phpserialize');
        $this->assertEquals('Zend\Serializer\Adapter\PhpSerialize', get_class($serializerStrategy->getSerializer()));
    }

    public function testCanSerialize()
    {
        $serializer = Serializer::factory('phpserialize');
        $serializerStrategy = new SerializableStrategy($serializer);
        $serialized = $serializerStrategy->extract('foo');
        $this->assertEquals($serialized, 's:3:"foo";');
    }

    public function testCanUnserialize()
    {
        $serializer = Serializer::factory('phpserialize');
        $serializerStrategy = new SerializableStrategy($serializer);
        $serialized = $serializerStrategy->hydrate('s:3:"foo";');
        $this->assertEquals($serialized, 'foo');
    }
}
