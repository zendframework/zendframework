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

use Zend\Filter\LocalizedToNormalized as LocalizedToNormalizedFilter;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Filter
 */
class LocalizedToNormalizedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testNumberNormalization()
    {
        $filter = new LocalizedToNormalizedFilter(array('locale' => 'de'));
        $valuesExpected = array(
            '0'         => '0',
            '1.234'     => '1234',
            '1,234'     => '1.234',
            '1.234,56'  => '1234.56',
            '-1.234'    => '-1234',
            '-1.234,56' => '-1234.56'
        );

        foreach ($valuesExpected as $input => $output) {
            $this->assertEquals($output, $filter($input), 'failed filter of ' . var_export($input, 1));
        }
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testDateNormalizationWithoutParameters()
    {
        $filter = new LocalizedToNormalizedFilter(array('locale' => 'de'));
        $valuesExpected = array(
            '11:22:33' => array(
                'date_format' => 'HH:mm:ss',
                'locale'      => 'de',
                'hour'        => '11',
                'minute'      => '22',
                'second'      => '33'),
            '20.04.2009' => array(
                'date_format' => 'dd.MM.yyyy',
                'locale'      => 'de',
                'day'         => '20',
                'month'       => '04',
                'year'        => '2009'),
            '20.April.2009' => array(
                'date_format' => 'dd.MM.yyyy',
                'locale'      => 'de',
                'day'         => '20',
                'month'       => '04',
                'year'        => '2009'),
            '20.04.09'      => array(
                'date_format' => 'dd.MM.yyyy',
                'locale'      => 'de',
                'day'         => '20',
                'month'       => '04',
                'year'        => '2009'),
            '20.April.09'   => array(
                'date_format' => 'dd.MM.yyyy',
                'locale'      => 'de',
                'day'         => '20',
                'month'       => '04',
                'year'        => '2009')
        );

        foreach ($valuesExpected as $input => $output) {
            $this->assertEquals($output, $filter($input), 'failed filter of ' . var_export($input, 1));
        }
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testDateNormalizationWithParameters()
    {
        $filter = new LocalizedToNormalizedFilter(array('locale' => 'de', 'date_format' => 'yyyy.dd.MM'));
        $valuesExpected = array(
            '2009.20.April' => array(
                'date_format' => 'yyyy.dd.MM',
                'locale'      => 'de',
                'day'         => '20',
                'month'       => '04',
                'year'        => '2009'),
            '2009.20.04' => array(
                'date_format' => 'yyyy.dd.MM',
                'locale'      => 'de',
                'day'         => '20',
                'month'       => '04',
                'year'        => '2009'),
            '09.20.04'      => array(
                'date_format' => 'yyyy.dd.MM',
                'locale'      => 'de',
                'day'         => '20',
                'month'       => '04',
                'year'        => '2009'),
            '09.20.April'   => array(
                'date_format' => 'yyyy.dd.MM',
                'locale'      => 'de',
                'day'         => '20',
                'month'       => '04',
                'year'        => '2009')
        );

        foreach ($valuesExpected as $input => $output) {
            $this->assertEquals($output, $filter($input), 'failed filter of ' . var_export($input, 1));
        }
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testNormalizationToInteger()
    {
        $filter = new LocalizedToNormalizedFilter(array('locale' => 'de', 'precision' => 0));
        $valuesExpected = array(
            '1.234,56' => '1234',
            '1,234'    => '1',
            '1234'     => '1234'
        );

        foreach ($valuesExpected as $input => $output) {
            $this->assertEquals($output, $filter($input), 'failed filter of ' . var_export($input, 1));
        }
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testNormalizationToFloat()
    {
        $filter = new LocalizedToNormalizedFilter(array('locale' => 'de', 'precision' => 2));
        $valuesExpected = array(
            '1.234,5678' => '1234.56',
            '1,234'    => '1.23',
            '1.234'     => '1234.00'
        );

        foreach ($valuesExpected as $input => $output) {
            $this->assertEquals($output, $filter($input), 'failed filter of ' . var_export($input, 1));
        }
    }

    /**
     * ZF-6532
     */
    public function testLongNumbers()
    {
        $filter = new LocalizedToNormalizedFilter(array('locale' => 'de', 'precision' => 0));
        $this->assertEquals('1000000', $filter('1.000.000,00'));
        $this->assertEquals('10000', $filter(10000));

        $this->assertEquals(array(
            'date_format' => 'dd.MM.yyyy',
            'locale' => 'de',
            'day' => '1',
            'month' => '2',
            'year' => '4'), $filter('1,2.4'));
    }
}
