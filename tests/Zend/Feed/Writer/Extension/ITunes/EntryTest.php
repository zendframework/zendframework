<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Zend/Feed/Writer/Entry.php';

class Zend_Feed_Writer_Extension_ITunes_EntryTest extends PHPUnit_Framework_TestCase
{

    public function testSetBlock()
    {
        $entry = new Zend_Feed_Writer_Entry;
        $entry->setItunesBlock('yes');
        $this->assertEquals('yes', $entry->getItunesBlock());
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testSetBlockThrowsExceptionOnNonAlphaValue()
    {
        $entry = new Zend_Feed_Writer_Entry;
        $entry->setItunesBlock('123');
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testSetBlockThrowsExceptionIfValueGreaterThan255CharsLength()
    {
        $entry = new Zend_Feed_Writer_Entry;
        $entry->setItunesBlock(str_repeat('a', 256));
    }
    
    public function testAddAuthors()
    {
        $entry = new Zend_Feed_Writer_Entry;
        $entry->addItunesAuthors(array('joe', 'jane'));
        $this->assertEquals(array('joe', 'jane'), $entry->getItunesAuthors());
    }
    
    public function testAddAuthor()
    {
        $entry = new Zend_Feed_Writer_Entry;
        $entry->addItunesAuthor('joe');
        $this->assertEquals(array('joe'), $entry->getItunesAuthors());
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testAddAuthorThrowsExceptionIfValueGreaterThan255CharsLength()
    {
        $entry = new Zend_Feed_Writer_Entry;
        $entry->addItunesAuthor(str_repeat('a', 256));
    }
    
    public function testSetDurationAsSeconds()
    {
        $entry = new Zend_Feed_Writer_Entry;
        $entry->setItunesDuration(23);
        $this->assertEquals(23, $entry->getItunesDuration());
    }
    
    public function testSetDurationAsMinutesAndSeconds()
    {
        $entry = new Zend_Feed_Writer_Entry;
        $entry->setItunesDuration('23:23');
        $this->assertEquals('23:23', $entry->getItunesDuration());
    }
    
    public function testSetDurationAsHoursMinutesAndSeconds()
    {
        $entry = new Zend_Feed_Writer_Entry;
        $entry->setItunesDuration('23:23:23');
        $this->assertEquals('23:23:23', $entry->getItunesDuration());
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testSetDurationThrowsExceptionOnUnknownFormat()
    {
        $entry = new Zend_Feed_Writer_Entry;
        $entry->setItunesDuration('abc');
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testSetDurationThrowsExceptionOnInvalidSeconds()
    {
        $entry = new Zend_Feed_Writer_Entry;
        $entry->setItunesDuration('23:456');
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testSetDurationThrowsExceptionOnInvalidMinutes()
    {
        $entry = new Zend_Feed_Writer_Entry;
        $entry->setItunesDuration('23:234:45');
    }
    
    public function testSetExplicitToYes()
    {
        $entry = new Zend_Feed_Writer_Entry;
        $entry->setItunesExplicit('yes');
        $this->assertEquals('yes', $entry->getItunesExplicit());
    }
    
    public function testSetExplicitToNo()
    {
        $entry = new Zend_Feed_Writer_Entry;
        $entry->setItunesExplicit('no');
        $this->assertEquals('no', $entry->getItunesExplicit());
    }
    
    public function testSetExplicitToClean()
    {
        $entry = new Zend_Feed_Writer_Entry;
        $entry->setItunesExplicit('clean');
        $this->assertEquals('clean', $entry->getItunesExplicit());
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testSetExplicitThrowsExceptionOnUnknownTerm()
    {
        $entry = new Zend_Feed_Writer_Entry;
        $entry->setItunesExplicit('abc');
    }
    
    public function testSetKeywords()
    {
        $entry = new Zend_Feed_Writer_Entry;
        $words = array(
            'a1', 'a2', 'a3', 'a4', 'a5', 'a6', 'a7', 'a8', 'a9', 'a10', 'a11', 'a12'
        );
        $entry->setItunesKeywords($words);
        $this->assertEquals($words, $entry->getItunesKeywords());
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testSetKeywordsThrowsExceptionIfMaxKeywordsExceeded()
    {
        $entry = new Zend_Feed_Writer_Entry;
        $words = array(
            'a1', 'a2', 'a3', 'a4', 'a5', 'a6', 'a7', 'a8', 'a9', 'a10', 'a11', 'a12', 'a13'
        );
        $entry->setItunesKeywords($words);
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testSetKeywordsThrowsExceptionIfFormattedKeywordsExceeds255CharLength()
    {
        $entry = new Zend_Feed_Writer_Entry;
        $words = array(
            str_repeat('a', 253), str_repeat('b', 2)
        );
        $entry->setItunesKeywords($words);
    }
    
    public function testSetSubtitle()
    {
        $entry = new Zend_Feed_Writer_Entry;
        $entry->setItunesSubtitle('abc');
        $this->assertEquals('abc', $entry->getItunesSubtitle());
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testSetSubtitleThrowsExceptionWhenValueExceeds255Chars()
    {
        $entry = new Zend_Feed_Writer_Entry;
        $entry->setItunesSubtitle(str_repeat('a', 256));
    }
    
    public function testSetSummary()
    {
        $entry = new Zend_Feed_Writer_Entry;
        $entry->setItunesSummary('abc');
        $this->assertEquals('abc', $entry->getItunesSummary());
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testSetSummaryThrowsExceptionWhenValueExceeds255Chars()
    {
        $entry = new Zend_Feed_Writer_Entry;
        $entry->setItunesSummary(str_repeat('a', 4001));
    }

}
