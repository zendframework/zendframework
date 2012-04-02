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
class CommentFeedTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->feedText = file_get_contents(
                'Zend/GData/YouTube/_files/CommentFeedDataSample1.xml',
                true);
        $this->feed = new YouTube\CommentFeed();
    }

    private function verifyAllSamplePropertiesAreCorrect ($commentFeed) {
        $this->assertEquals('http://gdata.youtube.com/feeds/videos/Lnio-pqLPgg/comments',
            $commentFeed->id->text);
        $this->assertEquals('2007-09-21T02:32:55.032Z', $commentFeed->updated->text);
        $this->assertEquals('http://schemas.google.com/g/2005#kind', $commentFeed->category[0]->scheme);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#comment', $commentFeed->category[0]->term);
        $this->assertEquals('http://www.youtube.com/img/pic_youtubelogo_123x63.gif', $commentFeed->logo->text);
        $this->assertEquals('text', $commentFeed->title->type);
        $this->assertEquals('Comments on \'"That Girl" - Original Song - Acoustic Version\'', $commentFeed->title->text);
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
        $newCommentFeed = new YouTube\CommentFeed();
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
        $newCommentFeed = new YouTube\CommentFeed();
        $newCommentFeed->transferFromXML($entryXml);
        $this->verifyAllSamplePropertiesAreCorrect($newCommentFeed);
        $newCommentFeedXml = $newCommentFeed->saveXML();
        $this->assertEquals($entryXml, $newCommentFeedXml);
    }

}
