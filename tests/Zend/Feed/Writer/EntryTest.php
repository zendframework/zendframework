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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Feed\Writer;

use DateTime;
use Zend\Feed\Writer;

/**
 * @category   Zend
 * @package    Zend_Exception
 * @subpackage UnitTests
 * @group      Zend_Feed
 * @group      Zend_Feed_Writer
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 */
class EntryTest extends \PHPUnit_Framework_TestCase
{

    protected $_feedSamplePath = null;

    public function setup()
    {
        $this->_feedSamplePath = dirname(__FILE__) . '/_files';
    }

    public function testAddsAuthorNameFromArray()
    {
        $entry = new Writer\Entry;
        $entry->addAuthor(array('name'=> 'Joe'));
        $this->assertEquals(array(array('name'=> 'Joe')), $entry->getAuthors());
    }

    public function testAddsAuthorEmailFromArray()
    {
        $entry = new Writer\Entry;
        $entry->addAuthor(array('name' => 'Joe',
                                'email'=> 'joe@example.com'));
        $this->assertEquals(array(array('name'  => 'Joe',
                                        'email' => 'joe@example.com')), $entry->getAuthors());
    }

    public function testAddsAuthorUriFromArray()
    {
        $entry = new Writer\Entry;
        $entry->addAuthor(array('name'=> 'Joe',
                                'uri' => 'http://www.example.com'));
        $this->assertEquals(array(array('name'=> 'Joe',
                                        'uri' => 'http://www.example.com')), $entry->getAuthors());
    }

    public function testAddAuthorThrowsExceptionOnInvalidNameFromArray()
    {
        $entry = new Writer\Entry;
        try {
            $entry->addAuthor(array('name'=> ''));
            $this->fail();
        } catch (Writer\Exception\InvalidArgumentException $e) {
        }
    }

    public function testAddAuthorThrowsExceptionOnInvalidEmailFromArray()
    {
        $entry = new Writer\Entry;
        try {
            $entry->addAuthor(array('name' => 'Joe',
                                    'email'=> ''));
            $this->fail();
        } catch (Writer\Exception\InvalidArgumentException $e) {
        }
    }

    public function testAddAuthorThrowsExceptionOnInvalidUriFromArray()
    {
        $this->markTestIncomplete('Pending Zend\URI fix for validation');
        $entry = new Writer\Entry;
        try {
            $entry->addAuthor(array('name'=> 'Joe',
                                    'uri' => 'notauri'));
            $this->fail();
        } catch (Writer\Exception\InvalidArgumentException $e) {
        }
    }

    public function testAddAuthorThrowsExceptionIfNameOmittedFromArray()
    {
        $entry = new Writer\Entry;
        try {
            $entry->addAuthor(array('uri'=> 'notauri'));
            $this->fail();
        } catch (Writer\Exception\InvalidArgumentException $e) {
        }
    }

    public function testAddsAuthorsFromArrayOfAuthors()
    {
        $entry = new Writer\Entry;
        $entry->addAuthors(array(
                                array('name'=> 'Joe',
                                      'uri' => 'http://www.example.com'),
                                array('name'=> 'Jane',
                                      'uri' => 'http://www.example.com')
                           ));
        $expected = array(
            array('name'=> 'Joe',
                  'uri' => 'http://www.example.com'),
            array('name'=> 'Jane',
                  'uri' => 'http://www.example.com')
        );
        $this->assertEquals($expected, $entry->getAuthors());
    }

