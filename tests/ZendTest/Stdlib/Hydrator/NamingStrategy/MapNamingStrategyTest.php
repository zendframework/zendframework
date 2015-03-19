<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\Hydrator\NamingStrategy;

use Zend\Stdlib\Hydrator\NamingStrategy\MapNamingStrategy;

class MapNamingStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testHydrateMap()
    {
        $namingStrategy = new MapNamingStrategy(array('foo' => 'bar'));

        $this->assertEquals('bar', $namingStrategy->hydrate('foo'));
        $this->assertEquals('foo', $namingStrategy->extract('bar'));
    }

    public function testHydrateAndExtractMaps()
    {
        $namingStrategy = new MapNamingStrategy(
            array('foo' => 'foo-hydrated'),
            array('bar' => 'bar-extracted')
        );

        $this->assertEquals('foo-hydrated', $namingStrategy->hydrate('foo'));
        $this->assertEquals('bar-extracted', $namingStrategy->extract('bar'));
    }

    public function testSingleMapInvalidValue()
    {
        $this->setExpectedException('InvalidArgumentException');
        new MapNamingStrategy(array('foo' => 3.1415));
    }
}
