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

use Zend\I18n\Validator\Time as TimeValidator;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class TimeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TimeValidator
     */
    protected $validator;

    public function setUp()
    {
        $this->validator = new TimeValidator(array('locale' => 'en'));
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
            array('0:00',   true, array('locale'=>'nl', 'timeFormat' => \IntlDateFormatter::SHORT)),
            array('01:01',   true, array('locale'=>'nl', 'timeFormat' => \IntlDateFormatter::SHORT)),
            array('01:01:01',   true, array('locale'=>'nl', 'timeFormat' => \IntlDateFormatter::MEDIUM)),
            array('01:01:01 +2',   true, array('locale'=>'nl', 'timeFormat' => \IntlDateFormatter::LONG)),
            array('03:30:42 am +2',   true, array('locale'=>'en', 'timeFormat' => \IntlDateFormatter::LONG)),
        );
    }

    /**
     * Ensures that timeFormat default is IntlDateFormatter::SHORT
     *
     * @return void
     */
    public function testTimeFormatDefault()
    {
        $this->assertEquals(\IntlDateFormatter::SHORT, $this->validator->getTimeFormat());
    }

    /**
     * Ensures that dateFormat is IntlDateFormatter::NONE by default for time validator
     *
     * @return void
     */
    public function testDateFormatDefault()
    {
        $this->assertEquals(\IntlDateFormatter::NONE, $this->validator->getDateFormat());
    }

    /**
     * Ensures that dateFormat can't be changed
     *
     * @return void
     */
    public function testDateFormatImmutable()
    {
        $this->setExpectedException('Zend\I18n\Exception\InvalidArgumentException', 'immutable');

        $this->validator->setDateFormat(\IntlDateFormatter::FULL);
    }

    /**
     * Makes sure error message set by DateTime Validator is changed to something more appropiate
     */
    public function testMessagesHasInvalidTimeKey()
    {
        $this->validator->isValid('not a valid time!');

        $this->assertArrayHasKey('dateInvalidTime', $this->validator->getMessages());
    }
}
