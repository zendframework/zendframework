<?php

namespace ZendTest\Stdlib;

use ArrayObject,
    PHPUnit_Framework_TestCase as TestCase,
    stdClass,
    Zend\Config\Config,
    Zend\Stdlib\IteratorToArray;

class IteratorToArrayTest extends TestCase
{
    public static function validIterators()
    {
        return array(
            array(array(
                'foo' => 'bar',
            ), array(
                'foo' => 'bar',
            )),
            array(new Config(array(
                'foo' => array(
                    'bar' => array(
                        'baz' => array(
                            'baz' => 'bat',
                        ),
                    ),
                ),
            )), array(
                'foo' => array(
                    'bar' => array(
                        'baz' => array(
                            'baz' => 'bat',
                        ),
                    ),
                ),
            )),
            array(new ArrayObject(array(
                'foo' => array(
                    'bar' => array(
                        'baz' => array(
                            'baz' => 'bat',
                        ),
                    ),
                ),
            )), array(
                'foo' => array(
                    'bar' => array(
                        'baz' => array(
                            'baz' => 'bat',
                        ),
                    ),
                ),
            )),
        );
    }

    public static function invalidIterators()
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
            array(new stdClass),
        );
    }

    /**
     * @dataProvider validIterators
     */
    public function testValidIteratorsReturnArrayRepresentation($test, $expected)
    {
        $result = IteratorToArray::convert($test);
        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider invalidIterators
     */
    public function testInvalidIteratorsRaiseInvalidArgumentException($test)
    {
        $this->setExpectedException('Zend\Stdlib\Exception\InvalidArgumentException');
        $this->assertFalse(IteratorToArray::convert($test));
    }
}
