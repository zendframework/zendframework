<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\I18n\Filter;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\I18n\Filter\NumberFormat as NumberFormatFilter;
use NumberFormatter;

class NumberFormatTest extends TestCase
{
    public function setUp()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }
    }

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

    public function numberToFormattedProvider()
    {
        if (!extension_loaded('intl')) {
            if (version_compare(\PHPUnit_Runner_Version::id(), '3.8.0-dev') === 1) {
                $this->markTestSkipped('ext/intl not enabled');
            } else {
                return array(array());
            }
        }

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
                '1 234 567,891'
            ),
        );
    }

    public function formattedToNumberProvider()
    {
        if (!extension_loaded('intl')) {
            if (version_compare(\PHPUnit_Runner_Version::id(), '3.8.0-dev') === 1) {
                $this->markTestSkipped('ext/intl not enabled');
            } else {
                return array(array());
            }
        }

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


    public function returnUnfilteredDataProvider()
    {
        return array(
            array(null),
            array(new \stdClass()),
            array(array(
                '1.234.567,891',
                '1.567,891'
            ))
        );
    }

    /**
     * @dataProvider returnUnfilteredDataProvider
     * @return void
     */
    public function testReturnUnfiltered($input)
    {
        $filter = new NumberFormatFilter('de_AT', NumberFormatter::DEFAULT_STYLE, NumberFormatter::TYPE_DOUBLE);

        $this->assertEquals($input, $filter->filter($input));
    }
}
