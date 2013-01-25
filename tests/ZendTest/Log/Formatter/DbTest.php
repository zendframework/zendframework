<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */

namespace ZendTest\Log\Formatter;

use DateTime;
use Zend\Log\Formatter\Db as DbFormatter;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @group      Zend_Log
 */
class DbTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultDateTimeFormat()
    {
        $formatter = new DbFormatter();
        $this->assertEquals(DbFormatter::DEFAULT_DATETIME_FORMAT, $formatter->getDateTimeFormat());
    }

    /**
     * @dataProvider provideDateTimeFormats
     */
    public function testSetDateTimeFormat($dateTimeFormat)
    {
        $formatter = new DbFormatter();
        $formatter->setDateTimeFormat($dateTimeFormat);

        $this->assertEquals($dateTimeFormat, $formatter->getDateTimeFormat());
    }

    /**
     * @return array
     */
    public function provideDateTimeFormats()
    {
        return array(
            array('r'),
            array('U'),
            array(DateTime::RSS),
        );
    }

    /**
     * @dataProvider provideDateTimeFormats
     */
    public function testAllowsSpecifyingDateTimeFormatAsConstructorArgument($dateTimeFormat)
    {
        $formatter = new DbFormatter($dateTimeFormat);

        $this->assertEquals($dateTimeFormat, $formatter->getDateTimeFormat());
    }

    public function testFormatDateTimeInEvent()
    {
        $datetime = new DateTime();
        $event = array('timestamp' => $datetime);
        $formatter = new DbFormatter();

        $format = DbFormatter::DEFAULT_DATETIME_FORMAT;
        $this->assertContains($datetime->format($format), $formatter->format($event));
    }
}
