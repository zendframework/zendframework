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
 * @version    $Id:$
 */

require_once 'Zend/Gdata/Gapps/MemberEntry.php';
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
class Zend_Gdata_Gapps_MemberEntryTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->entryText = file_get_contents(
                'Zend/Gdata/Gapps/_files/MemberEntryDataSample1.xml',
                true);
        $this->entry = new Zend_Gdata_Gapps_MemberEntry();
    }

    private function verifyAllSamplePropertiesAreCorrect ($memberEntry) {
        $this->assertEquals('https://www.google.com/a/feeds/group/2.0/example.com/us-sales/member/suejones%40example.com',
            $memberEntry->id->text);
        $this->assertEquals('1970-01-01T00:00:00.000Z', $memberEntry->updated->text);
        $this->assertEquals('self', $memberEntry->getLink('self')->rel);
        $this->assertEquals('application/atom+xml', $memberEntry->getLink('self')->type);
        $this->assertEquals('https://www.google.com/a/feeds/group/2.0/example.com/us-sales/member/suejones%40example.com', $memberEntry->getLink('self')->href);
        $this->assertEquals('edit', $memberEntry->getLink('edit')->rel);
        $this->assertEquals('application/atom+xml', $memberEntry->getLink('edit')->type);
        $this->assertEquals('https://www.google.com/a/feeds/group/2.0/example.com/us-sales/member/suejones%40example.com', $memberEntry->getLink('edit')->href);
        $this->assertEquals('memberId', $memberEntry->property[0]->name);
        $this->assertEquals('suejones@example.com', $memberEntry->property[0]->value);
        $this->assertEquals('memberType', $memberEntry->property[1]->name);
        $this->assertEquals('User', $memberEntry->property[1]->value);
        $this->assertEquals('directMember', $memberEntry->property[2]->name);
        $this->assertEquals('true', $memberEntry->property[2]->value);
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

    public function testEmptyMemberEntryToAndFromStringShouldMatch() {
        $entryXml = $this->entry->saveXML();
        $newMemberEntry = new Zend_Gdata_Gapps_MemberEntry();
        $newMemberEntry->transferFromXML($entryXml);
        $newMemberEntryXml = $newMemberEntry->saveXML();
        $this->assertTrue($entryXml == $newMemberEntryXml);
    }

    public function testSamplePropertiesAreCorrect () {
        $this->entry->transferFromXML($this->entryText);
        $this->verifyAllSamplePropertiesAreCorrect($this->entry);
    }

    public function testConvertMemberEntryToAndFromString() {
        $this->entry->transferFromXML($this->entryText);
        $entryXml = $this->entry->saveXML();
        $newMemberEntry = new Zend_Gdata_Gapps_MemberEntry();
        $newMemberEntry->transferFromXML($entryXml);
        $this->verifyAllSamplePropertiesAreCorrect($newMemberEntry);
        $newMemberEntryXml = $newMemberEntry->saveXML();
        $this->assertEquals($entryXml, $newMemberEntryXml);
    }

}
