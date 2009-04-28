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
 * @category     Zend
 * @package      Zend_Gdata_YouTube
 * @subpackage   UnitTests
 * @copyright    Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com);
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Test helper
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'Zend/Gdata/YouTube/PlaylistVideoFeed.php';
require_once 'Zend/Gdata/YouTube.php';

/**
 * @package Zend_Gdata_App
 * @subpackage UnitTests
 */
class Zend_Gdata_YouTube_PlaylistVideoFeedTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->feedText = file_get_contents(
                'Zend/Gdata/YouTube/_files/PlaylistVideoFeedDataSample1.xml',
                true);
        $this->feed = new Zend_Gdata_YouTube_PlaylistVideoFeed();
    }

    private function verifyAllSamplePropertiesAreCorrect ($playlistVideoFeed) {
        $this->assertEquals('http://gdata.youtube.com/feeds/playlists/46A2F8C9B36B6FE7',
            $playlistVideoFeed->id->text);
        $this->assertEquals('2007-09-20T13:42:19.000-07:00', $playlistVideoFeed->updated->text);
        $this->assertEquals('http://schemas.google.com/g/2005#kind', $playlistVideoFeed->category[0]->scheme);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#playlist', $playlistVideoFeed->category[0]->term);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007/tags.cat', $playlistVideoFeed->category[1]->scheme);
        $this->assertEquals('music', $playlistVideoFeed->category[1]->term);
        $this->assertEquals('http://www.youtube.com/img/pic_youtubelogo_123x63.gif', $playlistVideoFeed->logo->text);
        $this->assertEquals('text', $playlistVideoFeed->title->type);
        $this->assertEquals('YouTube Musicians', $playlistVideoFeed->title->text);;
        $this->assertEquals('self', $playlistVideoFeed->getLink('self')->rel);
        $this->assertEquals('application/atom+xml', $playlistVideoFeed->getLink('self')->type);
        $this->assertEquals('http://gdata.youtube.com/feeds/playlists/46A2F8C9B36B6FE7?start-index=1&max-results=25', $playlistVideoFeed->getLink('self')->href);
        $this->assertEquals('testuser', $playlistVideoFeed->author[0]->name->text);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/testuser', $playlistVideoFeed->author[0]->uri->text);
        $this->assertEquals(13, $playlistVideoFeed->totalResults->text);
        $this->assertEquals(13, count($playlistVideoFeed->entry));
        $entries = $playlistVideoFeed->entry;
        $this->assertEquals(1, $entries[0]->getPosition()->getText());
    }

    public function testEmptyEntryShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->feed->extensionElements));
        $this->assertTrue(count($this->feed->extensionElements) == 0);
    }

    public function testEmptyEntryShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->feed->extensionAttributes));
        $this->assertTrue(count($this->feed->extensionAttributes) == 0);
    }

    public function testSampleEntryShouldHaveNoExtensionElements() {
        $this->feed->transferFromXML($this->feedText);
        $this->assertTrue(is_array($this->feed->extensionElements));
        $this->assertTrue(count($this->feed->extensionElements) == 0);
    }

    public function testSampleEntryShouldHaveNoExtensionAttributes() {
        $this->feed->transferFromXML($this->feedText);
        $this->assertTrue(is_array($this->feed->extensionAttributes));
        $this->assertTrue(count($this->feed->extensionAttributes) == 0);
    }

    public function testEmptyPlaylistVideoFeedToAndFromStringShouldMatch() {
        $entryXml = $this->feed->saveXML();
        $newPlaylistVideoFeed = new Zend_Gdata_YouTube_PlaylistVideoFeed();
        $newPlaylistVideoFeed->transferFromXML($entryXml);
        $newPlaylistVideoFeedXml = $newPlaylistVideoFeed->saveXML();
        $this->assertTrue($entryXml == $newPlaylistVideoFeedXml);
    }

    public function testSamplePropertiesAreCorrect () {
        $this->feed->transferFromXML($this->feedText);
        $this->verifyAllSamplePropertiesAreCorrect($this->feed);
    }

    public function testConvertPlaylistVideoFeedToAndFromString() {
        $this->feed->transferFromXML($this->feedText);
        $feedXml = $this->feed->saveXML();
        $newPlaylistVideoFeed = new Zend_Gdata_YouTube_PlaylistVideoFeed();
        $newPlaylistVideoFeed->transferFromXML($feedXml);
        $this->verifyAllSamplePropertiesAreCorrect($newPlaylistVideoFeed);
        $newPlaylistVideoFeedXml = $newPlaylistVideoFeed->saveXML();
        $this->assertEquals($feedXml, $newPlaylistVideoFeedXml);
    }

}