    public function testAddsEnclosure()
    {
        $entry = new Writer\Entry;
        $entry->setEnclosure(array(
                                  'type'   => 'audio/mpeg',
                                  'uri'    => 'http://example.com/audio.mp3',
                                  'length' => '1337'
                             ));
        $expected = array(
            'type'   => 'audio/mpeg',
            'uri'    => 'http://example.com/audio.mp3',
            'length' => '1337'
        );
        $this->assertEquals($expected, $entry->getEnclosure());
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testAddsEnclosureThrowsExceptionOnMissingUri()
    {
        $this->markTestIncomplete('Pending Zend\URI fix for validation');
        $entry = new Writer\Entry;
        $entry->setEnclosure(array(
                                  'type'   => 'audio/mpeg',
                                  'length' => '1337'
                             ));
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testAddsEnclosureThrowsExceptionWhenUriIsInvalid()
    {
        $this->markTestIncomplete('Pending Zend\URI fix for validation');
        $entry = new Writer\Entry;
        $entry->setEnclosure(array(
                                  'type'   => 'audio/mpeg',
                                  'uri'    => 'http://',
                                  'length' => '1337'
                             ));
    }

    public function testSetsCopyright()
    {
        $entry = new Writer\Entry;
        $entry->setCopyright('Copyright (c) 2009 Paddy Brady');
        $this->assertEquals('Copyright (c) 2009 Paddy Brady', $entry->getCopyright());
    }

    public function testSetCopyrightThrowsExceptionOnInvalidParam()
    {
        $entry = new Writer\Entry;
        try {
            $entry->setCopyright('');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testSetsContent()
    {
        $entry = new Writer\Entry;
        $entry->setContent('I\'m content.');
        $this->assertEquals("I'm content.", $entry->getContent());
    }

    public function testSetContentThrowsExceptionOnInvalidParam()
    {
        $entry = new Writer\Entry;
        try {
            $entry->setContent('');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testSetDateCreatedDefaultsToCurrentTime()
    {
        $entry = new Writer\Entry;
        $entry->setDateCreated();
        $dateNow = new DateTime();
        $this->assertTrue($dateNow >= $entry->getDateCreated());
    }

    public function testSetDateCreatedUsesGivenUnixTimestamp()
    {
        $entry = new Writer\Entry;
        $entry->setDateCreated(1234567890);
        $myDate = new DateTime('@' . 1234567890);
        $this->assertEquals($myDate, $entry->getDateCreated());
    }

    /**
     * @group ZF-12070
     */
    public function testSetDateCreatedUsesGivenUnixTimestampWhenItIsLessThanTenDigits()
    {
        $entry = new Writer\Entry;
        $entry->setDateCreated(123456789);
        $myDate = new DateTime('@' . 123456789);
        $this->assertEquals($myDate, $entry->getDateCreated());
    }

    /**
     * @group ZF-11610
     */
    public function testSetDateCreatedUsesGivenUnixTimestampWhenItIsAVerySmallInteger()
    {
        $entry = new Writer\Entry;
        $entry->setDateCreated(123);
        $myDate = new DateTime('@' . 123);
        $this->assertEquals($myDate, $entry->getDateCreated());
    }

    public function testSetDateCreatedUsesDateTimeObject()
    {
        $myDate = new DateTime('@' . 1234567890);
        $entry = new Writer\Entry;
        $entry->setDateCreated($myDate);
        $this->assertEquals($myDate, $entry->getDateCreated());
    }

    public function testSetDateModifiedDefaultsToCurrentTime()
    {
        $entry = new Writer\Entry;
        $entry->setDateModified();
        $dateNow = new DateTime();
        $this->assertTrue($dateNow >= $entry->getDateModified());
    }

    public function testSetDateModifiedUsesGivenUnixTimestamp()
    {
        $entry = new Writer\Entry;
        $entry->setDateModified(1234567890);
        $myDate = new DateTime('@' . 1234567890);
        $this->assertEquals($myDate, $entry->getDateModified());
    }

    /**
     * @group ZF-12070
     */
    public function testSetDateModifiedUsesGivenUnixTimestampWhenItIsLessThanTenDigits()
    {
        $entry = new Writer\Entry;
        $entry->setDateModified(123456789);
        $myDate = new DateTime('@' . 123456789);
        $this->assertEquals($myDate, $entry->getDateModified());
    }

    /**
     * @group ZF-11610
     */
    public function testSetDateModifiedUsesGivenUnixTimestampWhenItIsAVerySmallInteger()
    {
        $entry = new Writer\Entry;
        $entry->setDateModified(123);
        $myDate = new DateTime('@' . 123);
        $this->assertEquals($myDate, $entry->getDateModified());
    }

    public function testSetDateModifiedUsesDateTimeObject()
    {
        $myDate = new DateTime('@' . 1234567890);
        $entry = new Writer\Entry;
        $entry->setDateModified($myDate);
        $this->assertEquals($myDate, $entry->getDateModified());
    }

    public function testSetDateCreatedThrowsExceptionOnInvalidParameter()
    {
        $entry = new Writer\Entry;
        try {
            $entry->setDateCreated('abc');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testSetDateModifiedThrowsExceptionOnInvalidParameter()
    {
        $entry = new Writer\Entry;
        try {
            $entry->setDateModified('abc');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testGetDateCreatedReturnsNullIfDateNotSet()
    {
        $entry = new Writer\Entry;
        $this->assertTrue(is_null($entry->getDateCreated()));
    }

    public function testGetDateModifiedReturnsNullIfDateNotSet()
    {
        $entry = new Writer\Entry;
        $this->assertTrue(is_null($entry->getDateModified()));
    }

    public function testGetCopyrightReturnsNullIfDateNotSet()
    {
        $entry = new Writer\Entry;
        $this->assertTrue(is_null($entry->getCopyright()));
    }

    public function testGetContentReturnsNullIfDateNotSet()
    {
        $entry = new Writer\Entry;
        $this->assertTrue(is_null($entry->getContent()));
    }

    public function testSetsDescription()
    {
        $entry = new Writer\Entry;
        $entry->setDescription('abc');
        $this->assertEquals('abc', $entry->getDescription());
    }

    public function testSetDescriptionThrowsExceptionOnInvalidParameter()
    {
        $entry = new Writer\Entry;
        try {
            $entry->setDescription('');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testGetDescriptionReturnsNullIfDateNotSet()
    {
        $entry = new Writer\Entry;
        $this->assertTrue(is_null($entry->getDescription()));
    }

    public function testSetsId()
    {
        $entry = new Writer\Entry;
        $entry->setId('http://www.example.com/id');
        $this->assertEquals('http://www.example.com/id', $entry->getId());
    }

    public function testSetIdThrowsExceptionOnInvalidParameter()
    {
        $entry = new Writer\Entry;
        try {
            $entry->setId('');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testGetIdReturnsNullIfNotSet()
    {
        $entry = new Writer\Entry;
        $this->assertTrue(is_null($entry->getId()));
    }

    public function testSetsLink()
    {
        $entry = new Writer\Entry;
        $entry->setLink('http://www.example.com/id');
        $this->assertEquals('http://www.example.com/id', $entry->getLink());
    }

    public function testSetLinkThrowsExceptionOnEmptyString()
    {
        $entry = new Writer\Entry;
        try {
            $entry->setLink('');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testSetLinkThrowsExceptionOnInvalidUri()
    {
        $entry = new Writer\Entry;
        try {
            $entry->setLink('http://');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testGetLinkReturnsNullIfNotSet()
    {
        $entry = new Writer\Entry;
        $this->assertTrue(is_null($entry->getLink()));
    }

    public function testSetsCommentLink()
    {
        $entry = new Writer\Entry;
        $entry->setCommentLink('http://www.example.com/id/comments');
        $this->assertEquals('http://www.example.com/id/comments', $entry->getCommentLink());
    }

    public function testSetCommentLinkThrowsExceptionOnEmptyString()
    {
        $entry = new Writer\Entry;
        try {
            $entry->setCommentLink('');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testSetCommentLinkThrowsExceptionOnInvalidUri()
    {
        $entry = new Writer\Entry;
        try {
            $entry->setCommentLink('http://');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testGetCommentLinkReturnsNullIfDateNotSet()
    {
        $entry = new Writer\Entry;
        $this->assertTrue(is_null($entry->getCommentLink()));
    }

    public function testSetsCommentFeedLink()
    {
        $entry = new Writer\Entry;

        $entry->setCommentFeedLink(array('uri' => 'http://www.example.com/id/comments',
                                         'type'=> 'rdf'));
        $this->assertEquals(array(array('uri' => 'http://www.example.com/id/comments',
                                        'type'=> 'rdf')), $entry->getCommentFeedLinks());
    }

    public function testSetCommentFeedLinkThrowsExceptionOnEmptyString()
    {
        $this->markTestIncomplete('Pending Zend\URI fix for validation');
        $entry = new Writer\Entry;
        try {
            $entry->setCommentFeedLink(array('uri' => '',
                                             'type'=> 'rdf'));
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testSetCommentFeedLinkThrowsExceptionOnInvalidUri()
    {
        $entry = new Writer\Entry;
        try {
            $entry->setCommentFeedLink(array('uri' => 'http://',
                                             'type'=> 'rdf'));
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testSetCommentFeedLinkThrowsExceptionOnInvalidType()
    {
        $entry = new Writer\Entry;
        try {
            $entry->setCommentFeedLink(array('uri' => 'http://www.example.com/id/comments',
                                             'type'=> 'foo'));
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testGetCommentFeedLinkReturnsNullIfNoneSet()
    {
        $entry = new Writer\Entry;
        $this->assertTrue(is_null($entry->getCommentFeedLinks()));
    }

    public function testSetsTitle()
    {
        $entry = new Writer\Entry;
        $entry->setTitle('abc');
        $this->assertEquals('abc', $entry->getTitle());
    }

    public function testSetTitleThrowsExceptionOnInvalidParameter()
    {
        $entry = new Writer\Entry;
        try {
            $entry->setTitle('');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testGetTitleReturnsNullIfDateNotSet()
    {
        $entry = new Writer\Entry;
        $this->assertTrue(is_null($entry->getTitle()));
    }

    public function testSetsCommentCount()
    {
        $entry = new Writer\Entry;
        $entry->setCommentCount('10');
        $this->assertEquals(10, $entry->getCommentCount());
    }

    public function testSetCommentCountThrowsExceptionOnInvalidEmptyParameter()
    {
        $entry = new Writer\Entry;
        try {
            $entry->setCommentCount('');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testSetCommentCountThrowsExceptionOnInvalidNonIntegerParameter()
    {
        $entry = new Writer\Entry;
        try {
            $entry->setCommentCount('a');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testGetCommentCountReturnsNullIfDateNotSet()
    {
        $entry = new Writer\Entry;
        $this->assertTrue(is_null($entry->getCommentCount()));
    }

}
