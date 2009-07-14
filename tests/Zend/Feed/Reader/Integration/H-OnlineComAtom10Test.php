<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Zend/Feed/Reader.php';

class Zend_Feed_Reader_Integration_HOnlineComAtom10Test extends PHPUnit_Framework_TestCase
{

    protected $_feedSamplePath = null;

    public function setup()
    {
        $this->_feedSamplePath = dirname(__FILE__) . '/_files/h-online.com-atom10.xml';
    }

    public function testGetsTitle()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals('The H - news feed', $feed->getTitle());
    }

    public function testGetsAuthors()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals(array('The H'), $feed->getAuthors());
    }

    public function testGetsSingleAuthor()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals('The H', $feed->getAuthor());
    }

    public function testGetsCopyright()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    public function testGetsDescription()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals('Technology news', $feed->getDescription());
    }

    public function testGetsLanguage()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLink()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals('http://www.h-online.com', $feed->getLink());
    }

    public function testGetsEncoding()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals('UTF-8', $feed->getEncoding());
    }

    public function testGetsEntryCount()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals(60, $feed->count());
    }

    /**
     * Entry level testing
     */

    public function testGetsEntryId()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.h-online.com/security/McAfee-update-brings-systems-down-again--/news/113689/from/rss', $entry->getId());
    }

    public function testGetsEntryTitle()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('McAfee update brings systems down again', $entry->getTitle());
    }

    public function testGetsEntryAuthors()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals(array('The H'), $entry->getAuthors());
    }

    public function testGetsEntrySingleAuthor()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('The H', $entry->getAuthor());
    }

    public function testGetsEntryDescription()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        /**
         * Note: "’" is not the same as "'" - don't replace in error
         */
        $this->assertEquals('A McAfee signature update is currently causing system failures and a lot of overtime for administrators', $entry->getDescription());
    }

    public function testGetsEntryContent()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('A McAfee signature update is currently causing system failures and a lot of overtime for administrators', $entry->getContent());
    }

    public function testGetsEntryLinks()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals(array('http://www.h-online.com/security/McAfee-update-brings-systems-down-again--/news/113689/from/rss'), $entry->getLinks());
    }

    public function testGetsEntryLink()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.h-online.com/security/McAfee-update-brings-systems-down-again--/news/113689/from/rss', $entry->getLink());
    }

    public function testGetsEntryPermaLink()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.h-online.com/security/McAfee-update-brings-systems-down-again--/news/113689/from/rss',
            $entry->getPermaLink());
    }

    public function testGetsEntryEncoding()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('UTF-8', $entry->getEncoding());
    }

}
