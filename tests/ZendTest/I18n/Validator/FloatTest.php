<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\I18n\Validator;

use Zend\I18n\Validator\Float as FloatValidator;
use Locale;
use NumberFormatter;

/**
 * @group      Zend_Validator
 */
class FloatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FloatValidator
     */
    protected $validator;

    /** @var string */
    protected $locale;

    public function setUp()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $this->locale    = Locale::getDefault();
        $this->validator = new FloatValidator(array('locale' => 'en'));
    }

    public function tearDown()
    {
        if (extension_loaded('intl')) {
            Locale::setDefault($this->locale);
        }
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @param string  $value    that will be tested
     * @param boolean $expected expected result of assertion
     * @param array   $options  fed into the validator before validation
     * @param string  $dataType The different types of testing data used
     * @dataProvider basicProvider
     * @return void
     */
    public function testBasic($value, $expected, $options, $dataType)
    {
        $this->validator->setOptions($options);

        $this->assertEquals(
            $expected,
            $this->validator->isValid($value),
            'Failed expecting ' . $value . ' being ' . ($expected ? 'true' : 'false')
            . sprintf(" (locale:%s, dataType:%s)", $this->validator->getLocale(), $dataType)
        );
    }

    public function basicProvider()
    {
        $trueArray           = array();
        $testingLocales      = array('en', 'de', 'zh-TW', 'ja', 'ar', 'ru', 'si', 'ml-IN', 'hi');
        $testingTrueExamples = array(1.00, 0.01, -0.1, .3, 10, '10.1', '5.00', '234', '.45');

        //Loop locales and examples for a more thorough set of "true" test data
        foreach ($testingLocales as $locale) {
            foreach ($testingTrueExamples as $example) {
                $trueArray[] = array(
                    NumberFormatter::create($locale, NumberFormatter::PATTERN_DECIMAL)
                        ->format($example, NumberFormatter::TYPE_DOUBLE),
                    true,
                    array('locale' => $locale)
                );
            }
            $falseArray[] = array(
                '10.1not a float',
                false,
                array('locale' => $locale)
            );
        }

        return array_merge($trueArray, $falseArray);
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
        $valid = new FloatValidator();
        $this->assertEquals('de', $valid->getLocale());
    }

    public function testEqualsMessageTemplates()
    {
        $validator = $this->validator;
        $this->assertAttributeEquals($validator->getOption('messageTemplates'), 'messageTemplates', $validator);
    }
}
