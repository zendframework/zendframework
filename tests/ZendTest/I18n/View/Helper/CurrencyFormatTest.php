<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_I18n
 */

namespace ZendTest\I18n\View\Helper;

use Locale;
use Zend\I18n\View\Helper\CurrencyFormat as CurrencyFormatHelper;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class CurrencyFormatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CurrencyFormatHelper
     */
    public $helper;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->helper = new CurrencyFormatHelper();
    }

    public function currencyProvider()
    {
        return array(
            //    locale   currency   show decimals      number   expected
            array('de_AT', 'EUR',     true,              1234.56, '€ 1.234,56'),
            array('de_AT', 'EUR',     true,              0.123,   '€ 0,12'),
            array('de_DE', 'EUR',     true,              1234567.891234567890000, '1.234.567,89 €'),
            array('de_DE', 'RUR',     true,              1234567.891234567890000, '1.234.567,89 RUR'),
            array('ru_RU', 'EUR',     true,              1234567.891234567890000, '1 234 567,89 €'),
            array('ru_RU', 'RUR',     true,              1234567.891234567890000, '1 234 567,89 р.'),
            array('en_US', 'EUR',     true,              1234567.891234567890000, '€1,234,567.89'),
            array('en_US', 'RUR',     true,              1234567.891234567890000, 'RUR1,234,567.89'),
            array('en_US', 'USD',     true,              1234567.891234567890000, '$1,234,567.89'),
            array('de_AT', 'EUR',     false,             1234.56, '€ 1.235'),
            array('de_AT', 'EUR',     false,             0.123,   '€ 0'),
            array('de_DE', 'EUR',     false,             1234567.891234567890000, '1.234.568 €'),
            //array('de_DE', 'RUB',     false,             1234567.891234567890000, '1.234.567,89 RUB'),
            //array('ru_RU', 'EUR',     false,             1234567.891234567890000, '1 234 568 €'),
            //array('ru_RU', 'RUR',     false,             1234567.891234567890000, '1 234 567 р.'),
            //array('en_US', 'EUR',     false,             1234567.891234567890000, '€1,234,568'),
            //array('en_US', 'EUR',     false,             1234567.891234567890000, '€1,234,568'),
            array('en_US', 'USD',     false,             1234567.891234567890000, '$1,234,568'),
        );
    }

    /**
     * @dataProvider currencyProvider
     */
    public function testBasic($locale, $currencyCode, $showDecimals, $number, $expected)
    {
        $this->assertMbStringEquals($expected, $this->helper->__invoke(
            $number, $currencyCode, $showDecimals, $locale
        ));
    }

    /**
     * @dataProvider currencyProvider
     */
    public function testSettersProvideDefaults($locale, $currencyCode, $showDecimals, $number, $expected)
    {
        $this->helper
             ->setLocale($locale)
             ->setShouldShowDecimals($showDecimals)
             ->setCurrencyCode($currencyCode);

        $this->assertMbStringEquals($expected, $this->helper->__invoke($number));
    }

    public function testDefaultLocale()
    {
        $this->assertMbStringEquals(Locale::getDefault(), $this->helper->getLocale());
    }

    public function assertMbStringEquals($expected, $test, $message = '')
    {
        $expected = str_replace(array("\xC2\xA0", ' '), '', $expected);
        $test     = str_replace(array("\xC2\xA0", ' '), '', $test);
        $this->assertEquals($expected, $test, $message);
    }
}
