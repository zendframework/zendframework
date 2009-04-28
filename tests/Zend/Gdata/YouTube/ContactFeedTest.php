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

require_once 'Zend/Gdata/YouTube/ContactFeed.php';
require_once 'Zend/Gdata/YouTube.php';

/**
 * @package Zend_Gdata_App
 * @subpackage UnitTests
 */
class Zend_Gdata_YouTube_ContactFeedTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->feedText = file_get_contents(
                'Zend/Gdata/YouTube/_files/ContactFeedDataSample1.xml',
                true);
        $this->feed = new Zend_Gdata_YouTube_ContactFeed();
    }

    private function verifyAllSamplePropertiesAreCorrect ($contactFeed) {
        $this->assertEquals('http://gdata.youtube.com/feeds/users/davidchoimusic/contacts',
            $contactFeed->id->text);
        $this->assertEquals('2007-09-21T02:44:41.135Z', $contactFeed->updated->text);
        $this->assertEquals('http://schemas.google.com/g/2005#kind', $contactFeed->category[0]->scheme);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#friend', $contactFeed->category[0]->term);
        $this->assertEquals('http://www.youtube.com/img/pic_youtubelogo_123x63.gif', $contactFeed->logo->text);
        $this->assertEquals('text', $contactFeed->title->type);
        $this->assertEquals('davidchoimusic\'s Contacts', $contactFeed->title->text);;
        $this->assertEquals('self', $contactFeed->getLink('self')->rel);
        $this->assertEquals('application/atom+xml', $contactFeed->getLink('self')->type);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/davidchoimusic/contacts?start-index=1&max-results=5', $contactFeed->getLink('self')->href);
        $this->assertEquals('davidchoimusic', $contactFeed->author[0]->name->text);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/davidchoimusic', $contactFeed->author[0]->uri->text);
        $this->assertEquals(1558, $contactFeed->totalResults->text);
        $this->assertEquals(1, $contactFeed->startIndex->text);
        $this->assertEquals(5, $contactFeed->itemsPerPage->text);
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

    public function testEmptyContactFeedToAndFromStringShouldMatch() {
        $entryXml = $this->feed->saveXML();
        $newContactFeed = new Zend_Gdata_YouTube_ContactFeed();
        $newContactFeed->transferFromXML($entryXml);
        $newContactFeedXml = $newContactFeed->saveXML();
        $this->assertTrue($entryXml == $newContactFeedXml);
    }

    public function testSamplePropertiesAreCorrect () {
        $this->feed->transferFromXML($this->feedText);
        $this->verifyAllSamplePropertiesAreCorrect($this->feed);
    }

    public function testConvertContactFeedToAndFromString() {
        $this->feed->transferFromXML($this->feedText);
        $entryXml = $this->feed->saveXML();
        $newContactFeed = new Zend_Gdata_YouTube_ContactFeed();
        $newContactFeed->transferFromXML($entryXml);
        $this->verifyAllSamplePropertiesAreCorrect($newContactFeed);
        $newContactFeedXml = $newContactFeed->saveXML();
        $this->assertEquals($entryXml, $newContactFeedXml);
    }

}
