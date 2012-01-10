<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Date
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Date;

use Zend\Date\Date,
    Zend\Date\DateObject,
    Zend\Cache\StorageFactory as CacheFactory,
    Zend\Cache\Storage\Adapter as CacheAdapter,
    Zend\Locale\Locale;

/**
 * @category   Zend
 * @package    Zend_Date
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Date
 */
class DateObjectTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Stores the original set timezone
     * @var string
     */
    private $_originaltimezone;

    public function setUp()
    {
        $this->_originaltimezone = date_default_timezone_get();
        date_default_timezone_set('Europe/Paris');
        $this->_cache = CacheFactory::factory(array(
            'adapter' => array(
                'name' => 'filesystem',
                'options' => array(
                    'ttl' => 120,
                ),
            ),
            'plugins' => array(
                array(
                    'name' => 'serializer',
                    'options' => array(
                        'serializer' => 'php_serialize',
                    ),
                ),
            ),
        ));
        DateObjectTestHelper::setOptions(array('cache' => $this->_cache));
    }

    public function tearDown()
    {
        date_default_timezone_set($this->_originaltimezone);
        $this->_cache->clear(CacheAdapter::MATCH_ALL);
    }

    /**
     * Test for date object creation null value
     */
    public function testCreationNull()
    {
        // look if locale is detectable
        try {
            $locale = new Locale();
        } catch (\Zend\Locale\Exception $e) {
            $this->markTestSkipped('Autodetection of locale failed');
            return;
        }

        $date = new Date(0);
        $this->assertTrue($date instanceof Date);
    }

    /**
     * Test for date object creation negative timestamp
     */
    public function testCreationNegative()
    {
        // look if locale is detectable
        try {
            $locale = new Locale();
        } catch (\Zend\Locale\Exception $e) {
            $this->markTestSkipped('Autodetection of locale failed');
            return;
        }

        $date = new Date(1000);
        $this->assertTrue($date instanceof Date);
    }

    /**
     * Test for date object creation text given
     */
    public function testCreationFailed()
    {
        // look if locale is detectable
        try {
            $locale = new Locale();
        } catch (\Zend\Locale\Exception $e) {
            $this->markTestSkipped('Autodetection of locale failed');
            return;
        }

        try {
            $date = new Date("notimestamp");
            $this->fail("exception expected");
        } catch (\Zend\Date\Exception $e) {
            // success
        }
    }

    /**
     * Test for setUnixTimestamp
     */
    public function testsetUnixTimestamp()
    {
        $date = new DateObjectTestHelper(Date::now());
        $diff = abs(time() - $date->getUnixTimestamp());
        $this->assertTrue(($diff < 2), "Zend_Date->setUnixTimestamp() returned a significantly "
            . "different timestamp than expected: $diff seconds");
        $date->setUnixTimestamp(0);
        $this->assertSame('0', (string)$date->setUnixTimestamp("12345678901234567890"));
        $this->assertSame("12345678901234567890", (string)$date->setUnixTimestamp("12345678901234567890"));

        $date->setUnixTimestamp();
        $diff = abs(time() - $date->getUnixTimestamp());
        $this->assertTrue($diff < 2, "setUnixTimestamp has a significantly different time than returned by time()): $diff seconds");
    }

    /**
     * Test for setUnixTimestampFailed
     */
    public function testsetUnixTimestampFailed()
    {
        try {
            $date = new DateObjectTestHelper(Date::now());
            $date->setUnixTimestamp("notimestamp");
            $this->fail("exception expected");
        } catch (\Zend\Date\Exception $e) {
            // success
        }
    }

    /**
     * Test for getUnixTimestamp
     */
    public function testgetUnixTimestamp()
    {
        $date = new DateObjectTestHelper(Date::now());
        $result = $date->getUnixTimestamp();
        $diff = abs($result - time());
        $this->assertTrue($diff < 2, "Instance of Zend_Date_DateObject has a significantly different time than returned by setTime(): $diff seconds");
    }

    /**
     * Test for mktime
     */
    public function testMkTimeforDateValuesInPHPRange()
    {
        $date = new DateObjectTestHelper(Date::now());
        $this->assertSame(  mktime(0, 0, 0, 12, 30, 2037), $date->mktime(0, 0, 0, 12, 30, 2037, false));
        $this->assertSame(gmmktime(0, 0, 0, 12, 30, 2037), $date->mktime(0, 0, 0, 12, 30, 2037, true ));

        $this->assertSame(  mktime(0, 0, 0,  1,  1, 2000), $date->mktime(0, 0, 0,  1,  1, 2000, false));
        $this->assertSame(gmmktime(0, 0, 0,  1,  1, 2000), $date->mktime(0, 0, 0,  1,  1, 2000, true ));

        $this->assertSame(  mktime(0, 0, 0,  1,  1, 1970), $date->mktime(0, 0, 0,  1,  1, 1970, false));
        $this->assertSame(gmmktime(0, 0, 0,  1,  1, 1970), $date->mktime(0, 0, 0,  1,  1, 1970, true ));

        $this->assertSame(  mktime(0, 0, 0, 12, 30, 1902), $date->mktime(0, 0, 0, 12, 30, 1902, false));
        $this->assertSame(gmmktime(0, 0, 0, 12, 30, 1902), $date->mktime(0, 0, 0, 12, 30, 1902, true ));
    }

    /**
     * Test for mktime
     */
    public function testMkTimeforDateValuesGreaterPHPRange()
    {
        $date = new DateObjectTestHelper(Date::now());
        $this->assertSame(2232658800,  $date->mktime(0, 0, 0,10, 1, 2040, false));
        $this->assertSame(2232662400,  $date->mktime(0, 0, 0,10, 1, 2040, true ));
        $this->assertSame(7258114800,  $date->mktime(0, 0, 0, 1, 1, 2200, false));
        $this->assertSame(7258118400,  $date->mktime(0, 0, 0, 1, 1, 2200, true ));
        $this->assertSame(16749586800, $date->mktime(0, 0, 0,10,10, 2500, false));
        $this->assertSame(16749590400, $date->mktime(0, 0, 0,10,10, 2500, true ));
        $this->assertSame(32503676400, $date->mktime(0, 0, 0, 1, 1, 3000, false));
        $this->assertSame(32503680000, $date->mktime(0, 0, 0, 1, 1, 3000, true ));
        $this->assertSame(95617580400, $date->mktime(0, 0, 0, 1, 1, 5000, false));
        $this->assertSame(95617584000, $date->mktime(0, 0, 0, 1, 1, 5000, true ));

        // test for different set external timezone
        // the internal timezone should always be used for calculation
        $date->setTimezone('Europe/Paris');
        $this->assertSame(1577833200, $date->mktime(0, 0, 0, 1, 1, 2020, false));
        $this->assertSame(1577836800, $date->mktime(0, 0, 0, 1, 1, 2020, true ));
        date_default_timezone_set('Indian/Maldives');
        $this->assertSame(1577833200, $date->mktime(0, 0, 0, 1, 1, 2020, false));
        $this->assertSame(1577836800, $date->mktime(0, 0, 0, 1, 1, 2020, true ));
    }

    /**
     * Test for mktime
     */
    public function testMkTimeforDateValuesSmallerPHPRange()
    {
        $date = new DateObjectTestHelper(Date::now());
        $this->assertSame(-2208992400,   $date->mktime(0, 0, 0, 1, 1, 1900, false));
        $this->assertSame(-2208988800,   $date->mktime(0, 0, 0, 1, 1, 1900, true ));
        $this->assertSame(-8520339600,   $date->mktime(0, 0, 0, 1, 1, 1700, false));
        $this->assertSame(-8520336000,   $date->mktime(0, 0, 0, 1, 1, 1700, true ));
        $this->assertSame(-14830995600,  $date->mktime(0, 0, 0, 1, 1, 1500, false));
        $this->assertSame(-14830992000,  $date->mktime(0, 0, 0, 1, 1, 1500, true ));
        $this->assertSame(-12219321600,  $date->mktime(0, 0, 0,10,10, 1582, false));
        $this->assertSame(-12219321600,  $date->mktime(0, 0, 0,10,10, 1582, true ));
        $this->assertSame(-30609795600,  $date->mktime(0, 0, 0, 1, 1, 1000, false));
        $this->assertSame(-30609792000,  $date->mktime(0, 0, 0, 1, 1, 1000, true ));
        $this->assertSame(-62167395600,  $date->mktime(0, 0, 0, 1, 1,    0, false));
        $this->assertSame(-62167392000,  $date->mktime(0, 0, 0, 1, 1,    0, true ));
        $this->assertSame(-125282595600, $date->mktime(0, 0, 0, 1, 1,-2000, false));
        $this->assertSame(-125282592000, $date->mktime(0, 0, 0, 1, 1,-2000, true));

        $this->assertSame(-2208992400, $date->mktime(0, 0, 0, 13, 1, 1899, false));
        $this->assertSame(-2208988800, $date->mktime(0, 0, 0, 13, 1, 1899, true));
        $this->assertSame(-2208992400, $date->mktime(0, 0, 0,-11, 1, 1901, false));
        $this->assertSame(-2208988800, $date->mktime(0, 0, 0,-11, 1, 1901, true));
    }

    public function testIsLeapYear()
    {
        $date = new DateObjectTestHelper(Date::now());
        $this->assertTrue ($date->checkLeapYear(2000));
        $this->assertFalse($date->checkLeapYear(2002));
        $this->assertTrue ($date->checkLeapYear(2004));
        $this->assertFalse($date->checkLeapYear(1899));
        $this->assertTrue ($date->checkLeapYear(1500));
        $this->assertFalse($date->checkLeapYear(1455));
    }

    public function testWeekNumber()
    {
        $date = new DateObjectTestHelper(Date::now());
        $this->assertSame((int) date('W',mktime(0, 0, 0,  1,  1, 2000)), $date->weekNumber(2000,  1,  1));
        $this->assertSame((int) date('W',mktime(0, 0, 0, 10,  1, 2020)), $date->weekNumber(2020, 10,  1));
        $this->assertSame((int) date('W',mktime(0, 0, 0,  5, 15, 2005)), $date->weekNumber(2005,  5, 15));
        $this->assertSame((int) date('W',mktime(0, 0, 0, 11, 22, 1994)), $date->weekNumber(1994, 11, 22));
        $this->assertSame((int) date('W',mktime(0, 0, 0, 12, 31, 2000)), $date->weekNumber(2000, 12, 31));
        $this->assertSame(52, $date->weekNumber(2050, 12, 31));
        $this->assertSame(23, $date->weekNumber(2050,  6,  6));
        $this->assertSame(52, $date->weekNumber(2056,  1,  1));
        $this->assertSame(52, $date->weekNumber(2049, 12, 31));
        $this->assertSame(53, $date->weekNumber(2048, 12, 31));
        $this->assertSame( 1, $date->weekNumber(2047, 12, 31));
    }

    public function testDayOfWeek()
    {
        $date = new DateObjectTestHelper(Date::now());
        $this->assertSame((int) date('w',mktime(0, 0, 0, 1, 1, 2000)), $date->dayOfWeekHelper(2000, 1, 1));
        $this->assertSame((int) date('w',mktime(0, 0, 0, 1, 2, 2000)), $date->dayOfWeekHelper(2000, 1, 2));
        $this->assertSame((int) date('w',mktime(0, 0, 0, 1, 3, 2000)), $date->dayOfWeekHelper(2000, 1, 3));
        $this->assertSame((int) date('w',mktime(0, 0, 0, 1, 4, 2000)), $date->dayOfWeekHelper(2000, 1, 4));
        $this->assertSame((int) date('w',mktime(0, 0, 0, 1, 5, 2000)), $date->dayOfWeekHelper(2000, 1, 5));
        $this->assertSame((int) date('w',mktime(0, 0, 0, 1, 6, 2000)), $date->dayOfWeekHelper(2000, 1, 6));
        $this->assertSame((int) date('w',mktime(0, 0, 0, 1, 7, 2000)), $date->dayOfWeekHelper(2000, 1, 7));
        $this->assertSame((int) date('w',mktime(0, 0, 0, 1, 8, 2000)), $date->dayOfWeekHelper(2000, 1, 8));
        $this->assertSame(6, $date->dayOfWeekHelper(2050, 1, 1));
        $this->assertSame(0, $date->dayOfWeekHelper(2050, 1, 2));
        $this->assertSame(1, $date->dayOfWeekHelper(2050, 1, 3));
        $this->assertSame(2, $date->dayOfWeekHelper(2050, 1, 4));
        $this->assertSame(3, $date->dayOfWeekHelper(2050, 1, 5));
        $this->assertSame(4, $date->dayOfWeekHelper(2050, 1, 6));
        $this->assertSame(5, $date->dayOfWeekHelper(2050, 1, 7));
        $this->assertSame(6, $date->dayOfWeekHelper(2050, 1, 8));
        $this->assertSame(4, $date->dayOfWeekHelper(1500, 1, 1));
    }

    public function testCalcSunInternal()
    {
        $date = new DateObjectTestHelper(10000000);
        $this->assertSame( 9961681, $date->calcSun(array('latitude' =>  38.4, 'longitude' => -29), -0.0145439, true ));
        $this->assertSame(10010367, $date->calcSun(array('latitude' =>  38.4, 'longitude' => -29), -0.0145439, false));
        $this->assertSame( 9967006, $date->calcSun(array('latitude' => -38.4, 'longitude' => -29), -0.0145439, true ));
        $this->assertSame(10005042, $date->calcSun(array('latitude' => -38.4, 'longitude' => -29), -0.0145439, false));
        $this->assertSame( 9947773, $date->calcSun(array('latitude' =>  38.4, 'longitude' =>  29), -0.0145439, true ));
        $this->assertSame( 9996438, $date->calcSun(array('latitude' =>  38.4, 'longitude' =>  29), -0.0145439, false));
        $this->assertSame( 9953077, $date->calcSun(array('latitude' => -38.4, 'longitude' =>  29), -0.0145439, true ));
        $this->assertSame( 9991134, $date->calcSun(array('latitude' => -38.4, 'longitude' =>  29), -0.0145439, false));
        $this->assertSame( 9923795, $date->calcSun(array('latitude' =>  38.4, 'longitude' => 129), -0.0145439, true ));
        $this->assertSame( 9972422, $date->calcSun(array('latitude' =>  38.4, 'longitude' => 129), -0.0145439, false));
        $this->assertSame( 9929062, $date->calcSun(array('latitude' => -38.4, 'longitude' => 129), -0.0145439, true ));
        $this->assertSame( 9967155, $date->calcSun(array('latitude' => -38.4, 'longitude' => 129), -0.0145439, false));
        $this->assertSame( 9985660, $date->calcSun(array('latitude' =>  38.4, 'longitude' =>-129), -0.0145439, true ));
        $this->assertSame(10034383, $date->calcSun(array('latitude' =>  38.4, 'longitude' =>-129), -0.0145439, false));
        $this->assertSame( 9991022, $date->calcSun(array('latitude' => -38.4, 'longitude' =>-129), -0.0145439, true ));
        $this->assertSame(10029021, $date->calcSun(array('latitude' => -38.4, 'longitude' =>-129), -0.0145439, false));

        $date = new DateObjectTestHelper(-148309884);
        $this->assertSame(-148322663, $date->calcSun(array('latitude' =>  38.4, 'longitude' => -29), -0.0145439, true ));
        $this->assertSame(-148274758, $date->calcSun(array('latitude' =>  38.4, 'longitude' => -29), -0.0145439, false));
        $this->assertSame(-148318117, $date->calcSun(array('latitude' => -38.4, 'longitude' => -29), -0.0145439, true ));
        $this->assertSame(-148279304, $date->calcSun(array('latitude' => -38.4, 'longitude' => -29), -0.0145439, false));
        $this->assertSame(-148336570, $date->calcSun(array('latitude' =>  38.4, 'longitude' =>  29), -0.0145439, true ));
        $this->assertSame(-148288687, $date->calcSun(array('latitude' =>  38.4, 'longitude' =>  29), -0.0145439, false));
        $this->assertSame(-148332046, $date->calcSun(array('latitude' => -38.4, 'longitude' =>  29), -0.0145439, true ));
        $this->assertSame(-148293211, $date->calcSun(array('latitude' => -38.4, 'longitude' =>  29), -0.0145439, false));
        $this->assertSame(-148360548, $date->calcSun(array('latitude' =>  38.4, 'longitude' => 129), -0.0145439, true ));
        $this->assertSame(-148312703, $date->calcSun(array('latitude' =>  38.4, 'longitude' => 129), -0.0145439, false));
        $this->assertSame(-148356061, $date->calcSun(array('latitude' => -38.4, 'longitude' => 129), -0.0145439, true ));
        $this->assertSame(-148317189, $date->calcSun(array('latitude' => -38.4, 'longitude' => 129), -0.0145439, false));
        $this->assertSame(-148298686, $date->calcSun(array('latitude' =>  38.4, 'longitude' =>-129), -0.0145439, true ));
        $this->assertSame(-148250742, $date->calcSun(array('latitude' =>  38.4, 'longitude' =>-129), -0.0145439, false));
        $this->assertSame(-148294101, $date->calcSun(array('latitude' => -38.4, 'longitude' =>-129), -0.0145439, true ));
        $this->assertSame(-148255327, $date->calcSun(array('latitude' => -38.4, 'longitude' =>-129), -0.0145439, false));
    }

    public function testGetDate()
    {
        $date = new DateObjectTestHelper(0);
        $this->assertTrue(is_array($date->getDateParts()));
        $this->assertTrue(is_array($date->getDateParts(1000000)));

        $test = array(             'seconds' =>   40,      'minutes' => 46,
            'hours'   => 14,       'mday'    =>   12,      'wday'    =>  1,
            'mon'     =>  1,       'year'    => 1970,      'yday'    => 11,
            'weekday' => 'Monday', 'month'   => 'January', 0         => 1000000);
        $result = $date->getDateParts(1000000);

        $this->assertSame((int) $test['seconds'], (int) $result['seconds']);
        $this->assertSame((int) $test['minutes'], (int) $result['minutes']);
        $this->assertSame((int) $test['hours'],   (int) $result['hours']  );
        $this->assertSame((int) $test['mday'],    (int) $result['mday']   );
        $this->assertSame((int) $test['wday'],    (int) $result['wday']   );
        $this->assertSame((int) $test['mon'],     (int) $result['mon']    );
        $this->assertSame((int) $test['year'],    (int) $result['year']   );
        $this->assertSame((int) $test['yday'],    (int) $result['yday']   );
        $this->assertSame(      $test['weekday'],       $result['weekday']);
        $this->assertSame(      $test['month'],         $result['month']  );
        $this->assertSame(      $test[0],               $result[0]        );

        $test = array(                'seconds' =>   20,      'minutes' => 33,
            'hours'   => 11,          'mday'    =>    6,      'wday'    =>  3,
            'mon'     =>  3,          'year'    => 1748,      'yday'    => 65,
            'weekday' => 'Wednesday', 'month'   => 'February', 0        => -7000000000);
        $result = $date->getDateParts(-7000000000);

        $this->assertSame((int) $test['seconds'], (int) $result['seconds']);
        $this->assertSame((int) $test['minutes'], (int) $result['minutes']);
        $this->assertSame((int) $test['hours'],   (int) $result['hours']  );
        $this->assertSame((int) $test['mday'],    (int) $result['mday']   );
        $this->assertSame((int) $test['wday'],    (int) $result['wday']   );
        $this->assertSame((int) $test['mon'],     (int) $result['mon']    );
        $this->assertSame((int) $test['year'],    (int) $result['year']   );
        $this->assertSame((int) $test['yday'],    (int) $result['yday']   );
        $this->assertSame(      $test['weekday'],       $result['weekday']);
        $this->assertSame(      $test['month'],         $result['month']  );
        $this->assertSame(      $test[0],               $result[0]        );

        $test = array(               'seconds' => 0,        'minutes' => 40,
            'hours'   => 2,          'mday'    => 26,       'wday'    => 2,
            'mon'     => 8,          'year'    => 2188,     'yday'    => 238,
            'weekday' => 'Tuesday', 'month'   => 'July', 0      => 6900000000);
        $result = $date->getDateParts(6900000000);

        $this->assertSame((int) $test['seconds'], (int) $result['seconds']);
        $this->assertSame((int) $test['minutes'], (int) $result['minutes']);
        $this->assertSame((int) $test['hours'],   (int) $result['hours']  );
        $this->assertSame((int) $test['mday'],    (int) $result['mday']   );
        $this->assertSame((int) $test['wday'],    (int) $result['wday']   );
        $this->assertSame((int) $test['mon'],     (int) $result['mon']    );
        $this->assertSame((int) $test['year'],    (int) $result['year']   );
        $this->assertSame((int) $test['yday'],    (int) $result['yday']   );
        $this->assertSame(      $test['weekday'],       $result['weekday']);
        $this->assertSame(      $test['month'],         $result['month']  );
        $this->assertSame(      $test[0],               $result[0]        );

        $test = array(               'seconds' => 0,        'minutes' => 40,
            'hours'   => 2,          'mday'    => 26,       'wday'    => 3,
            'mon'     => 8,          'year'    => 2188,     'yday'    => 238,
            'weekday' => 'Wednesday', 'month'   => 'July', 0      => 6900000000);
        $result = $date->getDateParts(6900000000, true);

        $this->assertSame((int) $test['seconds'], (int) $result['seconds']);
        $this->assertSame((int) $test['minutes'], (int) $result['minutes']);
        $this->assertSame((int) $test['hours'],   (int) $result['hours']  );
        $this->assertSame((int) $test['mday'],    (int) $result['mday']   );
        $this->assertSame((int) $test['mon'],     (int) $result['mon']    );
        $this->assertSame((int) $test['year'],    (int) $result['year']   );
        $this->assertSame((int) $test['yday'],    (int) $result['yday']   );
    }

    public function testDate()
    {
        $date = new DateObjectTestHelper(0);
        $this->assertTrue($date->date('U') > 0);
        $this->assertSame(           '0', $date->date('U',0          ));
        $this->assertSame(           '0', $date->date('U',0,false    ));
        $this->assertSame(           '0', $date->date('U',0,true     ));
        $this->assertSame(  '6900000000', $date->date('U',6900000000 ));
        $this->assertSame( '-7000000000', $date->date('U',-7000000000));
        $this->assertSame(          '06', $date->date('d',-7000000000));
        $this->assertSame(         'Wed', $date->date('D',-7000000000));
        $this->assertSame(           '6', $date->date('j',-7000000000));
        $this->assertSame(   'Wednesday', $date->date('l',-7000000000));
        $this->assertSame(           '3', $date->date('N',-7000000000));
        $this->assertSame(          'th', $date->date('S',-7000000000));
        $this->assertSame(           '3', $date->date('w',-7000000000));
        $this->assertSame(          '65', $date->date('z',-7000000000));
        $this->assertSame(          '10', $date->date('W',-7000000000));
        $this->assertSame(       'March', $date->date('F',-7000000000));
        $this->assertSame(          '03', $date->date('m',-7000000000));
        $this->assertSame(         'Mar', $date->date('M',-7000000000));
        $this->assertSame(           '3', $date->date('n',-7000000000));
        $this->assertSame(          '31', $date->date('t',-7000000000));
        $this->assertSame(         'CET', $date->date('T',-7000000000));
        $this->assertSame(           '1', $date->date('L',-7000000000));
        $this->assertSame(        '1748', $date->date('o',-7000000000));
        $this->assertSame(        '1748', $date->date('Y',-7000000000));
        $this->assertSame(          '48', $date->date('y',-7000000000));
        $this->assertSame(          'pm', $date->date('a',-7000000000));
        $this->assertSame(          'PM', $date->date('A',-7000000000));
        $this->assertSame(         '523', $date->date('B',-7000000000));
        $this->assertSame(          '12', $date->date('g',-7000000000));
        $this->assertSame(          '12', $date->date('G',-7000000000));
        $this->assertSame(          '12', $date->date('h',-7000000000));
        $this->assertSame(          '12', $date->date('H',-7000000000));
        $this->assertSame(          '33', $date->date('i',-7000000000));
        $this->assertSame(          '20', $date->date('s',-7000000000));
        $this->assertSame('Europe/Paris', $date->date('e',-7000000000));
        $this->assertSame(           '0', $date->date('I',-7000000000));
        $this->assertSame(       '+0100', $date->date('O',-7000000000));
        $this->assertSame(      '+01:00', $date->date('P',-7000000000));
        $this->assertSame(         'CET', $date->date('T',-7000000000));
        $this->assertSame(        '3600', $date->date('Z',-7000000000));
        $this->assertSame('1748-03-06T12:33:20+01:00', $date->date('c',-7000000000));
        $this->assertSame('Wed, 06 Mar 1748 12:33:20 +0100', $date->date('r',-7000000000));
        $this->assertSame( '-7000000000', $date->date('U'    ,-7000000000 ));
        $this->assertSame(           'H', $date->date('\\H'  ,-7000000000 ));
        $this->assertSame(           '.', $date->date('.'    ,-7000000000 ));
        $this->assertSame(    '12:33:20', $date->date('H:i:s',-7000000000 ));
        $this->assertSame( '06-Mar-1748', $date->date('d-M-Y',-7000000000 ));
        $this->assertSame(  '6900000000', $date->date('U',6900000000, true));
        $this->assertSame(         '152', $date->date('B',6900000000, true));
        $this->assertSame(          '12', $date->date('g',6899993000, true));
        $this->assertSame(           '1', $date->date('g',6899997000, true));
        $this->assertSame(           '1', $date->date('g',6900039200, true));
        $this->assertSame(          '12', $date->date('h',6899993000, true));
        $this->assertSame(          '01', $date->date('h',6899997000, true));
        $this->assertSame(          '01', $date->date('h',6900040200, true));
        $this->assertSame(         'UTC', $date->date('e',-7000000000,true));
        $this->assertSame(           '0', $date->date('I',-7000000000,true));
        $this->assertSame(         'GMT', $date->date('T',-7000000000,true));
        $this->assertSame(           '6', $date->date('N',6899740800, true));
        $this->assertSame(          'st', $date->date('S',6900518000, true));
        $this->assertSame(          'nd', $date->date('S',6900604800, true));
        $this->assertSame(          'rd', $date->date('S',6900691200, true));
        $this->assertSame(           '7', $date->date('N',6900432000, true));
        $date->setTimezone('Europe/Vienna');
        date_default_timezone_set('Indian/Maldives');
        $reference = $date->date('U');
        $this->assertTrue(abs($reference - time()) < 2);
        $this->assertSame('69000000', $date->date('U',69000000));

        // ISO Year (o) depends on the week number so 1.1. can be last year is week is 52/53
        $this->assertSame('1739', $date->date('o',$date->mktime(0, 0, 0, 1, 1, 1740)));
        $this->assertSame('1740', $date->date('o',$date->mktime(0, 0, 0, 1, 1, 1741)));
        $this->assertSame('1742', $date->date('o',$date->mktime(0, 0, 0, 1, 1, 1742)));
        $this->assertSame('1743', $date->date('o',$date->mktime(0, 0, 0, 1, 1, 1743)));
        $this->assertSame('1744', $date->date('o',$date->mktime(0, 0, 0, 1, 1, 1744)));
        $this->assertSame('1744', $date->date('o',$date->mktime(0, 0, 0, 1, 1, 1745)));
        $this->assertSame('1745', $date->date('o',$date->mktime(0, 0, 0, 1, 1, 1746)));
        $this->assertSame('1746', $date->date('o',$date->mktime(0, 0, 0, 1, 1, 1747)));
        $this->assertSame('1748', $date->date('o',$date->mktime(0, 0, 0, 1, 1, 1748)));
        $this->assertSame('1749', $date->date('o',$date->mktime(0, 0, 0, 1, 1, 1749)));
        $this->assertSame('2049', $date->date('o',$date->mktime(0, 0, 0, 1, 1, 2050)));
        $this->assertSame('2050', $date->date('o',$date->mktime(0, 0, 0, 1, 1, 2051)));
        $this->assertSame('2052', $date->date('o',$date->mktime(0, 0, 0, 1, 1, 2052)));
        $this->assertSame('2053', $date->date('o',$date->mktime(0, 0, 0, 1, 1, 2053)));
        $this->assertSame('2054', $date->date('o',$date->mktime(0, 0, 0, 1, 1, 2054)));
    }

    function testMktimeDay0And32()
    {
        // the following functionality is used by isTomorrow() and isYesterday() in Zend_Date.
        $date = new DateObjectTestHelper(0);
        $this->assertSame('20060101', $date->date('Ymd', $date->mktime(0, 0, 0, 12, 32, 2005)));
        $this->assertSame('20050301', $date->date('Ymd', $date->mktime(0, 0, 0,  2, 29, 2005)));
        $this->assertSame('20051231', $date->date('Ymd', $date->mktime(0, 0, 0,  1,  0, 2006)));
        $this->assertSame('20050131', $date->date('Ymd', $date->mktime(0, 0, 0,  2,  0, 2005)));
    }

    /**
     * Test for setTimezone()
     */
    public function testSetTimezone()
    {
        $date = new DateObjectTestHelper(0);

        date_default_timezone_set('Europe/Vienna');
        $date->setTimezone('Indian/Maldives');
        $this->assertSame('Indian/Maldives', $date->getTimezone());
        try {
            $date->setTimezone('Unknown');
            // without new phpdate false timezones do not throw an exception !
            // known and expected behaviour
            if (function_exists('timezone_open')) {
                $this->fail("exception expected");
            }
        } catch (\Zend\Date\Exception $e) {
            $this->assertRegexp('/not a known timezone/i', $e->getMessage());
            //$this->assertSame('Unknown', $e->getOperand());
        }
        $this->assertSame('Indian/Maldives', $date->getTimezone());
        $date->setTimezone();
        $this->assertSame('Europe/Vienna', $date->getTimezone());
    }

    /**
     * Test for gmtOffset
     */
    public function testgetGmtOffset()
    {
        $date = new DateObjectTestHelper(0);

        date_default_timezone_set('Europe/Vienna');
        $date->setTimezone();

        $this->assertSame(-3600, $date->getGmtOffset());
        $date->setTimezone('GMT');
        $this->assertSame(    0, $date->getGmtOffset());
    }

    /**
     * Test for _getTime
     */
    public function test_getTime()
    {
        $date = new DateObjectTestHelper(Date::now());
        $time = $date->_getTime();
        $diff = abs(time() - $time);
        $this->assertTrue(($diff < 2), "Zend_Date_DateObject->_getTime() returned a significantly "
            . "different timestamp than expected: $diff seconds");
    }
}

