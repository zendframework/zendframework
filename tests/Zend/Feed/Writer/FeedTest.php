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
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Feed\Writer;
use Zend\Feed;
use Zend\Feed\Writer\Feed as WriterFeed;
use Zend\Date;

/**
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @group      Zend_Feed
 * @group      Zend_Feed_Writer
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FeedTest extends \PHPUnit_Framework_TestCase
{

    protected $_feedSamplePath = null;

    public function setup()
    {
        $this->_feedSamplePath = dirname(__FILE__) . '/Writer/_files';
    }

    public function testAddsAuthorName()
    {
        $writer = new WriterFeed\Feed;
        $writer->addAuthor('Joe');
        $this->assertEquals(array('name'=>'Joe'), $writer->getAuthor());
    }

    public function testAddsAuthorEmail()
    {
        $writer = new WriterFeed\Feed;
        $writer->addAuthor('Joe', 'joe@example.com');
        $this->assertEquals(array('name'=>'Joe', 'email' => 'joe@example.com'), $writer->getAuthor());
    }

    public function testAddsAuthorUri()
    {
        $writer = new WriterFeed\Feed;
        $writer->addAuthor('Joe', null, 'http://www.example.com');
        $this->assertEquals(array('name'=>'Joe', 'uri' => 'http://www.example.com'), $writer->getAuthor());
    }

    public function testAddAuthorThrowsExceptionOnInvalidName()
    {
        $writer = new WriterFeed\Feed;
        try {
            $writer->addAuthor('');
            $this->fail();
        } catch (Feed\Exception $e) {
        }
    }

    public function testAddAuthorThrowsExceptionOnInvalidEmail()
    {
        $writer = new WriterFeed\Feed;
        try {
            $writer->addAuthor('Joe', '');
            $this->fail();
        } catch (Feed\Exception $e) {
        }
    }

    public function testAddAuthorThrowsExceptionOnInvalidUri()
    {
        $this->markTestSkipped('Skipped until Zend\URI is refactored for validation');
        $writer = new WriterFeed\Feed;
        try {
            $writer->addAuthor('Joe', null, 'notauri');
            $this->fail();
        } catch (Feed\Exception $e) {
        }
    }

    public function testAddsAuthorNameFromArray()
    {
        $writer = new WriterFeed\Feed;
        $writer->addAuthor(array('name'=>'Joe'));
        $this->assertEquals(array('name'=>'Joe'), $writer->getAuthor());
    }

    public function testAddsAuthorEmailFromArray()
    {
        $writer = new WriterFeed\Feed;
        $writer->addAuthor(array('name'=>'Joe','email'=>'joe@example.com'));
        $this->assertEquals(array('name'=>'Joe', 'email' => 'joe@example.com'), $writer->getAuthor());
    }

    public function testAddsAuthorUriFromArray()
    {
        $writer = new WriterFeed\Feed;
        $writer->addAuthor(array('name'=>'Joe','uri'=>'http://www.example.com'));
        $this->assertEquals(array('name'=>'Joe', 'uri' => 'http://www.example.com'), $writer->getAuthor());
    }

    public function testAddAuthorThrowsExceptionOnInvalidNameFromArray()
    {
        $writer = new WriterFeed\Feed;
        try {
            $writer->addAuthor(array('name'=>''));
            $this->fail();
        } catch (Feed\Exception $e) {
        }
    }

    public function testAddAuthorThrowsExceptionOnInvalidEmailFromArray()
    {
        $writer = new WriterFeed\Feed;
        try {
            $writer->addAuthor(array('name'=>'Joe','email'=>''));
            $this->fail();
        } catch (Feed\Exception $e) {
        }
    }

    public function testAddAuthorThrowsExceptionOnInvalidUriFromArray()
    {
        $this->markTestSkipped('Skipped until Zend\URI is refactored for validation');
        $writer = new WriterFeed\Feed;
        try {
            $writer->addAuthor(array('name'=>'Joe','uri'=>'notauri'));
            $this->fail();
        } catch (Feed\Exception $e) {
        }
    }

    public function testAddAuthorThrowsExceptionIfNameOmittedFromArray()
    {
        $writer = new WriterFeed\Feed;
        try {
            $writer->addAuthor(array('uri'=>'notauri'));
            $this->fail();
        } catch (Feed\Exception $e) {
        }
    }

    public function testAddsAuthorsFromArrayOfAuthors()
    {
        $writer = new WriterFeed\Feed;
        $writer->addAuthors(array(
            array('name'=>'Joe','uri'=>'http://www.example.com'),
            array('name'=>'Jane','uri'=>'http://www.example.com')
        ));
        $this->assertEquals(array('name'=>'Jane', 'uri' => 'http://www.example.com'), $writer->getAuthor(1));
    }

    public function testSetsCopyright()
    {
        $writer = new WriterFeed\Feed;
        $writer->setCopyright('Copyright (c) 2009 Paddy Brady');
        $this->assertEquals('Copyright (c) 2009 Paddy Brady', $writer->getCopyright());
    }

    public function testSetCopyrightThrowsExceptionOnInvalidParam()
    {
        $writer = new WriterFeed\Feed;
        try {
            $writer->setCopyright('');
            $this->fail();
        } catch (Feed\Exception $e) {
        }
    }

    public function testSetDateCreatedDefaultsToCurrentTime()
    {
        $writer = new WriterFeed\Feed;
        $writer->setDateCreated();
        $dateNow = new Date\Date;
        $this->assertTrue($dateNow->isLater($writer->getDateCreated()) || $dateNow->equals($writer->getDateCreated()));
    }

    public function testSetDateCreatedUsesGivenUnixTimestamp()
    {
        $writer = new WriterFeed\Feed;
        $writer->setDateCreated(1234567890);
        $myDate = new Date\Date('1234567890', Date\Date::TIMESTAMP);
        $this->assertTrue($myDate->equals($writer->getDateCreated()));
    }

    public function testSetDateCreatedUsesZendDateObject()
    {
        $writer = new WriterFeed\Feed;
        $writer->setDateCreated(new Date\Date('1234567890', Date\Date::TIMESTAMP));
        $myDate = new Date\Date('1234567890', Date\Date::TIMESTAMP);
        $this->assertTrue($myDate->equals($writer->getDateCreated()));
    }

    public function testSetDateModifiedDefaultsToCurrentTime()
    {
        $writer = new WriterFeed\Feed;
        $writer->setDateModified();
        $dateNow = new Date\Date;
        $this->assertTrue($dateNow->isLater($writer->getDateModified()) || $dateNow->equals($writer->getDateModified()));
    }

    public function testSetDateModifiedUsesGivenUnixTimestamp()
    {
        $writer = new WriterFeed\Feed;
        $writer->setDateModified(1234567890);
        $myDate = new Date\Date('1234567890', Date\Date::TIMESTAMP);
        $this->assertTrue($myDate->equals($writer->getDateModified()));
    }

    public function testSetDateModifiedUsesZendDateObject()
    {
        $writer = new WriterFeed\Feed;
        $writer->setDateModified(new Date\Date('1234567890', Date\Date::TIMESTAMP));
        $myDate = new Date\Date('1234567890', Date\Date::TIMESTAMP);
        $this->assertTrue($myDate->equals($writer->getDateModified()));
    }

    public function testSetDateCreatedThrowsExceptionOnInvalidParameter()
    {
        $writer = new WriterFeed\Feed;
        try {
            $writer->setDateCreated('abc');
            $this->fail();
        } catch (Feed\Exception $e) {
        }
    }

    public function testSetDateModifiedThrowsExceptionOnInvalidParameter()
    {
        $writer = new WriterFeed\Feed;
        try {
            $writer->setDateModified('abc');
            $this->fail();
        } catch (Feed\Exception $e) {
        }
    }

    public function testGetDateCreatedReturnsNullIfDateNotSet()
    {
        $writer = new WriterFeed\Feed;
        $this->assertTrue(is_null($writer->getDateCreated()));
    }

    public function testGetDateModifiedReturnsNullIfDateNotSet()
    {
        $writer = new WriterFeed\Feed;
        $this->assertTrue(is_null($writer->getDateModified()));
    }

    public function testGetCopyrightReturnsNullIfDateNotSet()
    {
        $writer = new WriterFeed\Feed;
        $this->assertTrue(is_null($writer->getCopyright()));
    }

    public function testSetsDescription()
    {
        $writer = new WriterFeed\Feed;
        $writer->setDescription('abc');
        $this->assertEquals('abc', $writer->getDescription());
    }

    public function testSetDescriptionThrowsExceptionOnInvalidParameter()
    {
        $writer = new WriterFeed\Feed;
        try {
            $writer->setDescription('');
            $this->fail();
        } catch (Feed\Exception $e) {
        }
    }

    public function testGetDescriptionReturnsNullIfDateNotSet()
    {
        $writer = new WriterFeed\Feed;
        $this->assertTrue(is_null($writer->getDescription()));
    }

    public function testSetsId()
    {
        $writer = new WriterFeed\Feed;
        $writer->setId('http://www.example.com/id');
        $this->assertEquals('http://www.example.com/id', $writer->getId());
    }

    public function testSetsIdAcceptsUrns()
    {
        $writer = new WriterFeed\Feed;
        $writer->setId('urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6');
        $this->assertEquals('urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6', $writer->getId());
    }

    public function testSetIdThrowsExceptionOnInvalidParameter()
    {
        $writer = new WriterFeed\Feed;
        try {
            $writer->setId('');
            $this->fail();
        } catch (Feed\Exception $e) {
        }
    }

    public function testSetIdThrowsExceptionOnInvalidUri()
    {
        $writer = new WriterFeed\Feed;
        try {
            $writer->setId('http://');
            $this->fail();
        } catch (Feed\Exception $e) {
        }
    }

    public function testGetIdReturnsNullIfDateNotSet()
    {
        $writer = new WriterFeed\Feed;
        $this->assertTrue(is_null($writer->getId()));
    }

    public function testSetsLanguage()
    {
        $writer = new WriterFeed\Feed;
        $writer->setLanguage('abc');
        $this->assertEquals('abc', $writer->getLanguage());
    }

    public function testSetLanguageThrowsExceptionOnInvalidParameter()
    {
        $writer = new WriterFeed\Feed;
        try {
            $writer->setLanguage('');
            $this->fail();
        } catch (Feed\Exception $e) {
        }
    }

    public function testGetLanguageReturnsNullIfDateNotSet()
    {
        $writer = new WriterFeed\Feed;
        $this->assertTrue(is_null($writer->getLanguage()));
    }

    public function testSetsLink()
    {
        $writer = new WriterFeed\Feed;
        $writer->setLink('http://www.example.com/id');
        $this->assertEquals('http://www.example.com/id', $writer->getLink());
    }

    public function testSetLinkThrowsExceptionOnEmptyString()
    {
        $writer = new WriterFeed\Feed;
        try {
            $writer->setLink('');
            $this->fail();
        } catch (Feed\Exception $e) {
        }
    }

    public function testSetLinkThrowsExceptionOnInvalidUri()
    {
        $writer = new WriterFeed\Feed;
        try {
            $writer->setLink('http://');
            $this->fail();
        } catch (Feed\Exception $e) {
        }
    }

    public function testGetLinkReturnsNullIfDateNotSet()
    {
        $writer = new WriterFeed\Feed;
        $this->assertTrue(is_null($writer->getLink()));
    }

    public function testSetsEncoding()
    {
        $writer = new WriterFeed\Feed;
        $writer->setEncoding('utf-16');
        $this->assertEquals('utf-16', $writer->getEncoding());
    }

    public function testSetEncodingThrowsExceptionOnInvalidParameter()
    {
        $writer = new WriterFeed\Feed;
        try {
            $writer->setEncoding('');
            $this->fail();
        } catch (Feed\Exception $e) {
        }
    }

    public function testGetEncodingReturnsUtf8IfNotSet()
    {
        $writer = new WriterFeed\Feed;
        $this->assertEquals('UTF-8', $writer->getEncoding());
    }

    public function testSetsTitle()
    {
        $writer = new WriterFeed\Feed;
        $writer->setTitle('abc');
        $this->assertEquals('abc', $writer->getTitle());
    }

    public function testSetTitleThrowsExceptionOnInvalidParameter()
    {
        $writer = new WriterFeed\Feed;
        try {
            $writer->setTitle('');
            $this->fail();
        } catch (Feed\Exception $e) {
        }
    }

    public function testGetTitleReturnsNullIfDateNotSet()
    {
        $writer = new WriterFeed\Feed;
        $this->assertTrue(is_null($writer->getTitle()));
    }

    public function testSetsGeneratorName()
    {
        $writer = new WriterFeed\Feed;
        $writer->setGenerator('ZFW');
        $this->assertEquals(array('name'=>'ZFW'), $writer->getGenerator());
    }

    public function testSetsGeneratorVersion()
    {
        $writer = new WriterFeed\Feed;
        $writer->setGenerator('ZFW', '1.0');
        $this->assertEquals(array('name'=>'ZFW', 'version' => '1.0'), $writer->getGenerator());
    }

    public function testSetsGeneratorUri()
    {
        $writer = new WriterFeed\Feed;
        $writer->setGenerator('ZFW', null, 'http://www.example.com');
        $this->assertEquals(array('name'=>'ZFW', 'uri' => 'http://www.example.com'), $writer->getGenerator());
    }

    public function testSetsGeneratorThrowsExceptionOnInvalidName()
    {
        $writer = new WriterFeed\Feed;
        try {
            $writer->setGenerator('');
            $this->fail();
        } catch (Feed\Exception $e) {
        }
    }

    public function testSetsGeneratorThrowsExceptionOnInvalidVersion()
    {
        $writer = new WriterFeed\Feed;
        try {
            $writer->addAuthor('ZFW', '');
            $this->fail();
        } catch (Feed\Exception $e) {
        }
    }

    public function testSetsGeneratorThrowsExceptionOnInvalidUri()
    {
        $this->markTestSkipped('Skipped until Zend\URI is refactored for validation');
        $writer = new WriterFeed\Feed;
        try {
            $writer->setGenerator('ZFW', null, 'notauri');
            $this->fail();
        } catch (Feed\Exception $e) {
        }
    }

    public function testGetGeneratorReturnsNullIfDateNotSet()
    {
        $writer = new WriterFeed\Feed;
        $this->assertTrue(is_null($writer->getGenerator()));
    }

    public function testSetsFeedLink()
    {
        $writer = new WriterFeed\Feed;
        $writer->setFeedLink('http://www.example.com/rss', 'RSS');
        $this->assertEquals(array('rss'=>'http://www.example.com/rss'), $writer->getFeedLinks());
    }

    public function testSetsFeedLinkThrowsExceptionOnInvalidType()
    {
        $writer = new WriterFeed\Feed;
        try {
            $writer->setFeedLink('http://www.example.com/rss', 'abc');
            $this->fail();
        } catch (Feed\Exception $e) {
        }
    }

    public function testSetsFeedLinkThrowsExceptionOnInvalidUri()
    {
        $writer = new WriterFeed\Feed;
        try {
            $writer->setFeedLink('http://', 'rss');
            $this->fail();
        } catch (Feed\Exception $e) {
        }
    }

    public function testGetFeedLinksReturnsNullIfNotSet()
    {
        $writer = new WriterFeed\Feed;
        $this->assertTrue(is_null($writer->getFeedLinks()));
    }
    
    public function testSetsBaseUrl()
    {
        $writer = new WriterFeed\Feed;
        $writer->setBaseUrl('http://www.example.com');
        $this->assertEquals('http://www.example.com', $writer->getBaseUrl());
    }

    public function testSetsBaseUrlThrowsExceptionOnInvalidUri()
    {
        $writer = new WriterFeed\Feed;
        try {
            $writer->setBaseUrl('http://');
            $this->fail();
        } catch (Feed\Exception $e) {
        }
    }

    public function testGetBaseUrlReturnsNullIfNotSet()
    {
        $writer = new WriterFeed\Feed;
        $this->assertTrue(is_null($writer->getBaseUrl()));
    }
    
    public function testAddsHubUrl()
    {
        $writer = new WriterFeed\Feed;
        $writer->addHub('http://www.example.com/hub');
        $this->assertEquals(array('http://www.example.com/hub'), $writer->getHubs());
    }
    
    public function testAddsManyHubUrls()
    {
        $writer = new WriterFeed\Feed;
        $writer->addHubs(array('http://www.example.com/hub', 'http://www.example.com/hub2'));
        $this->assertEquals(array('http://www.example.com/hub', 'http://www.example.com/hub2'), $writer->getHubs());
    }

    public function testAddingHubUrlThrowsExceptionOnInvalidUri()
    {
        $writer = new WriterFeed\Feed;
        try {
            $writer->addHub('http://');
            $this->fail();
        } catch (Feed\Exception $e) {
        }
    }

    public function testAddingHubUrlReturnsNullIfNotSet()
    {
        $writer = new WriterFeed\Feed;
        $this->assertTrue(is_null($writer->getHubs()));
    }

    public function testCreatesNewEntryDataContainer()
    {
        $writer = new WriterFeed\Feed;
        $entry = $writer->createEntry();
        $this->assertTrue($entry instanceof \Zend\Feed\Writer\Entry);
    }
    
    public function testAddsCategory()
    {
        $writer = new WriterFeed\Feed;
        $writer->addCategory(array('term'=>'cat_dog'));
        $this->assertEquals(array(array('term'=>'cat_dog')), $writer->getCategories());
    }
    
    public function testAddsManyCategories()
    {
        $writer = new WriterFeed\Feed;
        $writer->addCategories(array(array('term'=>'cat_dog'),array('term'=>'cat_mouse')));
        $this->assertEquals(array(array('term'=>'cat_dog'),array('term'=>'cat_mouse')), $writer->getCategories());
    }

    public function testAddingCategoryWithoutTermThrowsException()
    {
        $writer = new WriterFeed\Feed;
        try {
            $writer->addCategory(array('label' => 'Cats & Dogs', 'scheme' => 'http://www.example.com/schema1'));
            $this->fail();
        } catch (Feed\Exception $e) {
        }
    }
    
    public function testAddingCategoryWithInvalidUriAsSchemeThrowsException()
    {
        $writer = new WriterFeed\Feed;
        try {
            $writer->addCategory(array('term' => 'cat_dog', 'scheme' => 'http://'));
            $this->fail();
        } catch (Feed\Exception $e) {
        }
    }

    public function testGetCategoriesReturnsNullIfNotSet()
    {
        $writer = new WriterFeed\Feed;
        $this->assertTrue(is_null($writer->getCategories()));
    }

    public function testAddsAndOrdersEntriesByDateIfRequested()
    {
        $writer = new WriterFeed\Feed;
        $entry = $writer->createEntry();
        $entry->setDateCreated(1234567890);
        $entry2 = $writer->createEntry();
        $entry2->setDateCreated(1230000000);
        $writer->addEntry($entry);
        $writer->addEntry($entry2);
        $writer->orderByDate();
        $this->assertEquals(1230000000, $writer->getEntry(1)->getDateCreated()->get(Date\Date::TIMESTAMP));
    }

}
