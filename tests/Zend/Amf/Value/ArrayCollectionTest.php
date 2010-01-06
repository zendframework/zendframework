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
 * @package    Zend_Amf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Amf_Value_ArrayCollectionTest::main');
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';
require_once 'Zend/Amf/Value/Messaging/ArrayCollection.php';

/**
 * Test case for Zend_Amf_Value_MessageBody
 *
 * @category   Zend
 * @package    Zend_Amf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Amf
 */
class Zend_Amf_Value_ArrayCollectionTest extends PHPUnit_Framework_TestCase
{


    /**
     * Refrence to the array collection
     * @var Zend_Amf_Value_Message_ArrayCollection
     */
    protected $_arrayCollection;

    /**
     * Data to be used to populate the ArrayCollection
     */
    protected $_data;
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Amf_Value_ArrayCollectionTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        $data = array();
        $data[] = array('foo' => 'foo1', 'bar' => 'bar1');
        $data[] = array('foo' => 'foo2', 'bar' => 'bar2');
        $this->_data = $data;

    }

    public function tearDown()
    {
        unset($this->_arrayCollection);
        unset($this->_data);
    }

    public function testConstructorArrayCollectionTwo()
    {
        $this->_arrayCollection = new Zend_Amf_Value_Messaging_ArrayCollectionTwo($this->_data);
        $this->assertEquals('bar2', $this->_arrayCollection[1]['bar']);
    }

    /**
     * Check that the ArrayCollection can be accessed like a standard array.
     */
    public function testConstructorArrayCollection()
    {
        $this->_arrayCollection = new Zend_Amf_Value_Messaging_ArrayCollection($this->_data);
        $this->assertEquals('bar2', $this->_arrayCollection[1]['bar']);
    }

    /**
     * Check that we can get the count of the ArrayCollection
     */
    public function testCountable()
    {
        $this->_arrayCollection = new Zend_Amf_Value_Messaging_ArrayCollection($this->_data);
        $this->assertEquals(2, count($this->_arrayCollection));
    }

    /**
     * Test that we can foreach through the ArrayCollection
     */
    public function testIteratorArray()
    {
        $this->_arrayCollection = new Zend_Amf_Value_Messaging_ArrayCollection($this->_data);
        $total = count($this->_arrayCollection);
        $count = 0;
        foreach($this->_arrayCollection as $row) {
            $count++;
        }
        $this->assertEquals(2, $count);
    }

    /**
     * Test that we can alter an item based on it's offset
     */
    public function testOffsetExists()
    {
        $this->_arrayCollection = new Zend_Amf_Value_Messaging_ArrayCollection($this->_data);
        $this->assertTrue($this->_arrayCollection->offsetExists(1));
    }

    /**
     * Check that you can set and get the changes to an offset key.
     */
    public function testOffsetSetGet()
    {
        $this->_arrayCollection = new Zend_Amf_Value_Messaging_ArrayCollection($this->_data);
        $data = array('fooSet' => 'fooSet2', 'barSet' => 'barSet2');
        $this->_arrayCollection->offsetSet(1,$data);
        $this->assertEquals($data, $this->_arrayCollection->offsetGet(1));
    }

    /**
     * Check that you can delete an item from the arraycollection based on key.
     */
    public function testOffsetUnset()
    {
        $this->_arrayCollection = new Zend_Amf_Value_Messaging_ArrayCollection($this->_data);
        $data = array('foo' => 'foo1', 'bar' => 'bar1');
        $this->assertEquals($data, $this->_arrayCollection->offsetGet(0));
        $this->assertEquals(2, count($this->_arrayCollection));
        $this->_arrayCollection->offsetUnset(0);
        $this->assertEquals(1, count($this->_arrayCollection));
    }

    /**
     * Check that you can transform an ArrayCollection into a standard array with iterator_to_array
     */
    public function testIteratorToArray()
    {
        $this->_arrayCollection = new Zend_Amf_Value_Messaging_ArrayCollection($this->_data);
        $standardArray = iterator_to_array($this->_arrayCollection);
        $this->assertTrue(is_array($standardArray));
    }

    /**
     * Make sure that you can append more name values to the arraycollection
     */
    public function testAppend()
    {
        $this->_arrayCollection = new Zend_Amf_Value_Messaging_ArrayCollection($this->_data);
        $arrayCollectionTwo = new Zend_Amf_Value_Messaging_ArrayCollection();
        $arrayCollectionTwo->append(array('foo' => 'foo1', 'bar' => 'bar1'));
        $arrayCollectionTwo->append(array('foo' => 'foo2', 'bar' => 'bar2'));
        $this->assertEquals($arrayCollectionTwo, $this->_arrayCollection);
    }

    /**
     * Test to make sure that when the iterator as data it is a valid iterator
     *
    public function testValid()
    {
        unset($this->_arrayCollection);
        $this->_arrayCollection = new Zend_Amf_Value_Messaging_ArrayCollection();
        $this->assertFalse($this->_arrayCollection->valid());
        unset($this->_arrayCollection);
        $this->_arrayCollection = new Zend_Amf_Value_Messaging_ArrayCollection($this->_data);
        $this->assertTrue($this->_arrayCollection->valid());
    }
    */

    /*
    public function testArrayIterator()
    {
        $this->_arrayCollection = new Zend_Amf_Value_Messaging_ArrayCollection($this->_data);
        $data0 = array('foo' => 'foo1', 'bar' => 'bar1');
        $data1 = array('foo' => 'foo2', 'bar' => 'bar2');
        $data3 = array('kung' => 'foo', 'Bruce' => 'Lee');
        $this->_arrayCollection->offsetSet(3,$data3);
        $this->assertEquals($data0,$this->_arrayCollection->current());
        $this->_arrayCollection->next();
        $this->assertEquals($data1,$this->_arrayCollection->current());
        $this->_arrayCollection->next();
        var_dump($this->_arrayCollection->key());
        $this->assertEquals($data3,$this->_arrayCollection->current());
        $this->_arrayCollection->rewind();
        $this->assertEquals($data0,$this->_arrayCollection->current());

    }
    */


}

class Zend_Amf_Value_ArrayCollectionTest_SerializableData
{
    public function __toString()
    {
        return __CLASS__;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Amf_Value_ArrayCollectionTest::main') {
    Zend_Amf_Value_ArrayCollectionTest::main();
}
