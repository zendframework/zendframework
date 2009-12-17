<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Zend/Feed/Writer/Feed.php';

class Zend_Feed_Writer_Extension_ITunes_FeedTest extends PHPUnit_Framework_TestCase
{

    public function testSetBlock()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $feed->setItunesBlock('yes');
        $this->assertEquals('yes', $feed->getItunesBlock());
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testSetBlockThrowsExceptionOnNonAlphaValue()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $feed->setItunesBlock('123');
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testSetBlockThrowsExceptionIfValueGreaterThan255CharsLength()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $feed->setItunesBlock(str_repeat('a', 256));
    }
    
    public function testAddAuthors()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $feed->addItunesAuthors(array('joe', 'jane'));
        $this->assertEquals(array('joe', 'jane'), $feed->getItunesAuthors());
    }
    
    public function testAddAuthor()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $feed->addItunesAuthor('joe');
        $this->assertEquals(array('joe'), $feed->getItunesAuthors());
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testAddAuthorThrowsExceptionIfValueGreaterThan255CharsLength()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $feed->addItunesAuthor(str_repeat('a', 256));
    }
    
    public function testSetCategories()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $cats = array(
            'cat1',
            'cat2' => array('cat2-1', 'cat2-a&b')
        );
        $feed->setItunesCategories($cats);
        $this->assertEquals($cats, $feed->getItunesCategories());
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testSetCategoriesThrowsExceptionIfAnyCatNameGreaterThan255CharsLength()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $cats = array(
            'cat1',
            'cat2' => array('cat2-1', str_repeat('a', 256))
        );
        $feed->setItunesCategories($cats);
        $this->assertEquals($cats, $feed->getItunesAuthors());
    }
    
    public function testSetImageAsPngFile()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $feed->setItunesImage('http://www.example.com/image.png');
        $this->assertEquals('http://www.example.com/image.png', $feed->getItunesImage());
    }
    
    public function testSetImageAsJpgFile()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $feed->setItunesImage('http://www.example.com/image.jpg');
        $this->assertEquals('http://www.example.com/image.jpg', $feed->getItunesImage());
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testSetImageThrowsExceptionOnInvalidUri()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $feed->setItunesImage('http://');
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testSetImageThrowsExceptionOnInvalidImageExtension()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $feed->setItunesImage('http://www.example.com/image.gif');
    }
    
    public function testSetDurationAsSeconds()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $feed->setItunesDuration(23);
        $this->assertEquals(23, $feed->getItunesDuration());
    }
    
    public function testSetDurationAsMinutesAndSeconds()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $feed->setItunesDuration('23:23');
        $this->assertEquals('23:23', $feed->getItunesDuration());
    }
    
    public function testSetDurationAsHoursMinutesAndSeconds()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $feed->setItunesDuration('23:23:23');
        $this->assertEquals('23:23:23', $feed->getItunesDuration());
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testSetDurationThrowsExceptionOnUnknownFormat()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $feed->setItunesDuration('abc');
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testSetDurationThrowsExceptionOnInvalidSeconds()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $feed->setItunesDuration('23:456');
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testSetDurationThrowsExceptionOnInvalidMinutes()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $feed->setItunesDuration('23:234:45');
    }
    
    public function testSetExplicitToYes()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $feed->setItunesExplicit('yes');
        $this->assertEquals('yes', $feed->getItunesExplicit());
    }
    
    public function testSetExplicitToNo()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $feed->setItunesExplicit('no');
        $this->assertEquals('no', $feed->getItunesExplicit());
    }
    
    public function testSetExplicitToClean()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $feed->setItunesExplicit('clean');
        $this->assertEquals('clean', $feed->getItunesExplicit());
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testSetExplicitThrowsExceptionOnUnknownTerm()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $feed->setItunesExplicit('abc');
    }
    
    public function testSetKeywords()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $words = array(
            'a1', 'a2', 'a3', 'a4', 'a5', 'a6', 'a7', 'a8', 'a9', 'a10', 'a11', 'a12'
        );
        $feed->setItunesKeywords($words);
        $this->assertEquals($words, $feed->getItunesKeywords());
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testSetKeywordsThrowsExceptionIfMaxKeywordsExceeded()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $words = array(
            'a1', 'a2', 'a3', 'a4', 'a5', 'a6', 'a7', 'a8', 'a9', 'a10', 'a11', 'a12', 'a13'
        );
        $feed->setItunesKeywords($words);
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testSetKeywordsThrowsExceptionIfFormattedKeywordsExceeds255CharLength()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $words = array(
            str_repeat('a', 253), str_repeat('b', 2)
        );
        $feed->setItunesKeywords($words);
    }
    
    public function testSetNewFeedUrl()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $feed->setItunesNewFeedUrl('http://example.com/feed');
        $this->assertEquals('http://example.com/feed', $feed->getItunesNewFeedUrl());
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testSetNewFeedUrlThrowsExceptionOnInvalidUri()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $feed->setItunesNewFeedUrl('http://');
    }
    
    public function testAddOwner()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $feed->addItunesOwner(array('name'=>'joe','email'=>'joe@example.com'));
        $this->assertEquals(array(array('name'=>'joe','email'=>'joe@example.com')), $feed->getItunesOwners());
    }
    
    public function testAddOwners()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $feed->addItunesOwners(array(array('name'=>'joe','email'=>'joe@example.com')));
        $this->assertEquals(array(array('name'=>'joe','email'=>'joe@example.com')), $feed->getItunesOwners());
    }
    
    public function testSetSubtitle()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $feed->setItunesSubtitle('abc');
        $this->assertEquals('abc', $feed->getItunesSubtitle());
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testSetSubtitleThrowsExceptionWhenValueExceeds255Chars()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $feed->setItunesSubtitle(str_repeat('a', 256));
    }
    
    public function testSetSummary()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $feed->setItunesSummary('abc');
        $this->assertEquals('abc', $feed->getItunesSummary());
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testSetSummaryThrowsExceptionWhenValueExceeds4000Chars()
    {
        $feed = new Zend_Feed_Writer_Feed;
        $feed->setItunesSummary(str_repeat('a',4001));
    }

}
