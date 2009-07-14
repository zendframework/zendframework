<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Zend/Feed/Reader.php';

class Zend_Feed_Reader_Entry_RssTest extends PHPUnit_Framework_TestCase
{

    protected $_feedSamplePath = null;

    public function setup()
    {
        $this->_feedSamplePath = dirname(__FILE__) . '/_files/Rss';
    }

    /**
     * Get Id (Unencoded Text)
     */
    public function testGetsIdFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/1', $entry->getId());
    }

    public function testGetsIdFromRss094()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getId());
    }

    public function testGetsIdFromRss093()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getId());
    }

    public function testGetsIdFromRss092()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getId());
    }

    public function testGetsIdFromRss091()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getId());
    }

    public function testGetsIdFromRss10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getId());
    }

    public function testGetsIdFromRss090()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getId());
    }

    // DC 1.0

    public function testGetsIdFromRss20_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/dc10/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/1', $entry->getId());
    }

    public function testGetsIdFromRss094_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/dc10/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/1', $entry->getId());
    }

    public function testGetsIdFromRss093_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/dc10/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/1', $entry->getId());
    }

    public function testGetsIdFromRss092_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/dc10/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/1', $entry->getId());
    }

    public function testGetsIdFromRss091_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/dc10/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/1', $entry->getId());
    }

    public function testGetsIdFromRss10_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/dc10/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/1', $entry->getId());
    }

    public function testGetsIdFromRss090_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/dc10/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/1', $entry->getId());
    }

    // DC 1.1

    public function testGetsIdFromRss20_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/dc11/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/1', $entry->getId());
    }

    public function testGetsIdFromRss094_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/dc11/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/1', $entry->getId());
    }

    public function testGetsIdFromRss093_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/dc11/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/1', $entry->getId());
    }

    public function testGetsIdFromRss092_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/dc11/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/1', $entry->getId());
    }

    public function testGetsIdFromRss091_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/dc11/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/1', $entry->getId());
    }

    public function testGetsIdFromRss10_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/dc11/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/1', $entry->getId());
    }

    public function testGetsIdFromRss090_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/dc11/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/1', $entry->getId());
    }

    // Missing Id (but alternates to Title)

    public function testGetsIdFromRss20_Title()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/title/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getId());
    }

    public function testGetsIdFromRss094_Title()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/title/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getId());
    }

    public function testGetsIdFromRss093_Title()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/title/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getId());
    }

    public function testGetsIdFromRss092_Title()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/title/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getId());
    }

    public function testGetsIdFromRss091_Title()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/title/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getId());
    }

    public function testGetsIdFromRss10_Title()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/title/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getId());
    }

    public function testGetsIdFromRss090_Title()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/title/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getId());
    }

    // Missing Any Id

    public function testGetsIdFromRss20_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/none/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getId());
    }

    public function testGetsIdFromRss094_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/none/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getId());
    }

    public function testGetsIdFromRss093_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/none/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getId());
    }

    public function testGetsIdFromRss092_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/none/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getId());
    }

    public function testGetsIdFromRss091_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/none/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getId());
    }

    public function testGetsIdFromRss10_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/none/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getId());
    }

    public function testGetsIdFromRss090_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/none/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getId());
    }

    /**
     * Get Title (Unencoded Text)
     */
    public function testGetsTitleFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getTitle());
    }

    public function testGetsTitleFromRss094()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getTitle());
    }

    public function testGetsTitleFromRss093()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getTitle());
    }

    public function testGetsTitleFromRss092()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getTitle());
    }

    public function testGetsTitleFromRss091()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getTitle());
    }

    public function testGetsTitleFromRss10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getTitle());
    }

    public function testGetsTitleFromRss090()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getTitle());
    }

    // DC 1.0

    public function testGetsTitleFromRss20_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc10/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getTitle());
    }

    public function testGetsTitleFromRss094_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc10/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getTitle());
    }

    public function testGetsTitleFromRss093_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc10/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getTitle());
    }

    public function testGetsTitleFromRss092_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc10/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getTitle());
    }

    public function testGetsTitleFromRss091_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc10/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getTitle());
    }

    public function testGetsTitleFromRss10_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc10/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getTitle());
    }

    public function testGetsTitleFromRss090_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc10/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getTitle());
    }

    // DC 1.1

    public function testGetsTitleFromRss20_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc11/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getTitle());
    }

    public function testGetsTitleFromRss094_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc11/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getTitle());
    }

    public function testGetsTitleFromRss093_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc11/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getTitle());
    }

    public function testGetsTitleFromRss092_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc11/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getTitle());
    }

    public function testGetsTitleFromRss091_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc11/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getTitle());
    }

    public function testGetsTitleFromRss10_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc11/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getTitle());
    }

    public function testGetsTitleFromRss090_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc11/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getTitle());
    }

    // Missing Title

    public function testGetsTitleFromRss20_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/none/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getTitle());
    }

    public function testGetsTitleFromRss094_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/none/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getTitle());
    }

    public function testGetsTitleFromRss093_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/none/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getTitle());
    }

    public function testGetsTitleFromRss092_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/none/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getTitle());
    }

    public function testGetsTitleFromRss091_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/none/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getTitle());
    }

    public function testGetsTitleFromRss10_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/none/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getTitle());
    }

    public function testGetsTitleFromRss090_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/none/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getTitle());
    }

    /**
     * Get Authors (Unencoded Text)
     */
    public function testGetsAuthorsFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $entry->getAuthors());
    }

    public function testGetsAuthorsFromRss094()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array(), $entry->getAuthors());
    }

    public function testGetsAuthorsFromRss093()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array(), $entry->getAuthors());
    }

    public function testGetsAuthorsFromRss092()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array(), $entry->getAuthors());
    }

    public function testGetsAuthorsFromRss091()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array(), $entry->getAuthors());
    }

    public function testGetsAuthorsFromRss10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array(), $entry->getAuthors());
    }

    public function testGetsAuthorsFromRss090()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array(), $entry->getAuthors());
    }

    // DC 1.0

    public function testGetsAuthorsFromRss20_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $entry->getAuthors());
    }

    public function testGetsAuthorsFromRss094_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $entry->getAuthors());
    }

    public function testGetsAuthorsFromRss093_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $entry->getAuthors());
    }

    public function testGetsAuthorsFromRss092_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $entry->getAuthors());
    }

    public function testGetsAuthorsFromRss091_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $entry->getAuthors());
    }

    public function testGetsAuthorsFromRss10_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $entry->getAuthors());
    }

    public function testGetsAuthorsFromRss090_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $entry->getAuthors());
    }

    // DC 1.1

    public function testGetsAuthorsFromRss20_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $entry->getAuthors());
    }

    public function testGetsAuthorsFromRss094_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $entry->getAuthors());
    }

    public function testGetsAuthorsFromRss093_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $entry->getAuthors());
    }

    public function testGetsAuthorsFromRss092_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $entry->getAuthors());
    }

    public function testGetsAuthorsFromRss091_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $entry->getAuthors());
    }

    public function testGetsAuthorsFromRss10_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $entry->getAuthors());
    }

    public function testGetsAuthorsFromRss090_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $entry->getAuthors());
    }

    // Missing Author

    public function testGetsAuthorsFromRss20_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array(), $entry->getAuthors());
    }

    public function testGetsAuthorsFromRss094_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array(), $entry->getAuthors());
    }

    public function testGetsAuthorsFromRss093_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array(), $entry->getAuthors());
    }

    public function testGetsAuthorsFromRss092_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array(), $entry->getAuthors());
    }

    public function testGetsAuthorsFromRss091_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array(), $entry->getAuthors());
    }

    public function testGetsAuthorsFromRss10_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array(), $entry->getAuthors());
    }

    public function testGetsAuthorsFromRss090_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array(), $entry->getAuthors());
    }


    /**
     * Get Author (Unencoded Text)
     */
    public function testGetsAuthorFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Joe Bloggs', $entry->getAuthor());
    }

    public function testGetsAuthorFromRss094()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getAuthor());
    }

    public function testGetsAuthorFromRss093()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getAuthor());
    }

    public function testGetsAuthorFromRss092()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getAuthor());
    }

    public function testGetsAuthorFromRss091()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getAuthor());
    }

    public function testGetsAuthorFromRss10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getAuthor());
    }

    public function testGetsAuthorFromRss090()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getAuthor());
    }

    // DC 1.0

    public function testGetsAuthorFromRss20_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Joe Bloggs', $entry->getAuthor());
    }

    public function testGetsAuthorFromRss094_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Jane Bloggs', $entry->getAuthor(1));
    }

    public function testGetsAuthorFromRss093_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Joe Bloggs', $entry->getAuthor());
    }

    public function testGetsAuthorFromRss092_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Jane Bloggs', $entry->getAuthor(1));
    }

    public function testGetsAuthorFromRss091_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Joe Bloggs', $entry->getAuthor());
    }

    public function testGetsAuthorFromRss10_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Jane Bloggs', $entry->getAuthor(1));
    }

    public function testGetsAuthorFromRss090_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Joe Bloggs', $entry->getAuthor());
    }

    // DC 1.1

    public function testGetsAuthorFromRss20_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Jane Bloggs', $entry->getAuthor(1));
    }

    public function testGetsAuthorFromRss094_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Joe Bloggs', $entry->getAuthor());
    }

    public function testGetsAuthorFromRss093_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Jane Bloggs', $entry->getAuthor(1));
    }

    public function testGetsAuthorFromRss092_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Joe Bloggs', $entry->getAuthor());
    }

    public function testGetsAuthorFromRss091_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Jane Bloggs', $entry->getAuthor(1));
    }

    public function testGetsAuthorFromRss10_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Joe Bloggs', $entry->getAuthor());
    }

    public function testGetsAuthorFromRss090_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Jane Bloggs', $entry->getAuthor(1));
    }

    // Missing Id

    public function testGetsAuthorFromRss20_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getAuthor());
    }

    public function testGetsAuthorFromRss094_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getAuthor());
    }

    public function testGetsAuthorFromRss093_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getAuthor());
    }

    public function testGetsAuthorFromRss092_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getAuthor());
    }

    public function testGetsAuthorFromRss091_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getAuthor());
    }

    public function testGetsAuthorFromRss10_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getAuthor());
    }

    public function testGetsAuthorFromRss090_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getAuthor());
    }

    /**
     * Get Description (Unencoded Text)
     */
    public function testGetsDescriptionFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getDescription());
    }

    public function testGetsDescriptionFromRss094()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getDescription());
    }

    public function testGetsDescriptionFromRss093()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getDescription());
    }

    public function testGetsDescriptionFromRss092()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getDescription());
    }

    public function testGetsDescriptionFromRss091()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getDescription());
    }

    public function testGetsDescriptionFromRss10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getDescription());
    }

    public function testGetsDescriptionFromRss090()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getDescription());
    }

    // DC 1.0

    public function testGetsDescriptionFromRss20_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc10/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getDescription());
    }

    public function testGetsDescriptionFromRss094_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc10/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getDescription());
    }

    public function testGetsDescriptionFromRss093_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc10/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getDescription());
    }

    public function testGetsDescriptionFromRss092_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc10/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getDescription());
    }

    public function testGetsDescriptionFromRss091_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc10/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getDescription());
    }

    public function testGetsDescriptionFromRss10_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc10/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getDescription());
    }

    public function testGetsDescriptionFromRss090_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc10/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getDescription());
    }

    // DC 1.1

    public function testGetsDescriptionFromRss20_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc11/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getDescription());
    }

    public function testGetsDescriptionFromRss094_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc11/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getDescription());
    }

    public function testGetsDescriptionFromRss093_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc11/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getDescription());
    }

    public function testGetsDescriptionFromRss092_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc11/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getDescription());
    }

    public function testGetsDescriptionFromRss091_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc11/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getDescription());
    }

    public function testGetsDescriptionFromRss10_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc11/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getDescription());
    }

    public function testGetsDescriptionFromRss090_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc11/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getDescription());
    }

    // Missing Description

    public function testGetsDescriptionFromRss20_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/none/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getDescription());
    }

    public function testGetsDescriptionFromRss094_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/none/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getDescription());
    }

    public function testGetsDescriptionFromRss093_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/none/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getDescription());
    }

    public function testGetsDescriptionFromRss092_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/none/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getDescription());
    }

    public function testGetsDescriptionFromRss091_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/none/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getDescription());
    }

    public function testGetsDescriptionFromRss10_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/none/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getDescription());
    }

    public function testGetsDescriptionFromRss090_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/none/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getDescription());
    }

    /**
     * Get Content (Unencoded Text)
     */
    public function testGetsContentFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/content/plain/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Content', $entry->getContent());
    }

    public function testGetsContentFromRss094()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/content/plain/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Content', $entry->getContent());
    }

    public function testGetsContentFromRss093()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/content/plain/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Content', $entry->getContent());
    }

    public function testGetsContentFromRss092()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/content/plain/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Content', $entry->getContent());
    }

    public function testGetsContentFromRss091()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/content/plain/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Content', $entry->getContent());
    }

    public function testGetsContentFromRss10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/content/plain/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Content', $entry->getContent());
    }

    public function testGetsContentFromRss090()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/content/plain/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Content', $entry->getContent());
    }

    // Revert to Description if no Content

    public function testGetsContentFromRss20_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/content/plain/description/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getContent());
    }

    public function testGetsContentFromRss094_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/content/plain/description/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getContent());
    }

    public function testGetsContentFromRss093_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/content/plain/description/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getContent());
    }

    public function testGetsContentFromRss092_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/content/plain/description/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getContent());
    }

    public function testGetsContentFromRss091_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/content/plain/description/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getContent());
    }

    public function testGetsContentFromRss10_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/content/plain/description/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getContent());
    }

    public function testGetsContentFromRss090_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/content/plain/description/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getContent());
    }

    // Missing Content and Description

    public function testGetsContentFromRss20_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/content/plain/none/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getContent());
    }

    public function testGetsContentFromRss094_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/content/plain/none/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getContent());
    }

    public function testGetsContentFromRss093_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/content/plain/none/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getContent());
    }

    public function testGetsContentFromRss092_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/content/plain/none/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getContent());
    }

    public function testGetsContentFromRss091_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/content/plain/none/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getContent());
    }

    public function testGetsContentFromRss10_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/content/plain/none/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getContent());
    }

    public function testGetsContentFromRss090_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/content/plain/none/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getContent());
    }

    /**
     * Get Link (Unencoded Text)
     */
    public function testGetsLinkFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry', $entry->getLink());
    }

    public function testGetsLinkFromRss094()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry', $entry->getLink());
    }

    public function testGetsLinkFromRss093()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry', $entry->getLink());
    }

    public function testGetsLinkFromRss092()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry', $entry->getLink());
    }

    public function testGetsLinkFromRss091()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry', $entry->getLink());
    }

    public function testGetsLinkFromRss10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry', $entry->getLink());
    }

    public function testGetsLinkFromRss090()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry', $entry->getLink());
    }

    // Missing Link

    public function testGetsLinkFromRss20_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/none/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getLink());
    }

    public function testGetsLinkFromRss094_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/none/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getLink());
    }

    public function testGetsLinkFromRss093_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/none/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getLink());
    }

    public function testGetsLinkFromRss092_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/none/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getLink());
    }

    public function testGetsLinkFromRss091_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/none/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getLink());
    }

    public function testGetsLinkFromRss10_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/none/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getLink());
    }

    public function testGetsLinkFromRss090_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/none/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getLink());
    }

    /**
     * Get DateModified (Unencoded Text)
     */
    public function testGetsDateModifiedFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000', $entry->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    // DC 1.0

    public function testGetsDateModifiedFromRss20_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc10/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000', $entry->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateModifiedFromRss094_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc10/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000', $entry->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateModifiedFromRss093_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc10/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000', $entry->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateModifiedFromRss092_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc10/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000', $entry->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateModifiedFromRss091_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc10/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000', $entry->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateModifiedFromRss10_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc10/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000', $entry->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateModifiedFromRss090_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc10/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000', $entry->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    // DC 1.1

    public function testGetsDateModifiedFromRss20_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc11/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000', $entry->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateModifiedFromRss094_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc11/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000', $entry->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateModifiedFromRss093_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc11/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000', $entry->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateModifiedFromRss092_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc11/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000', $entry->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateModifiedFromRss091_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc11/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000', $entry->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateModifiedFromRss10_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc11/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000', $entry->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateModifiedFromRss090_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc11/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000', $entry->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    // Missing DateModified

    public function testGetsDateModifiedFromRss20_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/none/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getDateModified());
    }

    public function testGetsDateModifiedFromRss094_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/none/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getDateModified());
    }

    public function testGetsDateModifiedFromRss093_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/none/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getDateModified());
    }

    public function testGetsDateModifiedFromRss092_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/none/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getDateModified());
    }

    public function testGetsDateModifiedFromRss091_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/none/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getDateModified());
    }

    public function testGetsDateModifiedFromRss10_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/none/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getDateModified());
    }

    public function testGetsDateModifiedFromRss090_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/none/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getDateModified());
    }

    /**
     * Get CommentCount (Unencoded Text)
     */

    // Slash 1.0

    public function testGetsCommentCountFromRss20_Slash10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentcount/plain/slash10/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('321', $entry->getCommentCount());
    }

    public function testGetsCommentCountFromRss094_Slash10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentcount/plain/slash10/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('321', $entry->getCommentCount());
    }

    public function testGetsCommentCountFromRss093_Slash10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentcount/plain/slash10/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('321', $entry->getCommentCount());
    }

    public function testGetsCommentCountFromRss092_Slash10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentcount/plain/slash10/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('321', $entry->getCommentCount());
    }

    public function testGetsCommentCountFromRss091_Slash10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentcount/plain/slash10/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('321', $entry->getCommentCount());
    }

    public function testGetsCommentCountFromRss10_Slash10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentcount/plain/slash10/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('321', $entry->getCommentCount());
    }

    public function testGetsCommentCountFromRss090_Slash10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentcount/plain/slash10/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('321', $entry->getCommentCount());
    }

    // Atom Threaded 1.0

    public function testGetsCommentCountFromRss20_Thread10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentcount/plain/thread10/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('321', $entry->getCommentCount());
    }

    public function testGetsCommentCountFromRss094_Thread10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentcount/plain/thread10/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('321', $entry->getCommentCount());
    }

    public function testGetsCommentCountFromRss093_Thread10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentcount/plain/thread10/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('321', $entry->getCommentCount());
    }

    public function testGetsCommentCountFromRss092_Thread10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentcount/plain/thread10/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('321', $entry->getCommentCount());
    }

    public function testGetsCommentCountFromRss091_Thread10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentcount/plain/thread10/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('321', $entry->getCommentCount());
    }

    public function testGetsCommentCountFromRss10_Thread10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentcount/plain/thread10/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('321', $entry->getCommentCount());
    }

    public function testGetsCommentCountFromRss090_Thread10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentcount/plain/thread10/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('321', $entry->getCommentCount());
    }

    // Atom 1.0 (Threaded 1.0 atom:link attribute)

    public function testGetsCommentCountFromRss20_Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentcount/plain/atom10/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('321', $entry->getCommentCount());
    }

    public function testGetsCommentCountFromRss094_Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentcount/plain/atom10/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('321', $entry->getCommentCount());
    }

    public function testGetsCommentCountFromRss093_Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentcount/plain/atom10/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('321', $entry->getCommentCount());
    }

    public function testGetsCommentCountFromRss092_Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentcount/plain/atom10/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('321', $entry->getCommentCount());
    }

    public function testGetsCommentCountFromRss091_Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentcount/plain/atom10/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('321', $entry->getCommentCount());
    }

    public function testGetsCommentCountFromRss10_Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentcount/plain/atom10/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('321', $entry->getCommentCount());
    }

    public function testGetsCommentCountFromRss090_Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentcount/plain/atom10/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('321', $entry->getCommentCount());
    }

    // Missing Any CommentCount

    public function testGetsCommentCountFromRss20_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentcount/plain/none/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getCommentCount());
    }

    public function testGetsCommentCountFromRss094_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentcount/plain/none/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getCommentCount());
    }

    public function testGetsCommentCountFromRss093_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentcount/plain/none/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getCommentCount());
    }

    public function testGetsCommentCountFromRss092_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentcount/plain/none/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getCommentCount());
    }

    public function testGetsCommentCountFromRss091_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentcount/plain/none/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getCommentCount());
    }

    public function testGetsCommentCountFromRss10_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentcount/plain/none/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getCommentCount());
    }

    public function testGetsCommentCountFromRss090_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentcount/plain/none/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getCommentCount());
    }

    /**
     * Get CommentLink (Unencoded Text)
     */

    public function testGetsCommentLinkFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentlink/plain/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/comments', $entry->getCommentLink());
    }

    public function testGetsCommentLinkFromRss094()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentlink/plain/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/comments', $entry->getCommentLink());
    }

    public function testGetsCommentLinkFromRss093()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentlink/plain/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/comments', $entry->getCommentLink());
    }

    public function testGetsCommentLinkFromRss092()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentlink/plain/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/comments', $entry->getCommentLink());
    }

    public function testGetsCommentLinkFromRss091()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentlink/plain/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/comments', $entry->getCommentLink());
    }

    // Atom 1.0

    public function testGetsCommentLinkFromRss20_Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentlink/plain/atom10/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/comments', $entry->getCommentLink());
    }

    public function testGetsCommentLinkFromRss094_Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentlink/plain/atom10/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/comments', $entry->getCommentLink());
    }

    public function testGetsCommentLinkFromRss093_Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentlink/plain/atom10/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/comments', $entry->getCommentLink());
    }

    public function testGetsCommentLinkFromRss092_Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentlink/plain/atom10/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/comments', $entry->getCommentLink());
    }

    public function testGetsCommentLinkFromRss091_Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentlink/plain/atom10/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/comments', $entry->getCommentLink());
    }

    public function testGetsCommentLinkFromRss10_Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentlink/plain/atom10/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/comments', $entry->getCommentLink());
    }

    public function testGetsCommentLinkFromRss090_Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentlink/plain/atom10/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/comments', $entry->getCommentLink());
    }

    // Missing Any CommentLink

    public function testGetsCommentLinkFromRss20_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentlink/plain/none/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getCommentLink());
    }

    public function testGetsCommentLinkFromRss094_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentlink/plain/none/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getCommentLink());
    }

    public function testGetsCommentLinkFromRss093_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentlink/plain/none/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getCommentLink());
    }

    public function testGetsCommentLinkFromRss092_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentlink/plain/none/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getCommentLink());
    }

    public function testGetsCommentLinkFromRss091_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentlink/plain/none/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getCommentLink());
    }

    public function testGetsCommentLinkFromRss10_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentlink/plain/none/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getCommentLink());
    }

    public function testGetsCommentLinkFromRss090_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentlink/plain/none/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getCommentLink());
    }

    /**
     * Get CommentFeedLink (Unencoded Text)
     */

 // Atom 1.0

    public function testGetsCommentFeedLinkFromRss20_WellFormedWeb10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentfeedlink/plain/wellformedweb/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry/321/feed/rss/', $entry->getCommentFeedLink());
    }

    public function testGetsCommentFeedLinkFromRss094_WellFormedWeb10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentfeedlink/plain/wellformedweb/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry/321/feed/rss/', $entry->getCommentFeedLink());
    }

    public function testGetsCommentFeedLinkFromRss093_WellFormedWeb10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentfeedlink/plain/wellformedweb/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry/321/feed/rss/', $entry->getCommentFeedLink());
    }

    public function testGetsCommentFeedLinkFromRss092_WellFormedWeb10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentfeedlink/plain/wellformedweb/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry/321/feed/rss/', $entry->getCommentFeedLink());
    }

    public function testGetsCommentFeedLinkFromRss091_WellFormedWeb10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentfeedlink/plain/wellformedweb/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry/321/feed/rss/', $entry->getCommentFeedLink());
    }

    public function testGetsCommentFeedLinkFromRss10_WellFormedWeb10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentfeedlink/plain/wellformedweb/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry/321/feed/rss/', $entry->getCommentFeedLink());
    }

    public function testGetsCommentFeedLinkFromRss090_WellFormedWeb10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentfeedlink/plain/wellformedweb/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry/321/feed/rss/', $entry->getCommentFeedLink());
    }

    // Atom 1.0

    public function testGetsCommentFeedLinkFromRss20_Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentfeedlink/plain/atom10/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry/321/feed/rss/', $entry->getCommentFeedLink());
    }

    public function testGetsCommentFeedLinkFromRss094_Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentfeedlink/plain/atom10/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry/321/feed/rss/', $entry->getCommentFeedLink());
    }

    public function testGetsCommentFeedLinkFromRss093_Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentfeedlink/plain/atom10/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry/321/feed/rss/', $entry->getCommentFeedLink());
    }

    public function testGetsCommentFeedLinkFromRss092_Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentfeedlink/plain/atom10/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry/321/feed/rss/', $entry->getCommentFeedLink());
    }

    public function testGetsCommentFeedLinkFromRss091_Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentfeedlink/plain/atom10/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry/321/feed/rss/', $entry->getCommentFeedLink());
    }

    public function testGetsCommentFeedLinkFromRss10_Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentfeedlink/plain/atom10/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry/321/feed/rss/', $entry->getCommentFeedLink());
    }

    public function testGetsCommentFeedLinkFromRss090_Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentfeedlink/plain/atom10/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry/321/feed/rss/', $entry->getCommentFeedLink());
    }

    // Missing Any CommentFeedLink

    public function testGetsCommentFeedLinkFromRss20_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentfeedlink/plain/none/rss20.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getCommentFeedLink());
    }

    public function testGetsCommentFeedLinkFromRss094_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentfeedlink/plain/none/rss094.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getCommentFeedLink());
    }

    public function testGetsCommentFeedLinkFromRss093_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentfeedlink/plain/none/rss093.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getCommentFeedLink());
    }

    public function testGetsCommentFeedLinkFromRss092_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentfeedlink/plain/none/rss092.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getCommentFeedLink());
    }

    public function testGetsCommentFeedLinkFromRss091_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentfeedlink/plain/none/rss091.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getCommentFeedLink());
    }

    public function testGetsCommentFeedLinkFromRss10_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentfeedlink/plain/none/rss10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getCommentFeedLink());
    }

    public function testGetsCommentFeedLinkFromRss090_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/commentfeedlink/plain/none/rss090.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getCommentFeedLink());
    }

}
