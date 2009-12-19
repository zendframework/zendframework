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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/TestHelper.php';

require_once 'Zend/Feed/Writer/Feed.php';

/**
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @group      Zend_Feed
 * @group      Zend_Feed_Writer
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Feed_Writer_FeedTest extends PHPUnit_Framework_TestCase
{

    protected $_feedSamplePath = null;

    public function setup()
    {
        $this->_feedSamplePath = dirname(__FILE__) . '/Writer/_files';
    }

    public function testAddsAuthorName()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $writer->addAuthor('Joe');
        $this->assertEquals(array('name'=>'Joe'), $writer->getAuthor());
    }

    public function testAddsAuthorEmail()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $writer->addAuthor('Joe', 'joe@example.com');
        $this->assertEquals(array('name'=>'Joe', 'email' => 'joe@example.com'), $writer->getAuthor());
    }

    public function testAddsAuthorUri()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $writer->addAuthor('Joe', null, 'http://www.example.com');
        $this->assertEquals(array('name'=>'Joe', 'uri' => 'http://www.example.com'), $writer->getAuthor());
    }

    public function testAddAuthorThrowsExceptionOnInvalidName()
    {
        $writer = new Zend_Feed_Writer_Feed;
        try {
            $writer->addAuthor('');
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }

    public function testAddAuthorThrowsExceptionOnInvalidEmail()
    {
        $writer = new Zend_Feed_Writer_Feed;
        try {
            $writer->addAuthor('Joe', '');
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }

    public function testAddAuthorThrowsExceptionOnInvalidUri()
    {
        $writer = new Zend_Feed_Writer_Feed;
        try {
            $writer->addAuthor('Joe', null, 'notauri');
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }

    public function testAddsAuthorNameFromArray()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $writer->addAuthor(array('name'=>'Joe'));
        $this->assertEquals(array('name'=>'Joe'), $writer->getAuthor());
    }

    public function testAddsAuthorEmailFromArray()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $writer->addAuthor(array('name'=>'Joe','email'=>'joe@example.com'));
        $this->assertEquals(array('name'=>'Joe', 'email' => 'joe@example.com'), $writer->getAuthor());
    }

    public function testAddsAuthorUriFromArray()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $writer->addAuthor(array('name'=>'Joe','uri'=>'http://www.example.com'));
        $this->assertEquals(array('name'=>'Joe', 'uri' => 'http://www.example.com'), $writer->getAuthor());
    }

    public function testAddAuthorThrowsExceptionOnInvalidNameFromArray()
    {
        $writer = new Zend_Feed_Writer_Feed;
        try {
            $writer->addAuthor(array('name'=>''));
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }

    public function testAddAuthorThrowsExceptionOnInvalidEmailFromArray()
    {
        $writer = new Zend_Feed_Writer_Feed;
        try {
            $writer->addAuthor(array('name'=>'Joe','email'=>''));
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }

    public function testAddAuthorThrowsExceptionOnInvalidUriFromArray()
    {
        $writer = new Zend_Feed_Writer_Feed;
        try {
            $writer->addAuthor(array('name'=>'Joe','uri'=>'notauri'));
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }

    public function testAddAuthorThrowsExceptionIfNameOmittedFromArray()
    {
        $writer = new Zend_Feed_Writer_Feed;
        try {
            $writer->addAuthor(array('uri'=>'notauri'));
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }

    public function testAddsAuthorsFromArrayOfAuthors()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $writer->addAuthors(array(
            array('name'=>'Joe','uri'=>'http://www.example.com'),
            array('name'=>'Jane','uri'=>'http://www.example.com')
        ));
        $this->assertEquals(array('name'=>'Jane', 'uri' => 'http://www.example.com'), $writer->getAuthor(1));
    }

    public function testSetsCopyright()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $writer->setCopyright('Copyright (c) 2009 Paddy Brady');
        $this->assertEquals('Copyright (c) 2009 Paddy Brady', $writer->getCopyright());
    }

    public function testSetCopyrightThrowsExceptionOnInvalidParam()
    {
        $writer = new Zend_Feed_Writer_Feed;
        try {
            $writer->setCopyright('');
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }

    public function testSetDateCreatedDefaultsToCurrentTime()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $writer->setDateCreated();
        $dateNow = new Zend_Date;
        $this->assertTrue($dateNow->isLater($writer->getDateCreated()) || $dateNow->equals($writer->getDateCreated()));
    }

    public function testSetDateCreatedUsesGivenUnixTimestamp()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $writer->setDateCreated(1234567890);
        $myDate = new Zend_Date('1234567890', Zend_Date::TIMESTAMP);
        $this->assertTrue($myDate->equals($writer->getDateCreated()));
    }

    public function testSetDateCreatedUsesZendDateObject()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $writer->setDateCreated(new Zend_Date('1234567890', Zend_Date::TIMESTAMP));
        $myDate = new Zend_Date('1234567890', Zend_Date::TIMESTAMP);
        $this->assertTrue($myDate->equals($writer->getDateCreated()));
    }

    public function testSetDateModifiedDefaultsToCurrentTime()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $writer->setDateModified();
        $dateNow = new Zend_Date;
        $this->assertTrue($dateNow->isLater($writer->getDateModified()) || $dateNow->equals($writer->getDateModified()));
    }

    public function testSetDateModifiedUsesGivenUnixTimestamp()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $writer->setDateModified(1234567890);
        $myDate = new Zend_Date('1234567890', Zend_Date::TIMESTAMP);
        $this->assertTrue($myDate->equals($writer->getDateModified()));
    }

    public function testSetDateModifiedUsesZendDateObject()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $writer->setDateModified(new Zend_Date('1234567890', Zend_Date::TIMESTAMP));
        $myDate = new Zend_Date('1234567890', Zend_Date::TIMESTAMP);
        $this->assertTrue($myDate->equals($writer->getDateModified()));
    }

    public function testSetDateCreatedThrowsExceptionOnInvalidParameter()
    {
        $writer = new Zend_Feed_Writer_Feed;
        try {
            $writer->setDateCreated('abc');
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }

    public function testSetDateModifiedThrowsExceptionOnInvalidParameter()
    {
        $writer = new Zend_Feed_Writer_Feed;
        try {
            $writer->setDateModified('abc');
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }

    public function testGetDateCreatedReturnsNullIfDateNotSet()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $this->assertTrue(is_null($writer->getDateCreated()));
    }

    public function testGetDateModifiedReturnsNullIfDateNotSet()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $this->assertTrue(is_null($writer->getDateModified()));
    }

    public function testGetCopyrightReturnsNullIfDateNotSet()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $this->assertTrue(is_null($writer->getCopyright()));
    }

    public function testSetsDescription()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $writer->setDescription('abc');
        $this->assertEquals('abc', $writer->getDescription());
    }

    public function testSetDescriptionThrowsExceptionOnInvalidParameter()
    {
        $writer = new Zend_Feed_Writer_Feed;
        try {
            $writer->setDescription('');
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }

    public function testGetDescriptionReturnsNullIfDateNotSet()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $this->assertTrue(is_null($writer->getDescription()));
    }

    public function testSetsId()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $writer->setId('http://www.example.com/id');
        $this->assertEquals('http://www.example.com/id', $writer->getId());
    }

    public function testSetsIdAcceptsUrns()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $writer->setId('urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6');
        $this->assertEquals('urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6', $writer->getId());
    }

    public function testSetIdThrowsExceptionOnInvalidParameter()
    {
        $writer = new Zend_Feed_Writer_Feed;
        try {
            $writer->setId('');
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }

    public function testSetIdThrowsExceptionOnInvalidUri()
    {
        $writer = new Zend_Feed_Writer_Feed;
        try {
            $writer->setId('http://');
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }

    public function testGetIdReturnsNullIfDateNotSet()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $this->assertTrue(is_null($writer->getId()));
    }

    public function testSetsLanguage()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $writer->setLanguage('abc');
        $this->assertEquals('abc', $writer->getLanguage());
    }

    public function testSetLanguageThrowsExceptionOnInvalidParameter()
    {
        $writer = new Zend_Feed_Writer_Feed;
        try {
            $writer->setLanguage('');
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }

    public function testGetLanguageReturnsNullIfDateNotSet()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $this->assertTrue(is_null($writer->getLanguage()));
    }

    public function testSetsLink()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $writer->setLink('http://www.example.com/id');
        $this->assertEquals('http://www.example.com/id', $writer->getLink());
    }

    public function testSetLinkThrowsExceptionOnEmptyString()
    {
        $writer = new Zend_Feed_Writer_Feed;
        try {
            $writer->setLink('');
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }

    public function testSetLinkThrowsExceptionOnInvalidUri()
    {
        $writer = new Zend_Feed_Writer_Feed;
        try {
            $writer->setLink('http://');
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }

    public function testGetLinkReturnsNullIfDateNotSet()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $this->assertTrue(is_null($writer->getLink()));
    }

    public function testSetsEncoding()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $writer->setEncoding('utf-16');
        $this->assertEquals('utf-16', $writer->getEncoding());
    }

    public function testSetEncodingThrowsExceptionOnInvalidParameter()
    {
        $writer = new Zend_Feed_Writer_Feed;
        try {
            $writer->setEncoding('');
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }

    public function testGetEncodingReturnsUtf8IfNotSet()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $this->assertEquals('UTF-8', $writer->getEncoding());
    }

    public function testSetsTitle()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $writer->setTitle('abc');
        $this->assertEquals('abc', $writer->getTitle());
    }

    public function testSetTitleThrowsExceptionOnInvalidParameter()
    {
        $writer = new Zend_Feed_Writer_Feed;
        try {
            $writer->setTitle('');
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }

    public function testGetTitleReturnsNullIfDateNotSet()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $this->assertTrue(is_null($writer->getTitle()));
    }

    public function testSetsGeneratorName()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $writer->setGenerator('ZFW');
        $this->assertEquals(array('name'=>'ZFW'), $writer->getGenerator());
    }

    public function testSetsGeneratorVersion()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $writer->setGenerator('ZFW', '1.0');
        $this->assertEquals(array('name'=>'ZFW', 'version' => '1.0'), $writer->getGenerator());
    }

    public function testSetsGeneratorUri()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $writer->setGenerator('ZFW', null, 'http://www.example.com');
        $this->assertEquals(array('name'=>'ZFW', 'uri' => 'http://www.example.com'), $writer->getGenerator());
    }

    public function testSetsGeneratorThrowsExceptionOnInvalidName()
    {
        $writer = new Zend_Feed_Writer_Feed;
        try {
            $writer->setGenerator('');
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }

    public function testSetsGeneratorThrowsExceptionOnInvalidVersion()
    {
        $writer = new Zend_Feed_Writer_Feed;
        try {
            $writer->addAuthor('ZFW', '');
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }

    public function testSetsGeneratorThrowsExceptionOnInvalidUri()
    {
        $writer = new Zend_Feed_Writer_Feed;
        try {
            $writer->setGenerator('ZFW', null, 'notauri');
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }

    public function testGetGeneratorReturnsNullIfDateNotSet()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $this->assertTrue(is_null($writer->getGenerator()));
    }

    public function testSetsFeedLink()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $writer->setFeedLink('http://www.example.com/rss', 'RSS');
        $this->assertEquals(array('rss'=>'http://www.example.com/rss'), $writer->getFeedLinks());
    }

    public function testSetsFeedLinkThrowsExceptionOnInvalidType()
    {
        $writer = new Zend_Feed_Writer_Feed;
        try {
            $writer->setFeedLink('http://www.example.com/rss', 'abc');
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }

    public function testSetsFeedLinkThrowsExceptionOnInvalidUri()
    {
        $writer = new Zend_Feed_Writer_Feed;
        try {
            $writer->setFeedLink('http://', 'rss');
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }

    public function testGetFeedLinksReturnsNullIfNotSet()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $this->assertTrue(is_null($writer->getFeedLinks()));
    }
    
    public function testSetsBaseUrl()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $writer->setBaseUrl('http://www.example.com');
        $this->assertEquals('http://www.example.com', $writer->getBaseUrl());
    }

    public function testSetsBaseUrlThrowsExceptionOnInvalidUri()
    {
        $writer = new Zend_Feed_Writer_Feed;
        try {
            $writer->setBaseUrl('http://');
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }

    public function testGetBaseUrlReturnsNullIfNotSet()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $this->assertTrue(is_null($writer->getBaseUrl()));
    }
    
    public function testAddsHubUrl()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $writer->addHub('http://www.example.com/hub');
        $this->assertEquals(array('http://www.example.com/hub'), $writer->getHubs());
    }
    
    public function testAddsManyHubUrls()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $writer->addHubs(array('http://www.example.com/hub', 'http://www.example.com/hub2'));
        $this->assertEquals(array('http://www.example.com/hub', 'http://www.example.com/hub2'), $writer->getHubs());
    }

    public function testAddingHubUrlThrowsExceptionOnInvalidUri()
    {
        $writer = new Zend_Feed_Writer_Feed;
        try {
            $writer->addHub('http://');
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }

    public function testAddingHubUrlReturnsNullIfNotSet()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $this->assertTrue(is_null($writer->getHubs()));
    }

    public function testCreatesNewEntryDataContainer()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $entry = $writer->createEntry();
        $this->assertTrue($entry instanceof Zend_Feed_Writer_Entry);
    }
    
    public function testAddsCategory()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $writer->addCategory(array('term'=>'cat_dog'));
        $this->assertEquals(array(array('term'=>'cat_dog')), $writer->getCategories());
    }
    
    public function testAddsManyCategories()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $writer->addCategories(array(array('term'=>'cat_dog'),array('term'=>'cat_mouse')));
        $this->assertEquals(array(array('term'=>'cat_dog'),array('term'=>'cat_mouse')), $writer->getCategories());
    }

    public function testAddingCategoryWithoutTermThrowsException()
    {
        $writer = new Zend_Feed_Writer_Feed;
        try {
            $writer->addCategory(array('label' => 'Cats & Dogs', 'scheme' => 'http://www.example.com/schema1'));
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }
    
    public function testAddingCategoryWithInvalidUriAsSchemeThrowsException()
    {
        $writer = new Zend_Feed_Writer_Feed;
        try {
            $writer->addCategory(array('term' => 'cat_dog', 'scheme' => 'http://'));
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }

    public function testGetCategoriesReturnsNullIfNotSet()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $this->assertTrue(is_null($writer->getCategories()));
    }

    public function testAddsAndOrdersEntriesByDateIfRequested()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $entry = $writer->createEntry();
        $entry->setDateCreated(1234567890);
        $entry2 = $writer->createEntry();
        $entry2->setDateCreated(1230000000);
        $writer->addEntry($entry);
        $writer->addEntry($entry2);
        $writer->orderByDate();
        $this->assertEquals(1230000000, $writer->getEntry(1)->getDateCreated()->get(Zend_Date::TIMESTAMP));
    }

}
