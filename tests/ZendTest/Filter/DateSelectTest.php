<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Filter;

use Zend\Filter\DateSelect as DateSelectFilter;

class DateSelectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideFilter
     * @param $options
     * @param $input
     * @param $expected
     */
    public function testFilter($options, $input, $expected)
    {
        $sut = new DateSelectFilter();
        $sut->setOptions($options);
        $this->assertEquals($expected, $sut->filter($input));
    }

    public function provideFilter()
    {
        return array(
            array(array(), array('year' => '2014', 'month' => '10', 'day' => '26'), '2014-10-26'),
            array(array('nullOnEmpty' => true), array('year' => null, 'month' => '10', 'day' => '26'), null),
            array(array('null_on_empty' => true), array('year' => null, 'month' => '10', 'day' => '26'), null),
            array(array('nullOnAllEmpty' => true), array('year' => null, 'month' => null, 'day' => null), null),
            array(array('null_on_all_empty' => true), array('year' => null, 'month' => null, 'day' => null), null),
        );
    }

    /**
     * @expectedException \Zend\Filter\Exception\RuntimeException
     */
    public function testInvalidInput()
    {
        $sut = new DateSelectFilter();
        $sut->filter(array('year' => '2120', 'month' => '07'));
    }
}
