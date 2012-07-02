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
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Filter;

use Zend\Filter\Null as NullFilter;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Filter
 */
class NullTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorOptions()
    {
        $filter = new NullFilter(array(
            'type' => NullFilter::TYPE_INTEGER,
        ));

        $this->assertEquals(NullFilter::TYPE_INTEGER, $filter->getType());
    }

    public function testConstructorParams()
    {
        $filter = new NullFilter(NullFilter::TYPE_INTEGER);

        $this->assertEquals(NullFilter::TYPE_INTEGER, $filter->getType());
    }
    
    /**
     * @param mixed $value
     * @param bool  $expected
     * @dataProvider defaultTestProvider
     */
    public function testDefault($value, $expected)
    {
        $filter = new NullFilter();
        $this->assertSame($expected, $filter->filter($value));
    }    

    /**
     * @param int $type
     * @param array $testData
     * @dataProvider typeTestProvider
     */
    public function testTypes($type, $testData)
    {
        $filter = new NullFilter($type);
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
            $filter = new NullFilter(array('type' => $type));
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
        $filter = new NullFilter();
        $this->setExpectedException('\Zend\Filter\Exception\InvalidArgumentException', 'Unknown type value');
        $filter->setType(true);
    }

    public function testGettingDefaultType()
    {
        $filter = new NullFilter();
        $this->assertEquals(63, $filter->getType());
    }

    static public function defaultTestProvider()
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

    static public function typeTestProvider()
    {
        return array(
            array(
                NullFilter::TYPE_BOOLEAN,
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
                NullFilter::TYPE_INTEGER,
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
                NullFilter::TYPE_EMPTY_ARRAY,
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
                NullFilter::TYPE_STRING,
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
                NullFilter::TYPE_ZERO_STRING,
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
                NullFilter::TYPE_FLOAT,
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
                NullFilter::TYPE_ALL,
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

    static public function combinedTypeTestProvider()
    {
        return array(
            array(
                array(
                    array(
                        NullFilter::TYPE_ZERO_STRING,
                        NullFilter::TYPE_STRING,
                        NullFilter::TYPE_BOOLEAN,
                    ),
                    array(
                        'zero',
                        'string',
                        'boolean',
                    ),
                    NullFilter::TYPE_ZERO_STRING | NullFilter::TYPE_STRING | NullFilter::TYPE_BOOLEAN,
                    NullFilter::TYPE_ZERO_STRING + NullFilter::TYPE_STRING + NullFilter::TYPE_BOOLEAN,
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
