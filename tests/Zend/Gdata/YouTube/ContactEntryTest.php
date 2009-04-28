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

require_once 'Zend/Gdata/YouTube/ContactEntry.php';
require_once 'Zend/Gdata/YouTube.php';

/**
 * @package Zend_Gdata_App
 * @subpackage UnitTests
 */
class Zend_Gdata_YouTube_ContactEntryTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->entryText = file_get_contents(
                'Zend/Gdata/YouTube/_files/ContactEntryDataSample1.xml',
                true);
        $this->entry = new Zend_Gdata_YouTube_ContactEntry();
    }

    private function verifyAllSamplePropertiesAreCorrect ($contactEntry) {
        $this->assertEquals('http://gdata.youtube.com/feeds/users/davidchoimusic/contacts/testuser',
            $contactEntry->id->text);
        $this->assertEquals('2007-09-21T02:44:41.134Z', $contactEntry->updated->text);
        $this->assertEquals('http://schemas.google.com/g/2005#kind', $contactEntry->category[0]->scheme);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#friend', $contactEntry->category[0]->term);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007/contact.cat', $contactEntry->category[1]->scheme);
        $this->assertEquals('Friends', $contactEntry->category[1]->term);
        $this->assertEquals('text', $contactEntry->title->type);
        $this->assertEquals('testuser', $contactEntry->title->text);;
        $this->assertEquals('self', $contactEntry->getLink('self')->rel);
        $this->assertEquals('application/atom+xml', $contactEntry->getLink('self')->type);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/davidchoimusic/contacts/testuser', $contactEntry->getLink('self')->href);
        $this->assertEquals('davidchoimusic', $contactEntry->author[0]->name->text);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/davidchoimusic', $contactEntry->author[0]->uri->text);
        $this->assertEquals('testuser', $contactEntry->username->text);
        $this->assertEquals('accepted', $contactEntry->status->text);
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

    public function testEmptyContactEntryToAndFromStringShouldMatch() {
        $entryXml = $this->entry->saveXML();
        $newContactEntry = new Zend_Gdata_YouTube_ContactEntry();
        $newContactEntry->transferFromXML($entryXml);
        $newContactEntryXml = $newContactEntry->saveXML();
        $this->assertTrue($entryXml == $newContactEntryXml);
    }

    public function testSamplePropertiesAreCorrect () {
        $this->entry->transferFromXML($this->entryText);
        $this->verifyAllSamplePropertiesAreCorrect($this->entry);
    }

    public function testConvertContactEntryToAndFromString() {
        $this->entry->transferFromXML($this->entryText);
        $entryXml = $this->entry->saveXML();
        $newContactEntry = new Zend_Gdata_YouTube_ContactEntry();
        $newContactEntry->transferFromXML($entryXml);
        $this->verifyAllSamplePropertiesAreCorrect($newContactEntry);
        $newContactEntryXml = $newContactEntry->saveXML();
        $this->assertEquals($entryXml, $newContactEntryXml);
    }

}
