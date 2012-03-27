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
 * @package    Zend_GData_Health
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\GData;
use Zend\GData\Health;
use Zend\GData;
use Zend\GData\Health\Extension;

/**
 * @category   Zend
 * @package    Zend_GData_Health
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_Health
 */
class HealthOnlineTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        if (!constant('TESTS_ZEND_GDATA_ONLINE_ENABLED')) {
            $this->markTestSkipped('Zend_GData online tests are not enabled');
        }
        $this->user = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_EMAIL');
        $this->pass = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_PASSWORD');
        $serviceName = Health::HEALTH_SERVICE_NAME;
        $client = GData\ClientLogin::getHttpClient($this->user, $this->pass, $serviceName);
        $this->health = new Health($client, 'google-MyPHPApp-v1.0');
    }

    private function setupProfileID()
    {
        $profileListFeed = $this->health->getHealthProfileListFeed();
        $profileID = $profileListFeed->entry[0]->getProfileID();
        $this->health->setProfileID($profileID);
    }

    public function testSetProfileID()
    {
        $this->health->setProfileID('123456790');
        $this->assertEquals('123456790', $this->health->getProfileID());
    }

    public function testGetHealthProfileListFeedWithoutUsingClientLogin()
    {
        $client = new GData\HttpClient();
        $this->health = new Health($client);

        try {
            $feed = $this->health->getHealthProfileListFeed();
            $this->fail('Expecting to catch Zend_GData_App_AuthException');
        } catch (\Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('\Zend\GData\App\AuthException'),
                'Expecting Zend_GData_App_AuthException, got '.get_class($e));
        }
    }

    public function testGetHealthProfileFeedWithoutUsingClientLogin()
    {
        try {
            $feed = $this->health->getHealthProfileFeed();
            $this->fail('Expecting to catch Zend_GData_App_AuthException');
        } catch (\Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('\Zend\GData\App\AuthException'),
                'Expecting Zend_GData_App_AuthException, got '.get_class($e));
        }
    }

    public function testUseH9()
    {
        $serviceName = Health::H9_SANDBOX_SERVICE_NAME;
        $client = GData\ClientLogin::getHttpClient($this->user, $this->pass, $serviceName);
        $h9 = new Health($client, 'google-MyPHPApp-v1.0', true);

        $profileListFeed = $h9->getHealthProfileListFeed();
        $profileID = $profileListFeed->entry[0]->getProfileID();
        $h9->setProfileID($profileID);

        // query profile feed
        $feed1 = $h9->getHealthProfileFeed();
        $this->assertTrue($feed1 instanceof Health\ProfileFeed);
        foreach ($feed1->getEntries() as $entry) {
            $this->assertTrue($entry instanceof Health\ProfileEntry);
            $this->assertEquals($entry->getHttpClient(), $feed1->getHttpClient());
        }

        // send CCR
        $subject = "Title of your notice goes here";
        $body = "Notice body can contain <b>html</b> entities";
        $type = "html";
        $ccrXML = file_get_contents('Zend/GData/Health/_files/ccr_notice_sample.xml', true);

        $responseEntry = $h9->sendHealthNotice($subject, $body, $type, $ccrXML);

        $this->assertTrue($responseEntry instanceof Health\ProfileEntry);
        $this->assertEquals($subject, $responseEntry->title->text);
        $this->assertEquals($body, $responseEntry->content->text);
        $this->assertEquals($type, $responseEntry->content->type);
        $this->assertXmlStringEqualsXmlString($responseEntry->getCcr()->saveXML(), $ccrXML);
    }

    public function testGetHealthProfileListFeed()
    {
        // no query
        $feed1 = $this->health->getHealthProfileListFeed();
        $this->assertTrue($feed1 instanceof Health\ProfileListFeed);
        foreach ($feed1->getEntries() as $entry) {
            $this->assertTrue($entry instanceof Health\ProfileListEntry);
            $this->assertEquals($entry->getHttpClient(), $feed1->getHttpClient());
        }

        // with query object
        $query = new Health\Query('https://www.google.com/health/feeds/profile/list');
        $feed2 = $this->health->getHealthProfileListFeed($query);
        $this->assertTrue($feed2 instanceof Health\ProfileListFeed);
        foreach ($feed2->entry as $entry) {
            $this->assertTrue($entry instanceof Health\ProfileListEntry);
            $this->assertEquals($entry->getHttpClient(), $feed2->getHttpClient());
        }

        // with direct query string
        $feed3 = $this->health->getHealthProfileListFeed('https://www.google.com/health/feeds/profile/list');
        $this->assertTrue($feed3 instanceof Health\ProfileListFeed);
        foreach ($feed3->entry as $entry) {
            $this->assertTrue($entry instanceof Health\ProfileListEntry);
            $this->assertEquals($entry->getHttpClient(), $feed3->getHttpClient());
        }

        $this->assertEquals($feed1->saveXML(), $feed2->saveXML());
        $this->assertEquals($feed1->saveXML(), $feed3->saveXML());
        $this->assertEquals($feed2->saveXML(), $feed3->saveXML());
    }


    public function testGetProfileFeedNoQuery()
    {
        $this->setupProfileID();

        // no query, digest=false
        $profileFeed = $this->health->getHealthProfileFeed();
        $this->assertTrue($profileFeed instanceof Health\ProfileFeed);
        $this->assertTrue(count($profileFeed->entry) > 1, 'digest=false, should have multiple <entry> elements');
        foreach ($profileFeed->entry as $entry) {
            $this->assertTrue($entry instanceof Health\ProfileEntry);
            $ccr = $entry->getCcr();
            $this->assertTrue($ccr instanceof Extension\Ccr);
            $this->assertEquals($entry->getHttpClient(), $profileFeed->getHttpClient());
        }
    }

    public function testGetProfileFeedByQuery()
    {
        $this->setupProfileID();
        $profileID = $this->health->getProfileID();

        // with direct query string
        $feed1 = $this->health->getHealthProfileFeed(
            "https://www.google.com/health/feeds/profile/ui/{$profileID}?digest=true");
        $this->assertTrue($feed1 instanceof Health\ProfileFeed);
        $this->assertTrue(count($feed1->entry) === 1, 'digest=true, expected a single <entry> element');
        foreach ($feed1->entry as $entry) {
            $this->assertTrue($entry instanceof Health\ProfileEntry);
            $ccr = $entry->getCcr();
            $this->assertTrue($ccr instanceof Extension\Ccr);
            $this->assertEquals($entry->getHttpClient(), $feed1->getHttpClient());
        }

        // with query object
        $query = new Health\Query("https://www.google.com/health/feeds/profile/ui/{$profileID}");
        $query->setDigest('true');
        $feed2 = $this->health->getHealthProfileFeed($query);
        $this->assertTrue($feed2 instanceof Health\ProfileFeed);
        $this->assertTrue(count($feed2->entry) === 1, 'digest=true, expected a single <entry> element');
        foreach ($feed2->entry as $entry) {
            $this->assertTrue($entry instanceof Health\ProfileEntry);
            $ccr = $entry->getCcr();
            $this->assertTrue($ccr instanceof Extension\Ccr);
            $this->assertEquals($entry->getHttpClient(), $feed2->getHttpClient());
        }

        $this->assertEquals($feed1->saveXML(), $feed2->saveXML());
    }

    public function testGetProfileEntryNoQuery()
    {
        try {
            $entry = $this->health->getHealthProfileEntry();
            $this->fail('Expecting to catch Zend_GData_App_InvalidArgumentException');
        } catch (\Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('\Zend\GData\App\InvalidArgumentException'),
                'Expecting Zend_GData_App_InvalidArgumentException, got '.get_class($e));
        }
    }

    public function testGetProfileEntry()
    {
        $this->setupProfileID();
        $profileID = $this->health->getProfileID();

        $feed = $this->health->getHealthProfileFeed();
        $entryFromProfileQuery = $feed->entry[0];
        $this->assertTrue($entryFromProfileQuery instanceof Health\ProfileEntry);

        // direct query string
        $entry1 = $this->health->getHealthProfileEntry($entryFromProfileQuery->id->text);
        $this->assertTrue($entry1 instanceof Health\ProfileEntry);

        // query object
        $query = new Health\Query("https://www.google.com/health/feeds/profile/ui/{$profileID}");
        $entry2 = $this->health->getHealthProfileEntry($query);
        $this->assertTrue($entry2 instanceof Health\ProfileEntry);

        $this->assertEquals($entryFromProfileQuery->getHttpClient(), $entry1->getHttpClient());
        $this->assertEquals($entryFromProfileQuery->getHttpClient(), $entry2->getHttpClient());
        $this->assertEquals($entry1->getHttpClient(), $entry2->getHttpClient());

        $this->assertXmlStringEqualsXmlString($entryFromProfileQuery->getCcr()->saveXML(), $entry1->getCcr()->saveXML());
        $this->assertXmlStringEqualsXmlString($entryFromProfileQuery->getCcr()->saveXML(), $entry2->getCcr()->saveXML());
        $this->assertXmlStringEqualsXmlString($entry1->getCcr()->saveXML(), $entry2->getCcr()->saveXML());
    }

    public function testSendNoticeWithoutUsingClientLogin()
    {
        try {
            $responseEntry = $this->health->sendHealthNotice("", "");
            $this->fail('Expecting to catch Zend_GData_App_AuthException');
        } catch (\Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('\Zend\GData\App\AuthException'),
                'Expecting Zend_GData_App_AuthException, got '.get_class($e));
        }
    }

    public function testSendNoticeWithoutCcr()
    {
        $this->setupProfileID();
        $profileID = $this->health->getProfileID();

        $subject = "Title of your notice goes here";
        $body = "Notice body goes here";

        $responseEntry = $this->health->sendHealthNotice($subject, $body);

        $this->assertTrue($responseEntry instanceof Health\ProfileEntry);
        $this->assertEquals($subject, $responseEntry->title->text);
        $this->assertEquals($body, $responseEntry->content->text);
        $this->assertNull($responseEntry->getCcr());
    }

    public function testSendNoticeWithoutCcrUsingDirectInsert()
    {
        $this->setupProfileID();
        $profileID = $this->health->getProfileID();

        $subject = "Title of your notice goes here";
        $body = "Notice body goes here";

        $entry = new Health\ProfileEntry();

        $author = $this->health->newAuthor();
        $author->name = $this->health->newName('John Doe');
        $author->email = $this->health->newEmail('user@example.com');
        $entry->setAuthor(array(0 => $author));

        $entry->title = $this->health->newTitle($subject);
        $entry->content = $this->health->newContent($body);
        $entry->content->type = 'text';

        $ccrXML = file_get_contents('Zend/GData/Health/_files/ccr_notice_sample.xml', true);
        $entry->setCcr($ccrXML);

        $uri = "https://www.google.com/health/feeds/register/ui/{$profileID}";
        $responseEntry = $this->health->insertEntry($entry, $uri, '\Zend\GData\Health\ProfileEntry');

        $this->assertTrue($responseEntry instanceof Health\ProfileEntry);
        $this->assertEquals($subject, $responseEntry->title->text);
        $this->assertEquals($author->name->text, 'John Doe');
        $this->assertEquals($author->email->text, 'user@example.com');
        $this->assertEquals($body, $responseEntry->content->text);
    }

    public function testSendNoticeWithCcr()
    {
        $this->setupProfileID();
        $profileID = $this->health->getProfileID();

        $subject = "Title of your notice goes here";
        $body = "Notice body can contain <b>html</b> entities";
        $type = "html";
        $ccrXML = file_get_contents('Zend/GData/Health/_files/ccr_notice_sample.xml', true);

        $responseEntry = $this->health->sendHealthNotice($subject, $body, $type, $ccrXML);

        $this->assertTrue($responseEntry instanceof Health\ProfileEntry);
        $this->assertEquals($subject, $responseEntry->title->text);
        $this->assertEquals($body, $responseEntry->content->text);
        $this->assertEquals($type, $responseEntry->content->type);
        $this->assertXmlStringEqualsXmlString($responseEntry->getCcr()->saveXML(), $ccrXML);
    }
}

