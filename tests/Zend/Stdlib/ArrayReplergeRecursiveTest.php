<?php

namespace ZendTest\Stdlib;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Stdlib\ArrayReplergeRecursive;

class ArrayReplergeRecursiveTest extends TestCase
{
    public static function mergeArrays()
    {
        return array(
            'merge-integer-and-string keys' => array(
                array(
                    'foo',
                    3 => 'bar',
                    'baz' => 'baz'
                ),
                array(
                    'baz',
                ),
                array(
                    0     => 'foo',
                    3     => 'bar',
                    'baz' => 'baz',
                    4     => 'baz'
                )
            ),
            'merge-arrays-recursively' => array(
                array(
                    'foo' => array(
                        'baz'
                    )
                ),
                array(
                    'foo' => array(
                        'baz'
                    )
                ),
                array(
                    'foo' => array(
                        0 => 'baz',
                        1 => 'baz'
                    )
                )
            ),
            'replace-string-keys' => array(
                array(
                    'foo' => 'bar',
                    'bar' => array()
                ),
                array(
                    'foo' => 'baz',
                    'bar' => 'bat'
                ),
                array(
                    'foo' => 'baz',
                    'bar' => 'bat'
                )
            ), 
        );
    }
    
    /**
     * @dataProvider mergeArrays
     */
    public function testReplerge($a, $b, $expected)
    {
        $this->assertEquals($expected, ArrayReplergeRecursive::replerge($a, $b));
    }
}
