<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Feed
 */

namespace ZendTest\Feed\Writer;

use DateTime;
use Zend\Feed\Writer;
use Zend\Feed\Writer\Feed;
use Zend\Version\Version;

/**
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @group      Zend_Feed
 * @group      Zend_Feed_Writer
 */
class FeedTest extends \PHPUnit_Framework_TestCase
{

    protected $feedSamplePath = null;

    public function setup()
    {
        $this->feedSamplePath = dirname(__FILE__) . '/Writer/_files';
    }

    public function testAddsAuthorNameFromArray()
    {
        $writer = new Writer\Feed;
        $writer->addAuthor(array('name'=> 'Joe'));
        $this->assertEquals(array('name'=> 'Joe'), $writer->getAuthor());
    }

    public function testAddsAuthorEmailFromArray()
    {
        $writer = new Writer\Feed;
        $writer->addAuthor(array('name' => 'Joe',
                                 'email'=> 'joe@example.com'));
        $this->assertEquals(array('name'  => 'Joe',
                                  'email' => 'joe@example.com'), $writer->getAuthor());
    }

    public function testAddsAuthorUriFromArray()
    {
        $writer = new Writer\Feed;
        $writer->addAuthor(array('name'=> 'Joe',
                                 'uri' => 'http://www.example.com'));
        $this->assertEquals(array('name'=> 'Joe',
                                  'uri' => 'http://www.example.com'), $writer->getAuthor());
    }

    public function testAddAuthorThrowsExceptionOnInvalidNameFromArray()
    {
        $writer = new Writer\Feed;
        try {
            $writer->addAuthor(array('name'=> ''));
            $this->fail();
        } catch (Writer\Exception\InvalidArgumentException $e) {
        }
    }

    public function testAddAuthorThrowsExceptionOnInvalidEmailFromArray()
    {
        $writer = new Writer\Feed;
        try {
            $writer->addAuthor(array('name' => 'Joe',
                                     'email'=> ''));
            $this->fail();
        } catch (Writer\Exception\InvalidArgumentException $e) {
        }
    }

    public function testAddAuthorThrowsExceptionOnInvalidUriFromArray()
    {
        $this->markTestIncomplete('Pending Zend\URI fix for validation');
        $writer = new Writer\Feed;
        try {
            $writer->addAuthor(array('name'=> 'Joe',
                                     'uri' => 'notauri'));
            $this->fail();
        } catch (Writer\Exception\InvalidArgumentException $e) {
        }
    }

    public function testAddAuthorThrowsExceptionIfNameOmittedFromArray()
    {
        $writer = new Writer\Feed;
        try {
            $writer->addAuthor(array('uri'=> 'notauri'));
            $this->fail();
        } catch (Writer\Exception\InvalidArgumentException $e) {
        }
    }

    public function testAddsAuthorsFromArrayOfAuthors()
    {
        $writer = new Writer\Feed;
        $writer->addAuthors(array(
                                 array('name'=> 'Joe',
                                       'uri' => 'http://www.example.com'),
                                 array('name'=> 'Jane',
                                       'uri' => 'http://www.example.com')
                            ));
        $this->assertEquals(array('name'=> 'Jane',
                                  'uri' => 'http://www.example.com'), $writer->getAuthor(1));
    }

    public function testSetsCopyright()
    {
        $writer = new Writer\Feed;
        $writer->setCopyright('Copyright (c) 2009 Paddy Brady');
        $this->assertEquals('Copyright (c) 2009 Paddy Brady', $writer->getCopyright());
    }

