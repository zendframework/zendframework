<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace ZendTest\GData\YouTube;

use Zend\GData\YouTube;

/**
 * @category   Zend
 * @package    Zend_GData_YouTube
 * @subpackage UnitTests
 * @group      Zend_GData
 * @group      Zend_GData_YouTube
 */
class PlaylistListFeedTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->V2feedText = file_get_contents(
                'Zend/GData/YouTube/_files/PlaylistListFeedDataSampleV2.xml',
                true);

        $this->feed = new YouTube\PlaylistListFeed();
    }

    private function verifyAllSamplePropertiesAreCorrectV2 ($playlistListFeed)
    {
        $this->assertEquals('tag:youtube.com,2008:user:GoogleDevelopers:playlists',
            $playlistListFeed->id->text);
        $this->assertEquals('2008-12-10T09:56:03.000Z',
            $playlistListFeed->updated->text);
        $this->assertEquals('http://schemas.google.com/g/2005#kind',
            $playlistListFeed->category[0]->scheme);
        $this->assertEquals(
            'http://gdata.youtube.com/schemas/2007#playlistLink',
            $playlistListFeed->category[0]->term);
        $this->assertEquals(
            'http://www.youtube.com/img/pic_youtubelogo_123x63.gif',
            $playlistListFeed->logo->text);
        $this->assertEquals('Playlists of GoogleDevelopers',
            $playlistListFeed->title->text);
        $this->assertEquals('self', $playlistListFeed->getLink('self')->rel);
        $this->assertEquals('application/atom+xml',
            $playlistListFeed->getLink('self')->type);
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/api/users/GoogleDevelopers/' .
            'playlists?start-index=1&max-results=25&v=2',
            $playlistListFeed->getLink('self')->href);
        $this->assertEquals('GoogleDevelopers',
            $playlistListFeed->author[0]->name->text);
        $this->assertEquals('http://gdata.youtube.com/feeds/api/users/' .
            'googledevelopers', $playlistListFeed->author[0]->uri->text);
        $this->assertEquals(70, $playlistListFeed->totalResults->text);
    }

    public function testEmptyEntryShouldHaveNoExtensionElements()
    {
        $this->assertTrue(is_array($this->feed->extensionElements));
        $this->assertEquals(0, count($this->feed->extensionElements));
    }

    public function testEmptyEntryShouldHaveNoExtensionAttributes()
    {
        $this->assertTrue(is_array($this->feed->extensionAttributes));
        $this->assertEquals(0, count($this->feed->extensionAttributes));
    }

    public function testSampleEntryShouldHaveNoExtensionElementsV2()
    {
        $this->feed->setMajorProtocolVersion(2);
        $this->feed->transferFromXML($this->V2feedText);
        $this->assertTrue(is_array($this->feed->extensionElements));
        $this->assertEquals(0, count($this->feed->extensionElements));
    }

    public function testSampleEntryShouldHaveNoExtensionAttributesV2()
    {
        $this->feed->setMajorProtocolVersion(2);
        $this->feed->transferFromXML($this->V2feedText);
        $this->assertTrue(is_array($this->feed->extensionAttributes));
        $this->assertEquals(0, count($this->feed->extensionAttributes));
    }

    public function testEmptyPlaylistListFeedToAndFromStringShouldMatch()
    {
        $feedXml = $this->feed->saveXML();
        $newPlaylistListFeed = new YouTube\PlaylistListFeed();
        $newPlaylistListFeed->transferFromXML($feedXml);
        $newPlaylistListFeedXml = $newPlaylistListFeed->saveXML();
        $this->assertTrue($feedXml == $newPlaylistListFeedXml);
    }

    public function testEmptyPlaylistListFeedToAndFromStringShouldMatchV2()
    {
        $this->feed->setMajorProtocolVersion(2);
        $this->feed->transferFromXML($this->V2feedText);
        $feedXml = $this->feed->saveXML();
        $newPlaylistListFeed = new YouTube\PlaylistListFeed();
        $newPlaylistListFeed->transferFromXML($feedXml);
        $newPlaylistListFeed->setMajorProtocolVersion(2);
        $newPlaylistListFeedXml = $newPlaylistListFeed->saveXML();
        $this->assertTrue($feedXml == $newPlaylistListFeedXml);
    }

    public function testSamplePropertiesAreCorrectV2 ()
    {
        $this->feed->setMajorProtocolVersion(2);
        $this->feed->transferFromXML($this->V2feedText);
        $this->verifyAllSamplePropertiesAreCorrectV2($this->feed);
    }
}
