<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Validator
 */

namespace ZendTest\Validator;

use Zend\Validator;
use DateTime;
use DateInterval;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class DateStepTest extends \PHPUnit_Framework_TestCase
{
    public function stepTestsDataProvider()
    {
        $data = array(
            //    interval format            baseValue               value                  isValid
            array('PT1S', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-01-01T00:00:00Z', true ),
            array('PT1S', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-01-03T00:00:00Z', true ),
            array('PT1S', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-01-01T00:00:02Z', true ),
            array('PT2S', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-01-01T00:00:01Z', false),
            array('PT2S', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-01-01T00:00:16Z', true ),
            array('PT2S', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-01-03T00:00:00Z', true ),
            // minutes
            array('PT1M', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-03-01T00:00:00Z', true ),
            array('PT1M', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-03-01T00:00:30Z', false),
            array('PT1M', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-01-01T00:02:00Z', true ),
            array('PT2M', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-01-01T00:01:00Z', false),
            array('PT2M', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-01-01T00:16:00Z', true ),
            array('PT2M', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-03-01T00:00:00Z', true ),
            array('PT1M', 'H:i:s',           '00:00:00',             '12:34:00',             true ),
            array('PT2M', 'H:i:s',           '00:00:00',             '12:34:00',             true ),
            array('PT2M', 'H:i:s',           '00:00:00',             '12:35:00',             false),
            // hours
            array('PT1H', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-03-01T00:00:00Z', true ),
            array('PT1H', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-03-01T00:00:30Z', false),
            array('PT1H', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-01-01T02:00:00Z', true ),
            array('PT2H', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-01-01T01:00:00Z', false),
            array('PT2H', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-01-01T16:00:00Z', true ),
            array('PT2H', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-03-01T00:00:00Z', true ),
            // days
            array('P1D',  DateTime::ISO8601, '1970-01-01T00:00:00Z', '1973-01-01T00:00:00Z', true ),
            array('P1D',  DateTime::ISO8601, '1970-01-01T00:00:00Z', '1973-01-01T00:00:30Z', false),
            array('P2D',  DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-01-02T00:00:00Z', false),
            array('P2D',  DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-01-15T00:00:00Z', true ),
            array('P2D',  DateTime::ISO8601, '1971-01-01T00:00:00Z', '1973-01-01T00:00:00Z', false),
            array('P2D',  DateTime::ISO8601, '2000-01-01T00:00:00Z', '2001-01-01T00:00:00Z', true ), // leap year
            // weeks
            array('P1W',  DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-01-29T00:00:00Z', true ),
            // months
            array('P1M',  DateTime::ISO8601, '1970-01-01T00:00:00Z', '1973-01-01T00:00:00Z', true ),
            array('P1M',  DateTime::ISO8601, '1970-01-01T00:00:00Z', '1973-01-01T00:00:30Z', false),
            array('P2M',  DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-02-01T00:00:00Z', false),
            array('P2M',  DateTime::ISO8601, '1970-01-01T00:00:00Z', '1971-05-01T00:00:00Z', true ),
            array('P1M',  'Y-m',             '1970-01',              '1970-10',              true ),
            array('P2M',  '!Y-m',            '1970-01',              '1970-11',              true ),
            array('P2M',  'Y-m',             '1970-01',              '1970-10',              false),
            // years
            array('P1Y',  DateTime::ISO8601, '1970-01-01T00:00:00Z', '1973-01-01T00:00:00Z', true ),
            array('P1Y',  DateTime::ISO8601, '1970-01-01T00:00:00Z', '1973-01-01T00:00:30Z', false),
            array('P2Y',  DateTime::ISO8601, '1970-01-01T00:00:00Z', '1971-01-01T00:00:00Z', false),
            array('P2Y',  DateTime::ISO8601, '1970-01-01T00:00:00Z', '1976-01-01T00:00:00Z', true ),
            // complex
            array('P2M2DT12H', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-03-03T12:00:00Z', true ),
            array('P2M2DT12M', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-03-03T12:00:00Z', false),
        );

        // bug in DateTime fixed in 5.3.7
        if (version_compare(PHP_VERSION, '5.3.7', '>=')) {
            $data[] = array('P2W',  'Y-\WW',           '1970-W01',             '1973-W16',             true );
            $data[] = array('P2W',  'Y-\WW',           '1970-W01',             '1973-W17',             false);
        }
        return $data;
    }

    /**
     * @dataProvider stepTestsDataProvider
     */
    public function testDateStepValidation($interval, $format, $baseValue, $value, $isValid)
    {
        $validator = new Validator\DateStep(array(
            'format'       => $format,
            'baseValue'    => $baseValue,
            'step' => new DateInterval($interval),
        ));

        $this->assertEquals($isValid, $validator->isValid($value));
    }

    public function testGetMessagesReturnsDefaultValue()
    {
        $validator = new Validator\DateStep();
        $this->assertEquals(array(), $validator->getMessages());
    }

    public function testEqualsMessageTemplates()
    {
        $validator  = new Validator\DateStep(array());
        $this->assertObjectHasAttribute('messageTemplates', $validator);
        $this->assertAttributeEquals($validator->getOption('messageTemplates'), 'messageTemplates', $validator);
    }

    public function testStepError()
    {
        $validator = new Validator\DateStep(array(
            'format'       => 'Y-m-d',
            'baseValue'    => '2012-01-23',
            'step' => new DateInterval("P10D"),
        ));

        $this->assertFalse($validator->isValid('2012-13-13'));
    }
}