    public function testSetCopyrightThrowsExceptionOnInvalidParam()
    {
        $writer = new Writer\Feed;
        try {
            $writer->setCopyright('');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testSetDateCreatedDefaultsToCurrentTime()
    {
        $writer = new Writer\Feed;
        $writer->setDateCreated();
        $dateNow = new DateTime();
        $this->assertTrue($dateNow >= $writer->getDateCreated());
    }

    public function testSetDateCreatedUsesGivenUnixTimestamp()
    {
        $writer = new Writer\Feed;
        $writer->setDateCreated(1234567890);
        $myDate = new DateTime('@' . 1234567890);
        $this->assertEquals($myDate, $writer->getDateCreated());
    }

    /**
     * @group ZF-12023
     */
    public function testSetDateCreatedUsesGivenUnixTimestampThatIsLessThanTenDigits()
    {
        $writer = new Writer\Feed;
        $writer->setDateCreated(123456789);
        $myDate = new DateTime('@' . 123456789);
        $this->assertEquals($myDate, $writer->getDateCreated());
    }

    /**
     * @group ZF-11610
     */
    public function testSetDateCreatedUsesGivenUnixTimestampThatIsAVerySmallInteger()
    {
        $writer = new Writer\Feed;
        $writer->setDateCreated(123);
        $myDate = new DateTime('@' . 123);
        $this->assertEquals($myDate, $writer->getDateCreated());
    }

    public function testSetDateCreatedUsesDateTimeObject()
    {
        $myDate = new DateTime('@' . 1234567890);
        $writer = new Writer\Feed;
        $writer->setDateCreated($myDate);
        $this->assertEquals($myDate, $writer->getDateCreated());
    }

    public function testSetDateModifiedDefaultsToCurrentTime()
    {
        $writer = new Writer\Feed;
        $writer->setDateModified();
        $dateNow = new DateTime();
        $this->assertTrue($dateNow >= $writer->getDateModified());
    }

    public function testSetDateModifiedUsesGivenUnixTimestamp()
    {
        $writer = new Writer\Feed;
        $writer->setDateModified(1234567890);
        $myDate = new DateTime('@' . 1234567890);
        $this->assertEquals($myDate, $writer->getDateModified());
    }

    /**
     * @group ZF-12023
     */
    public function testSetDateModifiedUsesGivenUnixTimestampThatIsLessThanTenDigits()
    {
        $writer = new Writer\Feed;
        $writer->setDateModified(123456789);
        $myDate = new DateTime('@' . 123456789);
        $this->assertEquals($myDate, $writer->getDateModified());
    }

    /**
     * @group ZF-11610
     */
    public function testSetDateModifiedUsesGivenUnixTimestampThatIsAVerySmallInteger()
    {

        $writer = new Writer\Feed;
        $writer->setDateModified(123);
        $myDate = new DateTime('@' . 123);
        $this->assertEquals($myDate, $writer->getDateModified());
    }

    public function testSetDateModifiedUsesDateTimeObject()
    {
        $myDate = new DateTime('@' . 1234567890);
        $writer = new Writer\Feed;
        $writer->setDateModified($myDate);
        $this->assertEquals($myDate, $writer->getDateModified());
    }

    public function testSetDateCreatedThrowsExceptionOnInvalidParameter()
    {
        $writer = new Writer\Feed;
        try {
            $writer->setDateCreated('abc');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testSetDateModifiedThrowsExceptionOnInvalidParameter()
    {
        $writer = new Writer\Feed;
        try {
            $writer->setDateModified('abc');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testGetDateCreatedReturnsNullIfDateNotSet()
    {
        $writer = new Writer\Feed;
        $this->assertTrue(is_null($writer->getDateCreated()));
    }

    public function testGetDateModifiedReturnsNullIfDateNotSet()
    {
        $writer = new Writer\Feed;
        $this->assertTrue(is_null($writer->getDateModified()));
    }

    public function testSetLastBuildDateDefaultsToCurrentTime()
    {
        $writer = new Writer\Feed;
        $writer->setLastBuildDate();
        $dateNow = new DateTime();
        $this->assertTrue($dateNow >= $writer->getLastBuildDate());
    }

    public function testSetLastBuildDateUsesGivenUnixTimestamp()
    {
        $writer = new Writer\Feed;
        $writer->setLastBuildDate(1234567890);
        $myDate = new DateTime('@' . 1234567890);
        $this->assertEquals($myDate, $writer->getLastBuildDate());
    }

    /**
     * @group ZF-12023
     */
    public function testSetLastBuildDateUsesGivenUnixTimestampThatIsLessThanTenDigits()
    {
        $writer = new Writer\Feed;
        $writer->setLastBuildDate(123456789);
        $myDate = new DateTime('@' . 123456789);
        $this->assertEquals($myDate, $writer->getLastBuildDate());
    }

    /**
     * @group ZF-11610
     */
    public function testSetLastBuildDateUsesGivenUnixTimestampThatIsAVerySmallInteger()
    {
        $writer = new Writer\Feed;
        $writer->setLastBuildDate(123);
        $myDate = new DateTime('@' . 123);
        $this->assertEquals($myDate, $writer->getLastBuildDate());
    }

    public function testSetLastBuildDateUsesDateTimeObject()
    {
        $myDate = new DateTime('@' . 1234567890);
        $writer = new Writer\Feed;
        $writer->setLastBuildDate($myDate);
        $this->assertEquals($myDate, $writer->getLastBuildDate());
    }

    public function testSetLastBuildDateThrowsExceptionOnInvalidParameter()
    {
        $writer = new Writer\Feed;
        try {
            $writer->setLastBuildDate('abc');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testGetLastBuildDateReturnsNullIfDateNotSet()
    {
        $writer = new Writer\Feed;
        $this->assertTrue(is_null($writer->getLastBuildDate()));
    }

    public function testGetCopyrightReturnsNullIfDateNotSet()
    {
        $writer = new Writer\Feed;
        $this->assertTrue(is_null($writer->getCopyright()));
    }

    public function testSetsDescription()
    {
        $writer = new Writer\Feed;
        $writer->setDescription('abc');
        $this->assertEquals('abc', $writer->getDescription());
    }

    public function testSetDescriptionThrowsExceptionOnInvalidParameter()
    {
        $writer = new Writer\Feed;
        try {
            $writer->setDescription('');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testGetDescriptionReturnsNullIfDateNotSet()
    {
        $writer = new Writer\Feed;
        $this->assertTrue(is_null($writer->getDescription()));
    }

    public function testSetsId()
    {
        $writer = new Writer\Feed;
        $writer->setId('http://www.example.com/id');
        $this->assertEquals('http://www.example.com/id', $writer->getId());
    }

    public function testSetsIdAcceptsUrns()
    {
        $writer = new Writer\Feed;
        $writer->setId('urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6');
        $this->assertEquals('urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6', $writer->getId());
    }

    public function testSetsIdAcceptsSimpleTagUri()
    {
        $writer = new Writer\Feed;
        $writer->setId('tag:example.org,2010:/foo/bar/');
        $this->assertEquals('tag:example.org,2010:/foo/bar/', $writer->getId());
    }

    public function testSetsIdAcceptsComplexTagUri()
    {
        $writer = new Writer\Feed;
        $writer->setId('tag:diveintomark.org,2004-05-27:/archives/2004/05/27/howto-atom-linkblog');
        $this->assertEquals('tag:diveintomark.org,2004-05-27:/archives/2004/05/27/howto-atom-linkblog',
                            $writer->getId());
    }

    public function testSetIdThrowsExceptionOnInvalidParameter()
    {
        $writer = new Writer\Feed;
        try {
            $writer->setId('');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testSetIdThrowsExceptionOnInvalidUri()
    {
        $writer = new Writer\Feed;
        try {
            $writer->setId('http://');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testGetIdReturnsNullIfDateNotSet()
    {
        $writer = new Writer\Feed;
        $this->assertTrue(is_null($writer->getId()));
    }

    public function testSetsLanguage()
    {
        $writer = new Writer\Feed;
        $writer->setLanguage('abc');
        $this->assertEquals('abc', $writer->getLanguage());
    }

    public function testSetLanguageThrowsExceptionOnInvalidParameter()
    {
        $writer = new Writer\Feed;
        try {
            $writer->setLanguage('');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testGetLanguageReturnsNullIfDateNotSet()
    {
        $writer = new Writer\Feed;
        $this->assertTrue(is_null($writer->getLanguage()));
    }

    public function testSetsLink()
    {
        $writer = new Writer\Feed;
        $writer->setLink('http://www.example.com/id');
        $this->assertEquals('http://www.example.com/id', $writer->getLink());
    }

    public function testSetLinkThrowsExceptionOnEmptyString()
    {
        $writer = new Writer\Feed;
        try {
            $writer->setLink('');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testSetLinkThrowsExceptionOnInvalidUri()
    {
        $writer = new Writer\Feed;
        try {
            $writer->setLink('http://');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testGetLinkReturnsNullIfDateNotSet()
    {
        $writer = new Writer\Feed;
        $this->assertTrue(is_null($writer->getLink()));
    }

    public function testSetsEncoding()
    {
        $writer = new Writer\Feed;
        $writer->setEncoding('utf-16');
        $this->assertEquals('utf-16', $writer->getEncoding());
    }

    public function testSetEncodingThrowsExceptionOnInvalidParameter()
    {
        $writer = new Writer\Feed;
        try {
            $writer->setEncoding('');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testGetEncodingReturnsUtf8IfNotSet()
    {
        $writer = new Writer\Feed;
        $this->assertEquals('UTF-8', $writer->getEncoding());
    }

    public function testSetsTitle()
    {
        $writer = new Writer\Feed;
        $writer->setTitle('abc');
        $this->assertEquals('abc', $writer->getTitle());
    }

    public function testSetTitleThrowsExceptionOnInvalidParameter()
    {
        $writer = new Writer\Feed;
        try {
            $writer->setTitle('');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testGetTitleReturnsNullIfDateNotSet()
    {
        $writer = new Writer\Feed;
        $this->assertTrue(is_null($writer->getTitle()));
    }

    public function testSetsGeneratorName()
    {
        $writer = new Writer\Feed;
        $writer->setGenerator(array('name'=> 'ZFW'));
        $this->assertEquals(array('name'=> 'ZFW'), $writer->getGenerator());
    }

    public function testSetsGeneratorVersion()
    {
        $writer = new Writer\Feed;
        $writer->setGenerator(array('name'    => 'ZFW',
                                    'version' => '1.0'));
        $this->assertEquals(array('name'    => 'ZFW',
                                  'version' => '1.0'), $writer->getGenerator());
    }

    public function testSetsGeneratorUri()
    {
        $writer = new Writer\Feed;
        $writer->setGenerator(array('name'=> 'ZFW',
                                    'uri' => 'http://www.example.com'));
        $this->assertEquals(array('name'=> 'ZFW',
                                  'uri' => 'http://www.example.com'), $writer->getGenerator());
    }

    public function testSetsGeneratorThrowsExceptionOnInvalidName()
    {
        $writer = new Writer\Feed;
        try {
            $writer->setGenerator(array());
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testSetsGeneratorThrowsExceptionOnInvalidVersion()
    {
        $writer = new Writer\Feed;
        try {
            $writer->setGenerator(array('name'   => 'ZFW',
                                        'version'=> ''));
            $this->fail('Should have failed since version is empty');
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testSetsGeneratorThrowsExceptionOnInvalidUri()
    {
        $this->markTestIncomplete('Pending Zend\URI fix for validation');
        $writer = new Writer\Feed;
        try {
            $writer->setGenerator(array('name'=> 'ZFW',
                                        'uri' => 'notauri'));
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    /**
     * @deprecated
     */
    public function testSetsGeneratorName_Deprecated()
    {
        $writer = new Writer\Feed;
        $writer->setGenerator('ZFW');
        $this->assertEquals(array('name'=> 'ZFW'), $writer->getGenerator());
    }

    /**
     * @deprecated
     */
    public function testSetsGeneratorVersion_Deprecated()
    {
        $writer = new Writer\Feed;
        $writer->setGenerator('ZFW', '1.0');
        $this->assertEquals(array('name'    => 'ZFW',
                                  'version' => '1.0'), $writer->getGenerator());
    }

    /**
     * @deprecated
     */
    public function testSetsGeneratorUri_Deprecated()
    {
        $writer = new Writer\Feed;
        $writer->setGenerator('ZFW', null, 'http://www.example.com');
        $this->assertEquals(array('name'=> 'ZFW',
                                  'uri' => 'http://www.example.com'), $writer->getGenerator());
    }

    /**
     * @deprecated
     */
    public function testSetsGeneratorThrowsExceptionOnInvalidName_Deprecated()
    {
        $writer = new Writer\Feed;
        try {
            $writer->setGenerator('');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    /**
     * @deprecated
     */
    public function testSetsGeneratorThrowsExceptionOnInvalidVersion_Deprecated()
    {
        $writer = new Writer\Feed;
        try {
            $writer->setGenerator('ZFW', '');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    /**
     * @deprecated
     */
    public function testSetsGeneratorThrowsExceptionOnInvalidUri_Deprecated()
    {
        $this->markTestIncomplete('Pending Zend\URI fix for validation');
        $writer = new Writer\Feed;
        try {
            $writer->setGenerator('ZFW', null, 'notauri');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testGetGeneratorReturnsNullIfDateNotSet()
    {
        $writer = new Writer\Feed;
        $this->assertTrue(is_null($writer->getGenerator()));
    }

    public function testSetsFeedLink()
    {
        $writer = new Writer\Feed;
        $writer->setFeedLink('http://www.example.com/rss', 'RSS');
        $this->assertEquals(array('rss'=> 'http://www.example.com/rss'), $writer->getFeedLinks());
    }

    public function testSetsFeedLinkThrowsExceptionOnInvalidType()
    {
        $writer = new Writer\Feed;
        try {
            $writer->setFeedLink('http://www.example.com/rss', 'abc');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testSetsFeedLinkThrowsExceptionOnInvalidUri()
    {
        $writer = new Writer\Feed;
        try {
            $writer->setFeedLink('http://', 'rss');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testGetFeedLinksReturnsNullIfNotSet()
    {
        $writer = new Writer\Feed;
        $this->assertTrue(is_null($writer->getFeedLinks()));
    }

    public function testSetsBaseUrl()
    {
        $writer = new Writer\Feed;
        $writer->setBaseUrl('http://www.example.com');
        $this->assertEquals('http://www.example.com', $writer->getBaseUrl());
    }

    public function testSetsBaseUrlThrowsExceptionOnInvalidUri()
    {
        $writer = new Writer\Feed;
        try {
            $writer->setBaseUrl('http://');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testGetBaseUrlReturnsNullIfNotSet()
    {
        $writer = new Writer\Feed;
        $this->assertTrue(is_null($writer->getBaseUrl()));
    }

    public function testAddsHubUrl()
    {
        $writer = new Writer\Feed;
        $writer->addHub('http://www.example.com/hub');
        $this->assertEquals(array('http://www.example.com/hub'), $writer->getHubs());
    }

    public function testAddsManyHubUrls()
    {
        $writer = new Writer\Feed;
        $writer->addHubs(array('http://www.example.com/hub', 'http://www.example.com/hub2'));
        $this->assertEquals(array('http://www.example.com/hub', 'http://www.example.com/hub2'), $writer->getHubs());
    }

    public function testAddingHubUrlThrowsExceptionOnInvalidUri()
    {
        $writer = new Writer\Feed;
        try {
            $writer->addHub('http://');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testAddingHubUrlReturnsNullIfNotSet()
    {
        $writer = new Writer\Feed;
        $this->assertTrue(is_null($writer->getHubs()));
    }

    public function testCreatesNewEntryDataContainer()
    {
        $writer = new Writer\Feed;
        $entry  = $writer->createEntry();
        $this->assertTrue($entry instanceof Writer\Entry);
    }

    public function testAddsCategory()
    {
        $writer = new Writer\Feed;
        $writer->addCategory(array('term'=> 'cat_dog'));
        $this->assertEquals(array(array('term'=> 'cat_dog')), $writer->getCategories());
    }

    public function testAddsManyCategories()
    {
        $writer = new Writer\Feed;
        $writer->addCategories(array(array('term'=> 'cat_dog'), array('term'=> 'cat_mouse')));
        $this->assertEquals(array(array('term'=> 'cat_dog'), array('term'=> 'cat_mouse')), $writer->getCategories());
    }

    public function testAddingCategoryWithoutTermThrowsException()
    {
        $writer = new Writer\Feed;
        try {
            $writer->addCategory(array('label'  => 'Cats & Dogs',
                                       'scheme' => 'http://www.example.com/schema1'));
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testAddingCategoryWithInvalidUriAsSchemeThrowsException()
    {
        $writer = new Writer\Feed;
        try {
            $writer->addCategory(array('term'   => 'cat_dog',
                                       'scheme' => 'http://'));
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    // Image Tests

    public function testSetsImageUri()
    {
        $writer = new Writer\Feed;
        $writer->setImage(array(
                               'uri' => 'http://www.example.com/logo.gif'
                          ));
        $this->assertEquals(array(
                                 'uri' => 'http://www.example.com/logo.gif'
                            ), $writer->getImage());
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testSetsImageUriThrowsExceptionOnEmptyUri()
    {
        $writer = new Writer\Feed;
        $writer->setImage(array(
                               'uri' => ''
                          ));
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testSetsImageUriThrowsExceptionOnMissingUri()
    {
        $writer = new Writer\Feed;
        $writer->setImage(array());
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testSetsImageUriThrowsExceptionOnInvalidUri()
    {
        $writer = new Writer\Feed;
        $writer->setImage(array(
                               'uri' => 'http://'
                          ));
    }

    public function testSetsImageLink()
    {
        $writer = new Writer\Feed;
        $writer->setImage(array(
                               'uri'  => 'http://www.example.com/logo.gif',
                               'link' => 'http://www.example.com'
                          ));
        $this->assertEquals(array(
                                 'uri'  => 'http://www.example.com/logo.gif',
                                 'link' => 'http://www.example.com'
                            ), $writer->getImage());
    }

    public function testSetsImageTitle()
    {
        $writer = new Writer\Feed;
        $writer->setImage(array(
                               'uri'   => 'http://www.example.com/logo.gif',
                               'title' => 'Image title'
                          ));
        $this->assertEquals(array(
                                 'uri'   => 'http://www.example.com/logo.gif',
                                 'title' => 'Image title'
                            ), $writer->getImage());
    }

    public function testSetsImageHeight()
    {
        $writer = new Writer\Feed;
        $writer->setImage(array(
                               'uri'    => 'http://www.example.com/logo.gif',
                               'height' => '88'
                          ));
        $this->assertEquals(array(
                                 'uri'    => 'http://www.example.com/logo.gif',
                                 'height' => '88'
                            ), $writer->getImage());
    }

    public function testSetsImageWidth()
    {
        $writer = new Writer\Feed;
        $writer->setImage(array(
                               'uri'   => 'http://www.example.com/logo.gif',
                               'width' => '88'
                          ));
        $this->assertEquals(array(
                                 'uri'   => 'http://www.example.com/logo.gif',
                                 'width' => '88'
                            ), $writer->getImage());
    }

    public function testSetsImageDescription()
    {
        $writer = new Writer\Feed;
        $writer->setImage(array(
                               'uri'         => 'http://www.example.com/logo.gif',
                               'description' => 'Image description'
                          ));
        $this->assertEquals(array(
                                 'uri'         => 'http://www.example.com/logo.gif',
                                 'description' => 'Image description'
                            ), $writer->getImage());
    }

    public function testGetCategoriesReturnsNullIfNotSet()
    {
        $writer = new Writer\Feed;
        $this->assertTrue(is_null($writer->getCategories()));
    }

    public function testAddsAndOrdersEntriesByDateIfRequested()
    {
        $writer = new Writer\Feed;
        $entry  = $writer->createEntry();
        $entry->setDateCreated(1234567890);
        $entry2 = $writer->createEntry();
        $entry2->setDateCreated(1230000000);
        $writer->addEntry($entry);
        $writer->addEntry($entry2);
        $writer->orderByDate();
        $this->assertEquals(1230000000, $writer->getEntry(1)->getDateCreated()->getTimestamp());
    }

    /**
     * @covers Zend\Feed\Writer\Feed::orderByDate
     */
    public function testAddsAndOrdersEntriesByModifiedDate()
    {
        $writer = new Writer\Feed;
        $entry  = $writer->createEntry();
        $entry->setDateModified(1234567890);
        $entry2 = $writer->createEntry();
        $entry2->setDateModified(1230000000);
        $writer->addEntry($entry);
        $writer->addEntry($entry2);
        $writer->orderByDate();
        $this->assertEquals(1230000000, $writer->getEntry(1)->getDateModified()->getTimestamp());
    }

    /**
     * @covers Zend\Feed\Writer\Feed::getEntry
     */
    public function testGetEntry()
    {
        $writer = new Writer\Feed;
        $entry = $writer->createEntry();
        $entry->setTitle('foo');
        $writer->addEntry($entry);
        $this->assertEquals('foo', $writer->getEntry()->getTitle());
    }

    /**
     * @covers Zend\Feed\Writer\Feed::removeEntry
     */
    public function testGetEntryException()
    {
        $writer = new Writer\Feed;
        try {
            $writer->getEntry(1);
            $this->fail();
        } catch (Writer\Exception\InvalidArgumentException $e) {
        }
    }

    /**
     * @covers Zend\Feed\Writer\Feed::removeEntry
     */
    public function testRemoveEntry()
    {
        $writer = new Writer\Feed;
        $entry = $writer->createEntry();
        $entry->setDateCreated(1234567890);
        $entry2 = $writer->createEntry();
        $entry2->setDateCreated(1230000000);
        $entry3 = $writer->createEntry();
        $entry3->setDateCreated(1239999999);

        $writer->addEntry($entry);
        $writer->addEntry($entry2);
        $writer->addEntry($entry3);
        $writer->orderByDate();
        $this->assertEquals('1234567890', $writer->getEntry(1)->getDateCreated()->getTimestamp());

        $writer->removeEntry(1);
        $writer->orderByDate();
        $this->assertEquals('1230000000', $writer->getEntry(1)->getDateCreated()->getTimestamp());
    }


    /**
     * @covers Zend\Feed\Writer\Feed::removeEntry
     */
    public function testRemoveEntryException()
    {
        $writer = new Writer\Feed;
        try {
            $writer->removeEntry(1);
            $this->fail();
        } catch (Writer\Exception\InvalidArgumentException $e) {
        }
    }

    /**
     * @covers Zend\Feed\Writer\Feed::createTombstone
     */
    public function testCreateTombstone()
    {
        $writer = new Writer\Feed;
        $tombstone = $writer->createTombstone();

        $this->assertInstanceOf('Zend\Feed\Writer\Deleted', $tombstone);

        return $tombstone;
    }

    /**
     * @covers Zend\Feed\Writer\Feed::addTombstone
     */
    public function testAddTombstone()
    {
        $writer = new Writer\Feed;
        $tombstone = $writer->createTombstone();
        $writer->addTombstone($tombstone);

        $this->assertInstanceOf('Zend\Feed\Writer\Deleted', $writer->getEntry(0));
    }

    /**
     * @covers Zend\Feed\Writer\Feed::export
     */
    public function testExportRss()
    {
        $writer = new Writer\Feed;
        $writer->setTitle('foo');
        $writer->setDescription('bar');
        $writer->setLink('http://www.example.org');

        $export = $writer->export('rss');

        $feed = <<<'EOT'
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
  <channel>
    <title>foo</title>
    <description>bar</description>
    <generator>Zend_Feed_Writer %version% (http://framework.zend.com)</generator>
    <link>http://www.example.org</link>
  </channel>
</rss>

EOT;
        $feed = str_replace('%version%', Version::VERSION, $feed);
        $this->assertEquals($feed, $export);
    }

    /**
     * @covers Zend\Feed\Writer\Feed::export
     */
    public function testExportRssIgnoreExceptions()
    {
        $writer = new Writer\Feed;
        $export = $writer->export('rss', true);

        $feed = <<<'EOT'
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
  <channel>
    <generator>Zend_Feed_Writer %version% (http://framework.zend.com)</generator>
  </channel>
</rss>

EOT;
        $feed = str_replace('%version%', Version::VERSION, $feed);
        $this->assertEquals($feed, $export);
    }

    /**
     * @covers Zend\Feed\Writer\Feed::export
     */
    public function testExportWrongTypeException()
    {
        $writer = new Writer\Feed;
        try {
            $writer->export('foo');
            $this->fail();
        } catch (Writer\Exception\InvalidArgumentException $e) {
        }
    }

    public function testFluentInterface()
    {
        $writer = new Writer\Feed;
        $return = $writer->addAuthor(array('name' => 'foo'))
                         ->addAuthors(array(array('name' => 'foo')))
                         ->setCopyright('copyright')
                         ->addCategories(array(array('term' => 'foo')))
                         ->addCategory(array('term' => 'foo'))
                         ->addHub('foo')
                         ->addHubs(array('foo'))
                         ->setBaseUrl('http://www.example.com')
                         ->setDateCreated(null)
                         ->setDateModified(null)
                         ->setDescription('description')
                         ->setEncoding('utf-8')
                         ->setId('1')
                         ->setImage(array('uri' => 'http://www.example.com'))
                         ->setLanguage('fr')
                         ->setLastBuildDate(null)
                         ->setLink('foo')
                         ->setTitle('foo')
                         ->setType('foo');

        $this->assertSame($return, $writer);
    }
}
