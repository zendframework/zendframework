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
 * @package    Zend_Gdata_Gapps
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

require_once 'Zend/Gdata/Gapps/UserEntry.php';
require_once 'Zend/Gdata/Gapps.php';

/**
 * @category   Zend
 * @package    Zend_Gdata_Gapps
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Gdata
 * @group      Zend_Gdata_Gapps
 */
class Zend_Gdata_Gapps_UserEntryTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->entryText = file_get_contents(
                'Zend/Gdata/Gapps/_files/UserEntryDataSample1.xml',
                true);
        $this->entry = new Zend_Gdata_Gapps_UserEntry();
    }

    private function verifyAllSamplePropertiesAreCorrect ($userEntry) {
        $this->assertEquals('https://apps-apis.google.com/a/feeds/example.com/user/2.0/SusanJones',
            $userEntry->id->text);
        $this->assertEquals('1970-01-01T00:00:00.000Z', $userEntry->updated->text);
        $this->assertEquals('http://schemas.google.com/g/2005#kind', $userEntry->category[0]->scheme);
        $this->assertEquals('http://schemas.google.com/apps/2006#user', $userEntry->category[0]->term);
        $this->assertEquals('text', $userEntry->title->type);
        $this->assertEquals('SusanJones', $userEntry->title->text);;
        $this->assertEquals('self', $userEntry->getLink('self')->rel);
        $this->assertEquals('application/atom+xml', $userEntry->getLink('self')->type);
        $this->assertEquals('https://apps-apis.google.com/a/feeds/example.com/user/2.0/SusanJones', $userEntry->getLink('self')->href);
        $this->assertEquals('edit', $userEntry->getLink('edit')->rel);
        $this->assertEquals('application/atom+xml', $userEntry->getLink('edit')->type);
        $this->assertEquals('https://apps-apis.google.com/a/feeds/example.com/user/2.0/SusanJones', $userEntry->getLink('edit')->href);
        $this->assertEquals('SusanJones', $userEntry->login->username);
        $this->assertEquals('Jones', $userEntry->name->familyName);
        $this->assertEquals('Susan', $userEntry->name->givenName);
        $this->assertEquals('http://schemas.google.com/apps/2006#user.nicknames', $userEntry->getFeedLink('http://schemas.google.com/apps/2006#user.nicknames')->rel);
        $this->assertEquals('https://apps-apis.google.com/a/feeds/example.com/nickname/2.0?username=Susy-1321', $userEntry->getFeedLink('http://schemas.google.com/apps/2006#user.nicknames')->href);
        $this->assertEquals('http://schemas.google.com/apps/2006#user.emailLists', $userEntry->getFeedLink('http://schemas.google.com/apps/2006#user.emailLists')->rel);
        $this->assertEquals('https://apps-apis.google.com/a/feeds/example.com/emailList/2.0?recipient=us-sales@example.com', $userEntry->getFeedLink('http://schemas.google.com/apps/2006#user.emailLists')->href);
        $this->assertEquals('2048', $userEntry->quota->limit);
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

    public function testEmptyUserEntryToAndFromStringShouldMatch() {
        $entryXml = $this->entry->saveXML();
        $newUserEntry = new Zend_Gdata_Gapps_UserEntry();
        $newUserEntry->transferFromXML($entryXml);
        $newUserEntryXml = $newUserEntry->saveXML();
        $this->assertTrue($entryXml == $newUserEntryXml);
    }

    public function testGetFeedLinkReturnsAllStoredEntriesWhenUsedWithNoParameters() {
        // Prepare test data
        $entry1 = new Zend_Gdata_Extension_FeedLink();
        $entry1->rel = "first";
        $entry1->href= "foo";
        $entry2 = new Zend_Gdata_Extension_FeedLink();
        $entry2->rel = "second";
        $entry2->href= "bar";
        $data = array($entry1, $entry2);

        // Load test data and run test
        $this->entry->feedLink = $data;
        $this->assertEquals(2, count($this->entry->feedLink));
    }

    public function testGetFeedLinkCanReturnEntriesByRelValue() {
        // Prepare test data
        $entry1 = new Zend_Gdata_Extension_FeedLink();
        $entry1->rel = "first";
        $entry1->href= "foo";
        $entry2 = new Zend_Gdata_Extension_FeedLink();
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

    public function testConvertUserEntryToAndFromString() {
        $this->entry->transferFromXML($this->entryText);
        $entryXml = $this->entry->saveXML();
        $newUserEntry = new Zend_Gdata_Gapps_UserEntry();
        $newUserEntry->transferFromXML($entryXml);
        $this->verifyAllSamplePropertiesAreCorrect($newUserEntry);
        $newUserEntryXml = $newUserEntry->saveXML();
        $this->assertEquals($entryXml, $newUserEntryXml);
    }

}
