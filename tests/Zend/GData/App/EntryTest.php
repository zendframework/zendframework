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
 * @package    Zend_GData_App
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\GData\App;

use Zend\GData;
use Zend\GData\App;
use Zend\GData\App\Extension;
use Zend\Http;
use Zend\Http\Header\Etag;
use Zend\Uri;
use ZendTest\GData\TestAsset;

/**
 * @category   Zend
 * @package    Zend_GData_App
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_App
 */
class EntryTest extends \PHPUnit_Framework_TestCase
{
    public $entryText;
    public $httpEntrySample;
    /** @var \Zend\GData\App */
    public $entry;
    /** @var \Zend\Http\Client\Adapter\AdapterInterface */
    public $adapter;
    /** @var \Zend\GData\HttpClient */
    public $client;
    /** @var App */
    public $service;

    public function setUp()
    {
        $this->entryText = file_get_contents(
                'Zend/GData/App/_files/EntrySample1.xml',
                true);
        $this->httpEntrySample = file_get_contents(
                'Zend/GData/App/_files/EntrySampleHttp1.txt',
                true);
        $this->entry = new App\Entry();

        $this->adapter = new \ZendTest\GData\TestAsset\MockHttpClient();
        $this->client = new GData\HttpClient();
        $this->client->setAdapter($this->adapter);
        $this->service = new App($this->client);
    }

    public function testEmptyEntryShouldHaveEmptyExtensionsList()
    {
        $this->assertTrue(is_array($this->entry->extensionElements));
        $this->assertTrue(count($this->entry->extensionElements) == 0);
    }

    public function testEmptyEntryToAndFromStringShouldMatch()
    {
        $enryXml = $this->entry->saveXML();
        $newEntry = new App\Entry();
        $newEntry->transferFromXML($enryXml);
        $newEntryXml = $newEntry->saveXML();
        $this->assertTrue($enryXml == $newEntryXml);
    }

    public function testConvertEntryToAndFromString()
    {
        $this->entry->transferFromXML($this->entryText);
        $enryXml = $this->entry->saveXML();
        $newEntry = new App\Entry();
        $newEntry->transferFromXML($enryXml);
/*
        $this->assertEquals(1, count($newEntry->entry));
        $this->assertEquals('dive into mark', $newEntry->title->text);
        $this->assertEquals('text', $newEntry->title->type);
        $this->assertEquals('2005-07-31T12:29:29Z', $newEntry->updated->text);
        $this->assertEquals('tag:example.org,2003:3', $newEntry->id->text);
        $this->assertEquals(2, count($newEntry->link));
        $this->assertEquals('http://example.org/',
                $newEntry->getAlternateLink()->href);
        $this->assertEquals('en',
                $newEntry->getAlternateLink()->hrefLang);
        $this->assertEquals('text/html',
                $newEntry->getAlternateLink()->type);
        $this->assertEquals('http://example.org/enry.atom',
                $newEntry->getSelfLink()->href);
        $this->assertEquals('application/atom+xml',
                $newEntry->getSelfLink()->type);
        $this->assertEquals('Copyright (c) 2003, Mark Pilgrim',
                $newEntry->rights->text);
        $entry = $newEntry->entry[0];
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
*/
    }

    public function testCanSetAndGetEtag()
    {
        $data = Etag::fromString("Etag: W/&amp;FooBarBaz&amp;");
        $this->entry->setEtag($data);
        $this->assertEquals($this->entry->getEtag(), $data);
    }

    public function testCanSetAndgetService()
    {
        $data = new App();
        $this->entry->setService($data);
        $this->assertEquals($this->entry->getService(), $data);

        $data = null;
        $this->entry->setService($data);
        $this->assertEquals($this->entry->getService(), $data);
    }

    public function testsetServiceProvidesFluentInterface()
    {
        $result = $this->entry->setService(null);
        $this->assertEquals($this->entry, $result);
    }

