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
 * @package    Zend_GData_GApps
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\GData\GApps;
use Zend\GData\GApps;
use Zend\GData\Extension;

/**
 * @category   Zend
 * @package    Zend_GData_GApps
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_GApps
 */
class EmailListEntryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->entryText = file_get_contents(
                'Zend/GData/GApps/_files/EmailListEntryDataSample1.xml',
                true);
        $this->entry = new GApps\EmailListEntry();
    }

    private function verifyAllSamplePropertiesAreCorrect ($emailListEntry) {
        $this->assertEquals('https://apps-apis.google.com/a/feeds/example.com/emailList/2.0/us-sales',
            $emailListEntry->id->text);
        $this->assertEquals('1970-01-01T00:00:00.000Z', $emailListEntry->updated->text);
        $this->assertEquals('http://schemas.google.com/g/2005#kind', $emailListEntry->category[0]->scheme);
        $this->assertEquals('http://schemas.google.com/apps/2006#emailList', $emailListEntry->category[0]->term);
        $this->assertEquals('text', $emailListEntry->title->type);
        $this->assertEquals('us-sales', $emailListEntry->title->text);
        $this->assertEquals('self', $emailListEntry->getLink('self')->rel);
        $this->assertEquals('application/atom+xml', $emailListEntry->getLink('self')->type);
        $this->assertEquals('https://apps-apis.google.com/a/feeds/example.com/emailList/2.0/us-sales', $emailListEntry->getLink('self')->href);
        $this->assertEquals('edit', $emailListEntry->getLink('edit')->rel);
        $this->assertEquals('application/atom+xml', $emailListEntry->getLink('edit')->type);
        $this->assertEquals('https://apps-apis.google.com/a/feeds/example.com/emailList/2.0/us-sales', $emailListEntry->getLink('edit')->href);
        $this->assertEquals('us-sales', $emailListEntry->emailList->name);
        $this->assertEquals('http://schemas.google.com/apps/2006#emailList.recipients', $emailListEntry->getFeedLink('http://schemas.google.com/apps/2006#emailList.recipients')->rel);
        $this->assertEquals('http://apps-apis.google.com/a/feeds/example.com/emailList/2.0/us-sales/recipient/', $emailListEntry->getFeedLink('http://schemas.google.com/apps/2006#emailList.recipients')->href);
    }

    public function testEmptyEntryShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->entry->extensionElements));
        $this->assertTrue(count($this->entry->extensionElements) == 0);
    }

    public function testEmptyEntryShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->entry->extensionAttributes));
        $this->assertTrue(count($this->entry->extensionAttributes) == 0);
    }

    public function testSampleEntryShouldHaveNoExtensionElements() {
        $this->entry->transferFromXML($this->entryText);
        $this->assertTrue(is_array($this->entry->extensionElements));
        $this->assertTrue(count($this->entry->extensionElements) == 0);
    }

    public function testSampleEntryShouldHaveNoExtensionAttributes() {
        $this->entry->transferFromXML($this->entryText);
        $this->assertTrue(is_array($this->entry->extensionAttributes));
        $this->assertTrue(count($this->entry->extensionAttributes) == 0);
    }

    public function testEmptyEmailListEntryToAndFromStringShouldMatch() {
        $entryXml = $this->entry->saveXML();
        $newEmailListEntry = new GApps\EmailListEntry();
        $newEmailListEntry->transferFromXML($entryXml);
        $newEmailListEntryXml = $newEmailListEntry->saveXML();
        $this->assertTrue($entryXml == $newEmailListEntryXml);
    }

    public function testGetFeedLinkReturnsAllStoredEntriesWhenUsedWithNoParameters() {
        // Prepare test data
        $entry1 = new Extension\FeedLink();
        $entry1->rel = "first";
        $entry1->href= "foo";
        $entry2 = new Extension\FeedLink();
        $entry2->rel = "second";
        $entry2->href= "bar";
        $data = array($entry1, $entry2);

        // Load test data and run test
        $this->entry->feedLink = $data;
        $this->assertEquals(2, count($this->entry->feedLink));
    }

    public function testGetFeedLinkCanReturnEntriesByRelValue() {
        // Prepare test data
        $entry1 = new Extension\FeedLink();
        $entry1->rel = "first";
        $entry1->href= "foo";
        $entry2 = new Extension\FeedLink();
        $entry2->rel = "second";
        $entry2->href= "bar";
        $data = array($entry1, $entry2);

        // Load test data and run test
        $this->entry->feedLink = $data;
        $this->assertEquals($entry1, $this->entry->getFeedLink('first'));
        $this->assertEquals($entry2, $this->entry->getFeedLink('second'));
    }

    public function testSamplePropertiesAreCorrect () {
        $this->entry->transferFromXML($this->entryText);
        $this->verifyAllSamplePropertiesAreCorrect($this->entry);
    }

    public function testConvertEmailListEntryToAndFromString() {
        $this->entry->transferFromXML($this->entryText);
        $entryXml = $this->entry->saveXML();
        $newEmailListEntry = new GApps\EmailListEntry();
        $newEmailListEntry->transferFromXML($entryXml);
        $this->verifyAllSamplePropertiesAreCorrect($newEmailListEntry);
        $newEmailListEntryXml = $newEmailListEntry->saveXML();
        $this->assertEquals($entryXml, $newEmailListEntryXml);
    }

}
