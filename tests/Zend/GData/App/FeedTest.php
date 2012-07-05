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
 * @feed       Zend
 * @category   Zend
 * @package    Zend_GData_App
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\GData\App;

use Zend\GData\App;
use Zend\Http\Header\Etag;

/**
 * @category   Zend
 * @package    Zend_GData_App
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_App
 */
class FeedTest extends \PHPUnit_Framework_TestCase
{

    /** @var App\Feed */
    public $feed;

    public function setUp() {
        $this->feedText = file_get_contents(
                'Zend/GData/App/_files/FeedSample1.xml',
                true);
        $this->feed = new App\Feed();
    }

    public function testEmptyFeedShouldHaveEmptyExtensionsList() {
        $this->assertTrue(is_array($this->feed->extensionElements));
        $this->assertTrue(count($this->feed->extensionElements) == 0);
    }

    public function testEmptyFeedToAndFromStringShouldMatch() {
        $feedXml = $this->feed->saveXML();
        $newFeed = new App\Feed();
        $newFeed->transferFromXML($feedXml);
        $newFeedXml = $newFeed->saveXML();
        $this->assertTrue($feedXml == $newFeedXml);
    }

    public function testConvertFeedToAndFromString() {
        $this->feed->transferFromXML($this->feedText);
        $feedXml = $this->feed->saveXML();
        $newFeed = new App\Feed();
        $newFeed->transferFromXML($feedXml);
        $this->assertEquals(1, count($newFeed->entry));
        $this->assertEquals('dive into mark', $newFeed->title->text);
        $this->assertEquals('text', $newFeed->title->type);
        $this->assertEquals('2005-07-31T12:29:29Z', $newFeed->updated->text);
        $this->assertEquals('tag:example.org,2003:3', $newFeed->id->text);
        $this->assertEquals(2, count($newFeed->link));
        $this->assertEquals('http://example.org/',
                $newFeed->getAlternateLink()->href);
        $this->assertEquals('en',
                $newFeed->getAlternateLink()->hrefLang);
        $this->assertEquals('text/html',
                $newFeed->getAlternateLink()->type);
        $this->assertEquals('http://example.org/feed.atom',
                $newFeed->getSelfLink()->href);
        $this->assertEquals('application/atom+xml',
                $newFeed->getSelfLink()->type);
        $this->assertEquals('Copyright (c) 2003, Mark Pilgrim',
                $newFeed->rights->text);
        $entry = $newFeed->entry[0];
        $this->assertEquals('Atom draft-07 snapshot', $entry->title->text);
        $this->assertEquals('tag:example.org,2003:3.2397',
                $entry->id->text);
        $this->assertEquals('2005-07-31T12:29:29Z', $entry->updated->text);
        $this->assertEquals('2003-12-13T08:29:29-04:00',
                $entry->published->text);
        $this->assertEquals('Mark Pilgrim',
                $entry->author[0]->name->text);
        $this->assertEquals('http://example.org/',
                $entry->author[0]->uri->text);
        $this->assertEquals(2, count($entry->contributor));
        $this->assertEquals('Sam Ruby',
                $entry->contributor[0]->name->text);
        $this->assertEquals('Joe Gregorio',
                $entry->contributor[1]->name->text);
        $this->assertEquals('xhtml', $entry->content->type);
    }

    public function testCanAddIndividualEntries() {
        $this->feed->transferFromXML($this->feedText);
        $this->assertEquals(1, count($this->feed->entry));
        $oldTitle = $this->feed->entry[0]->title->text;
        $newEntry = new App\Entry();
        $newEntry->setTitle(new \Zend\GData\App\Extension\Title("Foo"));
        $this->feed->addEntry($newEntry);
        $this->assertEquals(2, count($this->feed->entry));
        $this->assertEquals($oldTitle, $this->feed->entry[0]->title->text);
        $this->assertEquals("Foo", $this->feed->entry[1]->title->text);
    }

    public function testCanSetAndGetEtag() {
        $data = Etag::fromString("Etag: W/&amp;FooBarBaz&amp;");
        $this->feed->setEtag($data);
        $this->assertEquals($this->feed->getEtag(), $data);
    }

    public function testSetServicePropagatesToChildren() {
        // Setup
        $entries = array(new App\Entry(), new App\Entry());
        foreach ($entries as $entry) {
            $this->feed->addEntry($entry);
        }

        // Set new service instance and test for propagation
        $s = new App();
        $this->feed->setService($s);

        $service = $this->feed->getService();
        if (!is_object($service)) {
            $this->fail('No feed service received');
        }
        $this->assertEquals('Zend\GData\App', get_class($service));

        foreach ($entries as $entry) {
            $service = $entry->getService();
            if (!is_object($service)) {
                $this->fail('No entry service received');
            }
            $this->assertEquals('Zend\GData\App', get_class($service));

        }

        // Set null service instance and test for propagation
        $s = null;
        $this->feed->setService($s);
        $this->assertFalse(is_object($this->feed->getService()));
        foreach ($entries as $entry) {
            $service = $entry->getService();
            $this->assertFalse(is_object($service));
        }
    }