class DateObjectTestHelper extends Date
{
    public function __construct($date = null, $part = null, $locale = null)
    {
        $this->setTimezone('Europe/Paris');
        parent::__construct($date, $part, $locale);
    }

    public function mktime($hour, $minute, $second, $month, $day, $year, $dst= -1, $gmt = false)
    {
        return parent::mktime($hour, $minute, $second, $month, $day, $year, $dst, $gmt);
    }

    public function getUnixTimestamp()
    {
        return parent::getUnixTimestamp();
    }

    public function setUnixTimestamp($timestamp = null)
    {
        return parent::setUnixTimestamp($timestamp);
    }

    public function weekNumber($year, $month, $day)
    {
        return parent::weekNumber($year, $month, $day);
    }

    public function dayOfWeekHelper($y, $m, $d)
    {
        return DateObject::dayOfWeek($y, $m, $d);
    }

    public function calcSun($location, $horizon, $rise = false)
    {
        return parent::calcSun($location, $horizon, $rise);
    }

    public function date($format, $timestamp = null, $gmt = false)
    {
        return parent::date($format, $timestamp, $gmt);
    }

    public function getDateParts($timestamp = null, $fast = null)
    {
        return parent::getDateParts($timestamp, $fast);
    }

    public function _getTime($sync = null)
    {
        return parent::_getTime($sync);
    }
}