    public function testGetHttpClientPullsFromServiceInstance()
    {
        $s = new App();
        $this->entry->setService($s);

        $c = new GData\HttpClient();
        $s->setHttpClient($c);
        $this->assertEquals($this->entry->getHttpClient(),
                $s->getHttpClient());

        $c = new Http\Client();
        $s->setHttpClient($c);
        $this->assertEquals($this->entry->getHttpClient(),
                $s->getHttpClient($c));
    }

    public function testSetHttpClientPushesIntoServiceInstance()
    {
        $s = new App();
        $this->entry->setService($s);

        $c = new GData\HttpClient();
        $this->entry->setHttpClient($c);
        $this->assertEquals(get_class($s->getHttpClient()),
                'Zend\GData\HttpClient');

        $c = new Http\Client();
        $this->entry->setHttpClient($c);
        $this->assertEquals(get_class($s->getHttpClient()),
                'Zend\Http\Client');
    }

    public function testSaveSupportsGDataV2()
    {
        // Prepare mock response
        $this->adapter->setResponse("HTTP/1.1 201 Created");

        // Make sure that we're using protocol v2
        $this->service->setMajorProtocolVersion(2);
        $this->entry->setService($this->service);

        // Set a URL for posting, so that save() will work
        $editLink = new Extension\Link('http://example.com',
                'edit');
        $this->entry->setLink(array($editLink));

        // Perform a (mock) save
        $this->entry->save();

        // Check to make sure that a v2 header was sent
        $headers = $this->adapter->popRequest()->headers;
        $found = false;
        foreach ($headers as $header => $value) {
            if ($header == 'GData-Version' && $value == 2)
                $found = true;
        }
        $this->assertTrue($found,
                'GData-Version header missing or incorrect.');
    }

    public function testDeleteSupportsGDataV2()
    {
        // Prepare mock response
        $this->adapter->setResponse("HTTP/1.1 200 OK");

        // Make sure that we're using protocol v2
        $this->service->setMajorProtocolVersion(2);
        $this->entry->setService($this->service);

        // Set a URL for posting, so that save() will work
        $editLink = new Extension\Link('http://example.com',
                'edit');
        $this->entry->setLink(array($editLink));

        // Perform a (mock) save
        $this->entry->delete();

        // Check to make sure that a v2 header was sent
        $headers = $this->adapter->popRequest()->headers;
        $found = false;
        foreach ($headers as $header => $value) {
            if ($header == 'GData-Version' && $value == 2)
                $found = true;
        }
        $this->assertTrue($found,
                'GData-Version header missing or incorrect.');
    }

    public function testIfMatchHeaderCanBeSetOnSave()
    {
        $etagOverride = 'foo';
        $etag = Etag::fromString('Etag: ABCD1234');
        $this->service->setMajorProtocolVersion(2);
        $this->adapter->setResponse($this->httpEntrySample);
        $entry = $this->service->newEntry();
        $entry->link = array(new Extension\Link(
                'http://www.example.com',
                'edit',
                'application/atom+xml'));
        $entry->setEtag($etag);
        $newEntry = $entry->save(null, null,
                array('If-Match' => $etagOverride));
        $headers = $this->adapter->popRequest()->headers;
        $found = false;
        foreach ($headers as $header => $value) {
            if ($header == 'If-Match' && $value == $etagOverride)
                $found = true;
        }
        $this->assertTrue($found,
                'If-Match header not found or incorrect');
    }

    public function testIfNoneMatchHeaderCanBeSetOnSave()
    {
        $etagOverride = 'foo';
        $etag = Etag::fromString('Etag: ABCD1234');
        $this->service->setMajorProtocolVersion(2);
        $this->adapter->setResponse($this->httpEntrySample);
        $entry = $this->service->newEntry();
        $entry->link = array(new Extension\Link(
                'http://www.example.com',
                'edit',
                'application/atom+xml'));
        $entry->setEtag($etag);
        $newEntry = $entry->save(null, null,
                array('If-None-Match' => $etagOverride));
        $headers = $this->adapter->popRequest()->headers;
        $found = false;
        foreach ($headers as $header => $value) {
            if ($header == 'If-None-Match' && $value == $etagOverride)
                $found = true;
        }
        $this->assertTrue($found,
                'If-None-Match header not found or incorrect');
    }

