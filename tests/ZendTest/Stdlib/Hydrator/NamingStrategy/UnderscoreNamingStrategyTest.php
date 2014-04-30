<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\Hydrator\NamingStrategy;

use Zend\Stdlib\Hydrator\NamingStrategy\UnderscoreNamingStrategy;

/**
 * Unit tests for {@see \Zend\Stdlib\Hydrator\NamingStrategy\UnderscoreNamingStrategy}
 *
 * @covers \Zend\Stdlib\Hydrator\NamingStrategy\UnderscoreNamingStrategy
 */
class UnderscoreNamingStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testNameHydratesToCamelCase()
    {
        $strategy = new UnderscoreNamingStrategy();
        $this->assertEquals('fooBarBaz', $strategy->hydrate('foo_bar_baz'));
    }

    public function testNameExtractsToUnderscore()
    {
        $strategy = new UnderscoreNamingStrategy();
        $this->assertEquals('foo_bar_baz', $strategy->extract('fooBarBaz'));
    }
}
