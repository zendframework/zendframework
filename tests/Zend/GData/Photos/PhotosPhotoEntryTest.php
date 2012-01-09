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

/**
 * @category   Zend
 * @package    Zend_GData_Photos
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_Photos
 */
class PhotosPhotoEntryTest extends \PHPUnit_Framework_TestCase
{

    protected $photoEntry = null;

    /**
      * Called before each test to setup any fixtures.
      */
    public function setUp()
    {
        $photoEntryText = file_get_contents(
                '_files/TestPhotoEntry.xml',
                true);
        $this->photoEntry = new \Zend\GData\Photos\PhotoEntry($photoEntryText);
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
      * Check for the existence of an <atom:id> and verify that it contains
      * the expected value.
      */
    public function testId()
    {
        $entry = $this->photoEntry;

        // Assert that the entry's ID is correct
        $this->assertTrue($entry->getId() instanceof \Zend\GData\App\Extension\Id);
        $this->verifyProperty2($entry, "id", "text",
                "http://picasaweb.google.com/data/entry/api/user/sample.user/albumid/1/photoid/100");
    }

    /**
      * Check for the existence of an <atom:published> and verify that it contains
      * the expected value.
      */
    public function testPublished()
    {
        $entry = $this->photoEntry;

        // Assert that the photo entry has an Atom Published object
        $this->assertTrue($entry->getPublished() instanceof \Zend\GData\App\Extension\Published);
        $this->verifyProperty2($entry, "published", "text", "2007-09-05T20:49:24.000Z");
    }

    /**
      * Check for the existence of an <atom:updated> and verify that it contains
      * the expected value.
      */
    public function testUpdated()
    {
        $entry = $this->photoEntry;

        // Assert that the entry's updated date is correct
        $this->assertTrue($entry->getUpdated() instanceof \Zend\GData\App\Extension\Updated);
        $this->verifyProperty2($entry, "updated", "text",
                "2007-09-21T18:19:38.000Z");
    }

    /**
      * Check for the existence of an <atom:title> and verify that it contains
      * the expected value.
      */
    public function testTitle()
    {
        $entry = $this->photoEntry;

        // Assert that the entry's title is correct
        $this->assertTrue($entry->getTitle() instanceof \Zend\GData\App\Extension\Title);
        $this->verifyProperty2($entry, "title", "text", "Aqua Graphite.jpg");
    }

    /**
      * Check for the existence of an <gphoto:id> and verify that it contains
      * the expected value.
      */
    public function testGphotoId()
    {
        $entry = $this->photoEntry;

        // Assert that the entry's title is correct
        $this->assertTrue($entry->getGphotoId() instanceof \Zend\GData\Photos\Extension\Id);
        $this->verifyProperty2($entry, "gphotoId", "text",
                "100");
        $this->verifyProperty3($entry, "gphotoId", "text",
                "100");
    }

    /**
     * Check for the existance of exif namespaced data and verify that it contains
     * the expected value.
     */
    public function testExifData()
    {
        $exifTags = $this->photoEntry->exifTags;
        $this->assertTrue($exifTags != null);
        $this->assertTrue($exifTags->flash != null);
        $this->assertTrue($exifTags->fstop != null);
        $this->assertTrue($exifTags->exposure != null);
        $this->assertTrue($exifTags->focallength != null);
        $this->assertTrue($exifTags->iso != null);
        $this->assertTrue($exifTags->time != null);
        $this->assertTrue($exifTags->distance != null);
        $this->assertTrue($exifTags->make != null);
        $this->assertTrue($exifTags->model != null);
        $this->assertTrue($exifTags->imageUniqueID != null);
        $this->assertEquals("true", $exifTags->flash->text);
        $this->assertEquals("11.0", $exifTags->fstop->text);
        $this->assertEquals("0.0040", $exifTags->exposure->text);
        $this->assertEquals("22.0", $exifTags->focallength->text);
        $this->assertEquals("200", $exifTags->iso->text);
        $this->assertEquals("1180950900000", $exifTags->time->text);
        $this->assertEquals("0.0",$exifTags->distance->text);
        $this->assertEquals("Fictitious Camera Company",$exifTags->make->text);
        $this->assertEquals("AMAZING-100D",$exifTags->model->text);
        $this->assertEquals("a5ce2e36b9df7d3cb081511c72e73926", $exifTags->imageUniqueID->text);
    }

    /**
     * Check for the geo data and verify that it contains the expected values
     */
    public function testGeoData()
    {
        $geoRssWhere = $this->photoEntry->geoRssWhere;
        $point = $geoRssWhere->point;
        $pos = $point->pos;
        $this->assertEquals("41.87194 12.56738", $pos->text);
    }


    /**
      * Check for the existence of an <gphoto:version> and verify that it contains
      * the expected value.
      */
    public function testGphotoVersion()
    {
        $entry = $this->photoEntry;

        // Assert that the entry's version is correct
        $this->assertTrue($entry->getGphotoVersion() instanceof \Zend\GData\Photos\Extension\Version);
        $this->verifyProperty2($entry, "gphotoVersion", "text",
                "1190398778006402");
        $this->verifyProperty3($entry, "gphotoVersion", "text",
                "1190398778006402");
    }

    /**
      * Check for the existence of an <gphoto:albumid> and verify that it contains
      * the expected value.
      */
    public function testGphotoAlbumId()
    {
        $entry = $this->photoEntry;

        // Assert that the entry's albumid is correct
        $this->assertTrue($entry->getGphotoAlbumId() instanceof \Zend\GData\Photos\Extension\AlbumId);
        $this->verifyProperty2($entry, "gphotoAlbumId", "text",
                "1");
        $this->verifyProperty3($entry, "gphotoAlbumId", "text",
                "1");
    }

    /**
      * Check for the existence of an <gphoto:width> and verify that it contains
      * the expected value.
      */
    public function testGphotoWidth()
    {
        $entry = $this->photoEntry;

        // Assert that the entry's width is correct
        $this->assertTrue($entry->getGphotoWidth() instanceof \Zend\GData\Photos\Extension\Width);
        $this->verifyProperty2($entry, "gphotoWidth", "text",
                "2560");
        $this->verifyProperty3($entry, "gphotoWidth", "text",
                "2560");
    }

    /**
      * Check for the existence of an <gphoto:height> and verify that it contains
      * the expected value.
      */
    public function testGphotoHeight()
    {
        $entry = $this->photoEntry;

        // Assert that the entry's height is correct
        $this->assertTrue($entry->getGphotoHeight() instanceof \Zend\GData\Photos\Extension\Height);
        $this->verifyProperty2($entry, "gphotoHeight", "text",
                "1600");
        $this->verifyProperty3($entry, "gphotoHeight", "text",
                "1600");
    }

    /**
      * Check for the existence of an <gphoto:size> and verify that it contains
      * the expected value.
      */
    public function testGphotoSize()
    {
        $entry = $this->photoEntry;

        // Assert that the entry's size is correct
        $this->assertTrue($entry->getGphotoSize() instanceof \Zend\GData\Photos\Extension\Size);
        $this->verifyProperty2($entry, "gphotoSize", "text",
                "798334");
        $this->verifyProperty3($entry, "gphotoSize", "text",
                "798334");
    }

    /**
      * Check for the existence of an <gphoto:client> and verify that it contains
      * the expected value.
      */
    public function testGphotoClient()
    {
        $entry = $this->photoEntry;

        // Assert that the entry's client is correct
        $this->assertTrue($entry->getGphotoClient() instanceof \Zend\GData\Photos\Extension\Client);
        $this->verifyProperty2($entry, "gphotoClient", "text",
                "");
        $this->verifyProperty3($entry, "gphotoClient", "text",
                "");
    }

    /**
      * Check for the existence of an <gphoto:checksum> and verify that it contains
      * the expected value.
      */
    public function testGphotoChecksum()
    {
        $entry = $this->photoEntry;

        // Assert that the entry's checksum is correct
        $this->assertTrue($entry->getGphotoChecksum() instanceof \Zend\GData\Photos\Extension\Checksum);
        $this->verifyProperty2($entry, "gphotoChecksum", "text",
                "");
        $this->verifyProperty3($entry, "gphotoChecksum", "text",
                "");
    }

    /**
      * Check for the existence of an <gphoto:timestamp> and verify that it contains
      * the expected value.
      */
    public function testGphotoTimestamp()
    {
        $entry = $this->photoEntry;

        // Assert that the entry's title is correct
        $this->assertTrue($entry->getGphotoTimestamp() instanceof \Zend\GData\Photos\Extension\Timestamp);
        $this->verifyProperty2($entry, "gphotoTimestamp", "text",
                "1189025363000");
        $this->verifyProperty3($entry, "gphotoTimestamp", "text",
                "1189025363000");
    }

    /**
      * Check for the existence of an <gphoto:commentingEnabled> and verify that it contains
      * the expected value.
      */
    public function testGphotoCommentingEnabled()
    {
        $entry = $this->photoEntry;

        // Assert that the entry's title is correct
        $this->assertTrue($entry->getGphotoCommentingEnabled() instanceof \Zend\GData\Photos\Extension\CommentingEnabled);
        $this->verifyProperty2($entry, "gphotoCommentingEnabled", "text",
                "true");
        $this->verifyProperty3($entry, "gphotoCommentingEnabled", "text",
                "true");
    }

    /**
      * Check for the existence of an <gphoto:commentCount> and verify that it contains
      * the expected value.
      */
    public function testGphotoCommentCount()
    {
        $entry = $this->photoEntry;

        // Assert that the entry's title is correct
        $this->assertTrue($entry->getGphotoCommentCount() instanceof \Zend\GData\Photos\Extension\CommentCount);
        $this->verifyProperty2($entry, "gphotoCommentCount", "text",
                "0");
        $this->verifyProperty3($entry, "gphotoCommentCount", "text",
                "0");
    }

    /**
      * Check for the existence of a <media:group>
      */
    public function testMediaGroup()
    {
        $entry = $this->photoEntry;

        // Assert that the entry's media group exists
        $this->assertTrue($entry->getMediaGroup() instanceof \Zend\GData\Media\Extension\MediaGroup);
    }

}
