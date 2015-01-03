<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\Hydrator\NamingStrategy;

use Zend\Stdlib\Hydrator\NamingStrategy\IdentityNamingStrategy;

/**
 * Tests for {@see \Zend\Stdlib\Hydrator\NamingStrategy\IdentityNamingStrategy}
 *
 * @covers \Zend\Stdlib\Hydrator\NamingStrategy\IdentityNamingStrategy
 */
class IdentityNamingStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getTestedNames
     *
     * @param string $name
     */
    public function testHydrate($name)
    {
        $namingStrategy = new IdentityNamingStrategy();

        $this->assertSame($name, $namingStrategy->hydrate($name));
    }

    /**
     * @dataProvider getTestedNames
     *
     * @param string $name
     */
    public function testExtract($name)
    {
        $namingStrategy = new IdentityNamingStrategy();

        $this->assertSame($name, $namingStrategy->extract($name));
    }

    /**
     * Data provider
     *
     * @return string[][]
     */
    public function getTestedNames()
    {
        return array(
            array(123),
            array(0),
            array('foo'),
            array('bar'),
        );
    }
}
