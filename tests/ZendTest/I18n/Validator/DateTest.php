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

use Zend\I18n\Validator\Date as DateValidator;
use Locale;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class DateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DateValidator
     */
    protected $validator;

    public function setUp()
    {
        $this->validator = new DateValidator(array('locale' => 'en'));
    }

    public function tearDown()
    {
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
            array('12/30/2013',   true, array('locale'=>'en')),
            array('30/12/2013',   false, array('locale'=>'en')),
            array('30.12.2013',   true, array('locale'=>'de')),
            array('12.30.2013',   false, array('locale'=>'de')),
            array('30-12-2013',   true, array('locale'=>'nl')),
            array('12-30-2013',   false, array('locale'=>'nl')),

            array('May 30, 2013',   true, array('locale'=>'en', 'dateFormat' => \IntlDateFormatter::MEDIUM)),
            array('30.Mai.2013',   true, array('locale'=>'de', 'dateFormat' => \IntlDateFormatter::MEDIUM)),
            array('30 Mei 2013',   true, array('locale'=>'nl', 'dateFormat' => \IntlDateFormatter::MEDIUM)),

            array('May 38, 2013',   false, array('locale'=>'en', 'dateFormat' => \IntlDateFormatter::FULL)),
            array('Dienstag, 28. Mai 2013',   true, array('locale'=>'de', 'dateFormat' => \IntlDateFormatter::FULL)),
            array('Maandag 28 Mei 2013',   true, array('locale'=>'nl', 'dateFormat' => \IntlDateFormatter::FULL)),
        );
    }

    /**
     * Ensures that dateFormat default is IntlDateFormatter::SHORT
     *
     * @return void
     */
    public function testDateFormatDefault()
    {
        $this->assertEquals(\IntlDateFormatter::SHORT, $this->validator->getDateFormat());
    }

    /**
     * Ensures that timeFormat is IntlDateFormatter::NONE by default for date validator
     *
     * @return void
     */
    public function testTimeFormatDefault()
    {
        $this->assertEquals(\IntlDateFormatter::NONE, $this->validator->getTimeFormat());
    }

    /**
     * Ensures that timeFormat can't be changed
     *
     * @return void
     */
    public function testTimeFormatNotChangeable()
    {
        $this->setExpectedException('Zend\I18n\Exception\InvalidArgumentException', 'immutable');

        $this->validator->setTimeFormat(\IntlDateFormatter::FULL);
    }

    /**
     * Makes sure error message set by DateTime Validator is changed to something more appropiate
     */
    public function testMessagesHasInvalidDateKey()
    {
        $this->validator->isValid('not a valid date!');

        $this->assertArrayHasKey('dateInvalidDate', $this->validator->getMessages());
    }

}
