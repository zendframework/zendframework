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
 * @package    Zend_GData_YouTube
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\GData\YouTube;
use Zend\GData\YouTube;

/**
 * Test helper
 */


/**
 * @category   Zend
 * @package    Zend_GData_YouTube
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_YouTube
 */
class PlaylistListFeedTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->feedText = file_get_contents(
                'Zend/GData/YouTube/_files/PlaylistListFeedDataSample1.xml',
                true);
        $this->V2feedText = file_get_contents(
                'Zend/GData/YouTube/_files/PlaylistListFeedDataSampleV2.xml',
                true);

        $this->feed = new YouTube\PlaylistListFeed();
    }

    private function verifyAllSamplePropertiesAreCorrect ($playlistListFeed) {
        $this->assertEquals('http://gdata.youtube.com/feeds/users/testuser/playlists',
            $playlistListFeed->id->text);
        $this->assertEquals('2007-09-20T20:59:47.530Z', $playlistListFeed->updated->text);
        $this->assertEquals('http://schemas.google.com/g/2005#kind', $playlistListFeed->category[0]->scheme);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#playlistLink', $playlistListFeed->category[0]->term);
        $this->assertEquals('http://www.youtube.com/img/pic_youtubelogo_123x63.gif', $playlistListFeed->logo->text);
        $this->assertEquals('text', $playlistListFeed->title->type);
        $this->assertEquals('testuser\'s Playlists', $playlistListFeed->title->text);
        $this->assertEquals('self', $playlistListFeed->getLink('self')->rel);
        $this->assertEquals('application/atom+xml', $playlistListFeed->getLink('self')->type);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/testuser/playlists?start-index=1&max-results=25', $playlistListFeed->getLink('self')->href);
        $this->assertEquals('testuser', $playlistListFeed->author[0]->name->text);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/testuser', $playlistListFeed->author[0]->uri->text);
        $this->assertEquals(2, $playlistListFeed->totalResults->text);
    }

    private function verifyAllSamplePropertiesAreCorrectV2 ($playlistListFeed) {
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

    public function testEmptyEntryShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->feed->extensionElements));
        $this->assertEquals(0, count($this->feed->extensionElements));
    }

    public function testEmptyEntryShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->feed->extensionAttributes));
        $this->assertEquals(0, count($this->feed->extensionAttributes));
    }

    public function testSampleEntryShouldHaveNoExtensionElements() {
        $this->feed->transferFromXML($this->feedText);
        $this->assertTrue(is_array($this->feed->extensionElements));
        $this->assertEquals(0, count($this->feed->extensionElements));
    }

    public function testSampleEntryShouldHaveNoExtensionAttributes() {
        $this->feed->transferFromXML($this->feedText);
        $this->assertTrue(is_array($this->feed->extensionAttributes));
        $this->assertEquals(0, count($this->feed->extensionAttributes));
    }

    public function testSampleEntryShouldHaveNoExtensionElementsV2() {
        $this->feed->setMajorProtocolVersion(2);
        $this->feed->transferFromXML($this->V2feedText);
        $this->assertTrue(is_array($this->feed->extensionElements));
        $this->assertEquals(0, count($this->feed->extensionElements));
    }

    public function testSampleEntryShouldHaveNoExtensionAttributesV2() {
        $this->feed->setMajorProtocolVersion(2);
        $this->feed->transferFromXML($this->V2feedText);
        $this->assertTrue(is_array($this->feed->extensionAttributes));
        $this->assertEquals(0, count($this->feed->extensionAttributes));
    }

    public function testEmptyPlaylistListFeedToAndFromStringShouldMatch() {
        $feedXml = $this->feed->saveXML();
        $newPlaylistListFeed = new YouTube\PlaylistListFeed();
        $newPlaylistListFeed->transferFromXML($feedXml);
        $newPlaylistListFeedXml = $newPlaylistListFeed->saveXML();
        $this->assertTrue($feedXml == $newPlaylistListFeedXml);
    }

    public function testEmptyPlaylistListFeedToAndFromStringShouldMatchV2() {
        $this->feed->setMajorProtocolVersion(2);
        $this->feed->transferFromXML($this->V2feedText);
        $feedXml = $this->feed->saveXML();
        $newPlaylistListFeed = new YouTube\PlaylistListFeed();
        $newPlaylistListFeed->transferFromXML($feedXml);
        $newPlaylistListFeed->setMajorProtocolVersion(2);
        $newPlaylistListFeedXml = $newPlaylistListFeed->saveXML();
        $this->assertTrue($feedXml == $newPlaylistListFeedXml);
    }

    public function testSamplePropertiesAreCorrect () {
        $this->feed->transferFromXML($this->feedText);
        $this->verifyAllSamplePropertiesAreCorrect($this->feed);
    }

    public function testSamplePropertiesAreCorrectV2 () {
        $this->feed->setMajorProtocolVersion(2);
        $this->feed->transferFromXML($this->V2feedText);
        $this->verifyAllSamplePropertiesAreCorrectV2($this->feed);
    }

    public function testConvertPlaylistListFeedToAndFromString() {
        $this->feed->transferFromXML($this->feedText);
        $entryXml = $this->feed->saveXML();
        $newPlaylistListFeed = new YouTube\PlaylistListFeed();
        $newPlaylistListFeed->transferFromXML($entryXml);
        $this->verifyAllSamplePropertiesAreCorrect($newPlaylistListFeed);
        $newPlaylistListFeedXml = $newPlaylistListFeed->saveXML();
        $this->assertEquals($entryXml, $newPlaylistListFeedXml);
    }

}
