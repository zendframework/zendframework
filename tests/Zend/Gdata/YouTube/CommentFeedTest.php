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

require_once 'Zend/Gdata/YouTube/CommentFeed.php';
require_once 'Zend/Gdata/YouTube.php';

/**
 * @package Zend_Gdata_App
 * @subpackage UnitTests
 */
class Zend_Gdata_YouTube_CommentFeedTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->feedText = file_get_contents(
                'Zend/Gdata/YouTube/_files/CommentFeedDataSample1.xml',
                true);
        $this->feed = new Zend_Gdata_YouTube_CommentFeed();
    }

    private function verifyAllSamplePropertiesAreCorrect ($commentFeed) {
        $this->assertEquals('http://gdata.youtube.com/feeds/videos/Lnio-pqLPgg/comments',
            $commentFeed->id->text);
        $this->assertEquals('2007-09-21T02:32:55.032Z', $commentFeed->updated->text);
        $this->assertEquals('http://schemas.google.com/g/2005#kind', $commentFeed->category[0]->scheme);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#comment', $commentFeed->category[0]->term);
        $this->assertEquals('http://www.youtube.com/img/pic_youtubelogo_123x63.gif', $commentFeed->logo->text);
        $this->assertEquals('text', $commentFeed->title->type);
        $this->assertEquals('Comments on \'"That Girl" - Original Song - Acoustic Version\'', $commentFeed->title->text);;
        $this->assertEquals('self', $commentFeed->getLink('self')->rel);
        $this->assertEquals('application/atom+xml', $commentFeed->getLink('self')->type);
        $this->assertEquals('http://gdata.youtube.com/feeds/videos/Lnio-pqLPgg/comments?start-index=1&max-results=4', $commentFeed->getLink('self')->href);
        $this->assertEquals('YouTube', $commentFeed->author[0]->name->text);
        $this->assertEquals('http://www.youtube.com/', $commentFeed->author[0]->uri->text);
        $this->assertEquals(100, $commentFeed->totalResults->text);
        $this->assertEquals(1, $commentFeed->startIndex->text);
        $this->assertEquals(4, $commentFeed->itemsPerPage->text);
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

    public function testEmptyCommentFeedToAndFromStringShouldMatch() {
        $entryXml = $this->feed->saveXML();
        $newCommentFeed = new Zend_Gdata_YouTube_CommentFeed();
        $newCommentFeed->transferFromXML($entryXml);
        $newCommentFeedXml = $newCommentFeed->saveXML();
        $this->assertTrue($entryXml == $newCommentFeedXml);
    }

    public function testSamplePropertiesAreCorrect () {
        $this->feed->transferFromXML($this->feedText);
        $this->verifyAllSamplePropertiesAreCorrect($this->feed);
    }

    public function testConvertCommentFeedToAndFromString() {
        $this->feed->transferFromXML($this->feedText);
        $entryXml = $this->feed->saveXML();
        $newCommentFeed = new Zend_Gdata_YouTube_CommentFeed();
        $newCommentFeed->transferFromXML($entryXml);
        $this->verifyAllSamplePropertiesAreCorrect($newCommentFeed);
        $newCommentFeedXml = $newCommentFeed->saveXML();
        $this->assertEquals($entryXml, $newCommentFeedXml);
    }

}
