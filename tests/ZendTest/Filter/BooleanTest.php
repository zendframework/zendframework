<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Filter
 */

namespace ZendTest\Filter;

use Zend\Filter\Boolean as BooleanFilter;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 */
class BooleanTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorOptions()
    {
        $filter = new BooleanFilter(array(
            'type'    => BooleanFilter::TYPE_INTEGER,
            'casting' => false,
        ));

        $this->assertEquals(BooleanFilter::TYPE_INTEGER, $filter->getType());
        $this->assertFalse($filter->getCasting());
    }

    public function testConstructorParams()
    {
        $filter = new BooleanFilter(BooleanFilter::TYPE_INTEGER, false);

        $this->assertEquals(BooleanFilter::TYPE_INTEGER, $filter->getType());
        $this->assertFalse($filter->getCasting());
    }

    /**
     * @param mixed $value
     * @param bool  $expected
     * @dataProvider defaultTestProvider
     */
    public function testDefault($value, $expected)
    {
        $filter = new BooleanFilter();
        $this->assertSame($expected, $filter->filter($value));
    }

    /**
     * @param mixed $value
     * @param bool  $expected
     * @dataProvider noCastingTestProvider
     */
    public function testNoCasting($value, $expected)
    {
        $filter = new BooleanFilter('all', false);
        $this->assertEquals($expected, $filter->filter($value));
    }

    /**
     * @param int $type
     * @param array $testData
     * @dataProvider typeTestProvider
     */
    public function testTypes($type, $testData)
    {
        $filter = new BooleanFilter($type);
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
            $filter = new BooleanFilter(array('type' => $type));
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

    public function testLocalized()
    {
        $filter = new BooleanFilter(array(
            'type' => BooleanFilter::TYPE_LOCALIZED,
            'translations' => array(
                'yes' => true,
                'y'   => true,
                'no'  => false,
                'n'   => false,
                'yay' => true,
                'nay' => false,
            )
        ));

        $this->assertTrue($filter->filter('yes'));
        $this->assertTrue($filter->filter('yay'));
        $this->assertFalse($filter->filter('n'));
        $this->assertFalse($filter->filter('nay'));
    }

    public function testSettingFalseType()
    {
        $filter = new BooleanFilter();
        $this->setExpectedException('\Zend\Filter\Exception\InvalidArgumentException', 'Unknown type value');
        $filter->setType(true);
    }

    public function testGettingDefaultType()
    {
        $filter = new BooleanFilter();
        $this->assertEquals(127, $filter->getType());
    }

    public static function defaultTestProvider()
    {
        return array(
            array(false, false),
            array(true, true),
            array(0, false),
            array(1, true),
            array(0.0, false),
            array(1.0, true),
            array('', false),
            array('abc', true),
            array('0', false),
            array('1', true),
            array(array(), false),
            array(array(0), true),
            array(null, false),
            array('false', true),
            array('true', true),
            array('no', true),
            array('yes', true),
        );
    }

    public static function noCastingTestProvider()
    {
        return array(
            array(false, false),
            array(true, true),
            array(0, false),
            array(1, true),
            array(2, 2),
            array(0.0, false),
            array(1.0, true),
            array(0.5, 0.5),
            array('', false),
            array('abc', 'abc'),
            array('0', false),
            array('1', true),
            array('2', '2'),
            array(array(), false),
            array(array(0), array(0)),
            array(null, null),
            array('false', false),
            array('true', true),
        );
    }

    public static function typeTestProvider()
    {
        return array(
            array(
                BooleanFilter::TYPE_BOOLEAN,
                array(
                    array(false, false),
                    array(true, true),
                    array(0, true),
                    array(1, true),
                    array(0.0, true),
                    array(1.0, true),
                    array('', true),
                    array('abc', true),
                    array('0', true),
                    array('1', true),
                    array(array(), true),
                    array(array(0), true),
                    array(null, true),
                    array('false', true),
                    array('true', true),
                    array('no', true),
                    array('yes', true),
                )
            ),
            array(
                BooleanFilter::TYPE_INTEGER,
                array(
                    array(false, true),
                    array(true, true),
                    array(0, false),
                    array(1, true),
                    array(0.0, true),
                    array(1.0, true),
                    array('', true),
                    array('abc', true),
                    array('0', true),
                    array('1', true),
                    array(array(), true),
                    array(array(0), true),
                    array(null, true),
                    array('false', true),
                    array('true', true),
                    array('no', true),
                    array('yes', true),
                )
            ),
            array(
                BooleanFilter::TYPE_FLOAT,
                array(
                    array(false, true),
                    array(true, true),
                    array(0, true),
                    array(1, true),
                    array(0.0, false),
                    array(1.0, true),
                    array('', true),
                    array('abc', true),
                    array('0', true),
                    array('1', true),
                    array(array(), true),
                    array(array(0), true),
                    array(null, true),
                    array('false', true),
                    array('true', true),
                    array('no', true),
                    array('yes', true),
                )
            ),
            array(
                BooleanFilter::TYPE_STRING,
                array(
                    array(false, true),
                    array(true, true),
                    array(0, true),
                    array(1, true),
                    array(0.0, true),
                    array(1.0, true),
                    array('', false),
                    array('abc', true),
                    array('0', true),
                    array('1', true),
                    array(array(), true),
                    array(array(0), true),
                    array(null, true),
                    array('false', true),
                    array('true', true),
                    array('no', true),
                    array('yes', true),
                )
            ),
            array(
                BooleanFilter::TYPE_ZERO_STRING,
                array(
                    array(false, true),
                    array(true, true),
                    array(0, true),
                    array(1, true),
                    array(0.0, true),
                    array(1.0, true),
                    array('', true),
                    array('abc', true),
                    array('0', false),
                    array('1', true),
                    array(array(), true),
                    array(array(0), true),
                    array(null, true),
                    array('false', true),
                    array('true', true),
                    array('no', true),
                    array('yes', true),
                )
            ),
            array(
                BooleanFilter::TYPE_EMPTY_ARRAY,
                array(
                    array(false, true),
                    array(true, true),
                    array(0, true),
                    array(1, true),
                    array(0.0, true),
                    array(1.0, true),
                    array('', true),
                    array('abc', true),
                    array('0', true),
                    array('1', true),
                    array(array(), false),
                    array(array(0), true),
                    array(null, true),
                    array('false', true),
                    array('true', true),
                    array('no', true),
                    array('yes', true),
                )
            ),
            array(
                BooleanFilter::TYPE_NULL,
                array(
                    array(false, true),
                    array(true, true),
                    array(0, true),
                    array(1, true),
                    array(0.0, true),
                    array(1.0, true),
                    array('', true),
                    array('abc', true),
                    array('0', true),
                    array('1', true),
                    array(array(), true),
                    array(array(0), true),
                    array(null, false),
                    array('false', true),
                    array('true', true),
                    array('no', true),
                    array('yes', true),
                )
            ),
            array(
                BooleanFilter::TYPE_PHP,
                array(
                    array(false, false),
                    array(true, true),
                    array(0, false),
                    array(1, true),
                    array(0.0, false),
                    array(1.0, true),
                    array('', false),
                    array('abc', true),
                    array('0', false),
                    array('1', true),
                    array(array(), false),
                    array(array(0), true),
                    array(null, false),
                    array('false', true),
                    array('true', true),
                    array('no', true),
                    array('yes', true),
                )
            ),
            array(
                BooleanFilter::TYPE_FALSE_STRING,
                array(
                    array(false, true),
                    array(true, true),
                    array(0, true),
                    array(1, true),
                    array(0.0, true),
                    array(1.0, true),
                    array('', true),
                    array('abc', true),
                    array('0', true),
                    array('1', true),
                    array(array(), true),
                    array(array(0), true),
                    array(null, true),
                    array('false', false),
                    array('true', true),
                    array('no', true),
                    array('yes', true),
                )
            ),
            // default behaviour with no translations provided
            // all values filtered as true
            array(
                BooleanFilter::TYPE_LOCALIZED,
                array(
                    array(false, true),
                    array(true, true),
                    array(0, true),
                    array(1, true),
                    array(0.0, true),
                    array(1.0, true),
                    array('', true),
                    array('abc', true),
                    array('0', true),
                    array('1', true),
                    array(array(), true),
                    array(array(0), true),
                    array(null, true),
                    array('false', true),
                    array('true', true),
                    array('no', true),
                    array('yes', true),
                )
            ),
            array(
                BooleanFilter::TYPE_ALL,
                array(
                    array(false, false),
                    array(true, true),
                    array(0, false),
                    array(1, true),
                    array(0.0, false),
                    array(1.0, true),
                    array('', false),
                    array('abc', true),
                    array('0', false),
                    array('1', true),
                    array(array(), false),
                    array(array(0), true),
                    array(null, false),
                    array('false', false),
                    array('true', true),
                    array('no', true),
                    array('yes', true),
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
                        BooleanFilter::TYPE_ZERO_STRING,
                        BooleanFilter::TYPE_STRING,
                        BooleanFilter::TYPE_BOOLEAN,
                    ),
                    array(
                        'zero',
                        'string',
                        'boolean',
                    ),
                    BooleanFilter::TYPE_ZERO_STRING | BooleanFilter::TYPE_STRING | BooleanFilter::TYPE_BOOLEAN,
                    BooleanFilter::TYPE_ZERO_STRING + BooleanFilter::TYPE_STRING + BooleanFilter::TYPE_BOOLEAN,
                ),
                array(
                    array(false, false),
                    array(true, true),
                    array(0, true),
                    array(1, true),
                    array(0.0, true),
                    array(1.0, true),
                    array('', false),
                    array('abc', true),
                    array('0', false),
                    array('1', true),
                    array(array(), true),
                    array(array(0), true),
                    array(null, true),
                    array('false', true),
                    array('true', true),
                    array('no', true),
                    array('yes', true),
                )
            )
        );
    }
}