    public function testCanSetMajorProtocolVersion()
    {
        $expectedVersion = 42;
        $this->feed->setMajorProtocolVersion($expectedVersion);
        $receivedVersion = $this->feed->getMajorProtocolVersion();
        $this->assertEquals($expectedVersion, $receivedVersion);
    }

    public function testCanSetMinorProtocolVersion()
    {
        $expectedVersion = 42;
        $this->feed->setMinorProtocolVersion($expectedVersion);
        $receivedVersion = $this->feed->getMinorProtocolVersion();
        $this->assertEquals($expectedVersion, $receivedVersion);
    }

    public function testEntriesInheritFeedVersionOnCreate()
    {
        $major = 98;
        $minor = 12;
        $this->feed->setMajorProtocolVersion($major);
        $this->feed->setMinorProtocolVersion($minor);
        $this->feed->transferFromXML($this->feedText);
        foreach ($this->feed->entries as $entry) {
            $this->assertEquals($major, $entry->getMajorProtocolVersion());
            $this->assertEquals($minor, $entry->getMinorProtocolVersion());
        }
    }

    public function testEntriesInheritFeedVersionOnUpdate()
    {
        $major = 98;
        $minor = 12;
        $this->feed->transferFromXML($this->feedText);
        $this->feed->setMajorProtocolVersion($major);
        $this->feed->setMinorProtocolVersion($minor);
        foreach ($this->feed->entries as $entry) {
            $this->assertEquals($major, $entry->getMajorProtocolVersion());
            $this->assertEquals($minor, $entry->getMinorProtocolVersion());
        }
    }

    public function testDefaultMajorProtocolVersionIs1()
    {
        $this->assertEquals(1, $this->feed->getMajorProtocolVersion());
    }

    public function testDefaultMinorProtocolVersionIsNull()
    {
        $this->assertNull($this->feed->getMinorProtocolVersion());
    }

    public function testLookupNamespaceUsesCurrentVersion()
    {
        $prefix = 'test';
        $v1TestString = 'TEST-v1';
        $v2TestString = 'TEST-v2';

        App\AbstractBase::flushNamespaceLookupCache();
        $feed = $this->feed;
        $feed->registerNamespace($prefix, $v1TestString, 1, 0);
        $feed->registerNamespace($prefix, $v2TestString, 2, 0);
        $feed->setMajorProtocolVersion(1);
        $result = $feed->lookupNamespace($prefix);
        $this->assertEquals($v1TestString, $result);
        $feed->setMajorProtocolVersion(2);
        $result = $feed->lookupNamespace($prefix);
        $this->assertEquals($v2TestString, $result);
        $feed->setMajorProtocolVersion(null); // Should default to latest
        $result = $feed->lookupNamespace($prefix);
    }

    public function testLookupNamespaceObeysParentBehavior()
    {
        $prefix = 'test';
        $testString10 = 'TEST-v1-0';
        $testString20 = 'TEST-v2-0';
        $testString11 = 'TEST-v1-1';
        $testString21 = 'TEST-v2-1';
        $testString12 = 'TEST-v1-2';
        $testString22 = 'TEST-v2-2';

        App\AbstractBase::flushNamespaceLookupCache();
        $feed = $this->feed;
        $feed->registerNamespace($prefix, $testString10, 1, 0);
        $feed->registerNamespace($prefix, $testString20, 2, 0);
        $feed->registerNamespace($prefix, $testString11, 1, 1);
        $feed->registerNamespace($prefix, $testString21, 2, 1);
        $feed->registerNamespace($prefix, $testString12, 1, 2);
        $feed->registerNamespace($prefix, $testString22, 2, 2);

        // Assumes default version (1)
        $result = $feed->lookupNamespace($prefix, 1, null);
        $this->assertEquals($testString12, $result);
        $result = $feed->lookupNamespace($prefix, 2, null);
        $this->assertEquals($testString22, $result);
        $result = $feed->lookupNamespace($prefix, 1, 1);
        $this->assertEquals($testString11, $result);
        $result = $feed->lookupNamespace($prefix, 2, 1);
        $this->assertEquals($testString21, $result);
        $result = $feed->lookupNamespace($prefix, null, null);
        $this->assertEquals($testString12, $result);
        $result = $feed->lookupNamespace($prefix, null, 1);
        $this->assertEquals($testString11, $result);

        // Override to retrieve latest version
        $feed->setMajorProtocolVersion(null);
        $result = $feed->lookupNamespace($prefix, null, null);
        $this->assertEquals($testString22, $result);
        $result = $feed->lookupNamespace($prefix, null, 1);
        $this->assertEquals($testString21, $result);
    }

    /**
     * @group ZF-10242
     */
    public function testCount()
    {
        $feed = new App\Feed();
        $feed->addEntry('foo')
             ->addEntry('bar');
        $this->assertEquals(2, $feed->count());
        $this->assertEquals(2, count($feed));
    }
}
