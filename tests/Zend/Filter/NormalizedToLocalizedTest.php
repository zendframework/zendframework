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

use Zend\Filter\NormalizedToLocalized as NormalizedToLocalizedFilter;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Filter
 */
class NormalizedToLocalizedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testNumberLocalization()
    {
        $filter = new NormalizedToLocalizedFilter(array('locale' => 'de'));
        $valuesExpected = array(
            1  => '0',
            2  => 0,
            3  => '1234',
            4  => 1234,
            5  => '1.234',
            6  => '1234.56',
            7  => 1234.56,
            8  => '-1234',
            9  => -1234,
            10 => '-1234.56',
            11 => -1234.56
        );

        $valuesReceived = array(
            1  => '0',
            2  => '0',
            3  => '1.234',
            4  => '1.234',
            5  => '1,234',
            6  => '1.234,56',
            7  => '1.234,56',
            8  => '-1.234',
            9  => '-1.234',
            10 => '-1.234,56',
            11 => '-1.234,56'
        );

        foreach ($valuesExpected as $key => $value) {
            $this->assertEquals($valuesReceived[$key], $filter($value), 'failed filter of ' . var_export($value, 1));
        }
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testDateLocalizationWithoutParameters()
    {
        $filter = new NormalizedToLocalizedFilter(array('locale' => 'de', 'date_format' => 'HH:mm:ss'));
        $valuesExpected[1] = array(
            'hour'         => '11',
            'minute'       => '22',
            'second'       => '33');
        $valuesReceived[1] = '11:22:33';

        foreach ($valuesExpected as $key => $value) {
            $this->assertEquals($valuesReceived[$key], $filter($value), 'failed filter of ' . var_export($value, 1));
        }

        $filter = new NormalizedToLocalizedFilter(array('locale' => 'de', 'date_format' => 'dd.MM.yyyy'));
        $valuesExpected[1] = array(
            'date_format'  => 'dd.MM.yyyy',
            'locale'       => 'de',
            'day'          => '20',
            'month'        => '04',
            'year'         => '2009');
        $valuesReceived[1] = '20.04.2009';

        $valuesExpected[2] = array(
            'date_format'  => null,
            'locale'       => 'de',
            'day'          => '20',
            'month'        => '04',
            'year'         => '2009');
        $valuesReceived[2] = '20.04.2009';

        $valuesExpected[3] = array(
            'date_format'  => 'dd.MM.yyyy',
            'day'          => '20',
            'month'        => '04',
            'year'         => '2009');
        $valuesReceived[3] = '20.04.2009';

        $valuesExpected[4] = array(
            'day'          => '20',
            'month'        => '04',
            'year'         => '2009');
        $valuesReceived[4] = '20.04.2009';

        foreach ($valuesExpected as $key => $value) {
            $this->assertEquals($valuesReceived[$key], $filter($value), 'failed filter of ' . var_export($value, 1));
        }
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testDateLocalizationWithParameters()
    {
        $filter = new NormalizedToLocalizedFilter(array('locale' => 'de', 'date_format' => 'yyyy.dd.MM'));

        // Note that any non date array key like date_format or locale does not
        // change filter parameters... only day, month and year are used
        $valuesExpected[1] = array(
            'date_format' => 'yyyy.dd.MM',
            'locale'      => 'de',
            'day'         => '20',
            'month'       => '04',
            'year'        => '2009');
        $valuesReceived[1] = '2009.20.04';

        $valuesExpected[2] = array(
            'day'         => '20',
            'month'       => '04',
            'year'        => '2009');
        $valuesReceived[2] = '2009.20.04';

        $valuesExpected[3] = array(
            'locale'      => 'de',
            'day'         => '20',
            'month'       => '04',
            'year'        => '2009');
        $valuesReceived[3] = '2009.20.04';

        $valuesExpected[4] = array(
            'date_format' => null,
            'day'         => '20',
            'month'       => '04',
            'year'        => '2009');
        $valuesReceived[4] = '2009.20.04';

        foreach ($valuesExpected as $key => $value) {
            $this->assertEquals($valuesReceived[$key], $filter($value), 'failed filter of ' . var_export($value, 1));
        }
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testLocalizationToInteger()
    {
        $filter = new NormalizedToLocalizedFilter(array('locale' => 'de', 'precision' => 0));
        $valuesExpected = array(
            1 => '1234.56',
            2 => 1234.56,
            3 => '1.234',
            4 => 1.234,
            5 => '1234',
            6 => 1234
        );

        $valuesReceived = array(
            1 => '1.235',
            2 => '1.235',
            3 => '1',
            4 => '1',
            5 => '1.234',
            6 => '1.234'
        );

        foreach ($valuesExpected as $key => $value) {
            $this->assertEquals($valuesReceived[$key], $filter($value), 'failed filter of ' . var_export($value, 1));
        }
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testLocalizationToFloat()
    {
        $filter = new NormalizedToLocalizedFilter(array('locale' => 'de', 'precision' => 2));

        $valuesExpected = array(
            1 => '1234.5678',
            2 => 1234.5678,
            3 => '1.234',
            4 => 1.234,
            5 => '1234',
            6 => 1234
        );

        $valuesReceived = array(
            1 => '1.234,57',
            2 => '1.234,57',
            3 => '1,23',
            4 => '1,23',
            5 => '1.234,00',
            6 => '1.234,00'
        );

        foreach ($valuesExpected as $key => $value) {
            $this->assertEquals($valuesReceived[$key], $filter($value), 'failed filter of ' . var_export($value, 1));
        }
    }
}
