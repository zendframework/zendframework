<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\Hydrator\NamingStrategy;

use Zend\Stdlib\Hydrator\NamingStrategy\MapNamingStrategy;

class MapNamingStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyMap()
    {
        $namingStrategy = new MapNamingStrategy();

        $this->assertEquals('foo', $namingStrategy->hydrate('foo'));
        $this->assertEquals('bar', $namingStrategy->extract('bar'));
    }

    public function testFlippedMap()
    {
        $namingStrategy = new MapNamingStrategy(array('foo' => 'bar'));

        $this->assertEquals('bar', $namingStrategy->hydrate('foo'));
        $this->assertEquals('foo', $namingStrategy->extract('bar'));
    }

    public function testSeparatedMaps()
    {
        $namingStrategy = new MapNamingStrategy(array('foo' => 'bar'), array('bar' => 'foo'));

        $this->assertEquals('bar', $namingStrategy->hydrate('foo'));
        $this->assertEquals('foo', $namingStrategy->extract('bar'));
    }
}
