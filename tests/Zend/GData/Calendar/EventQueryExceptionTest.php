<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace ZendTest\GData\Calendar;

/**
 * @category   Zend
 * @package    Zend_GData_Calendar
 * @subpackage UnitTests
 * @group      Zend_GData
 * @group      Zend_GData_Calendar
 */
class EventQueryExceptionTest extends \PHPUnit_Framework_TestCase
{

    const GOOGLE_DEVELOPER_CALENDAR = 'developer-calendar@google.com';

    public function setUp()
    {
        $this->query = new \Zend\GData\Calendar\EventQuery();
    }

    public function testSingleEventsThrowsExceptionOnSetInvalidValue()
    {
        $this->query->resetParameters();
        $singleEvents = 'puppy';
        $this->setExpectedException('Zend\GData\App\Exception');
        $this->query->setUser(self::GOOGLE_DEVELOPER_CALENDAR);
        $this->query->setSingleEvents($singleEvents);
    }

    public function testFutureEventsThrowsExceptionOnSetInvalidValue()
    {
        $this->query->resetParameters();
        $futureEvents = 'puppy';
        $this->setExpectedException('Zend\GData\App\Exception');
        $this->query->setUser(self::GOOGLE_DEVELOPER_CALENDAR);
        $this->query->setFutureEvents($futureEvents);
    }

}
