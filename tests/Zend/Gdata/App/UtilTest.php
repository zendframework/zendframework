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
 * @package    Zend_Gdata_App
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

require_once 'Zend/Http/Client.php';
require_once 'Zend/Gdata/App/Util.php';
require_once 'Zend/Gdata/App/Exception.php';

/**
 * @category   Zend
 * @package    Zend_Gdata_App
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Gdata
 * @group      Zend_Gdata_App
 */
class Zend_Gdata_App_UtilTest extends PHPUnit_Framework_TestCase
{

    public function testFormatTimestampFromString()
    {
        // assert that a correctly formatted timestamp is not modified
        $date = Zend_Gdata_App_Util::formatTimestamp('2006-12-01');
        $this->assertEquals('2006-12-01', $date);
    }

    public function testFormatTimestampFromStringWithTimezone()
    {
        // assert that a correctly formatted timestamp is not modified
        $date = Zend_Gdata_App_Util::formatTimestamp('2007-01-10T13:31:12-04:00');
        $this->assertEquals('2007-01-10T13:31:12-04:00', $date);
    }

    public function testFormatTimestampWithMilliseconds()
    {
        // assert that a correctly formatted timestamp is not modified
        $date = Zend_Gdata_App_Util::formatTimestamp('1956-12-14T43:09:54.52376Z');
        $this->assertEquals('1956-12-14T43:09:54.52376Z', $date);
    }

    public function testFormatTimestampUsingZuluAsOffset()
    {
        // assert that a correctly formatted timestamp is not modified
        $date = Zend_Gdata_App_Util::formatTimestamp('2024-03-19T01:38:12Z');
        $this->assertEquals('2024-03-19T01:38:12Z', $date);
    }

    public function testFormatTimestampUsingLowercaseTAndZ()
    {
        // assert that a correctly formatted timestamp is not modified
        $date = Zend_Gdata_App_Util::formatTimestamp('1945-07-19t12:19:08z');
        $this->assertEquals('1945-07-19t12:19:08z', $date);
    }

    public function testFormatTimestampFromStringWithNonCompliantDate()
    {
        // assert that a non-compliant date is converted to RFC 3339
        $date = Zend_Gdata_App_Util::formatTimestamp('2007/07/13');
        $this->assertEquals('2007-07-13T00:00:00', $date);
    }

    public function testFormatTimestampFromInteger()
    {
        $ts = 1164960000; // Fri Dec  1 00:00:00 PST 2006
        $date = Zend_Gdata_App_Util::formatTimestamp($ts);
        $this->assertEquals('2006-12-01T08:00:00+00:00', $date);
    }

    public function testExceptionFormatTimestampNonsense()
    {
        $util = new Zend_Gdata_App_Util();
        try {
            $ts = Zend_Gdata_App_Util::formatTimestamp('nonsense string');
        } catch (Zend_Gdata_App_Exception $e) {
            $this->assertEquals('Invalid timestamp: nonsense string.', $e->getMessage());
            return;
        }
        // Excetion not thrown, this is bad.
        $this->fail("Exception not thrown.");
    }

    public function testExceptionFormatTimestampSemiInvalid()
    {
        $util = new Zend_Gdata_App_Util();
        try {
            $ts = Zend_Gdata_App_Util::formatTimestamp('2007-06-05adslfkja');
        } catch (Zend_Gdata_App_Exception $e) {
            $this->assertEquals('Invalid timestamp: 2007-06-05adslfkja.', $e->getMessage());
            return;
        }
        // Excetion not thrown, this is bad.
        $this->fail("Exception not thrown.");
    }

    public function testExceptionFormatTimestampInvalidTime()
    {
        $util = new Zend_Gdata_App_Util();
        try {
            $ts = Zend_Gdata_App_Util::formatTimestamp('2007-06-05Tadslfkja');
        } catch (Zend_Gdata_App_Exception $e) {
            $this->assertEquals('Invalid timestamp: 2007-06-05Tadslfkja.', $e->getMessage());
            return;
        }
        // Excetion not thrown, this is bad.
        $this->fail("Exception not thrown.");
    }

    public function testExceptionFormatTimestampInvalidOffset()
    {
        $util = new Zend_Gdata_App_Util();
        try {
            $ts = Zend_Gdata_App_Util::formatTimestamp('2007-06-05T02:51:12+egg');
        } catch (Zend_Gdata_App_Exception $e) {
            $this->assertEquals('Invalid timestamp: 2007-06-05T02:51:12+egg.', $e->getMessage());
            return;
        }
        // Excetion not thrown, this is bad.
        $this->fail("Exception not thrown.");
    }

    public function testExceptionFormatTimestampInvalidOffsetHours()
    {
        $util = new Zend_Gdata_App_Util();
        try {
            $ts = Zend_Gdata_App_Util::formatTimestamp('2007-06-05T02:51:12-ab:00');
        } catch (Zend_Gdata_App_Exception $e) {
            $this->assertEquals('Invalid timestamp: 2007-06-05T02:51:12-ab:00.', $e->getMessage());
            return;
        }
        // Excetion not thrown, this is bad.
        $this->fail("Exception not thrown.");
    }

    public function testFindGreatestBoundedValueReturnsMax() {
        $data = array(-1 => null,
                      0 => null,
                      1 => null,
                      2 => null,
                      3 => null,
                      5 => null,
                      -2 => null);
        $result = Zend_Gdata_App_Util::findGreatestBoundedValue(99, $data);
        $this->assertEquals(5, $result);
    }

    public function testFindGreatestBoundedValueReturnsMaxWhenBounded() {
        $data = array(-1 => null,
                      0 => null,
                      1 => null,
                      2 => null,
                      3 => null,
                      5 => null,
                      -2 => null);
        $result = Zend_Gdata_App_Util::findGreatestBoundedValue(4, $data);
        $this->assertEquals(3, $result);
    }

    public function testFindGreatestBoundedValueReturnsMaxWhenUnbounded() {
        $data = array(-1 => null,
                      0 => null,
                      1 => null,
                      2 => null,
                      3 => null,
                      5 => null,
                      -2 => null);
        $result = Zend_Gdata_App_Util::findGreatestBoundedValue(null, $data);
        $this->assertEquals(5, $result);
    }

    public function testFindGreatestBoundedValueReturnsZeroWhenZeroBounded() {
        $data = array(-1 => null,
                      0 => null,
                      1 => null,
                      2 => null,
                      3 => null,
                      5 => null,
                      -2 => null);
        $result = Zend_Gdata_App_Util::findGreatestBoundedValue(0, $data);
        $this->assertEquals(0, $result);
    }

    public function testFindGreatestBoundedValueFailsWhenNegativelyBounded() {
        $data = array(-1 => null,
                      0 => null,
                      1 => null,
                      2 => null,
                      3 => null,
                      5 => null,
                      -2 => null);
        try {
            $result = Zend_Gdata_App_Util::findGreatestBoundedValue(-1, $data);
            $failed = true;
        } catch (Zend_Gdata_App_Exception $e) {
            $failed = false;
        }
        $this->assertFalse($failed, 'Exception not raised.');
    }

}