    public function testCanSetUriOnSave()
    {
        $uri = 'http://example.net:8080/foo/bar';
        $uriObject = new Uri\Http($uri);
        $uriObject->setPort('8080');
        $this->adapter->setResponse($this->httpEntrySample);
        $entry = $this->service->newEntry();
        $newEntry = $entry->save($uri);
        $request = $this->adapter->popRequest();

        $this->assertEquals($uriObject, $request->uri);
    }

    public function testCanSetClassnameOnSave()
    {
        $className = '\Zend\GData\Entry';
        $this->adapter->setResponse($this->httpEntrySample);
        $entry = $this->service->newEntry();
        $entry->link = array(new Extension\Link(
                'http://www.example.com',
                'edit',
                'application/atom+xml'));
        $newEntry = $entry->save(null, $className);
        $this->assertEquals('Zend\GData\Entry', get_class($newEntry));
    }

    public function testIfNoneMatchSetOnReload()
    {
        $etag = Etag::fromString('Etag: ABCD1234');
        $this->adapter->setResponse($this->httpEntrySample);
        $entry = $this->service->newEntry();
        $entry->link = array(new Extension\Link(
                'http://www.example.com',
                'edit',
                'application/atom+xml'));
        $entry->setEtag($etag);
        $newEntry = $entry->reload();
        $headers = $this->adapter->popRequest()->headers;
        $found = false;
        foreach ($headers as $header => $value) {
            if ($header == 'If-None-Match' && $value == $etag->getFieldValue())
                $found = true;
        }
        $this->assertTrue($found, 'If-None-Match header not found or incorrect');
    }

    public function testIfNoneMatchCanBeSetOnReload()
    {
        $etagOverride = 'foo';
        $etag = Etag::fromString('Etag: ABCD1234');
        $this->adapter->setResponse($this->httpEntrySample);
        $entry = $this->service->newEntry();
        $entry->link = array(new Extension\Link(
                'http://www.example.com',
                'edit',
                'application/atom+xml'));
        $entry->setEtag($etag);
        $newEntry = $entry->reload(null, null,
                array('If-None-Match' => $etagOverride));
        $headers = $this->adapter->popRequest()->headers;
        $found = false;
        foreach ($headers as $header => $value) {
            if ($header == 'If-None-Match' && $value == $etagOverride)
                $found = true;
        }
        $this->assertTrue($found,'If-None-Match header not found or incorrect');
    }

    public function testReloadReturnsEntryObject()
    {
        $etag = Etag::fromString('Etag: ABCD1234');
        $this->adapter->setResponse($this->httpEntrySample);
        $entry = $this->service->newEntry();
        $entry->link = array(new Extension\Link(
                'http://www.example.com',
                'edit',
                'application/atom+xml'));
        $entry->setEtag($etag);
        $newEntry = $entry->reload();
        $this->assertEquals('Zend\GData\App\Entry', get_class($newEntry));
    }

    public function testReloadPopulatesEntryObject()
    {
        $etag = Etag::fromString('Etag: ABCD1234');
        $this->adapter->setResponse($this->httpEntrySample);
        $entry = $this->service->newEntry();
        $entry->link = array(new Extension\Link(
                'http://www.example.com',
                'edit',
                'application/atom+xml'));
        $entry->setEtag($etag);
        $newEntry = $entry->reload();
        $this->assertEquals('Hello world', $newEntry->title->text);
    }

    public function testReloadDoesntThrowExceptionIfNoEtag()
    {
        $this->adapter->setResponse($this->httpEntrySample);
        $entry = $this->service->newEntry();
        $entry->link = array(new Extension\Link(
                'http://www.example.com',
                'edit',
                'application/atom+xml'));
        $newEntry = $entry->reload();
        $this->assertEquals('Zend\GData\App\Entry', get_class($newEntry));
    }

