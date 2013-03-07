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

        $this->validator = new DateTimeValidator(array('locale' => 'en'));
    }

    public function tearDown()
    {
        Locale::setDefault($this->locale);
        date_default_timezone_set($this->timezone);
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @dataProvider basicProvider
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
            array('5-10-04',     false, array()),
//            array('20150303 01:01',   true, array('locale'=>'nl', 'timeFormat' => \IntlDateFormatter::SHORT)),
//            array('2/34/2015',   false, array()),
//            array('2/15/2015',   true, array()),
//            array('5/10/04',     true, array()),
//            array('January 12, 1952',     true,
//                array('locale'=>'en_UK', 'dateFormat' => \IntlDateFormatter::LONG, 'timeFormat' => \IntlDateFormatter::NONE, 'calender' => \IntlDateFormatter::TRADITIONAL)),
//
//            array('5 October 2004',     true, array()),
//
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
     * Ensures that ommited pattern results in pattern being set (after isValid)
     */
    public function testOptionPatternOmmited()
    {
        $this->validator->isValid('does not matter');

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
