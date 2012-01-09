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

/**
 * @category   Zend
 * @package    Zend_GData_GApps
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_GApps
 */
class NicknameEntryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->entryText = file_get_contents(
                'Zend/GData/GApps/_files/NicknameEntryDataSample1.xml',
                true);
        $this->entry = new GApps\NicknameEntry();
    }

    private function verifyAllSamplePropertiesAreCorrect ($nicknameEntry) {
        $this->assertEquals('https://apps-apis.google.com/a/feeds/example.com/nickname/2.0/Susy',
            $nicknameEntry->id->text);
        $this->assertEquals('1970-01-01T00:00:00.000Z', $nicknameEntry->updated->text);
        $this->assertEquals('http://schemas.google.com/g/2005#kind', $nicknameEntry->category[0]->scheme);
        $this->assertEquals('http://schemas.google.com/apps/2006#nickname', $nicknameEntry->category[0]->term);
        $this->assertEquals('text', $nicknameEntry->title->type);
        $this->assertEquals('Susy', $nicknameEntry->title->text);
        $this->assertEquals('self', $nicknameEntry->getLink('self')->rel);
        $this->assertEquals('application/atom+xml', $nicknameEntry->getLink('self')->type);
        $this->assertEquals('https://apps-apis.google.com/a/feeds/example.com/nickname/2.0/Susy', $nicknameEntry->getLink('self')->href);
        $this->assertEquals('edit', $nicknameEntry->getLink('edit')->rel);
        $this->assertEquals('application/atom+xml', $nicknameEntry->getLink('edit')->type);
        $this->assertEquals('https://apps-apis.google.com/a/feeds/example.com/nickname/2.0/Susy', $nicknameEntry->getLink('edit')->href);
        $this->assertEquals('Susy', $nicknameEntry->nickname->name);
        $this->assertEquals('SusanJones', $nicknameEntry->login->username);
        $this->assertEquals(false, $nicknameEntry->login->suspended);
        $this->assertEquals(false, $nicknameEntry->login->admin);
        $this->assertEquals(false, $nicknameEntry->login->changePasswordAtNextLogin);
        $this->assertEquals(true, $nicknameEntry->login->agreedToTerms);
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

    public function testEmptyNicknameEntryToAndFromStringShouldMatch() {
        $entryXml = $this->entry->saveXML();
        $newNicknameEntry = new GApps\NicknameEntry();
        $newNicknameEntry->transferFromXML($entryXml);
        $newNicknameEntryXml = $newNicknameEntry->saveXML();
        $this->assertTrue($entryXml == $newNicknameEntryXml);
    }

    public function testSamplePropertiesAreCorrect () {
        $this->entry->transferFromXML($this->entryText);
        $this->verifyAllSamplePropertiesAreCorrect($this->entry);
    }

    public function testConvertNicknameEntryToAndFromString() {
        $this->entry->transferFromXML($this->entryText);
        $entryXml = $this->entry->saveXML();
        $newNicknameEntry = new GApps\NicknameEntry();
        $newNicknameEntry->transferFromXML($entryXml);
        $this->verifyAllSamplePropertiesAreCorrect($newNicknameEntry);
        $newNicknameEntryXml = $newNicknameEntry->saveXML();
        $this->assertEquals($entryXml, $newNicknameEntryXml);
    }

}
