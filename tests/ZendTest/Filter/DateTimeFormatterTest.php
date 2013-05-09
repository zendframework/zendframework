<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Filter
 */

namespace ZendTest\Filter;

use DateTime;
use Zend\Filter\DateTimeFormatter;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 */
class DateTimeFormatterTest extends \PHPUnit_Framework_TestCase
{
    protected $defaultTimezone;

    public function setUp()
    {
        $this->defaultTimezone = date_default_timezone_get();
    }

    public function tearDown()
    {
        date_default_timezone_set($this->defaultTimezone);
    }

    public function testFormatterDoesNotFormatAnEmptyString()
    {
        date_default_timezone_set('UTC');

        $filter = new DateTimeFormatter();
        $result = $filter->filter('');
        $this->assertEquals('', $result);
    }

    public function testFormatterDoesNotFormatNull()
    {
        $filter = new DateTimeFormatter();
        $result = $filter->filter(null);
        $this->assertEquals(null, $result);
    }

    public function testFormatterFormatsZero()
    {
        date_default_timezone_set('UTC');

        $filter = new DateTimeFormatter();
        $result = $filter->filter(0);
        $this->assertEquals('1970-01-01T00:00:00+0000', $result);
    }

    public function testDateTimeFormatted()
    {
        date_default_timezone_set('UTC');

        $filter = new DateTimeFormatter();
        $result = $filter->filter('2012-01-01');
        $this->assertEquals('2012-01-01T00:00:00+0000', $result);
    }

    public function testDateTimeFormattedWithAlternateTimezones()
    {
        $filter = new DateTimeFormatter();

        date_default_timezone_set('Europe/Paris');

        $resultParis = $filter->filter('2012-01-01');
        $this->assertEquals('2012-01-01T00:00:00+0100', $resultParis);

        date_default_timezone_set('America/New_York');

        $resultNewYork = $filter->filter('2012-01-01');
        $this->assertEquals('2012-01-01T00:00:00-0500', $resultNewYork);
    }

    public function testSetFormat()
    {
        date_default_timezone_set('UTC');

        $filter = new DateTimeFormatter();
        $filter->setFormat(DateTime::RFC1036);
        $result = $filter->filter('2012-01-01');
        $this->assertEquals('Sun, 01 Jan 12 00:00:00 +0000', $result);
    }

    public function testFormatDateTimeFromTimestamp()
    {
        date_default_timezone_set('UTC');

        $filter = new DateTimeFormatter();
        $result = $filter->filter(1359739801);
        $this->assertEquals('2013-02-01T17:30:01+0000', $result);
    }

    public function testInvalidArgumentExceptionThrownOnInvalidInput()
    {
        $this->setExpectedException('Zend\Filter\Exception\InvalidArgumentException');

        $filter = new DateTimeFormatter();
        $result = $filter->filter('2013-31-31');
    }
}
