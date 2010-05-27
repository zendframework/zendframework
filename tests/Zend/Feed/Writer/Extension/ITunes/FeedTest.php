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
 * @package    Zend_Json
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Feed\Writer\Extension\ITunes;
use Zend\Feed\Writer\Feed;

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

    public function testSetBlock()
    {
        $feed = new Feed\Feed;
        $feed->setItunesBlock('yes');
        $this->assertEquals('yes', $feed->getItunesBlock());
    }
    
    public function testSetBlockThrowsExceptionOnNonAlphaValue()
    {
        $this->setExpectedException('Zend\Feed\Exception');
        $feed = new Feed\Feed;
        $feed->setItunesBlock('123');
    }
    
    public function testSetBlockThrowsExceptionIfValueGreaterThan255CharsLength()
    {
        $this->setExpectedException('Zend\Feed\Exception');
        $feed = new Feed\Feed;
        $feed->setItunesBlock(str_repeat('a', 256));
    }
    
    public function testAddAuthors()
    {
        $feed = new Feed\Feed;
        $feed->addItunesAuthors(array('joe', 'jane'));
        $this->assertEquals(array('joe', 'jane'), $feed->getItunesAuthors());
    }
    
    public function testAddAuthor()
    {
        $feed = new Feed\Feed;
        $feed->addItunesAuthor('joe');
        $this->assertEquals(array('joe'), $feed->getItunesAuthors());
    }
    
    public function testAddAuthorThrowsExceptionIfValueGreaterThan255CharsLength()
    {
        $this->setExpectedException('Zend\Feed\Exception');
        $feed = new Feed\Feed;
        $feed->addItunesAuthor(str_repeat('a', 256));
    }
    
    public function testSetCategories()
    {
        $feed = new Feed\Feed;
        $cats = array(
            'cat1',
            'cat2' => array('cat2-1', 'cat2-a&b')
        );
        $feed->setItunesCategories($cats);
        $this->assertEquals($cats, $feed->getItunesCategories());
    }
    
    public function testSetCategoriesThrowsExceptionIfAnyCatNameGreaterThan255CharsLength()
    {
        $this->setExpectedException('Zend\Feed\Exception');
        $feed = new Feed\Feed;
        $cats = array(
            'cat1',
            'cat2' => array('cat2-1', str_repeat('a', 256))
        );
        $feed->setItunesCategories($cats);
        $this->assertEquals($cats, $feed->getItunesAuthors());
    }
    
    public function testSetImageAsPngFile()
    {
        $feed = new Feed\Feed;
        $feed->setItunesImage('http://www.example.com/image.png');
        $this->assertEquals('http://www.example.com/image.png', $feed->getItunesImage());
    }
    
    public function testSetImageAsJpgFile()
    {
        $feed = new Feed\Feed;
        $feed->setItunesImage('http://www.example.com/image.jpg');
        $this->assertEquals('http://www.example.com/image.jpg', $feed->getItunesImage());
    }
    
    public function testSetImageThrowsExceptionOnInvalidUri()
    {
        $this->setExpectedException('Zend\Feed\Exception');
        $feed = new Feed\Feed;
        $feed->setItunesImage('http://');
    }
    
    public function testSetImageThrowsExceptionOnInvalidImageExtension()
    {
        $this->setExpectedException('Zend\Feed\Exception');
        $feed = new Feed\Feed;
        $feed->setItunesImage('http://www.example.com/image.gif');
    }
    
    public function testSetDurationAsSeconds()
    {
        $feed = new Feed\Feed;
        $feed->setItunesDuration(23);
        $this->assertEquals(23, $feed->getItunesDuration());
    }
    
    public function testSetDurationAsMinutesAndSeconds()
    {
        $feed = new Feed\Feed;
        $feed->setItunesDuration('23:23');
        $this->assertEquals('23:23', $feed->getItunesDuration());
    }
    
    public function testSetDurationAsHoursMinutesAndSeconds()
    {
        $feed = new Feed\Feed;
        $feed->setItunesDuration('23:23:23');
        $this->assertEquals('23:23:23', $feed->getItunesDuration());
    }
    
    public function testSetDurationThrowsExceptionOnUnknownFormat()
    {
        $this->setExpectedException('Zend\Feed\Exception');
        $feed = new Feed\Feed;
        $feed->setItunesDuration('abc');
    }
    
    public function testSetDurationThrowsExceptionOnInvalidSeconds()
    {
        $this->setExpectedException('Zend\Feed\Exception');
        $feed = new Feed\Feed;
        $feed->setItunesDuration('23:456');
    }
    
    public function testSetDurationThrowsExceptionOnInvalidMinutes()
    {
        $this->setExpectedException('Zend\Feed\Exception');
        $feed = new Feed\Feed;
        $feed->setItunesDuration('23:234:45');
    }
    
    public function testSetExplicitToYes()
    {
        $feed = new Feed\Feed;
        $feed->setItunesExplicit('yes');
        $this->assertEquals('yes', $feed->getItunesExplicit());
    }
    
    public function testSetExplicitToNo()
    {
        $feed = new Feed\Feed;
        $feed->setItunesExplicit('no');
        $this->assertEquals('no', $feed->getItunesExplicit());
    }
    
    public function testSetExplicitToClean()
    {
        $feed = new Feed\Feed;
        $feed->setItunesExplicit('clean');
        $this->assertEquals('clean', $feed->getItunesExplicit());
    }
    
    public function testSetExplicitThrowsExceptionOnUnknownTerm()
    {
        $this->setExpectedException('Zend\Feed\Exception');
        $feed = new Feed\Feed;
        $feed->setItunesExplicit('abc');
    }
    
    public function testSetKeywords()
    {
        $feed = new Feed\Feed;
        $words = array(
            'a1', 'a2', 'a3', 'a4', 'a5', 'a6', 'a7', 'a8', 'a9', 'a10', 'a11', 'a12'
        );
        $feed->setItunesKeywords($words);
        $this->assertEquals($words, $feed->getItunesKeywords());
    }
    
    public function testSetKeywordsThrowsExceptionIfMaxKeywordsExceeded()
    {
        $this->setExpectedException('Zend\Feed\Exception');
        $feed = new Feed\Feed;
        $words = array(
            'a1', 'a2', 'a3', 'a4', 'a5', 'a6', 'a7', 'a8', 'a9', 'a10', 'a11', 'a12', 'a13'
        );
        $feed->setItunesKeywords($words);
    }
    
    public function testSetKeywordsThrowsExceptionIfFormattedKeywordsExceeds255CharLength()
    {
        $this->setExpectedException('Zend\Feed\Exception');
        $feed = new Feed\Feed;
        $words = array(
            str_repeat('a', 253), str_repeat('b', 2)
        );
        $feed->setItunesKeywords($words);
    }
    
    public function testSetNewFeedUrl()
    {
        $feed = new Feed\Feed;
        $feed->setItunesNewFeedUrl('http://example.com/feed');
        $this->assertEquals('http://example.com/feed', $feed->getItunesNewFeedUrl());
    }
    
    public function testSetNewFeedUrlThrowsExceptionOnInvalidUri()
    {
        $this->setExpectedException('Zend\Feed\Exception');
        $feed = new Feed\Feed;
        $feed->setItunesNewFeedUrl('http://');
    }
    
    public function testAddOwner()
    {
        $feed = new Feed\Feed;
        $feed->addItunesOwner(array('name'=>'joe','email'=>'joe@example.com'));
        $this->assertEquals(array(array('name'=>'joe','email'=>'joe@example.com')), $feed->getItunesOwners());
    }
    
    public function testAddOwners()
    {
        $feed = new Feed\Feed;
        $feed->addItunesOwners(array(array('name'=>'joe','email'=>'joe@example.com')));
        $this->assertEquals(array(array('name'=>'joe','email'=>'joe@example.com')), $feed->getItunesOwners());
    }
    
    public function testSetSubtitle()
    {
        $feed = new Feed\Feed;
        $feed->setItunesSubtitle('abc');
        $this->assertEquals('abc', $feed->getItunesSubtitle());
    }
    
    public function testSetSubtitleThrowsExceptionWhenValueExceeds255Chars()
    {
        $this->setExpectedException('Zend\Feed\Exception');
        $feed = new Feed\Feed;
        $feed->setItunesSubtitle(str_repeat('a', 256));
    }
    
    public function testSetSummary()
    {
        $feed = new Feed\Feed;
        $feed->setItunesSummary('abc');
        $this->assertEquals('abc', $feed->getItunesSummary());
    }
    
    public function testSetSummaryThrowsExceptionWhenValueExceeds4000Chars()
    {
        $this->setExpectedException('Zend\Feed\Exception');
        $feed = new Feed\Feed;
        $feed->setItunesSummary(str_repeat('a',4001));
    }

}
