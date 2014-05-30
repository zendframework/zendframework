<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Http\Header;

use Zend\Http\Header\Date;
use Zend\Http\Header\Exception\InvalidArgumentException;
use DateTime;
use DateTimeZone;

class DateTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        // set to RFC default date format
        Date::setDateFormat(Date::DATE_RFC1123);
    }


    public function testDateFromStringCreatesValidDateHeader()
    {
        $dateHeader = Date::fromString('Date: Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $dateHeader);
        $this->assertInstanceOf('Zend\Http\Header\Date', $dateHeader);
    }

    public function testDateFromTimeCreatesValidDateHeader()
    {
        // testing using a strtotime()-compatible string
        $dateHeader = Date::fromTime('+12 hours');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $dateHeader);
        $this->assertInstanceOf('Zend\Http\Header\Date', $dateHeader);
        $date     = new \DateTime(null, new \DateTimeZone('GMT'));
        $interval = $dateHeader->date()->diff($date, true);
        $this->assertSame('+12 hours', $interval->format('%R%H hours'));

        // testing using unix time
        $dateHeader = Date::fromTime(time() + 12 * 60 * 60);
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $dateHeader);
        $this->assertInstanceOf('Zend\Http\Header\Date', $dateHeader);
        $date     = new \DateTime(null, new \DateTimeZone('GMT'));
        $interval = $dateHeader->date()->diff($date, true);
        $this->assertSame('+12 hours', $interval->format('%R%H hours'));
    }

    public function testDateFromTimeDetectsBadInput()
    {
        // testing using a movie title
        try {
            $badResult = Date::fromTime('3 Days of the Condor');
        } catch (InvalidArgumentException $e) {
            // nothing to do here - the exception is meant to be silently discarded
        }

        // $badResult would only be set if the exception was not properly thrown
        if (isset($badResult)) {
            $this->fail('Expected an exception to be thrown in Date::fromTime()');
        }
    }

    public function testDateGetFieldNameReturnsHeaderName()
    {
        $dateHeader = new Date();
        $this->assertEquals('Date', $dateHeader->getFieldName());
    }

    public function testDateGetFieldValueReturnsProperValue()
    {
        $dateHeader = new Date();
        $dateHeader->setDate('Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertEquals('Sun, 06 Nov 1994 08:49:37 GMT', $dateHeader->getFieldValue());
    }

    public function testDateToStringReturnsHeaderFormattedString()
    {
        $dateHeader = new Date();
        $dateHeader->setDate('Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertEquals('Date: Sun, 06 Nov 1994 08:49:37 GMT', $dateHeader->toString());
    }

    /** Implementation specific tests */

    public function testDateReturnsDateTimeObject()
    {
        $dateHeader = new Date();
        $this->assertInstanceOf('\DateTime', $dateHeader->date());
    }

    public function testDateFromStringCreatesValidDateTime()
    {
        $dateHeader = Date::fromString('Date: Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertInstanceOf('\DateTime', $dateHeader->date());
        $this->assertEquals('Sun, 06 Nov 1994 08:49:37 GMT', $dateHeader->date()->format('D, d M Y H:i:s \G\M\T'));
    }

    public function testDateReturnsProperlyFormattedDate()
    {
        $date = new DateTime('now', new DateTimeZone('GMT'));

        $dateHeader = new Date();
        $dateHeader->setDate($date);
        $this->assertEquals($date->format('D, d M Y H:i:s \G\M\T'), $dateHeader->getDate());
    }

    public function testDateThrowsExceptionForInvalidDate()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException', 'Invalid date');
        $dateHeader = new Date();
        $dateHeader->setDate('~~~~');
    }

    public function testDateCanCompareDates()
    {
        $dateHeader = new Date();
        $dateHeader->setDate('1 day ago');
        $this->assertEquals(-1, $dateHeader->compareTo(new DateTime('now')));
    }

    public function testDateCanOutputDatesInOldFormats()
    {
        Date::setDateFormat(Date::DATE_ANSIC);

        $dateHeader = new Date();
        $dateHeader->setDate('Sun, 06 Nov 1994 08:49:37 GMT');

        $this->assertEquals('Date: Sun Nov 6 08:49:37 1994', $dateHeader->toString());
    }
}
