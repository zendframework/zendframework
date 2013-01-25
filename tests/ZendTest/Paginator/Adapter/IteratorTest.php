<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Paginator
 */

namespace ZendTest\Paginator\Adapter;

use Zend\Paginator\Adapter;
use Zend\Paginator\Paginator;
use Zend\Paginator\Exception;

/**
 * @category   Zend
 * @package    Zend_Paginator
 * @subpackage UnitTests
 * @group      Zend_Paginator
 */
class IteratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\Paginator\Adapter\Iterator
     */
    private $adapter;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();
        $iterator = new \ArrayIterator(range(1, 101));
        $this->adapter = new Adapter\Iterator($iterator);
    }
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        $this->adapter = null;
        parent::tearDown();
    }

    public function testGetsItemsAtOffsetZero()
    {
        $actual = $this->adapter->getItems(0, 10);
        $this->assertInstanceOf('LimitIterator', $actual);

        $i = 1;
        foreach ($actual as $item) {
            $this->assertEquals($i, $item);
            $i++;
        }
    }

    public function testGetsItemsAtOffsetTen()
    {
        $actual = $this->adapter->getItems(10, 10);
        $this->assertInstanceOf('LimitIterator', $actual);

        $i = 11;
        foreach ($actual as $item) {
            $this->assertEquals($i, $item);
            $i++;
        }
    }

    public function testReturnsCorrectCount()
    {
        $this->assertEquals(101, $this->adapter->count());
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
        $this->paginator = new Paginator(new Adapter\Iterator(new \ArrayIterator(array())));
        $items = $this->paginator->getCurrentItems();

        foreach ($items as $item);
    }

    /**
     * @group ZF-8084
     */
    public function testGetItemsSerializable()
    {
        $items = $this->adapter->getItems(0, 1);
        $innerIterator = $items->getInnerIterator();
        $items = unserialize(serialize($items));
        $this->assertTrue( ($items->getInnerIterator() == $innerIterator), 'getItems has to be serializable to use caching');
    }

    /**
     * @group ZF-4151
     */
    public function testEmptySet()
    {
        $iterator = new \ArrayIterator(array());
        $this->adapter = new Adapter\Iterator($iterator);
        $actual = $this->adapter->getItems(0, 10);
        $this->assertEquals(array(), $actual);
    }
}
