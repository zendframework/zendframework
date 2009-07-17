<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Zend/Feed/Reader.php';

class Zend_Feed_Reader_Feed_RssTest extends PHPUnit_Framework_TestCase
{

    protected $_feedSamplePath = null;

    public function setup()
    {
        if (Zend_Registry::isRegistered('Zend_Locale')) {
            $registry = Zend_Registry::getInstance();
            unset($registry['Zend_Locale']);
        }
        $this->_feedSamplePath = dirname(__FILE__) . '/_files/Rss';
    }

    /**
     * Get Title (Unencoded Text)
     */
    public function testGetsTitleFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/rss20.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss094()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/rss094.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss093()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/rss093.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss092()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/rss092.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss091()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/rss091.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/rss10.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss090()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/rss090.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    // DC 1.0

    public function testGetsTitleFromRss20_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc10/rss20.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss094_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc10/rss094.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss093_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc10/rss093.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss092_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc10/rss092.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss091_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc10/rss091.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss10_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc10/rss10.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss090_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc10/rss090.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    // DC 1.1

    public function testGetsTitleFromRss20_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc11/rss20.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss094_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc11/rss094.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss093_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc11/rss093.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss092_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc11/rss092.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss091_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc11/rss091.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss10_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc11/rss10.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss090_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc11/rss090.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    // Atom 1.0

    public function testGetsTitleFromRss20_atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/atom10/rss20.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss094_atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/atom10/rss094.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss093_atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/atom10/rss093.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss092_atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/atom10/rss092.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss091_atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/atom10/rss091.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss10_atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/atom10/rss10.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss090_atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/atom10/rss090.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    // Missing Title

