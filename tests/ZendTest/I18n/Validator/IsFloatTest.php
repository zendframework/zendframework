<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\I18n\Validator;

use Zend\I18n\Validator\IsFloat as IsFloatValidator;
use Locale;
use NumberFormatter;

/**
 * @group      Zend_Validator
 */
class IsFloatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IsFloatValidator
     */
    protected $validator;

    /**
     * @var string
     */
    protected $locale;

    public function setUp()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $this->locale    = Locale::getDefault();
        $this->validator = new IsFloatValidator(array('locale' => 'en'));
    }

    public function tearDown()
    {
        if (extension_loaded('intl')) {
            Locale::setDefault($this->locale);
        }
    }

    /**
     * Test float and interger type variables. Includes decimal and scientific notation NumberFormatter-formatted
     * versions. Should return true for all locales.
     *
     * @param string  $value    that will be tested
     * @param boolean $expected expected result of assertion
     * @param string  $locale   locale for validation
     * @dataProvider floatAndIntegerProvider
     * @return void
     */
    public function testFloatAndIntegers($value, $expected, $locale, $type)
    {
        $this->validator->setLocale($locale);

        $this->assertEquals(
            $expected,
            $this->validator->isValid($value),
            'Failed expecting ' . $value . ' being ' . ($expected ? 'true' : 'false') .
            sprintf(" (locale:%s, type:%s)", $locale, $type) . ', ICU Version:' . INTL_ICU_VERSION . '-' .
            INTL_ICU_DATA_VERSION
        );
    }

    public function floatAndIntegerProvider()
    {
        $trueArray       = array();
        $testingLocales  = array('ar', 'bn', 'de', 'dz', 'en', 'fr-CH', 'ja', 'ks', 'ml-IN', 'mr', 'my', 'ps', 'ru');
        $testingExamples = array(1000, -2000, +398.00, 0.04, -0.5, .6, -.70, 8E10, -9.3456E-2, 10.23E6,
            123.1234567890987654321);

        //Loop locales and examples for a more thorough set of "true" test data
        foreach ($testingLocales as $locale) {
            foreach ($testingExamples as $example) {
                $trueArray[] = array($example, true, $locale, 'raw');
                //Decimal Formatted
                $trueArray[] = array(
                    NumberFormatter::create($locale, NumberFormatter::DECIMAL)
                        ->format($example, NumberFormatter::TYPE_DOUBLE),
                    true,
                    $locale,
                    'decimal'
                );
                //Scientific Notation Formatted
                $trueArray[] = array(
                    NumberFormatter::create($locale, NumberFormatter::SCIENTIFIC)
                        ->format($example, NumberFormatter::TYPE_DOUBLE),
                    true,
                    $locale,
                    'scientific'
                );
            }
        }
        return $trueArray;
    }

    /**
     * Test manually-generated strings for specific locales. These are "look-alike" strings where graphemes such as
     * NO-BREAK SPACE, ARABIC THOUSANDS SEPARATOR, and ARABIC DECIMAL SEPARATOR are replaced with more typical ASCII
     * characters.
     *
     * @param string  $value    that will be tested
     * @param boolean $expected expected result of assertion
     * @param string  $locale   locale for validation
     * @dataProvider lookAlikeProvider
     * @return void
     */
    public function testlookAlikes($value, $expected, $locale)
    {
        $this->validator->setLocale($locale);

        $this->assertEquals(
            $expected,
            $this->validator->isValid($value),
            'Failed expecting ' . $value . ' being ' . ($expected ? 'true' : 'false') . sprintf(" (locale:%s)", $locale)
        );
    }

    public function lookAlikeProvider()
    {
        $trueArray     = array();
        $testingArray  = array(
            'ar' => "\xD9\xA1'\xD9\xA1\xD9\xA1\xD9\xA1,\xD9\xA2\xD9\xA3",
            'ru' => '2 000,00'
        );

        //Loop locales and examples for a more thorough set of "true" test data
        foreach ($testingArray as $locale => $example) {
            $trueArray[] = array($example, true, $locale);
        }
        return $trueArray;
    }

    /**
     * Test manually-generated strings for specific locales. These are "look-alike" strings where graphemes such as
     * NO-BREAK SPACE, ARABIC THOUSANDS SEPARATOR, and ARABIC DECIMAL SEPARATOR are replaced with more typical ASCII
     * characters.
     *
     * @param string  $value    that will be tested
     * @param boolean $expected expected result of assertion
     * @param string  $locale   locale for validation
     * @dataProvider validationFailureProvider
     * @return void
     */
    public function testValidationFailures($value, $expected, $locale)
    {
        $this->validator->setLocale($locale);

        $this->assertEquals(
            $expected,
            $this->validator->isValid($value),
            'Failed expecting ' . $value . ' being ' . ($expected ? 'true' : 'false') . sprintf(" (locale:%s)", $locale)
        );
    }

    public function validationFailureProvider()
    {
        $trueArray     = array();
        $testingArray  = array(
            'ar'    => array('10.1', '66notflot.6'),
            'ru'    => array('10.1', '66notflot.6', '2,000.00', '2 00'),
            'en'    => array('10,1', '66notflot.6', '2.000,00', '2 000', '2,00'),
            'fr-CH' => array('10,1', '66notflot.6', '2,000.00', "2'00")
        );

        //Loop locales and examples for a more thorough set of "true" test data
        foreach ($testingArray as $locale => $exampleArray) {
            foreach ($exampleArray as $example) {
                $trueArray[] = array($example, false, $locale);
            }
        }
        return $trueArray;
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $this->assertEquals(array(), $this->validator->getMessages());
    }

    /**
     * Ensures that set/getLocale() works
     */
    public function testSettingLocales()
    {
        $this->validator->setLocale('de');
        $this->assertEquals('de', $this->validator->getLocale());
    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation()
    {
        $this->assertFalse($this->validator->isValid(array(1 => 1)));
    }

    /**
     * @ZF-7489
     */
    public function testUsingApplicationLocale()
    {
        Locale::setDefault('de');
        $valid = new IsFloatValidator();
        $this->assertEquals('de', $valid->getLocale());
    }

    public function testEqualsMessageTemplates()
    {
        $validator = $this->validator;
        $this->assertAttributeEquals($validator->getOption('messageTemplates'), 'messageTemplates', $validator);
    }

    /**
     * @group 6647
     * @group 6648
     */
    public function testNotFloat()
    {
        $this->assertFalse($this->validator->isValid('2.000.000,00'));

        $message = $this->validator->getMessages();
        $this->assertContains('does not appear to be a float', $message['notFloat']);
    }
}