    public function testReloadExtractsURIFromEditLink()
    {
        $expectedUri = 'http://www.example.com:81';
        $etag = Etag::fromString('Etag: ABCD1234');
        $this->service->setMajorProtocolVersion(2);
        $this->adapter->setResponse($this->httpEntrySample);
        $entry = $this->service->newEntry();
        $entry->link = array(new Extension\Link(
                $expectedUri,
                'edit',
                'application/atom+xml'));
        $entry->setEtag($etag);
        $newEntry = $entry->reload();
        $requestUri = $this->adapter->popRequest()->uri;
        $expectedUriObject = new Uri\Http($expectedUri);
        $expectedUriObject->setPort('81');
        $this->assertEquals($expectedUriObject, $requestUri);
    }

    public function testReloadAllowsCustomURI()
    {
        $uriOverride = 'http://www.example.org';
        $etag = Etag::fromString('Etag: ABCD1234');
        $this->service->setMajorProtocolVersion(2);
        $this->adapter->setResponse($this->httpEntrySample);
        $entry = $this->service->newEntry();
        $entry->link = array(new Extension\Link(
                'http://www.example.com',
                'edit',
                'application/atom+xml'));
        $entry->setEtag($etag);
        $newEntry = $entry->reload($uriOverride);
        $requestUri = $this->adapter->popRequest()->uri;
        $uriOverrideObject = new Uri\Http($uriOverride);
        $this->assertEquals($uriOverrideObject, $requestUri);
    }

    public function testReloadReturnsNullIfEntryNotModified()
    {
        $etag = Etag::fromString('Etag: ABCD1234');
        $this->service->setMajorProtocolVersion(2);
        $this->adapter->setResponse('HTTP/1.1 304 Not Modified');
        $entry = $this->service->newEntry();
        $entry->link = array(new Extension\Link(
                'http://www.example.com',
                'edit',
                'application/atom+xml'));
        $entry->setEtag($etag);
        $newEntry = $entry->reload();
        $this->assertEquals(null, $newEntry);
    }

    public function testCanSetReloadReturnClassname()
    {
        $className = '\Zend\GData\Entry';
        $etag = Etag::fromString('Etag: ABCD1234');
        $this->service->setMajorProtocolVersion(2);
        $this->adapter->setResponse($this->httpEntrySample);
        $entry = $this->service->newEntry();
        $entry->link = array(new Extension\Link(
                'http://www.example.com',
                'edit',
                'application/atom+xml'));
        $entry->setEtag($etag);
        $newEntry = $entry->reload(null, $className);
        $this->assertEquals('Zend\GData\Entry', get_class($newEntry));
    }

    public function testReloadInheritsClassname()
    {
        $className = '\Zend\GData\Entry';
        $etag = Etag::fromString('Etag: ABCD1234');
        $this->service->setMajorProtocolVersion(2);
        $this->adapter->setResponse($this->httpEntrySample);
        $entry = new $className;
        $entry->setService($this->service);
        $entry->link = array(new Extension\Link(
                'http://www.example.com',
                'edit',
                'application/atom+xml'));
        $entry->setEtag($etag);
        $newEntry = $entry->reload();
        $this->assertEquals('Zend\GData\Entry', get_class($newEntry));
    }

    public function testCanSetMajorProtocolVersion()
    {
        $expectedVersion = 42;
        $entry = $this->service->newEntry();
        $entry->setMajorProtocolVersion($expectedVersion);
        $receivedVersion = $entry->getMajorProtocolVersion();
        $this->assertEquals($expectedVersion, $receivedVersion);
    }

    public function testCanSetMinorProtocolVersion()
    {
        $expectedVersion = 42;
        $entry = $this->service->newEntry();
        $entry->setMinorProtocolVersion($expectedVersion);
        $receivedVersion = $entry->getMinorProtocolVersion();
        $this->assertEquals($expectedVersion, $receivedVersion);
    }

    public function testMajorProtocolVersionCannotBeZero()
    {
        $expectedVersion = 0;
        $entry = $this->service->newEntry();
        $this->setExpectedException('Zend\GData\App\InvalidArgumentException');
        $entry->setMajorProtocolVersion($expectedVersion);
    }

    public function testMajorProtocolVersionCannotBeNegative()
    {
        $expectedVersion = -1;
        $entry = $this->service->newEntry();
        $this->setExpectedException('Zend\GData\App\InvalidArgumentException');
        $entry->setMajorProtocolVersion($expectedVersion);
    }

