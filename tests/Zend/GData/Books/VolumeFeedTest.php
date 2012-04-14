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
 * @package    Zend_GData_Books
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\GData\Books;
use Zend\GData\Books;

/**
 * Test helper
 */


/**
 * @category   Zend
 * @package    Zend_GData_Books
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_Books
 */
class VolumeFeedTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->feedText = file_get_contents(
                'Zend/GData/Books/_files/VolumeFeedDataSample1.xml',
                true);
        $this->feed = new Books\VolumeFeed();
    }

    private function verifyAllSamplePropertiesAreCorrect ($volumeFeed) {
        $this->assertEquals('http://www.google.com/books/feeds/volumes',
            $volumeFeed->id->text);
        $this->assertEquals('2008-10-07T16:41:52.000Z', $volumeFeed->updated->text);
        $this->assertEquals('http://schemas.google.com/g/2005#kind', $volumeFeed->category[0]->scheme);
        $this->assertEquals('http://schemas.google.com/books/2008#volume', $volumeFeed->category[0]->term);
        $this->assertEquals('text', $volumeFeed->title->type);
        $this->assertEquals('Search results for Hamlet', $volumeFeed->title->text);
        $this->assertEquals('self', $volumeFeed->getLink('self')->rel);
        $this->assertEquals('application/atom+xml', $volumeFeed->getLink('self')->type);
        $this->assertEquals('http://www.google.com/books/feeds/volumes?q=Hamlet&start-index=3&max-results=5', $volumeFeed->getLink('self')->href);
        $this->assertEquals('Google Books Search', $volumeFeed->author[0]->name->text);
        $this->assertEquals('http://www.google.com', $volumeFeed->author[0]->uri->text);
        $this->assertEquals(512, $volumeFeed->totalResults->text);
        $this->assertEquals(3, $volumeFeed->startIndex->text);
        $this->assertEquals(5, $volumeFeed->itemsPerPage->text);
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

    public function testEmptyVolumeFeedToAndFromStringShouldMatch() {
        $entryXml = $this->feed->saveXML();
        $newVolumeFeed = new Books\VolumeFeed();
        $newVolumeFeed->transferFromXML($entryXml);
        $newVolumeFeedXml = $newVolumeFeed->saveXML();
        $this->assertEquals($entryXml, $newVolumeFeedXml);
    }

    public function testSamplePropertiesAreCorrect () {
        $this->feed->transferFromXML($this->feedText);
        $this->verifyAllSamplePropertiesAreCorrect($this->feed);
    }

    public function testConvertVolumeFeedToAndFromString() {
        $this->feed->transferFromXML($this->feedText);
        $entryXml = $this->feed->saveXML();
        $newVolumeFeed = new Books\VolumeFeed();
        $newVolumeFeed->transferFromXML($entryXml);
        $this->verifyAllSamplePropertiesAreCorrect($newVolumeFeed);
        $newVolumeFeedXml = $newVolumeFeed->saveXML();
        $this->assertEquals($entryXml, $newVolumeFeedXml);
    }

}
