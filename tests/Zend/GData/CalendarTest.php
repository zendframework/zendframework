<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace ZendTest\GData;

use Zend\GData\Calendar;

/**
 * @category   Zend
 * @package    Zend_GData_Calendar
 * @subpackage UnitTests
 * @group      Zend_GData
 * @group      Zend_GData_Calendar
 */
class CalendarTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->eventFeedText = file_get_contents(
                'Zend/GData/Calendar/_files/TestDataEventFeedSample1.xml',
                true);
        $this->eventFeed = new Calendar\EventFeed();
    }

    public function testEmptyEventFeedShouldHaveNoExtensionElements()
    {
        $this->assertTrue(is_array($this->eventFeed->extensionElements));
        $this->assertTrue(count($this->eventFeed->extensionElements) == 0);
    }

    public function testEmptyEventFeedShouldHaveNoExtensionAttributes()
    {
        $this->assertTrue(is_array($this->eventFeed->extensionAttributes));
        $this->assertTrue(count($this->eventFeed->extensionAttributes) == 0);
    }

    public function testSampleEventFeedShouldHaveNoExtensionElements()
    {
        $this->eventFeed->transferFromXML($this->eventFeedText);
        $this->assertTrue(is_array($this->eventFeed->extensionElements));
        $this->assertTrue(count($this->eventFeed->extensionElements) == 0);
    }

    public function testSampleEventFeedShouldHaveNoExtensionAttributes()
    {
        $this->eventFeed->transferFromXML($this->eventFeedText);
        $this->assertTrue(is_array($this->eventFeed->extensionAttributes));
        $this->assertTrue(count($this->eventFeed->extensionAttributes) == 0);
    }

    public function testEventFeedToAndFromString()
    {
        $this->eventFeed->transferFromXML($this->eventFeedText);
        $entryCount = 0;
        foreach ($this->eventFeed as $entry) {
            $entryCount++;
            $this->assertTrue($entry instanceof Calendar\EventEntry);
        }
        $this->assertTrue($entryCount > 0);

        /* Grab XML from $this->eventFeed and convert back to objects */
        $newEventFeed = new Calendar\EventFeed(
                $this->eventFeed->saveXML());
        $newEntryCount = 0;
        foreach ($newEventFeed as $entry) {
            $newEntryCount++;
            $this->assertTrue($entry instanceof Calendar\EventEntry);
        }
        $this->assertEquals($entryCount, $newEntryCount);
    }

    public function testEntryCount()
    {
        $this->eventFeed->transferFromXML($this->eventFeedText);
        //TODO feeds implementing ArrayAccess would be helpful here
        $entryCount = 0;
        foreach ($this->eventFeed as $entry) {
            $entryCount++;
        }
        $this->assertEquals($entryCount, 10);
        $this->assertEquals($entryCount, $this->eventFeed->totalResults->text);
    }

}
