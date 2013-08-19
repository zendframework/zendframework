<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_I18n
 */

namespace ZendTest\I18n\Filter;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\I18n\Filter\NumberFormat as NumberFormatFilter;
use NumberFormatter;

class NumberFormatTest extends TestCase
{
    public function testConstructWithOptions()
    {
        $filter = new NumberFormatFilter(array(
            'locale' => 'en_US',
            'style'  => NumberFormatter::DECIMAL
        ));

        $this->assertEquals('en_US', $filter->getLocale());
        $this->assertEquals(NumberFormatter::DECIMAL, $filter->getStyle());
    }

    public function testConstructWithParameters()
    {
        $filter = new NumberFormatFilter('en_US', NumberFormatter::DECIMAL);

        $this->assertEquals('en_US', $filter->getLocale());
        $this->assertEquals(NumberFormatter::DECIMAL, $filter->getStyle());
    }


    /**
     * @param $locale
     * @param $style
     * @param $type
     * @param $value
     * @param $expected
     * @dataProvider numberToFormattedProvider
     */
    public function testNumberToFormatted($locale, $style, $type, $value, $expected)
    {
        $filter = new NumberFormatFilter($locale, $style, $type);
        $this->assertEquals($expected, $filter->filter($value));
    }

    /**
     * @param $locale
     * @param $style
     * @param $type
     * @param $value
     * @param $expected
     * @dataProvider formattedToNumberProvider
     */
    public function testFormattedToNumber($locale, $style, $type, $value, $expected)
    {
        $filter = new NumberFormatFilter($locale, $style, $type);
        $this->assertEquals($expected, $filter->filter($value));
    }

    public static function numberToFormattedProvider()
    {
        return array(
            array(
                'en_US',
                NumberFormatter::DEFAULT_STYLE,
                NumberFormatter::TYPE_DOUBLE,
                1234567.8912346,
                '1,234,567.891'
            ),
            array(
                'de_DE',
                NumberFormatter::DEFAULT_STYLE,
                NumberFormatter::TYPE_DOUBLE,
                1234567.8912346,
                '1.234.567,891'
            ),
            array(
                'ru_RU',
                NumberFormatter::DEFAULT_STYLE,
                NumberFormatter::TYPE_DOUBLE,
                1234567.8912346,
                '1 234 567,891'
            ),
        );
    }

    public static function formattedToNumberProvider()
    {
        return array(
            array(
                'en_US',
                NumberFormatter::DEFAULT_STYLE,
                NumberFormatter::TYPE_DOUBLE,
                '1,234,567.891',
                1234567.891,
            ),
            array(
                'de_DE',
                NumberFormatter::DEFAULT_STYLE,
                NumberFormatter::TYPE_DOUBLE,
                '1.234.567,891',
                1234567.891,
            ),
            array(
                'ru_RU',
                NumberFormatter::DEFAULT_STYLE,
                NumberFormatter::TYPE_DOUBLE,
                '1 234 567,891',
                1234567.891,
            ),
        );
    }
}
