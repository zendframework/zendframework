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
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'Zend/Gdata/Calendar.php';
require_once 'Zend/Gdata/Calendar/EventEntry.php';
require_once 'Zend/Gdata/ClientLogin.php';
require_once 'Zend/Http/Client.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_CalendarOnlineTest extends PHPUnit_Framework_TestCase
{

    const GOOGLE_DEVELOPER_CALENDAR = 'developer-calendar@google.com';
    const ZEND_CONFERENCE_EVENT = 'bn2h4o4mc3a03ci4t48j3m56pg';

    public function setUp()
    {
        $user = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_EMAIL');
        $pass = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_PASSWORD');
        $service = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;
        $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
        $this->gdata = new Zend_Gdata_Calendar($client);
    }

    public function testCalendarListFeed() 
    {
        $calFeed = $this->gdata->getCalendarListFeed();
        $this->assertTrue(strpos($calFeed->title->text, 'Calendar List') 
                !== false);
        $calCount = 0;
        foreach ($calFeed as $calendar) {
            $calCount++;
        }
        $this->assertTrue($calCount > 0);
    } 

    /**
     * @see ZF-1701
     */
    /*
    public function testCalendarOnlineFeed()
    {
        $eventFeed = $this->gdata->getCalendarEventFeed();
        foreach ($eventFeed as $event) {
            $title = $event->title;
            $times = $event->when;
            $location = $event->where;
            $recurrence = $event->recurrence;
        }
    }
	*/

    function getEvent($eventId)
    {
        $query = $this->gdata->newEventQuery();
        $query->setUser('default');
        $query->setVisibility('private');
        $query->setProjection('full');
        $query->setEvent($eventId);

        $eventEntry = $this->gdata->getCalendarEventEntry($query);
        $this->assertTrue(
                $eventEntry instanceof Zend_Gdata_Calendar_EventEntry);
        return $eventEntry;
    }

    public function createEvent(
            $title = 'Tennis with Beth',
            $desc='Meet for a quick lesson', $where = 'On the courts',
            $startDate = '2008-01-20', $startTime = '10:00',
            $endDate = '2008-01-20', $endTime = '11:00', $tzOffset = '-08')
    {
        $newEntry = $this->gdata->newEventEntry();
        $newEntry->title = $this->gdata->newTitle(trim($title));
        $newEntry->where  = array($this->gdata->newWhere($where));

        $newEntry->content = $this->gdata->newContent($desc);
        $newEntry->content->type = 'text';

        $when = $this->gdata->newWhen();
        $when->startTime = "{$startDate}T{$startTime}:00.000{$tzOffset}:00";
        $when->endTime = "{$endDate}T{$endTime}:00.000{$tzOffset}:00";
        $reminder = $this->gdata->newReminder();
        $reminder->minutes = '30';
        $reminder->method = 'email';
        $when->reminders = array($reminder);
        $newEntry->when = array($when);

        $createdEntry = $this->gdata->insertEvent($newEntry);

        $this->assertEquals($title, $createdEntry->title->text);
        $this->assertEquals($desc, $createdEntry->content->text);
        $this->assertEquals(strtotime($when->startTime), 
                strtotime($createdEntry->when[0]->startTime));
        $this->assertEquals(strtotime($when->endTime), 
                strtotime($createdEntry->when[0]->endTime));
        $this->assertEquals($reminder->method, 
                $createdEntry->when[0]->reminders[0]->method);
        $this->assertEquals($reminder->minutes, 
                $createdEntry->when[0]->reminders[0]->minutes);
        $this->assertEquals($where, $createdEntry->where[0]->valueString);
        
        return $createdEntry;
    }

    function updateEvent ($eventId, $newTitle)
    {
        $eventOld = $this->getEvent($eventId);
        $eventOld->title = $this->gdata->newTitle($newTitle);
        $eventOld->save();
        $eventNew = $this->getEvent($eventId);
        $this->assertEquals($newTitle, $eventNew->title->text);
        return $eventNew;
    }

    public function testCreateEvent()
    {
        $createdEntry = $this->createEvent();
    }

    public function testCreateAndUpdateEvent()
    {
        $newTitle = 'my new title';
        $createdEntry = $this->createEvent();
        preg_match('#.*/([A-Za-z0-9]+)$#', $createdEntry->id->text, $matches);
        $id = $matches[1];
        $updatedEvent = $this->updateEvent($id, $newTitle); 
        $this->assertEquals($newTitle, $updatedEvent->title->text);
    }

    public function testCreateAndDeleteEvent()
    {
        /* deletion can be performed in several different ways-- test all */
        $createdEntry = $this->createEvent();
        $createdEntry->delete();

        $createdEntry2 = $this->createEvent();
        $this->gdata->delete($createdEntry2); 

        $createdEntry3 = $this->createEvent();
        $this->gdata->delete($createdEntry3->getEditLink()->href); 
    }
}
