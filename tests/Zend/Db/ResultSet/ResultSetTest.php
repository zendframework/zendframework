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
 * @package    Zend_Db
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Db\ResultSet;

use ArrayObject,
    ArrayIterator,
    PHPUnit_Framework_TestCase as TestCase,
    SplStack,
    stdClass,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\ResultSet\Row,
    Zend\Db\ResultSet\RowObjectInterface;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ResultSetTest extends TestCase
{
    /**
     * @var ResultSet
     */
    protected $set;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {

        $this->set = new ResultSet;
    }

    public function testRowObjectPrototypeIsPopulatedByRowObjectByDefault()
    {
        $row = $this->set->getRowObjectPrototype();
        $this->assertInstanceOf('Zend\Db\ResultSet\Row', $row);
    }

    public function testRowObjectPrototypeIsMutable()
    {
        $row = new Row();
        $this->set->setRowObjectPrototype($row);
        $this->assertSame($row, $this->set->getRowObjectPrototype());
    }

    public function testRowObjectPrototypeMayBePassedToConstructor()
    {
        $row = new Row();
        $set = new ResultSet($row);
        $this->assertSame($row, $set->getRowObjectPrototype());
    }

    public function testReturnTypeIsObjectByDefault()
    {
        $this->assertEquals(ResultSet::TYPE_OBJECT, $this->set->getReturnType());
    }

    public function testReturnTypeMayBeSetToArray()
    {
        $this->set->setReturnType(ResultSet::TYPE_ARRAY);
        $this->assertEquals(ResultSet::TYPE_ARRAY, $this->set->getReturnType());
    }

    public function invalidReturnTypes()
    {
        return array(
            array(null),
            array(1),
            array(1.0),
            array(true),
            array(false),
            array('string'),
            array(array('foo')),
            array(new stdClass),
        );
    }

    /**
     * @dataProvider invalidReturnTypes
     */
    public function testSettingInvalidReturnTypeRaisesException($type)
    {
        $this->setExpectedException('Zend\Db\ResultSet\Exception\InvalidArgumentException');
        $this->set->setReturnType($type);
    }

    public function testDataSourceIsNullByDefault()
    {
        $this->assertNull($this->set->getDataSource());
    }

    public function testCanProvideIteratorAsDataSource()
    {
        $it = new SplStack;
        $this->set->setDataSource($it);
        $this->assertSame($it, $this->set->getDataSource());
    }

    public function testCanProvideIteratorAggregateAsDataSource()
    {
        $iteratorAggregate = $this->getMock('IteratorAggregate', array('getIterator'), array(new SplStack));
        $iteratorAggregate->expects($this->any())->method('getIterator')->will($this->returnValue($iteratorAggregate));
        $this->set->setDataSource($iteratorAggregate);
        $this->assertSame($iteratorAggregate->getIterator(), $this->set->getDataSource());
    }

    /**
     * @dataProvider invalidReturnTypes
     */
    public function testInvalidDataSourceRaisesException($dataSource)
    {
        if (is_array($dataSource)) {
            // this is valid
            return;
        }
        $this->setExpectedException('Zend\Db\ResultSet\Exception\InvalidArgumentException');
        $this->set->setDataSource($dataSource);
    }

    public function testFieldCountIsZeroWithNoDataSourcePresent()
    {
        $this->assertEquals(0, $this->set->getFieldCount());
    }

    public function getArrayDataSource($count)
    {
        $array = array();
        for ($i = 0; $i < $count; $i++) {
            $array[] = array(
                'id'    => $i,
                'title' => 'title ' . $i,
            );
        }
        return new ArrayIterator($array);
    }

    public function testFieldCountRepresentsNumberOfFieldsInARowOfData()
    {
        $this->set->setReturnType(ResultSet::TYPE_ARRAY);
        $dataSource = $this->getArrayDataSource(10);
        $this->set->setDataSource($dataSource);
        $this->assertEquals(2, $this->set->getFieldCount());
    }

    public function testWhenReturnTypeIsArrayThenIterationReturnsArrays()
    {
        $this->set->setReturnType(ResultSet::TYPE_ARRAY);
        $dataSource = $this->getArrayDataSource(10);
        $this->set->setDataSource($dataSource);
        foreach ($this->set as $index => $row) {
            $this->assertEquals($dataSource[$index], $row);
        }
    }

    public function testWhenReturnTypeIsObjectThenIterationReturnsRowObjects()
    {
        $dataSource = $this->getArrayDataSource(10);
        $this->set->setDataSource($dataSource);
        foreach ($this->set as $index => $row) {
            $this->assertInstanceOf('Zend\Db\ResultSet\RowObjectInterface', $row);
            $this->assertEquals($dataSource[$index], $row->getArrayCopy());
        }
    }

    public function testCountReturnsCountOfRows()
    {
        $count      = rand(3, 75);
        $dataSource = $this->getArrayDataSource($count);
        $this->set->setDataSource($dataSource);
        $this->assertEquals($count, $this->set->count());
    }

    public function testToArrayRaisesExceptionForRowsThatAreNotArraysOrArrayCastable()
    {
        $count      = rand(3, 75);
        $dataSource = $this->getArrayDataSource($count);
        foreach ($dataSource as $index => $row) {
            $dataSource[$index] = (object) $row;
        }
        $this->set->setDataSource($dataSource);
        $this->setExpectedException('Zend\Db\ResultSet\Exception\RuntimeException');
        $this->set->toArray();
    }

    public function testToArrayCreatesArrayOfArraysRepresentingRows()
    {
        $count      = rand(3, 75);
        $dataSource = $this->getArrayDataSource($count);
        $this->set->setDataSource($dataSource);
        $test = $this->set->toArray();
        $this->assertEquals($dataSource->getArrayCopy(), $test, var_export($test, 1));
    }
}
