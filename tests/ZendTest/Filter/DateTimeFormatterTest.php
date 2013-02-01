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
    public function setUp()
    {
        date_default_timezone_set('UTC');
    }

    public function testDateTimeFormatted()
    {
        $filter = new DateTimeFormatter();
        $result = $filter->filter('2012-01-01');
        $this->assertEquals('2012-01-01T00:00:00+0000', $result);
    }

    public function testSetFormat()
    {
        $filter = new DateTimeFormatter();
        $filter->setFormat(DateTime::RFC1036);
        $result = $filter->filter('2012-01-01');
        $this->assertEquals('Sun, 01 Jan 12 00:00:00 +0000', $result);
    }

    public function testFormatDateTimeFromTimestamp()
    {
        $filter = new DateTimeFormatter();
        $result = $filter->filter(1359739801);
        $this->assertEquals('2013-02-01T17:30:01+0000', $result);
    }

    public function testOriginalValueReturnedOnInvalidInput()
    {
        $filter = new DateTimeFormatter();
        $result = $filter->filter('2013-31-31');
        $this->assertEquals('2013-31-31', $result);
    }
}
