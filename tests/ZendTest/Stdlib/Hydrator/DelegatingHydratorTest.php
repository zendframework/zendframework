<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\Hydrator;

use Zend\Stdlib\Hydrator\DelegatingHydrator;
use ArrayObject;

/**
 * Unit tests for {@see \Zend\Stdlib\Hydrator\DelegatingHydrator}
 *
 * @covers \Zend\Stdlib\Hydrator\DelegatingHydrator
 */
class DelegatingHydratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DelegatingHydrator
     */
    protected $hydrator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $hydrators;

    /**
     * @var ArrayObject
     */
    protected $object;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->hydrators = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $this->hydrator = new DelegatingHydrator($this->hydrators);
        $this->object = new ArrayObject;
    }

    public function testExtract()
    {
        $this->hydrators->expects($this->any())
            ->method('has')
            ->with('ArrayObject')
            ->will($this->returnValue(true));

        $hydrator = $this->getMock('Zend\Stdlib\Hydrator\HydratorInterface');

        $this->hydrators->expects($this->any())
            ->method('get')
            ->with('ArrayObject')
            ->will($this->returnValue($hydrator));

        $hydrator->expects($this->any())
            ->method('extract')
            ->with($this->object)
            ->will($this->returnValue(array('foo' => 'bar')));

        $this->assertEquals(array('foo' => 'bar'), $hydrator->extract($this->object));
    }

    public function testHydrate()
    {
        $this->hydrators->expects($this->any())
            ->method('has')
            ->with('ArrayObject')
            ->will($this->returnValue(true));

        $hydrator = $this->getMock('Zend\Stdlib\Hydrator\HydratorInterface');

        $this->hydrators->expects($this->any())
            ->method('get')
            ->with('ArrayObject')
            ->will($this->returnValue($hydrator));

        $hydrator->expects($this->any())
            ->method('hydrate')
            ->with(array('foo' => 'bar'), $this->object)
            ->will($this->returnValue($this->object));
        $this->assertEquals($this->object, $hydrator->hydrate(array('foo' => 'bar'), $this->object));
    }
}
