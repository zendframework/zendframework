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

require_once 'Zend/Gdata/Photos.php';
require_once 'Zend/Gdata/Photos/UserFeed.php';
require_once 'Zend/Http/Client.php';
require_once 'Zend/Http/Client/Adapter/Test.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_Photos_PhotosUserFeedTest extends PHPUnit_Framework_TestCase
{
    
    protected $userFeed = null;

    /** 
      * Called before each test to setup any fixtures.
      */
    public function setUp()
    {
        $userFeedText = file_get_contents(
                '_files/TestUserFeed.xml',
                true);
        $this->userFeed = new Zend_Gdata_Photos_UserFeed($userFeedText);
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
      * Verify that a given property is set to a specific value,
      * that it keeps that value when set using the setter, 
      * and that the getter and magic variable return the same value.
      *
      * @param object $obj The object to be interrogated.
      * @param string $name The name of the property to be verified. 
      * @param string $secondName 2nd level accessor function name   
      * @param object $value The expected value of the property.
      */
    protected function verifyProperty3($obj, $name, $secondName, $value)
    {
        $propName = $name;
        $propGetter = "get" . ucfirst($name);
        $propSetter = "set" . ucfirst($name);
        $secondGetter = "get" . ucfirst($secondName);
        $secondSetter = "set" . ucfirst($secondName);

        $this->assertEquals($obj->$propGetter(), $obj->$propName);
        $obj->$propSetter($obj->$propName);
        $this->assertEquals($value, $obj->$propGetter()->$secondGetter());
    }

    /** 
      * Convert sample feed to XML then back to objects. Ensure that 
      * all objects are instances of appropriate entry type and object count matches.
      */
    public function testUserFeedToAndFromString()
    {
        $entryCount = 0;
        foreach ($this->userFeed as $entry) {
            $entryCount++;
            $this->assertTrue($entry instanceof Zend_Gdata_Photos_AlbumEntry ||
                              $entry instanceof Zend_Gdata_Photos_PhotoEntry ||
                              $entry instanceof Zend_Gdata_Photos_TagEntry);
        }
        $this->assertTrue($entryCount > 0);
        
        /* Grab XML from $this->userFeed and convert back to objects */ 
        $newListFeed = new Zend_Gdata_Photos_UserFeed( 
                $this->userFeed->saveXML());
        $newEntryCount = 0;
        foreach ($newListFeed as $entry) {
            $newEntryCount++;
            $this->assertTrue($entry instanceof Zend_Gdata_Photos_AlbumEntry ||
                              $entry instanceof Zend_Gdata_Photos_PhotoEntry ||
                              $entry instanceof Zend_Gdata_Photos_TagEntry);
        }
        $this->assertEquals($entryCount, $newEntryCount);
    }
    
    /** 
      * Ensure that the number of entries equals the number 
      * of entries defined in the sample file.
      */
    public function testEntryCount()
    {
        $entryCount = 0;
        foreach ($this->userFeed as $entry) {
            $entryCount++;
        }
        $this->assertEquals(3, $entryCount);
    }

    /** 
      * Check for the existence of an <atom:author> and verify that they 
      * contain the expected values.
      */
    public function testAuthor()
    {
        $feed = $this->userFeed;

        // Assert that the feed's author is correct
        $feedAuthor = $feed->getAuthor();
        $this->assertEquals($feedAuthor, $feed->author);
        $this->assertEquals(1, count($feedAuthor));
        $this->assertTrue($feedAuthor[0] instanceof Zend_Gdata_App_Extension_Author);
        $this->verifyProperty2($feedAuthor[0], "name", "text", "sample");
        $this->assertTrue($feedAuthor[0]->getUri() instanceof Zend_Gdata_App_Extension_Uri);
        $this->verifyProperty2($feedAuthor[0], "uri", "text", "http://picasaweb.google.com/sample.user");

        // Assert that each entry has valid author data
        foreach ($feed as $entry) {
            if ($entry instanceof Zend_Gdata_Photos_AlbumEntry) {
                $entryAuthor = $entry->getAuthor();
                $this->assertEquals(1, count($entryAuthor));
                $this->verifyProperty2($entryAuthor[0], "name", "text", "sample");
                $this->verifyProperty2($entryAuthor[0], "uri", "text", "http://picasaweb.google.com/sample.user");
            }       
        }
    }

    /**
      * Check for the existence of an <atom:id> and verify that it contains
      * the expected value.
      */
    public function testId()
    {
        $feed = $this->userFeed;

        // Assert that the feed's ID is correct
        $this->assertTrue($feed->getId() instanceof Zend_Gdata_App_Extension_Id);
        $this->verifyProperty2($feed, "id", "text", 
                "http://picasaweb.google.com/data/feed/api/user/sample.user");

        // Assert that all entries have an Atom ID object
        foreach ($feed as $entry) {
            $this->assertTrue($entry->getId() instanceof Zend_Gdata_App_Extension_Id);
        }

        // Assert one of the entry's IDs
        $entry = $feed[0];
        $this->verifyProperty2($entry, "id", "text", 
                "http://picasaweb.google.com/data/entry/api/user/sample.user/albumid/100");
    }

    /**
      * Check for the existence of an <atom:published> and verify that it contains 
      * the expected value.
      */
    public function testPublished()
    {
        $feed = $this->userFeed;

        // Assert that all entries have an Atom Published object
        foreach ($feed as $entry) {
            $this->assertTrue($entry->getPublished() instanceof Zend_Gdata_App_Extension_Published);
        }

        // Assert one of the entry's Published dates
        $entry = $feed[0];
        $this->verifyProperty2($entry, "published", "text", "2007-09-05T07:00:00.000Z");
    }

    /**
      * Check for the existence of an <atom:updated> and verify that it contains 
      * the expected value.
      */
    public function testUpdated()
    {
        $feed = $this->userFeed;

        // Assert that the feed's updated date is correct
        $this->assertTrue($feed->getUpdated() instanceof Zend_Gdata_App_Extension_Updated);
        $this->verifyProperty2($feed, "updated", "text", 
                "2007-09-20T21:09:39.111Z");

        // Assert that all entries have an Atom Updated object
        foreach ($feed as $entry) {
            $this->assertTrue($entry->getUpdated() instanceof Zend_Gdata_App_Extension_Updated);
        }

        // Assert one of the entry's Updated dates
        $entry = $feed[0];
        $this->verifyProperty2($entry, "updated", "text", "2007-09-05T20:49:24.000Z");
    }

    /**
      * Check for the existence of an <atom:title> and verify that it contains
      * the expected value.
      */
    public function testTitle()
    {
        $feed = $this->userFeed;

        // Assert that the feed's title is correct
        $this->assertTrue($feed->getTitle() instanceof Zend_Gdata_App_Extension_Title);
        $this->verifyProperty2($feed, "title", "text", 
                "sample.user");

        // Assert that all entries have an Atom ID object
        foreach ($feed as $entry) {
            $this->assertTrue($entry->getTitle() instanceof Zend_Gdata_App_Extension_Title);
        }

        // Assert one of the entry's Titles
        $entry = $feed[0];
        $this->verifyProperty2($entry, "title", "text", "Test");
    }

    /**
      * Check for the existence of an <atom:subtitle> and verify that it contains
      * the expected value.
      */
    public function testSubtitle()
    {
        $feed = $this->userFeed;

        // Assert that the feed's title is correct
        $this->assertTrue($feed->getSubtitle() instanceof Zend_Gdata_App_Extension_Subtitle);
        $this->verifyProperty2($feed, "subtitle", "text", 
                "");
    }

    /**
      * Check for the existence of an <atom:generator> and verify that it contains
      * the expected value.
      */
    public function testGenerator()
    {
        $feed = $this->userFeed;

        // Assert that the feed's generator is correct
        $this->assertTrue($feed->getGenerator() instanceof Zend_Gdata_App_Extension_Generator);
        $this->verifyProperty2($feed, "generator", "version", "1.00");
        $this->verifyProperty2($feed, "generator", "uri", "http://picasaweb.google.com/");
        $this->verifyProperty2($feed, "generator", "text", "Picasaweb");
    }

    /**
      * Check for the existence of an <atom:icon> and verify that it contains
      * the expected value.
      */
    public function testIcon()
    {
        $feed = $this->userFeed;

        // Assert that the feed's title is correct
        $this->assertTrue($feed->getIcon() instanceof Zend_Gdata_App_Extension_Icon);
        $this->verifyProperty2($feed, "icon", "text", 
                "http://lh5.google.com/sample.user/AAAAuZnob5E/AAAAAAAAAAA/EtCbNCdLGxM/s64-c/sample.user");
    }

    /**
      * Check for the existence of an <gphoto:user> and verify that it contains
      * the expected value.
      */
    public function testGphotoUser()
    {
        $feed = $this->userFeed;

        // Assert that the feed's title is correct
        $this->assertTrue($feed->getGphotoUser() instanceof Zend_Gdata_Photos_Extension_User);
        $this->verifyProperty2($feed, "gphotoUser", "text", 
                "sample.user");
        $this->verifyProperty3($feed, "gphotoUser", "text", 
                "sample.user");
    }

    /**
      * Check for the existence of an <gphoto:nickname> and verify that it contains
      * the expected value.
      */
    public function testGphotoNickname()
    {
        $feed = $this->userFeed;

        // Assert that the feed's title is correct
        $this->assertTrue($feed->getGphotoNickname() instanceof Zend_Gdata_Photos_Extension_Nickname);
        $this->verifyProperty2($feed, "gphotoNickname", "text", 
                "sample");
        $this->verifyProperty3($feed, "gphotoNickname", "text", 
                "sample");
    }

    /**
      * Check for the existence of an <gphoto:thumbnail> and verify that it contains
      * the expected value.
      */
    public function testGphotoThumbnail()
    {
        $feed = $this->userFeed;

        // Assert that the feed's title is correct
        $this->assertTrue($feed->getGphotoThumbnail() instanceof Zend_Gdata_Photos_Extension_Thumbnail);
        $this->verifyProperty2($feed, "gphotoThumbnail", "text", 
                "http://lh5.google.com/sample.user/AAAAuZnob5E/AAAAAAAAAAA/EtCbNCdLGxM/s64-c/sample.user");
        $this->verifyProperty3($feed, "gphotoThumbnail", "text", 
                "http://lh5.google.com/sample.user/AAAAuZnob5E/AAAAAAAAAAA/EtCbNCdLGxM/s64-c/sample.user");
    }
    
}
