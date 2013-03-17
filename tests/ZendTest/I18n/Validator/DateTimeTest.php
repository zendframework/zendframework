<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_I18n
 */

namespace ZendTest\I18n\Validator;

use Zend\I18n\Validator\DateTime as DateTimeValidator;
use Locale;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class DateTimeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DateTimeValidator
     */
    protected $validator;

    protected $locale;

    protected $timezone;

    public function setUp()
    {
        $this->locale = Locale::getDefault();
        $this->timezone = date_default_timezone_get();

        $this->validator = new DateTimeValidator(array('locale' => 'en', 'timezone'=>'Europe/Amsterdam'));
    }

    public function tearDown()
    {
        Locale::setDefault($this->locale);
        date_default_timezone_set($this->timezone);
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @param string  $value    that will be tested
     * @param boolean $expected expected result of assertion
     * @param array   $options  fed into the validator before validation
     * @dataProvider basicProvider name of method that provide the above parameters
     * @return void
     */
    public function testBasic($value, $expected, $options = array())
    {
        $this->validator->setOptions($options);

        $this->assertEquals($expected, $this->validator->isValid($value),
            'Failed expecting ' . $value . ' being ' . ($expected ? 'true' : 'false') .
                sprintf(" (locale:%s, dateFormat: %s, timeFormat: %s, pattern:%s)", $this->validator->getLocale(), $this->validator->getDateFormat(), $this->validator->getTimeFormat(), $this->validator->getPattern()));
    }

    public function basicProvider()
    {
        return array(
            array('May 30, 2013',   true, array('locale'=>'en', 'dateFormat' => \IntlDateFormatter::MEDIUM, 'timeFormat' => \IntlDateFormatter::NONE)),
            array('30.Mai.2013',   true, array('locale'=>'de', 'dateFormat' => \IntlDateFormatter::MEDIUM, 'timeFormat' => \IntlDateFormatter::NONE)),
            array('30 Mei 2013',   true, array('locale'=>'nl', 'dateFormat' => \IntlDateFormatter::MEDIUM, 'timeFormat' => \IntlDateFormatter::NONE)),

            array('May 38, 2013',   false, array('locale'=>'en', 'dateFormat' => \IntlDateFormatter::FULL, 'timeFormat' => \IntlDateFormatter::NONE)),
            array('Dienstag, 28. Mai 2013',   true, array('locale'=>'de', 'dateFormat' => \IntlDateFormatter::FULL, 'timeFormat' => \IntlDateFormatter::NONE)),
            array('Maandag 28 Mei 2013',   true, array('locale'=>'nl', 'dateFormat' => \IntlDateFormatter::FULL, 'timeFormat' => \IntlDateFormatter::NONE)),

            array('0:00',   true, array('locale'=>'nl', 'dateFormat' => \IntlDateFormatter::NONE, 'timeFormat' => \IntlDateFormatter::SHORT)),
            array('01:01',   true, array('locale'=>'nl', 'dateFormat' => \IntlDateFormatter::NONE, 'timeFormat' => \IntlDateFormatter::SHORT)),
            array('01:01:01',   true, array('locale'=>'nl', 'dateFormat' => \IntlDateFormatter::NONE, 'timeFormat' => \IntlDateFormatter::MEDIUM)),
            array('01:01:01 +2',   true, array('locale'=>'nl', 'dateFormat' => \IntlDateFormatter::NONE, 'timeFormat' => \IntlDateFormatter::LONG)),
            array('03:30:42 am +2',   true, array('locale'=>'en', 'dateFormat' => \IntlDateFormatter::NONE, 'timeFormat' => \IntlDateFormatter::LONG)),
        );
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
    public function testOptionLocale()
    {
        $this->validator->setLocale('de');
        $this->assertEquals('de', $this->validator->getLocale());
    }

    public function testApplicationOptionLocale()
    {
        Locale::setDefault('nl');
        $valid = new DateTimeValidator();
        $this->assertEquals(Locale::getDefault(), $valid->getLocale());
    }

    /**
     * Ensures that set/getTimezone() works
     */
    public function testOptionTimezone()
    {
        $this->validator->setLocale('Europe/Berlin');
        $this->assertEquals('Europe/Berlin', $this->validator->getLocale());
    }

    public function testApplicationOptionTimezone()
    {
        date_default_timezone_set('Europe/Berlin');
        $valid = new DateTimeValidator();
        $this->assertEquals(date_default_timezone_get(), $valid->getTimezone());
    }

    /**
     * Ensures that an omitted pattern results in a calculated pattern by IntlDateFormatter
     */
    public function testOptionPatternOmitted()
    {
        // null before validation
        $this->assertNull($this->validator->getPattern());

        $this->validator->isValid('does not matter');

        // set after
        $this->assertEquals('yyyyMMdd hh:mm a', $this->validator->getPattern());
    }

    /**
     * Ensures that setting the pattern results in pattern used (by the validation process)
     */
    public function testOptionPattern()
    {
        $this->validator->setOptions(array('pattern'=>'hh:mm'));

        $this->assertTrue($this->validator->isValid('02:00'));
        $this->assertEquals('hh:mm', $this->validator->getPattern());
    }

}
