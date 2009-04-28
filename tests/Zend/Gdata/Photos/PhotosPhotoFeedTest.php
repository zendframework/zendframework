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
require_once 'Zend/Gdata/Photos/PhotoFeed.php';
require_once 'Zend/Http/Client.php';
require_once 'Zend/Http/Client/Adapter/Test.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_Photos_PhotosPhotoFeedTest extends PHPUnit_Framework_TestCase
{
    
    protected $photoFeed = null;

    /** 
      * Called before each test to setup any fixtures.
      */
    public function setUp()
    {
        $photoFeedText = file_get_contents(
                '_files/TestPhotoFeed.xml',
                true);
        $this->photoFeed = new Zend_Gdata_Photos_PhotoFeed($photoFeedText);
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
    public function testPhotoFeedToAndFromString()
    {
        $entryCount = 0;
        foreach ($this->photoFeed as $entry) {
            $entryCount++;
            $this->assertTrue($entry instanceof Zend_Gdata_Photos_CommentEntry ||
                              $entry instanceof Zend_Gdata_Photos_TagEntry);
        }
        $this->assertTrue($entryCount > 0);
        
        /* Grab XML from $this->photoFeed and convert back to objects */ 
        $newListFeed = new Zend_Gdata_Photos_PhotoFeed( 
                $this->photoFeed->saveXML());
        $newEntryCount = 0;
        foreach ($newListFeed as $entry) {
            $newEntryCount++;
            $this->assertTrue($entry instanceof Zend_Gdata_Photos_CommentEntry ||
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
        foreach ($this->photoFeed as $entry) {
            $entryCount++;
        }
        $this->assertEquals(3, $entryCount);
    }

    /**
      * Check for the existence of an <atom:id> and verify that it contains
      * the expected value.
      */
    public function testId()
    {
        $feed = $this->photoFeed;

        // Assert that the feed's ID is correct
        $this->assertTrue($feed->getId() instanceof Zend_Gdata_App_Extension_Id);
        $this->verifyProperty2($feed, "id", "text", 
                "http://picasaweb.google.com/data/feed/api/user/sample.user/albumid/1/photoid/100");

        // Assert that all entries have an Atom ID object
        foreach ($feed as $entry) {
            $this->assertTrue($entry->getId() instanceof Zend_Gdata_App_Extension_Id);
        }

        // Assert one of the entry's IDs
        $entry = $feed[0];
        $this->verifyProperty2($entry, "id", "text", 
                "http://picasaweb.google.com/data/entry/api/user/sample.user/albumid/1/photoid/100/tag/tag");
    }

    /**
      * Check for the existence of an <atom:updated> and verify that it contains 
      * the expected value.
      */
    public function testUpdated()
    {
        $feed = $this->photoFeed;

        // Assert that the feed's updated date is correct
        $this->assertTrue($feed->getUpdated() instanceof Zend_Gdata_App_Extension_Updated);
        $this->verifyProperty2($feed, "updated", "text", 
                "2007-09-21T18:23:05.000Z");

        // Assert that all entries have an Atom Updated object
        foreach ($feed as $entry) {
            $this->assertTrue($entry->getUpdated() instanceof Zend_Gdata_App_Extension_Updated);
        }

        // Assert one of the entry's Updated dates
        $entry = $feed[0];
        $this->verifyProperty2($entry, "updated", "text", "2007-09-21T18:23:05.000Z");
    }

    /**
      * Check for the existence of an <atom:title> and verify that it contains
      * the expected value.
      */
    public function testTitle()
    {
        $feed = $this->photoFeed;

        // Assert that the feed's title is correct
        $this->assertTrue($feed->getTitle() instanceof Zend_Gdata_App_Extension_Title);
        $this->verifyProperty2($feed, "title", "text", "Aqua Blue.jpg");

        // Assert that all entries have an Atom ID object
        foreach ($feed as $entry) {
            $this->assertTrue($entry->getTitle() instanceof Zend_Gdata_App_Extension_Title);
        }
    }

    /**
      * Check for the existence of an <atom:subtitle> and verify that it contains
      * the expected value.
      */
    public function testSubtitle()
    {
        $feed = $this->photoFeed;

        // Assert that the feed's title is correct
        $this->assertTrue($feed->getSubtitle() instanceof Zend_Gdata_App_Extension_Subtitle);
        $this->verifyProperty2($feed, "subtitle", "text", 
                "Blue");
    }

    /**
      * Check for the existence of an <atom:generator> and verify that it contains
      * the expected value.
      */
    public function testGenerator()
    {
        $feed = $this->photoFeed;

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
        $feed = $this->photoFeed;

        // Assert that the feed's title is correct
        $this->assertTrue($feed->getIcon() instanceof Zend_Gdata_App_Extension_Icon);
        $this->verifyProperty2($feed, "icon", "text", 
                "http://lh4.google.com/sample.user/Rt8WU4DZEKI/AAAAAAAAABY/IVgLqmnzJII/s288/Aqua%20Blue.jpg");
    }

    /**
      * Check for the existence of an <gphoto:id> and verify that it contains
      * the expected value.
      */
    public function testGphotoId()
    {
        $feed = $this->photoFeed;

        // Assert that the feed's title is correct
        $this->assertTrue($feed->getGphotoId() instanceof Zend_Gdata_Photos_Extension_Id);
        $this->verifyProperty2($feed, "gphotoId", "text", 
                "100");
        $this->verifyProperty3($feed, "gphotoId", "text", 
                "100");
    }

    /**
      * Check for the existence of an <gphoto:version> and verify that it contains
      * the expected value.
      */
    public function testGphotoVersion()
    {
        $feed = $this->photoFeed;

        // Assert that the feed's version is correct
        $this->assertTrue($feed->getGphotoVersion() instanceof Zend_Gdata_Photos_Extension_Version);
        $this->verifyProperty2($feed, "gphotoVersion", "text", 
                "1190398985145172");
        $this->verifyProperty3($feed, "gphotoVersion", "text", 
                "1190398985145172");
    }

    /**
      * Check for the existence of an <gphoto:albumid> and verify that it contains
      * the expected value.
      */
    public function testGphotoAlbumId()
    {
        $feed = $this->photoFeed;

        // Assert that the feed's albumid is correct
        $this->assertTrue($feed->getGphotoAlbumId() instanceof Zend_Gdata_Photos_Extension_AlbumId);
        $this->verifyProperty2($feed, "gphotoAlbumId", "text", 
                "1");
        $this->verifyProperty3($feed, "gphotoAlbumId", "text", 
                "1");
    }

    /**
      * Check for the existence of an <gphoto:timestamp> and verify that it contains
      * the expected value.
      */
    public function testGphotoTimestamp()
    {
        $feed = $this->photoFeed;

        // Assert that the feed's timestamp is correct
        $this->assertTrue($feed->getGphotoTimestamp() instanceof Zend_Gdata_Photos_Extension_Timestamp);
        $this->verifyProperty2($feed, "gphotoTimestamp", "text", 
                "1189025362000");
        $this->verifyProperty3($feed, "gphotoTimestamp", "text", 
                "1189025362000");
    }

    /**
      * Check for the existence of an <gphoto:width> and verify that it contains
      * the expected value.
      */
    public function testGphotoWidth()
    {
        $feed = $this->photoFeed;

        // Assert that the feed's width is correct
        $this->assertTrue($feed->getGphotoWidth() instanceof Zend_Gdata_Photos_Extension_Width);
        $this->verifyProperty2($feed, "gphotoWidth", "text", 
                "2560");
        $this->verifyProperty3($feed, "gphotoWidth", "text", 
                "2560");
    }

    /**
      * Check for the existence of an <gphoto:height> and verify that it contains
      * the expected value.
      */
    public function testGphotoHeight()
    {
        $feed = $this->photoFeed;

        // Assert that the feed's height is correct
        $this->assertTrue($feed->getGphotoHeight() instanceof Zend_Gdata_Photos_Extension_Height);
        $this->verifyProperty2($feed, "gphotoHeight", "text", 
                "1600");
        $this->verifyProperty3($feed, "gphotoHeight", "text", 
                "1600");
    }

    /**
      * Check for the existence of an <gphoto:size> and verify that it contains
      * the expected value.
      */
    public function testGphotoSize()
    {
        $feed = $this->photoFeed;

        // Assert that the feed's size is correct
        $this->assertTrue($feed->getGphotoSize() instanceof Zend_Gdata_Photos_Extension_Size);
        $this->verifyProperty2($feed, "gphotoSize", "text", 
                "883405");
        $this->verifyProperty3($feed, "gphotoSize", "text", 
                "883405");
    }

    /**
      * Check for the existence of an <gphoto:client> and verify that it contains
      * the expected value.
      */
    public function testGphotoClient()
    {
        $feed = $this->photoFeed;

        // Assert that the feed's client is correct
        $this->assertTrue($feed->getGphotoClient() instanceof Zend_Gdata_Photos_Extension_Client);
        $this->verifyProperty2($feed, "gphotoClient", "text", 
                "");
        $this->verifyProperty3($feed, "gphotoClient", "text", 
                "");
    }

    /**
      * Check for the existence of an <gphoto:checksum> and verify that it contains
      * the expected value.
      */
    public function testGphotoChecksum()
    {
        $feed = $this->photoFeed;

        // Assert that the feed's checksum is correct
        $this->assertTrue($feed->getGphotoChecksum() instanceof Zend_Gdata_Photos_Extension_Checksum);
        $this->verifyProperty2($feed, "gphotoChecksum", "text", 
                "");
        $this->verifyProperty3($feed, "gphotoChecksum", "text", 
                "");
    }

    /**
      * Check for the existence of an <gphoto:commentingEnabled> and verify that it contains
      * the expected value.
      */
    public function testGphotoCommentingEnabled()
    {
        $feed = $this->photoFeed;

        // Assert that the feed's title is correct
        $this->assertTrue($feed->getGphotoCommentingEnabled() instanceof Zend_Gdata_Photos_Extension_CommentingEnabled);
        $this->verifyProperty2($feed, "gphotoCommentingEnabled", "text", 
                "true");
        $this->verifyProperty3($feed, "gphotoCommentingEnabled", "text", 
                "true");
    }

    /**
      * Check for the existence of an <gphoto:commentCount> and verify that it contains
      * the expected value.
      */
    public function testGphotoCommentCount()
    {
        $feed = $this->photoFeed;

        // Assert that the feed's title is correct
        $this->assertTrue($feed->getGphotoCommentCount() instanceof Zend_Gdata_Photos_Extension_CommentCount);
        $this->verifyProperty2($feed, "gphotoCommentCount", "text", 
                "1");
        $this->verifyProperty3($feed, "gphotoCommentCount", "text", 
                "1");
    }
    
}
