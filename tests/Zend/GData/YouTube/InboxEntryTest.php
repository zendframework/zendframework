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

/**
 * @namespace
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
class InboxEntryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->entryText = file_get_contents(
                'Zend/GData/YouTube/_files/InboxEntryDataSample1.xml',
                true);
        $this->v2entryText = file_get_contents(
                'Zend/GData/YouTube/_files/' .
                'InboxEntryDataSampleV2.xml',
                true);

        $this->entry = new YouTube\InboxEntry();
    }

    private function verifyAllSamplePropertiesAreCorrect ($InboxEntry) {
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/api/users/andyland74/' .
            'inbox/ffb9a5f32cd5f55',
            $InboxEntry->id->text);
        $this->assertEquals('http://schemas.google.com/g/2005#kind',
            $InboxEntry->category[0]->scheme);
        $this->assertEquals(
            'http://gdata.youtube.com/schemas/2007#videoMessage',
            $InboxEntry->category[0]->term);
        $this->assertEquals('andyland74sFriend sent you a video!',
            $InboxEntry->title->text);
        $this->assertEquals('self',
            $InboxEntry->getLink('self')->rel);
        $this->assertEquals('application/atom+xml',
            $InboxEntry->getLink('self')->type);
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/api/users/andyland74/' .
            'inbox/ffb9a5f32cd5f55',
            $InboxEntry->getLink('self')->href);
        $this->assertEquals('andyland74sFriend',
            $InboxEntry->author[0]->name->text);
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/api/users/andyland74sFriend',
            $InboxEntry->author[0]->uri->text);
        $this->assertEquals(
            'Check out this video!',
            $InboxEntry->getDescription()->text);
    }

    public function verifyAllSamplePropertiesAreCorrectV2(
        $InboxEntry) {
        $this->assertEquals(
            'tag:youtube,2008:user:andyland74:inbox:D_uaXzLRX1U',
            $InboxEntry->id->text);
        $this->assertEquals('2008-06-10T13:55:32.000-07:00',
            $InboxEntry->published->text);
        $this->assertEquals('2008-06-10T13:55:32.000-07:00',
            $InboxEntry->updated->text);
        $this->assertEquals(
            'http://schemas.google.com/g/2005#kind',
            $InboxEntry->category[0]->scheme);
        $this->assertEquals(
            'http://gdata.youtube.com/schemas/2007#videoMessage',
            $InboxEntry->category[0]->term);
        $this->assertEquals(
            'http://gdata.youtube.com/schemas/2007/keywords.cat',
            $InboxEntry->category[1]->scheme);
        $this->assertEquals(
            'surfing',
            $InboxEntry->category[1]->term);
        $this->assertEquals(
            'http://gdata.youtube.com/schemas/2007/categories.cat',
            $InboxEntry->category[2]->scheme);
        $this->assertEquals(
            'People',
            $InboxEntry->category[2]->term);
        $this->assertEquals('self',
            $InboxEntry->getLink('self')->rel);
        $this->assertEquals('application/atom+xml',
            $InboxEntry->getLink('self')->type);
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/api/users/andyland74/' .
            'inbox/ffb9a5f32cd5f55?v=2',
            $InboxEntry->getLink('self')->href);
        $this->assertEquals('related',
            $InboxEntry->getLink('related')->rel);
        $this->assertEquals('application/atom+xml',
            $InboxEntry->getLink('related')->type);
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/api/videos/jXE6G9CYcJs?v=2',
            $InboxEntry->getLink('related')->href);
        $this->assertEquals('alternate',
            $InboxEntry->getLink('alternate')->rel);
        $this->assertEquals('text/html',
            $InboxEntry->getLink('alternate')->type);
        $this->assertEquals(
            'http://www.youtube.com/watch?v=jXE6G9CYcJs',
            $InboxEntry->getLink('alternate')->href);
        $this->assertEquals('andyland74sFriend',
            $InboxEntry->author[0]->name->text);
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/api/users/andyland74sFriend',
            $InboxEntry->author[0]->uri->text);
        $this->assertEquals(877, $InboxEntry->getRating()->numRaters);
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/api/videos/jXE6G9CYcJs/comments',
            $InboxEntry->getComments()->getFeedLink()->getHref());
        $this->assertEquals(286355,
            $InboxEntry->getStatistics()->getViewCount());
    }

    public function testEmptyEntryShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->entry->extensionElements));
        $this->assertEquals(0, count($this->entry->extensionElements));
    }

    public function testEmptyEntryShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->entry->extensionAttributes));
        $this->assertEquals(0, count($this->entry->extensionAttributes));
    }

    public function testSampleEntryShouldHaveNoExtensionElements() {
        $this->entry->transferFromXML($this->entryText);
        $this->assertTrue(is_array($this->entry->extensionElements));
        $this->assertEquals(0, count($this->entry->extensionElements));
    }

    public function testSampleEntryShouldHaveNoExtensionAttributes() {
        $this->entry->transferFromXML($this->entryText);
        $this->assertTrue(is_array($this->entry->extensionAttributes));
        $this->assertEquals(0, count($this->entry->extensionAttributes));
    }

    public function testSampleEntryShouldHaveNoExtensionElementsV2() {
        $this->entry->setMajorProtocolVersion(2);
        $this->entry->transferFromXML($this->v2entryText);
        $this->assertTrue(is_array($this->entry->extensionElements));
        $this->assertEquals(0, count($this->entry->extensionElements));
    }

    public function testSampleEntryShouldHaveNoExtensionAttributesV2() {
        $this->entry->setMajorProtocolVersion(2);
        $this->entry->transferFromXML($this->v2entryText);
        $this->assertTrue(is_array($this->entry->extensionAttributes));
        $this->assertEquals(0, count($this->entry->extensionAttributes));
    }

    public function testEmptyInboxEntryToAndFromStringShouldMatch() {
        $entryXml = $this->entry->saveXML();
        $newInboxEntry = new YouTube\InboxEntry();
        $newInboxEntry->transferFromXML($entryXml);
        $newInboxEntryXml = $newInboxEntry->saveXML();
        $this->assertTrue($entryXml == $newInboxEntryXml);
    }

    public function testEmptyInboxEntryToAndFromStringShouldMatchV2() {
        $this->entry->transferFromXML($this->v2entryText);
        $entryXml = $this->entry->saveXML();
        $newInboxEntry = new YouTube\InboxEntry();
        $newInboxEntry->transferFromXML($entryXml);
        $newInboxEntry->setMajorProtocolVersion(2);
        $newInboxEntryXml = $newInboxEntry->saveXML();
        $this->assertTrue($entryXml == $newInboxEntryXml);
    }

    public function testSamplePropertiesAreCorrect () {
        $this->entry->transferFromXML($this->entryText);
        $this->verifyAllSamplePropertiesAreCorrect($this->entry);
    }

    public function testSamplePropertiesAreCorrectV2 () {
        $this->entry->setMajorProtocolVersion(2);
        $this->entry->transferFromXML($this->v2entryText);
        $this->verifyAllSamplePropertiesAreCorrectV2($this->entry);
    }

    public function testConvertInboxEntryToAndFromString() {
        $this->entry->transferFromXML($this->entryText);
        $entryXml = $this->entry->saveXML();
        $newInboxEntry = new YouTube\InboxEntry();
        $newInboxEntry->transferFromXML($entryXml);
        $this->verifyAllSamplePropertiesAreCorrect($newInboxEntry);
        $newInboxEntryXml = $newInboxEntry->saveXML();
        $this->assertEquals($entryXml, $newInboxEntryXml);
    }

}
