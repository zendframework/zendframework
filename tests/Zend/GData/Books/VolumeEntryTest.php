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
class VolumeEntryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->entryText = file_get_contents(
                'Zend/GData/Books/_files/VolumeEntryDataSample1.xml',
                true);
        $this->entry = new Books\VolumeEntry();
    }

    private function verifySamplePropertiesAreCorrect ($volumeEntry) {
        $this->assertEquals('http://www.google.com/books/feeds/volumes/Mfer_MFwQrkC',
            $volumeEntry->id->text);
        $this->assertEquals('2008-10-07T15:28:15.000Z', $volumeEntry->updated->text);
        $this->assertEquals('http://schemas.google.com/g/2005#kind', $volumeEntry->category[0]->scheme);
        $this->assertEquals('http://schemas.google.com/books/2008#volume', $volumeEntry->category[0]->term);
        $this->assertEquals('http://bks1.books.google.com/books?id=Mfer_MFwQrkC&printsec=frontcover&img=1&zoom=5&sig=ACfU3U1o90VpMryPI7WKSyIhmAWdC1uDtw&source=gbs_gdata', $volumeEntry->getThumbnailLink()->href);
        $this->assertEquals('http://books.google.com/books?id=Mfer_MFwQrkC&ie=ISO-8859-1&source=gbs_gdata', $volumeEntry->getInfoLink()->href);
        $this->assertEquals(null, $volumeEntry->getPreviewLink());
        $creators = $volumeEntry->getCreators();
        $this->assertEquals('William Shakespeare', $creators[0]->text);
        $titles = $volumeEntry->getTitles();
        $this->assertEquals('Hamlet', $titles[0]->text);
        $dates = $volumeEntry->getDates();
        $this->assertEquals('2002-02', $dates[0]->text);
        $identifiers = $volumeEntry->getIdentifiers();
        $this->assertEquals('Mfer_MFwQrkC', $identifiers[0]->text);
        $this->assertEquals('ISBN:0198320493', $identifiers[1]->text);
        $languages = $volumeEntry->getLanguages();
        $this->assertEquals('en', $languages[0]->text);
        $formats = $volumeEntry->getFormats();
        $this->assertEquals('178 pages', $formats[0]->text);
        $publishers = $volumeEntry->getPublishers();
        $this->assertEquals('Oxford University Press', $publishers[0]->text);
        $subjects = $volumeEntry->getSubjects();
        $this->assertEquals('Denmark', $subjects[0]->text);
        $this->assertEquals(null, $volumeEntry->getPreviewLink());
        $this->assertEquals('http://www.google.com/books/feeds/users/me/volumes', $volumeEntry->getAnnotationLink()->href);
        $this->assertEquals('http://books.google.com/books?id=Mfer_MFwQrkC&ie=ISO-8859-1&source=gbs_gdata', $volumeEntry->getInfoLink()->href);
        $this->assertEquals('http://bks1.books.google.com/books?id=Mfer_MFwQrkC&printsec=frontcover&img=1&zoom=5&sig=ACfU3U1o90VpMryPI7WKSyIhmAWdC1uDtw&source=gbs_gdata', $volumeEntry->getThumbnailLink()->href);
        $this->assertEquals('Denmark', $subjects[0]->text);
        $this->assertEquals('http://schemas.google.com/books/2008#view_partial', $volumeEntry->getViewability()->value);
        $this->assertEquals('Mfer_MFwQrkC', $volumeEntry->getVolumeId());
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

    public function testEmptyVolumeEntryToAndFromStringShouldMatch() {
        $entryXml = $this->entry->saveXML();
        $newVolumeEntry = new Books\VolumeEntry();
        $newVolumeEntry->transferFromXML($entryXml);
        $newVolumeEntryXml = $newVolumeEntry->saveXML();
        $this->assertEquals($entryXml, $newVolumeEntryXml);
    }

    public function testSamplePropertiesAreCorrect () {
        $this->entry->transferFromXML($this->entryText);
        $this->verifySamplePropertiesAreCorrect($this->entry);
    }

    public function testConvertVolumeEntryToAndFromString() {
        $this->entry->transferFromXML($this->entryText);
        $entryXml = $this->entry->saveXML();
        $newVolumeEntry = new Books\VolumeEntry();
        $newVolumeEntry->transferFromXML($entryXml);
        $this->verifySamplePropertiesAreCorrect($newVolumeEntry);
        $newVolumeEntryXml = $newVolumeEntry->saveXML();
        $this->assertEquals($entryXml, $newVolumeEntryXml);
    }

}