    public function testGetsTitleFromRss20_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getTitle());
    }

    public function testGetsTitleFromRss094_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getTitle());
    }

    public function testGetsTitleFromRss093_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getTitle());
    }

    public function testGetsTitleFromRss092_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getTitle());
    }

    public function testGetsTitleFromRss091_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getTitle());
    }

    public function testGetsTitleFromRss10_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getTitle());
    }

    public function testGetsTitleFromRss090_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getTitle());
    }

    /**
     * Get Authors (Unencoded Text)
     */
    public function testGetsAuthorArrayFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss20.xml')
        );
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss094()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss094.xml')
        );
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss093()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss093.xml')
        );
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss092()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss092.xml')
        );
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss091()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss091.xml')
        );
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss10.xml')
        );
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss090()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss090.xml')
        );
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors());
    }

    // DC 1.0

    public function testGetsAuthorArrayFromRss20_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss20.xml')
        );
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss094_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss094.xml')
        );
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss093_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss093.xml')
        );
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss092_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss092.xml')
        );
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss091_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss091.xml')
        );
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss10_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss10.xml')
        );
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss090_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss090.xml')
        );
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors());
    }

    // DC 1.1

    public function testGetsAuthorArrayFromRss20_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss20.xml')
        );
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss094_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss094.xml')
        );
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss093_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss093.xml')
        );
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss092_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss092.xml')
        );
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss091_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss091.xml')
        );
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss10_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss10.xml')
        );
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss090_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss090.xml')
        );
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors());
    }

    // Atom 1.0

    public function testGetsAuthorArrayFromRss20_Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/atom10/rss20.xml')
        );
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss094_Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/atom10/rss094.xml')
        );
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss093_Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/atom10/rss093.xml')
        );
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss092_Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/atom10/rss092.xml')
        );
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss091_Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/atom10/rss091.xml')
        );
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss10_Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/atom10/rss10.xml')
        );
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss090_Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/atom10/rss090.xml')
        );
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors());
    }

    // Missing Authors

    public function testGetsAuthorArrayFromRss20_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss094_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss093_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss092_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss091_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss10_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss090_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getAuthors());
    }

    /**
     * Get Single Author (Unencoded Text)
     */
    public function testGetsSingleAuthorFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss20.xml')
        );
        $this->assertEquals('Joe Bloggs', $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss094()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss094.xml')
        );
        $this->assertEquals('Joe Bloggs', $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss093()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss093.xml')
        );
        $this->assertEquals('Joe Bloggs', $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss092()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss092.xml')
        );
        $this->assertEquals('Joe Bloggs', $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss091()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss091.xml')
        );
        $this->assertEquals('Joe Bloggs', $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss10.xml')
        );
        $this->assertEquals('Joe Bloggs', $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss090()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss090.xml')
        );
        $this->assertEquals('Joe Bloggs', $feed->getAuthor());
    }

    // DC 1.0

    public function testGetsSingleAuthorFromRss20_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss20.xml')
        );
        $this->assertEquals('Joe Bloggs', $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss094_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss094.xml')
        );
        $this->assertEquals('Joe Bloggs', $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss093_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss093.xml')
        );
        $this->assertEquals('Joe Bloggs', $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss092_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss092.xml')
        );
        $this->assertEquals('Joe Bloggs', $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss091_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss091.xml')
        );
        $this->assertEquals('Joe Bloggs', $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss10_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss10.xml')
        );
        $this->assertEquals('Joe Bloggs', $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss090_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss090.xml')
        );
        $this->assertEquals('Joe Bloggs', $feed->getAuthor());
    }

    // DC 1.1

    public function testGetsSingleAuthorFromRss20_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss20.xml')
        );
        $this->assertEquals('Joe Bloggs', $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss094_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss094.xml')
        );
        $this->assertEquals('Joe Bloggs', $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss093_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss093.xml')
        );
        $this->assertEquals('Joe Bloggs', $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss092_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss092.xml')
        );
        $this->assertEquals('Joe Bloggs', $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss091_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss091.xml')
        );
        $this->assertEquals('Joe Bloggs', $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss10_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss10.xml')
        );
        $this->assertEquals('Joe Bloggs', $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss090_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss090.xml')
        );
        $this->assertEquals('Joe Bloggs', $feed->getAuthor());
    }

    // Missing Author

    public function testGetsSingleAuthorFromRss20_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss094_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss093_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss092_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss091_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss10_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss090_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getAuthor());
    }

    /**
     * Get Copyright (Unencoded Text)
     */
    public function testGetsCopyrightFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/rss20.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss094()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/rss094.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss093()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/rss093.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss092()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/rss092.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss091()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/rss091.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/rss10.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss090()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/rss090.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    // DC 1.0

    public function testGetsCopyrightFromRss20_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc10/rss20.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss094_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc10/rss094.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss093_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc10/rss093.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss092_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc10/rss092.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss091_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc10/rss091.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss10_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc10/rss10.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss090_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc10/rss090.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    // DC 1.1

    public function testGetsCopyrightFromRss20_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc11/rss20.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss094_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc11/rss094.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss093_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc11/rss093.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss092_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc11/rss092.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss091_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc11/rss091.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss10_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc11/rss10.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss090_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc11/rss090.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    // Missing Copyright

    public function testGetsCopyrightFromRss20_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss094_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss093_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss092_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss091_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss10_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss090_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    /**
     * Get Description (Unencoded Text)
     */
    public function testGetsDescriptionFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/rss20.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss094()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/rss094.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss093()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/rss093.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss092()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/rss092.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss091()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/rss091.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/rss10.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss090()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/rss090.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    // DC 1.0

    public function testGetsDescriptionFromRss20_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc10/rss20.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss094_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc10/rss094.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss093_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc10/rss093.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss092_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc10/rss092.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss091_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc10/rss091.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss10_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc10/rss10.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss090_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc10/rss090.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    // DC 1.1

    public function testGetsDescriptionFromRss20_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc11/rss20.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss094_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc11/rss094.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss093_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc11/rss093.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss092_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc11/rss092.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss091_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc11/rss091.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss10_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc11/rss10.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss090_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc11/rss090.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    // Missing Description

    public function testGetsDescriptionFromRss20_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getDescription());
    }

    public function testGetsDescriptionFromRss094_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getDescription());
    }

    public function testGetsDescriptionFromRss093_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getDescription());
    }

    public function testGetsDescriptionFromRss092_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getDescription());
    }

    public function testGetsDescriptionFromRss091_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getDescription());
    }

    public function testGetsDescriptionFromRss10_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getDescription());
    }

    public function testGetsDescriptionFromRss090_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getDescription());
    }

    /**
     * Get Language (Unencoded Text)
     */
    public function testGetsLanguageFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/rss20.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss094()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/rss094.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss093()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/rss093.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss092()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/rss092.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss091()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/rss091.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/rss10.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLanguageFromRss090()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/rss090.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    // DC 1.0

    public function testGetsLanguageFromRss20_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc10/rss20.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss094_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc10/rss094.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss093_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc10/rss093.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss092_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc10/rss092.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss091_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc10/rss091.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss10_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc10/rss10.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss090_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc10/rss090.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    // DC 1.1

    public function testGetsLanguageFromRss20_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc11/rss20.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss094_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc11/rss094.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss093_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc11/rss093.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss092_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc11/rss092.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss091_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc11/rss091.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss10_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc11/rss10.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss090_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc11/rss090.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    // Other

    public function testGetsLanguageFromRss10_XmlLang()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/rdf/rss10.xml')
        );
        $this->assertEquals('en', $feed->getLanguage());
    }

    // Missing Language

    public function testGetsLanguageFromRss20_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLanguageFromRss094_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLanguageFromRss093_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLanguageFromRss092_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLanguageFromRss091_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLanguageFromRss10_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLanguageFromRss090_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    /**
     * Get Link (Unencoded Text)
     */
    public function testGetsLinkFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/rss20.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromRss094()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/rss094.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromRss093()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/rss093.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromRss092()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/rss092.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromRss091()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/rss091.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromRss10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/rss10.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromRss090()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/rss090.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    // Missing Link

    public function testGetsLinkFromRss20_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getLink());
    }

    public function testGetsLinkFromRss094_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getLink());
    }

    public function testGetsLinkFromRss093_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getLink());
    }

    public function testGetsLinkFromRss092_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getLink());
    }

    public function testGetsLinkFromRss091_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getLink());
    }

    public function testGetsLinkFromRss10_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getLink());
    }

    public function testGetsLinkFromRss090_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getLink());
    }

    /**
     * Implements Countable
     */

    public function testCountableInterface()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/none/rss090.xml')
        );
        $this->assertEquals(0, count($feed));
    }

    /**
     * Get Feed Link (Unencoded Text)
     */
    public function testGetsFeedLinkFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/rss20.xml')
        );
        $this->assertEquals('http://www.example.com/feed/rss', $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss094()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/rss094.xml')
        );
        $this->assertEquals('http://www.example.com/feed/rss', $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss093()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/rss093.xml')
        );
        $this->assertEquals('http://www.example.com/feed/rss', $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss092()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/rss092.xml')
        );
        $this->assertEquals('http://www.example.com/feed/rss', $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss091()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/rss091.xml')
        );
        $this->assertEquals('http://www.example.com/feed/rss', $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/rss10.xml')
        );
        $this->assertEquals('http://www.example.com/feed/rss', $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss090()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/rss090.xml')
        );
        $this->assertEquals('http://www.example.com/feed/rss', $feed->getFeedLink());
    }

    // Missing Feed Link

    public function testGetsFeedLinkFromRss20_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss094_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss093_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss092_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss091_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss10_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss090_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getFeedLink());
    }

    /**
     * Get Generator (Unencoded Text)
     */
    public function testGetsGeneratorFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/rss20.xml')
        );
        $this->assertEquals('Zend_Feed_Writer', $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss094()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/rss094.xml')
        );
        $this->assertEquals('Zend_Feed_Writer', $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss093()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/rss093.xml')
        );
        $this->assertEquals('Zend_Feed_Writer', $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss092()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/rss092.xml')
        );
        $this->assertEquals('Zend_Feed_Writer', $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss091()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/rss091.xml')
        );
        $this->assertEquals('Zend_Feed_Writer', $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/rss10.xml')
        );
        $this->assertEquals('Zend_Feed_Writer', $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss090()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/rss090.xml')
        );
        $this->assertEquals('Zend_Feed_Writer', $feed->getGenerator());
    }

    // Missing Generator

    public function testGetsGeneratorFromRss20_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss094_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss093_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss092_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss091_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss10_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss090_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getGenerator());
    }

    /**
     * Get Date Modified (Unencoded Text)
     */
    public function testGetsDateModifiedFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/rss20.xml')
        );
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000',
	$feed->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    // DC 1.0

    public function testGetsDateModifiedFromRss20_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc10/rss20.xml')
        );
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000',
	$feed->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateModifiedFromRss094_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc10/rss094.xml')
        );
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000',
	$feed->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateModifiedFromRss093_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc10/rss093.xml')
        );
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000',
	$feed->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateModifiedFromRss092_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc10/rss092.xml')
        );
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000',
	$feed->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateModifiedFromRss091_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc10/rss091.xml')
        );
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000',
	$feed->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateModifiedFromRss10_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc10/rss10.xml')
        );
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000',
	$feed->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateModifiedFromRss090_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc10/rss090.xml')
        );
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000',
	$feed->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    // DC 1.1

    public function testGetsDateModifiedFromRss20_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc11/rss20.xml')
        );
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000',
	$feed->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateModifiedFromRss094_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc11/rss094.xml')
        );
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000',
	$feed->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateModifiedFromRss093_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc11/rss093.xml')
        );
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000',
	$feed->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateModifiedFromRss092_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc11/rss092.xml')
        );
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000',
	$feed->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateModifiedFromRss091_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc11/rss091.xml')
        );
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000',
	$feed->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateModifiedFromRss10_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc11/rss10.xml')
        );
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000',
	$feed->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateModifiedFromRss090_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc11/rss090.xml')
        );
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000',
	$feed->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    // Atom 1.0

    public function testGetsDateModifiedFromRss20_atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/atom10/rss20.xml')
        );
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000',
	$feed->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateModifiedFromRss094_atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/atom10/rss094.xml')
        );
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000',
	$feed->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateModifiedFromRss093_atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/atom10/rss093.xml')
        );
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000',
	$feed->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateModifiedFromRss092_atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/atom10/rss092.xml')
        );
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000',
	$feed->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateModifiedFromRss091_atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/atom10/rss091.xml')
        );
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000',
	$feed->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateModifiedFromRss10_atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/atom10/rss10.xml')
        );
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000',
	$feed->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateModifiedFromRss090_atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/atom10/rss090.xml')
        );
        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000',
	$feed->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    // Missing DateModified

    public function testGetsDateModifiedFromRss20_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getDateModified());
    }

    public function testGetsDateModifiedFromRss094_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getDateModified());
    }

    public function testGetsDateModifiedFromRss093_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getDateModified());
    }

    public function testGetsDateModifiedFromRss092_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getDateModified());
    }

    public function testGetsDateModifiedFromRss091_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getDateModified());
    }

    public function testGetsDateModifiedFromRss10_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getDateModified());
    }

    public function testGetsDateModifiedFromRss090_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getDateModified());
    }


}
