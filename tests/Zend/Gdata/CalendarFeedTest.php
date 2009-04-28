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

require_once 'Zend/Gdata/Calendar.php';
require_once 'Zend/Gdata/Calendar/EventFeed.php';
require_once 'Zend/Http/Client.php';
require_once 'Zend/Http/Client/Adapter/Test.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_CalendarFeedTest extends PHPUnit_Framework_TestCase
{
    protected $listFeed = null;

    /** 
      * Called before each test to setup any fixtures.
      */
    public function setUp()
    {
        $listFeedText = file_get_contents(
                'Zend/Gdata/Calendar/_files/ListFeedSample1.xml',
                true);
        $this->listFeed = new Zend_Gdata_Calendar_ListFeed($listFeedText);
    }

    /**
      * Verify that a given property is set to a specific value 
      * and that the getter and magic variable return the same value.
      *
      * @param object $obj The object to be interrogated.
      * @param string $name The name of the property to be verified.
      * @param object $value The expected value of the property.
      */
    protected function verifyProperty($obj, $name, $value)
    {
        $propName = $name;
        $propGetter = "get" . ucfirst($name);

        $this->assertEquals($obj->$propGetter(), $obj->$propName);
        $this->assertEquals($value, $obj->$propGetter());
    }

    /**
      * Verify that a given property is set to a specific value 
      * and that the getter and magic variable return the same value.
      *
      * @param object $obj The object to be interrogated.
      * @param string $name The name of the property to be verified.
      * @param string $secondName 2nd level accessor function name      
      * @param object $value The expected value of the property.
      */
    protected function verifyProperty2($obj, $name, $secondName, $value)
    {
        $propName = $name;
        $propGetter = "get" . ucfirst($name);
        $secondGetter = "get" . ucfirst($secondName);

        $this->assertEquals($obj->$propGetter(), $obj->$propName);
        $this->assertEquals($value, $obj->$propGetter()->$secondGetter());
    }

    /** 
      * Convert sample feed to XML then back to objects. Ensure that 
      * all objects are instances of EventEntry and object count matches.
      */
    public function testEventFeedToAndFromString()
    {
        $entryCount = 0;
        foreach ($this->listFeed as $entry) {
            $entryCount++;
            $this->assertTrue($entry instanceof Zend_Gdata_Calendar_ListEntry);
        }
        $this->assertTrue($entryCount > 0);

        /* Grab XML from $this->listFeed and convert back to objects */ 
        $newListFeed = new Zend_Gdata_Calendar_ListFeed( 
                $this->listFeed->saveXML());
        $newEntryCount = 0;
        foreach ($newListFeed as $entry) {
            $newEntryCount++;
            $this->assertTrue($entry instanceof Zend_Gdata_Calendar_ListEntry);
        }
        $this->assertEquals($entryCount, $newEntryCount);
    }

    /** 
      * Ensure that there number of lsit feeds equals the number 
      * of calendars defined in the sample file.
      */
    public function testEntryCount()
    {
        //TODO feeds implementing ArrayAccess would be helpful here
        $entryCount = 0;
        foreach ($this->listFeed as $entry) {
            $entryCount++;
        }
        $this->assertEquals(9, $entryCount);
    }

    /** 
      * Check for the existence of an <atom:author> and verify that they 
      * contain the expected values.
      */
    public function testAuthor()
    {
        $feed = $this->listFeed;

        // Assert that the feed's author is correct
        $feedAuthor = $feed->getAuthor();
        $this->assertEquals($feedAuthor, $feed->author);
        $this->assertEquals(1, count($feedAuthor));
        $this->assertTrue($feedAuthor[0] instanceof Zend_Gdata_App_Extension_Author);
        $this->verifyProperty2($feedAuthor[0], "name", "text", "GData Ops Demo");
        $this->verifyProperty2($feedAuthor[0], "email", "text", "gdata.ops.demo@gmail.com");
        $this->assertTrue($feedAuthor[0]->getUri() instanceof Zend_Gdata_App_Extension_Uri);
        $this->verifyProperty2($feedAuthor[0], "uri", "text", "http://test.address.invalid/");

        // Assert that each entry has valid author data
        foreach ($feed as $entry) {
            $entryAuthor = $entry->getAuthor();
            $this->assertEquals(1, count($entryAuthor));
            $this->verifyProperty2($entryAuthor[0], "name", "text", "GData Ops Demo");
            $this->verifyProperty2($entryAuthor[0], "email", "text", "gdata.ops.demo@gmail.com");
            $this->verifyProperty($entryAuthor[0], "uri", null);            
        }
    }

    /**
      * Check for the existence of an <atom:id> and verify that it contains
      * the expected value.
      */
    public function testId()
    {
        $feed = $this->listFeed;

        // Assert that the feed's ID is correct
        $this->assertTrue($feed->getId() instanceof Zend_Gdata_App_Extension_Id);
        $this->verifyProperty2($feed, "id", "text", 
                "http://www.google.com/calendar/feeds/default");

        // Assert that all entry's have an Atom ID object
        foreach ($feed as $entry) {
            $this->assertTrue($entry->getId() instanceof Zend_Gdata_App_Extension_Id);
        }

        // Assert one of the entry's IDs
        $entry = $feed[1];
        $this->verifyProperty2($entry, "id", "text", 
                "http://www.google.com/calendar/feeds/default/ri3u1buho56d1k2papoec4c16s%40group.calendar.google.com");
    }

    /**
      * Check for the existence of an <atom:published> and verify that it contains 
      * the expected value.
      */
    public function testPublished()
    {
        $feed = $this->listFeed;

        // Assert that all entry's have an Atom Published object
        foreach ($feed as $entry) {
            $this->assertTrue($entry->getPublished() instanceof Zend_Gdata_App_Extension_Published);
        }

        // Assert one of the entry's Published dates
        $entry = $feed[1];
        $this->verifyProperty2($entry, "published", "text", "2007-05-30T00:23:27.005Z");
    }

    /**
      * Check for the existence of an <atom:published> and verify that it contains 
      * the expected value.
      */
    public function testUpdated()
    {
        $feed = $this->listFeed;

        // Assert that the feed's updated date is correct
        $this->assertTrue($feed->getUpdated() instanceof Zend_Gdata_App_Extension_Updated);
        $this->verifyProperty2($feed, "updated", "text", 
                "2007-05-30T00:23:26.998Z");

        // Assert that all entry's have an Atom Published object
        foreach ($feed as $entry) {
            $this->assertTrue($entry->getUpdated() instanceof Zend_Gdata_App_Extension_Updated);
        }

        // Assert one of the entry's Published dates
        $entry = $feed[1];
        $this->verifyProperty2($entry, "updated", "text", "2007-05-30T00:20:38.000Z");
    }

    /**
      * Check for the existence of an <atom:title> and verify that it contains
      * the expected value.
      */
    public function testTitle()
    {
        $feed = $this->listFeed;

        // Assert that the feed's title is correct
        $this->assertTrue($feed->getTitle() instanceof Zend_Gdata_App_Extension_Title);
        $this->verifyProperty2($feed, "title", "text", 
                "GData Ops Demo's Calendar List");

        // Assert that all entry's have an Atom ID object
        foreach ($feed as $entry) {
            $this->assertTrue($entry->getTitle() instanceof Zend_Gdata_App_Extension_Title);
        }

        // Assert one of the entry's Titles
        $entry = $feed[1];
        $this->verifyProperty2($entry, "title", "text", "My Other Awesome Calendar");
    }

    /**
      * Check for the existence of an <gCal:color> and verify that it contains
      * the expected value.
      */
    public function testColor()
    {
        $feed = $this->listFeed;

        // Assert that all entry's have an color object
        foreach ($feed as $entry) {
            $this->assertTrue($entry->getColor() instanceof Zend_Gdata_Calendar_Extension_Color);
        }

        // Assert one of the entry's Titles
        $entry = $feed[1];
        $this->verifyProperty2($entry, "color", "value", "#A32929");
    }

    /**
      * Check for the existence of an <gCal:accessLevel> and verify that it contains
      * the expected value.
      */
    public function testAccessLevel()
    {
        $feed = $this->listFeed;

        // Assert that all entry's have an accessLevel object
        foreach ($feed as $entry) {
            $this->assertTrue($entry->getAccessLevel() instanceof Zend_Gdata_Calendar_Extension_AccessLevel);
        }

        // Assert one of the entry's Titles
        $entry = $feed[1];
        $this->verifyProperty2($entry, "accessLevel", "value", "owner");
    }

    /**
      * Check for the existence of an <gCal:timezone> and verify that it contains
      * the expected value.
      */
    public function testTimezone()
    {
        $feed = $this->listFeed;

        // Assert that all entry's have an accessLevel object
        foreach ($feed as $entry) {
            $this->assertTrue($entry->getTimezone() instanceof Zend_Gdata_Calendar_Extension_Timezone);
        }

        // Assert one of the entry's Titles
        $entry = $feed[1];
        $this->verifyProperty2($entry, "timezone", "value", "America/Chicago");
    }

    /**
      * Check for the existence of an <gCal:hidden> and verify that it contains
      * the expected value.
      */
    public function testHidden()
    {
        $feed = $this->listFeed;

        // Assert that all entry's have an accessLevel object
        foreach ($feed as $entry) {
            $this->assertTrue($entry->getHidden() instanceof Zend_Gdata_Calendar_Extension_Hidden);
        }

        // Assert one of the entry's Titles
        $entry = $feed[1];
        $this->verifyProperty2($entry, "hidden", "value", false);
    }

    /**
      * Check for the existence of an <gCal:selected> and verify that it contains
      * the expected value.
      */
    public function testSelected()
    {
        $feed = $this->listFeed;

        // Assert that all entry's have a selected object
        foreach ($feed as $entry) {
            $this->assertTrue($entry->getSelected() instanceof Zend_Gdata_Calendar_Extension_Selected);
        }

        // Assert one of the entry's Titles
        $entry = $feed[1];
        $this->verifyProperty2($entry, "selected", "value", true);
    }

    /**
      * Check for the existence of an <openSearch:startIndex> and verify that it contains
      * the expected value.
      */
    public function testStartIndex()
    {
        $feed = $this->listFeed;

        // Assert that the feed's startIndex is correct
        $this->assertTrue($feed->getStartIndex() instanceof Zend_Gdata_Extension_OpenSearchStartIndex);
        $this->verifyProperty2($feed, "startIndex", "text", "1");
    }

    /**
      * Check for the existence of an <gd:where> and verify that it contains
      * the expected value.
      */
    public function testWhere()
    {
        $feed = $this->listFeed;

        // Assert one of the entry's where values
        $entry = $feed[1];
        $this->assertEquals($entry->getWhere(), $entry->where);  
        $this->assertTrue($entry->where[0] instanceof Zend_Gdata_Extension_Where);
        $this->assertEquals("Palo Alto, California", $entry->where[0]->getValueString());
    }

    /**
      * Check for the existence of an <atom:summary> and verify that it contains
      * the expected value.
      */
    public function testSummary()
    {
        $feed = $this->listFeed;

        // Assert one of the entry's summaries
        $entry = $feed[1];
        $this->assertTrue($entry->getSummary() instanceof Zend_Gdata_App_Extension_Summary);
        $this->verifyProperty2($entry, "summary", "text", "This is my other calendar.");
    }

    /**
      * Check for the existence of an <atom:generator> and verify that it contains
      * the expected value.
      */
    public function testGenerator()
    {
        $feed = $this->listFeed;

        // Assert that the feed's generator is correct
        $this->assertTrue($feed->getGenerator() instanceof Zend_Gdata_App_Extension_Generator);
        $this->verifyProperty2($feed, "generator", "version", "1.0");
        $this->verifyProperty2($feed, "generator", "uri", "http://www.google.com/calendar");
    }

}
