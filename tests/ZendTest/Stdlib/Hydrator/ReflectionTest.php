<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\Hydrator;

use stdClass;
use Zend\Stdlib\Hydrator\Reflection;

/**
 * Unit tests for {@see \Zend\Stdlib\Hydrator\Reflection}
 *
 * @covers \Zend\Stdlib\Hydrator\Reflection
 * @group Zend_Stdlib
 */
class ReflectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Reflection
     */
    protected $hydrator;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->hydrator = new Reflection();
    }

    public function testCanExtract()
    {
        $this->assertSame(array(), $this->hydrator->extract(new stdClass()));
    }

    public function testCanHydrate()
    {
        $object = new stdClass();

        $this->assertSame($object, $this->hydrator->hydrate(array('foo' => 'bar'), $object));
    }
}
