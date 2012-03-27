<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Paginator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Paginator\Adapter;
use Zend\Paginator\Adapter;
use Zend\Paginator\Exception;

/**
 * @category   Zend
 * @package    Zend_Paginator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Paginator
 */
class IteratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Paginator_Adapter_Iterator
     */
    private $_adapter;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();
        $iterator = new \ArrayIterator(range(1, 101));
        $this->_adapter = new Adapter\Iterator($iterator);
    }
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        $this->_adapter = null;
        parent::tearDown();
    }

    public function testGetsItemsAtOffsetZero()
    {
        $actual = $this->_adapter->getItems(0, 10);
        $this->assertInstanceOf('LimitIterator', $actual);

        $i = 1;
        foreach ($actual as $item) {
            $this->assertEquals($i, $item);
            $i++;
        }
    }

    public function testGetsItemsAtOffsetTen()
    {
        $actual = $this->_adapter->getItems(10, 10);
        $this->assertInstanceOf('LimitIterator', $actual);

        $i = 11;
        foreach ($actual as $item) {
            $this->assertEquals($i, $item);
            $i++;
        }
    }

    public function testReturnsCorrectCount()
    {
        $this->assertEquals(101, $this->_adapter->count());
    }

    public function testThrowsExceptionIfNotCountable()
    {
        $iterator = new \LimitIterator(new \ArrayIterator(range(1, 101)));

        $this->setExpectedException('Zend\Paginator\Adapter\Exception\InvalidArgumentException', 'Iterator must implement Countable');
        new Adapter\Iterator($iterator);
    }

    /**
     * @group ZF-4151
     */
    public function testDoesNotThrowOutOfBoundsExceptionIfIteratorIsEmpty()
    {
        $this->_paginator = \Zend\Paginator\Paginator::factory(new \ArrayIterator(array()));
        $items = $this->_paginator->getCurrentItems();
        try {
            foreach ($items as $item);
        } catch (\OutOfBoundsException $e) {
            $this->fail('Empty iterator caused in an OutOfBoundsException');
        }
    }

    /**
     * @group ZF-8084
     */
    public function testGetItemsSerializable() {
        $items = $this->_adapter->getItems(0, 1);
        $innerIterator = $items->getInnerIterator();
        $items = unserialize(serialize($items));
        $this->assertTrue( ($items->getInnerIterator() == $innerIterator), 'getItems has to be serializable to use caching');
    }

    /**
     * @group ZF-4151
     */
    public function testEmptySet() {
        $iterator = new \ArrayIterator(array());
        $this->_adapter = new Adapter\Iterator($iterator);
        $actual = $this->_adapter->getItems(0, 10);
        $this->assertEquals(array(), $actual);
    }
}
