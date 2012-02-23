<?php

namespace ZendTest\Stdlib;

use PHPUnit_Framework_TestCase as TestCase,
    stdClass,
    Zend\Stdlib\IsAssocArray;

class IsAssocArrayTest extends TestCase
{
    public static function validAssocArrays()
    {
        return array(
            array(array(
                'foo' => 'bar',
            )),
            array(array(
                'bar',
                'foo' => 'bar',
                'baz',
            )),
        );
    }

    public static function validAssocEmptyArrays()
    {
        $array = self::validAssocArrays();
        $array[] = array(array());
        return $array;
    }

    public static function invalidAssocArrays()
    {
        return array(
            array(null),
            array(true),
            array(false),
            array(0),
            array(1),
            array(0.0),
            array(1.0),
            array('string'),
            array(array(0, 1, 2)),
            array(new stdClass),
        );
    }

    /**
     * @dataProvider validAssocArrays
     */
    public function testValidAssocArraysReturnTrue($test)
    {
        $this->assertTrue(IsAssocArray::test($test));
    }

    /**
     * @dataProvider validAssocEmptyArrays
     */
    public function testValidAssocEmptyArraysReturnTrue($test)
    {
        $this->assertTrue(isAssocArray::test($test, true));
    }

    /**
     * @dataProvider invalidAssocArrays
     */
    public function testInvalidAssocArraysReturnFalse($test)
    {
        $this->assertFalse(IsAssocArray::test($test));
    }

    /**
     * @dataProvider invalidAssocArrays
     */
    public function testInvalidAssocEmptyArraysReturnFalse($test)
    {
        $this->assertFalse(IsAssocArray::test($test, true));
    }
}
