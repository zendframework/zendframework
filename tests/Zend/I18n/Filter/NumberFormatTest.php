<?php

namespace ZendTest\I18n\Filter;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\I18n\Filter\NumberFormat as NumberFormatFilter;
use NumberFormatter;

class NumberFormatTest extends TestCase
{
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

    static public function numberToFormattedProvider()
    {
        return array(
            array(
                'en_US',
                null,
                null,
                1234567.8912346,
                '1,234,567.891'
            ),
            array(
                'de_DE',
                null,
                null,
                1234567.8912346,
                '1.234.567,891'
            ),
            array(
                'ru_RU',
                null,
                null,
                1234567.8912346,
                '1 234 567,891'
            ),
        );
    }

    static public function formattedToNumberProvider()
    {
        return array(
            array(
                'en_US',
                null,
                null,
                '1,234,567.891',
                1234567.891,
            ),
            array(
                'de_DE',
                null,
                null,
                '1.234.567,891',
                1234567.891,
            ),
            array(
                'ru_RU',
                null,
                null,
                '1 234 567,891',
                1234567.891,
            ),
        );
    }
}
