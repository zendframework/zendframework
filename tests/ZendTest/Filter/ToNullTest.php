<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Filter;

use Zend\Filter\ToNull as ToNullFilter;

/**
 * @group      Zend_Filter
 */
class ToNullTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorOptions()
    {
        $filter = new ToNullFilter(array(
            'type' => ToNullFilter::TYPE_INTEGER,
        ));

        $this->assertEquals(ToNullFilter::TYPE_INTEGER, $filter->getType());
    }

    public function testConstructorParams()
    {
        $filter = new ToNullFilter(ToNullFilter::TYPE_INTEGER);

        $this->assertEquals(ToNullFilter::TYPE_INTEGER, $filter->getType());
    }

    /**
     * @param mixed $value
     * @param bool  $expected
     * @dataProvider defaultTestProvider
     */
    public function testDefault($value, $expected)
    {
        $filter = new ToNullFilter();
        $this->assertSame($expected, $filter->filter($value));
    }

    /**
     * @param int $type
     * @param array $testData
     * @dataProvider typeTestProvider
     */
    public function testTypes($type, $testData)
    {
        $filter = new ToNullFilter($type);
        foreach ($testData as $data) {
            list($value, $expected) = $data;
            $message = sprintf(
                '%s (%s) is not filtered as %s; type = %s',
                var_export($value, true),
                gettype($value),
                var_export($expected, true),
                $type
            );
            $this->assertSame($expected, $filter->filter($value), $message);
        }
    }

    /**
     * @param array $typeData
     * @param array $testData
     * @dataProvider combinedTypeTestProvider
     */
    public function testCombinedTypes($typeData, $testData)
    {
        foreach ($typeData as $type) {
            $filter = new ToNullFilter(array('type' => $type));
            foreach ($testData as $data) {
                list($value, $expected) = $data;
                $message = sprintf(
                    '%s (%s) is not filtered as %s; type = %s',
                    var_export($value, true),
                    gettype($value),
                    var_export($expected, true),
                    var_export($type, true)
                );
                $this->assertSame($expected, $filter->filter($value), $message);
            }
        }
    }

    public function testSettingFalseType()
    {
        $filter = new ToNullFilter();
        $this->setExpectedException('\Zend\Filter\Exception\InvalidArgumentException', 'Unknown type value');
        $filter->setType(true);
    }

    public function testGettingDefaultType()
    {
        $filter = new ToNullFilter();
        $this->assertEquals(63, $filter->getType());
    }

    public static function defaultTestProvider()
    {
        return array(
            array(null, null),
            array(false, null),
            array(true, true),
            array(0, null),
            array(1, 1),
            array(0.0, null),
            array(1.0, 1.0),
            array('', null),
            array('abc', 'abc'),
            array('0', null),
            array('1', '1'),
            array(array(), null),
            array(array(0), array(0)),
        );
    }

    public static function typeTestProvider()
    {
        return array(
            array(
                ToNullFilter::TYPE_BOOLEAN,
                array(
                    array(null, null),
                    array(false, null),
                    array(true, true),
                    array(0, 0),
                    array(1, 1),
                    array(0.0, 0.0),
                    array(1.0, 1.0),
                    array('', ''),
                    array('abc', 'abc'),
                    array('0', '0'),
                    array('1', '1'),
                    array(array(), array()),
                    array(array(0), array(0)),
                )
            ),
            array(
                ToNullFilter::TYPE_INTEGER,
                array(
                    array(null, null),
                    array(false, false),
                    array(true, true),
                    array(0, null),
                    array(1, 1),
                    array(0.0, 0.0),
                    array(1.0, 1.0),
                    array('', ''),
                    array('abc', 'abc'),
                    array('0', '0'),
                    array('1', '1'),
                    array(array(), array()),
                    array(array(0), array(0)),
                )
            ),
            array(
                ToNullFilter::TYPE_EMPTY_ARRAY,
                array(
                    array(null, null),
                    array(false, false),
                    array(true, true),
                    array(0, 0),
                    array(1, 1),
                    array(0.0, 0.0),
                    array(1.0, 1.0),
                    array('', ''),
                    array('abc', 'abc'),
                    array('0', '0'),
                    array('1', '1'),
                    array(array(), null),
                    array(array(0), array(0)),
                )
            ),
            array(
                ToNullFilter::TYPE_STRING,
                array(
                    array(null, null),
                    array(false, false),
                    array(true, true),
                    array(0, 0),
                    array(1, 1),
                    array(0.0, 0.0),
                    array(1.0, 1.0),
                    array('', null),
                    array('abc', 'abc'),
                    array('0', '0'),
                    array('1', '1'),
                    array(array(), array()),
                    array(array(0), array(0)),
                )
            ),
            array(
                ToNullFilter::TYPE_ZERO_STRING,
                array(
                    array(null, null),
                    array(false, false),
                    array(true, true),
                    array(0, 0),
                    array(1, 1),
                    array(0.0, 0.0),
                    array(1.0, 1.0),
                    array('', ''),
                    array('abc', 'abc'),
                    array('0', null),
                    array('1', '1'),
                    array(array(), array()),
                    array(array(0), array(0)),
                )
            ),
            array(
                ToNullFilter::TYPE_FLOAT,
                array(
                    array(null, null),
                    array(false, false),
                    array(true, true),
                    array(0, 0),
                    array(1, 1),
                    array(0.0, null),
                    array(1.0, 1.0),
                    array('', ''),
                    array('abc', 'abc'),
                    array('0', '0'),
                    array('1', '1'),
                    array(array(), array()),
                    array(array(0), array(0)),
                )
            ),
            array(
                ToNullFilter::TYPE_ALL,
                array(
                    array(null, null),
                    array(false, null),
                    array(true, true),
                    array(0, null),
                    array(1, 1),
                    array(0.0, null),
                    array(1.0, 1.0),
                    array('', null),
                    array('abc', 'abc'),
                    array('0', null),
                    array('1', '1'),
                    array(array(), null),
                    array(array(0), array(0)),
                )
            ),
        );
    }

    public static function combinedTypeTestProvider()
    {
        return array(
            array(
                array(
                    array(
                        ToNullFilter::TYPE_ZERO_STRING,
                        ToNullFilter::TYPE_STRING,
                        ToNullFilter::TYPE_BOOLEAN,
                    ),
                    array(
                        'zero',
                        'string',
                        'boolean',
                    ),
                    ToNullFilter::TYPE_ZERO_STRING | ToNullFilter::TYPE_STRING | ToNullFilter::TYPE_BOOLEAN,
                    ToNullFilter::TYPE_ZERO_STRING + ToNullFilter::TYPE_STRING + ToNullFilter::TYPE_BOOLEAN,
                ),
                array(
                    array(null, null),
                    array(false, null),
                    array(true, true),
                    array(0, 0),
                    array(1, 1),
                    array(0.0, 0.0),
                    array(1.0, 1.0),
                    array('', null),
                    array('abc', 'abc'),
                    array('0', null),
                    array('1', '1'),
                    array(array(), array()),
                    array(array(0), array(0)),
                )
            )
        );
    }
}
