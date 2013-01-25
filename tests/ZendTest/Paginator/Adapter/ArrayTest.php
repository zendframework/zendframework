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

/**
 * @category   Zend
 * @package    Zend_Paginator
 * @subpackage UnitTests
 * @group      Zend_Paginator
 */
class ArrayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Zend\Paginator\Adapter\Array
     */
    private $adapter;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();
        $this->adapter = new Adapter\ArrayAdapter(range(1, 101));
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
        $expected = range(1, 10);
        $actual = $this->adapter->getItems(0, 10);
        $this->assertEquals($expected, $actual);
    }

    public function testGetsItemsAtOffsetTen()
    {
        $expected = range(11, 20);
        $actual = $this->adapter->getItems(10, 10);
        $this->assertEquals($expected, $actual);
    }

    public function testReturnsCorrectCount()
    {
        $this->assertEquals(101, $this->adapter->count());
    }


    /**
     * @group ZF-4151
     */
    public function testEmptySet()
    {
        $this->adapter = new Adapter\ArrayAdapter(array());
        $actual = $this->adapter->getItems(0, 10);
        $this->assertEquals(array(), $actual);
    }
}
