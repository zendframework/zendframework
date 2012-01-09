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
 * @package    Zend_GData_Photos
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\GData\Photos;
use Zend\GData\Photos;
use Zend\GData\App\Extension;

/**
 * @category   Zend
 * @package    Zend_GData_Photos
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_Photos
 */
class PhotosAlbumFeedTest extends \PHPUnit_Framework_TestCase
{

    protected $albumFeed = null;

    /**
      * Called before each test to setup any fixtures.
      */
    public function setUp()
    {
        $albumFeedText = file_get_contents(
                '_files/TestAlbumFeed.xml',
                true);
        $this->albumFeed = new Photos\AlbumFeed($albumFeedText);
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
    public function testAlbumFeedToAndFromString()
    {
        $entryCount = 0;
        foreach ($this->albumFeed as $entry) {
            $entryCount++;
            $this->assertTrue($entry instanceof Photos\PhotoEntry ||
                              $entry instanceof Photos\TagEntry);
        }
        $this->assertTrue($entryCount > 0);

        /* Grab XML from $this->albumFeed and convert back to objects */
        $newListFeed = new Photos\UserFeed(
                $this->albumFeed->saveXML());
        $newEntryCount = 0;
        foreach ($newListFeed as $entry) {
            $newEntryCount++;
            $this->assertTrue($entry instanceof Photos\PhotoEntry ||
                              $entry instanceof Photos\TagEntry);
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
        foreach ($this->albumFeed as $entry) {
            $entryCount++;
        }
        $this->assertEquals(4, $entryCount);
    }

    /**
      * Check for the existence of an <atom:author> and verify that they
      * contain the expected values.
      */
    public function testAuthor()
    {
        $feed = $this->albumFeed;

        // Assert that the feed's author is correct
        $feedAuthor = $feed->getAuthor();
        $this->assertEquals($feedAuthor, $feed->author);
        $this->assertEquals(1, count($feedAuthor));
        $this->assertTrue($feedAuthor[0] instanceof Extension\Author);
        $this->verifyProperty2($feedAuthor[0], "name", "text", "sample");
        $this->assertTrue($feedAuthor[0]->getUri() instanceof Extension\Uri);
        $this->verifyProperty2($feedAuthor[0], "uri", "text", "http://picasaweb.google.com/sample.user");

        // Assert that each entry has valid author data
        foreach ($feed as $entry) {
            if ($entry instanceof Photos\AlbumEntry) {
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
        $feed = $this->albumFeed;

        // Assert that the feed's ID is correct
        $this->assertTrue($feed->getId() instanceof Extension\Id);
        $this->verifyProperty2($feed, "id", "text",
                "http://picasaweb.google.com/data/feed/api/user/sample.user/albumid/1");

        // Assert that all entries have an Atom ID object
        foreach ($feed as $entry) {
            $this->assertTrue($entry->getId() instanceof Extension\Id);
        }

        // Assert one of the entry's IDs
        $entry = $feed[0];
        $this->verifyProperty2($entry, "id", "text",
                "http://picasaweb.google.com/data/entry/api/user/sample.user/albumid/1/photoid/2");
    }

    /**
      * Check for the existence of an <atom:published> and verify that it contains
      * the expected value.
      */
    public function testPublished()
    {
        $feed = $this->albumFeed;

        // Assert that all photo entries have an Atom Published object
        foreach ($feed as $entry) {
            if ($entry instanceof Photos\PhotoEntry) {
                $this->assertTrue($entry->getPublished() instanceof Extension\Published);
            }
        }

        // Assert one of the entry's Published dates
        $entry = $feed[0];
        $this->verifyProperty2($entry, "published", "text", "2007-09-05T20:49:23.000Z");
    }

    /**
      * Check for the existence of an <atom:updated> and verify that it contains
      * the expected value.
      */
    public function testUpdated()
    {
        $feed = $this->albumFeed;

        // Assert that the feed's updated date is correct
        $this->assertTrue($feed->getUpdated() instanceof Extension\Updated);
        $this->verifyProperty2($feed, "updated", "text",
                "2007-09-21T18:23:05.000Z");

        // Assert that all entries have an Atom Updated object
        foreach ($feed as $entry) {
            $this->assertTrue($entry->getUpdated() instanceof Extension\Updated);
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
        $feed = $this->albumFeed;

        // Assert that the feed's title is correct
        $this->assertTrue($feed->getTitle() instanceof Extension\Title);
        $this->verifyProperty2($feed, "title", "text", "Test");

        // Assert that all entries have an Atom ID object
        foreach ($feed as $entry) {
            $this->assertTrue($entry->getTitle() instanceof Extension\Title);
        }

        // Assert one of the entry's Titles
        $entry = $feed[0];
        $this->verifyProperty2($entry, "title", "text", "Aqua Blue.jpg");
    }

    /**
      * Check for the existence of an <atom:subtitle> and verify that it contains
      * the expected value.
      */
    public function testSubtitle()
    {
        $feed = $this->albumFeed;

        // Assert that the feed's title is correct
        $this->assertTrue($feed->getSubtitle() instanceof Extension\Subtitle);
        $this->verifyProperty2($feed, "subtitle", "text",
                "");
    }

    /**
      * Check for the existence of an <atom:generator> and verify that it contains
      * the expected value.
      */
    public function testGenerator()
    {
        $feed = $this->albumFeed;

        // Assert that the feed's generator is correct
        $this->assertTrue($feed->getGenerator() instanceof Extension\Generator);
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
        $feed = $this->albumFeed;

        // Assert that the feed's title is correct
        $this->assertTrue($feed->getIcon() instanceof Extension\Icon);
        $this->verifyProperty2($feed, "icon", "text",
                "http://lh6.google.com/sample.user/Rt8WNoDZEJE/AAAAAAAAABk/HQGlDhpIgWo/s160-c/Test.jpg");
    }

    /**
      * Check for the existence of an <gphoto:user> and verify that it contains
      * the expected value.
      */
    public function testGphotoUser()
    {
        $feed = $this->albumFeed;

        // Assert that the feed's title is correct
        $this->assertTrue($feed->getGphotoUser() instanceof \Zend\GData\Photos\Extension\User);
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
        $feed = $this->albumFeed;

        // Assert that the feed's title is correct
        $this->assertTrue($feed->getGphotoNickname() instanceof \Zend\GData\Photos\Extension\Nickname);
        $this->verifyProperty2($feed, "gphotoNickname", "text",
                "sample");
        $this->verifyProperty3($feed, "gphotoNickname", "text",
                "sample");
    }

    /**
      * Check for the existence of an <gphoto:name> and verify that it contains
      * the expected value.
      */
    public function testGphotoName()
    {
        $feed = $this->albumFeed;

        // Assert that the feed's title is correct
        $this->assertTrue($feed->getGphotoName() instanceof \Zend\GData\Photos\Extension\Name);
        $this->verifyProperty2($feed, "gphotoName", "text",
                "Test");
    }

    /**
      * Check for the existence of an <gphoto:id> and verify that it contains
      * the expected value.
      */
    public function testGphotoId()
    {
        $feed = $this->albumFeed;

        // Assert that the feed's title is correct
        $this->assertTrue($feed->getGphotoId() instanceof \Zend\GData\Photos\Extension\Id);
        $this->verifyProperty2($feed, "gphotoId", "text",
                "1");
        $this->verifyProperty3($feed, "gphotoId", "text",
                "1");
    }

    /**
      * Check for the existence of an <gphoto:location> and verify that it contains
      * the expected value.
      */
    public function testGphotoLocation()
    {
        $feed = $this->albumFeed;

        // Assert that the feed's title is correct
        $this->assertTrue($feed->getGphotoLocation() instanceof \Zend\GData\Photos\Extension\Location);
        $this->verifyProperty2($feed, "gphotoLocation", "text",
                "");
        $this->verifyProperty3($feed, "gphotoLocation", "text",
                "");
    }

    /**
      * Check for the existence of an <gphoto:access> and verify that it contains
      * the expected value.
      */
    public function testGphotoAccess()
    {
        $feed = $this->albumFeed;

        // Assert that the feed's title is correct
        $this->assertTrue($feed->getGphotoAccess() instanceof \Zend\GData\Photos\Extension\Access);
        $this->verifyProperty2($feed, "gphotoAccess", "text",
                "public");
        $this->verifyProperty3($feed, "gphotoAccess", "text",
                "public");
    }

    /**
      * Check for the existence of an <gphoto:timestamp> and verify that it contains
      * the expected value.
      */
    public function testGphotoTimestamp()
    {
        $feed = $this->albumFeed;

        // Assert that the feed's title is correct
        $this->assertTrue($feed->getGphotoTimestamp() instanceof \Zend\GData\Photos\Extension\Timestamp);
        $this->verifyProperty2($feed, "gphotoTimestamp", "text",
                "1188975600000");
        $this->verifyProperty3($feed, "gphotoTimestamp", "text",
                "1188975600000");
    }

    /**
      * Check for the existence of an <gphoto:numphotos> and verify that it contains
      * the expected value.
      */
    public function testGphotoNumPhotos()
    {
        $feed = $this->albumFeed;

        // Assert that the feed's title is correct
        $this->assertTrue($feed->getGphotoNumPhotos() instanceof \Zend\GData\Photos\Extension\NumPhotos);
        $this->verifyProperty2($feed, "gphotoNumPhotos", "text",
                "2");
        $this->verifyProperty3($feed, "gphotoNumPhotos", "text",
                "2");
    }

    /**
      * Check for the existence of an <gphoto:commentingEnabled> and verify that it contains
      * the expected value.
      */
    public function testGphotoCommentingEnabled()
    {
        $feed = $this->albumFeed;

        // Assert that the feed's title is correct
        $this->assertTrue($feed->getGphotoCommentingEnabled() instanceof \Zend\GData\Photos\Extension\CommentingEnabled);
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
        $feed = $this->albumFeed;

        // Assert that the feed's title is correct
        $this->assertTrue($feed->getGphotoCommentCount() instanceof \Zend\GData\Photos\Extension\CommentCount);
        $this->verifyProperty2($feed, "gphotoCommentCount", "text",
                "0");
        $this->verifyProperty3($feed, "gphotoCommentCount", "text",
                "0");
    }

}