    public function testMajorProtocolVersionMayBeNull()
    {
        $expectedVersion = null;
        $entry = $this->service->newEntry();
        $entry->setMajorProtocolVersion($expectedVersion);
        $receivedVersion = $entry->getMajorProtocolVersion();
        $this->assertNull($receivedVersion);
    }

    public function testMinorProtocolVersionMayBeZero()
    {
        $expectedVersion = 0;
        $entry = $this->service->newEntry();
        $entry->setMinorProtocolVersion($expectedVersion);
        $receivedVersion = $entry->getMinorProtocolVersion();
        $this->assertEquals($expectedVersion, $receivedVersion);
    }

    public function testMinorProtocolVersionCannotBeNegative()
    {
        $expectedVersion = -1;
        $entry = $this->service->newEntry();
        $this->setExpectedException('Zend\GData\App\InvalidArgumentException');
        $entry->setMinorProtocolVersion($expectedVersion);
    }

    public function testMinorProtocolVersionMayBeNull()
    {
        $expectedVersion = null;
        $entry = $this->service->newEntry();
        $entry->setMinorProtocolVersion($expectedVersion);
        $receivedVersion = $entry->getMinorProtocolVersion();
        $this->assertNull($receivedVersion);
    }

    public function testDefaultMajorProtocolVersionIs1()
    {
        $entry = $this->service->newEntry();
        $this->assertEquals(1, $entry->getMajorProtocolVersion());
    }

    public function testDefaultMinorProtocolVersionIsNull()
    {
        $entry = $this->service->newEntry();
        $this->assertNull($entry->getMinorProtocolVersion());
    }

    public function testLookupNamespaceUsesCurrentVersion()
    {
        $prefix = 'test';
        $v1TestString = 'TEST-v1';
        $v2TestString = 'TEST-v2';

        App\AbstractBase::flushNamespaceLookupCache();
        $entry = $this->service->newEntry();
        $entry->registerNamespace($prefix, $v1TestString, 1, 0);
        $entry->registerNamespace($prefix, $v2TestString, 2, 0);
        $entry->setMajorProtocolVersion(1);
        $result = $entry->lookupNamespace($prefix);
        $this->assertEquals($v1TestString, $result);
        $entry->setMajorProtocolVersion(2);
        $result = $entry->lookupNamespace($prefix);
        $this->assertEquals($v2TestString, $result);
        $entry->setMajorProtocolVersion(null); // Should default to latest
        $result = $entry->lookupNamespace($prefix);
        $this->assertEquals($v2TestString, $result);
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
        $entry = $this->service->newEntry();
        $entry->registerNamespace($prefix, $testString10, 1, 0);
        $entry->registerNamespace($prefix, $testString20, 2, 0);
        $entry->registerNamespace($prefix, $testString11, 1, 1);
        $entry->registerNamespace($prefix, $testString21, 2, 1);
        $entry->registerNamespace($prefix, $testString12, 1, 2);
        $entry->registerNamespace($prefix, $testString22, 2, 2);

        // Assumes default version (1)
        $result = $entry->lookupNamespace($prefix, 1, null);
        $this->assertEquals($testString12, $result);
        $result = $entry->lookupNamespace($prefix, 2, null);
        $this->assertEquals($testString22, $result);
        $result = $entry->lookupNamespace($prefix, 1, 1);
        $this->assertEquals($testString11, $result);
        $result = $entry->lookupNamespace($prefix, 2, 1);
        $this->assertEquals($testString21, $result);
        $result = $entry->lookupNamespace($prefix, null, null);
        $this->assertEquals($testString12, $result);
        $result = $entry->lookupNamespace($prefix, null, 1);
        $this->assertEquals($testString11, $result);

        // Override to retrieve latest version
        $entry->setMajorProtocolVersion(null);
        $result = $entry->lookupNamespace($prefix, null, null);
        $this->assertEquals($testString22, $result);
        $result = $entry->lookupNamespace($prefix, null, 1);
        $this->assertEquals($testString21, $result);
    }

}
