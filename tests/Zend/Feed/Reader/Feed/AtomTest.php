<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Zend/Feed/Reader.php';

class Zend_Feed_Reader_Feed_AtomTest extends PHPUnit_Framework_TestCase
{

    protected $_feedSamplePath = null;

    public function setup()
    {
        $this->_feedSamplePath = dirname(__FILE__) . '/_files/Atom';
    }

    /**
     * Get Title (Unencoded Text)
     */
    public function testGetsTitleFromAtom03()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/atom03.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/atom10.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    /**
     * Get Authors (Unencoded Text)
     */
    public function testGetsAuthorArrayFromAtom03()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/atom03.xml')
        );

        $authors = array(
            0 => 'joe@example.com (Joe Bloggs)',
            1 => 'Joe Bloggs',
            3 => 'joe@example.com',
            4 => 'http://www.example.com',
            6 => 'jane@example.com (Jane Bloggs)'
        );

        $this->assertEquals($authors, $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/atom10.xml')
        );

        $authors = array(
            0 => 'joe@example.com (Joe Bloggs)',
            1 => 'Joe Bloggs',
            3 => 'joe@example.com',
            4 => 'http://www.example.com',
            6 => 'jane@example.com (Jane Bloggs)'
        );

        $this->assertEquals($authors, $feed->getAuthors());
    }

	/**
     * Get Single Author (Unencoded Text)
     */
    public function testGetsSingleAuthorFromAtom03()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/atom03.xml')
        );

        $this->assertEquals('joe@example.com (Joe Bloggs)', $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/atom10.xml')
        );

        $this->assertEquals('joe@example.com (Joe Bloggs)', $feed->getAuthor());
    }

    /**
     * Get creation date (Unencoded Text)
     */
    public function testGetsDateCreatedFromAtom03()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/datecreated/plain/atom03.xml')
        );

        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000',
        $feed->getDateCreated()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateCreatedFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/datecreated/plain/atom10.xml')
        );

        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000',
        $feed->getDateCreated()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    /**
     * Get modification date (Unencoded Text)
     */
    public function testGetsDateModifiedFromAtom03()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/datemodified/plain/atom03.xml')
        );

        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000',
        $feed->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    public function testGetsDateModifiedFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/datemodified/plain/atom10.xml')
        );

        $this->assertEquals('Saturday 07 March 2009 08 03 50 +0000',
        $feed->getDateModified()->toString('EEEE dd MMMM YYYY HH mm ss ZZZ'));
    }

    /**
     * Get Generator (Unencoded Text)
     */
    public function testGetsGeneratorFromAtom03()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/atom03.xml')
        );
        $this->assertEquals('Zend_Feed', $feed->getGenerator());
    }

    public function testGetsGeneratorFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/atom10.xml')
        );
        $this->assertEquals('Zend_Feed', $feed->getGenerator());
    }

    /**
     * Get Copyright (Unencoded Text)
     */
    public function testGetsCopyrightFromAtom03()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/atom03.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/atom10.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    /**
     * Get Description (Unencoded Text)
     */
    public function testGetsDescriptionFromAtom03()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/atom03.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/atom10.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    /**
     * Get Id (Unencoded Text)
     */
    public function testGetsIdFromAtom03()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/atom03.xml')
        );
        $this->assertEquals('123', $feed->getId());
    }

    public function testGetsIdFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/atom10.xml')
        );
        $this->assertEquals('123', $feed->getId());
    }

    /**
     * Get Language (Unencoded Text)
     */
    public function testGetsLanguageFromAtom03()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/atom03.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/atom10.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    /**
     * Get Link (Unencoded Text)
     */
    public function testGetsLinkFromAtom03()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/atom03.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/atom10.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    /**
     * Get Feed Link (Unencoded Text)
     */
    public function testGetsFeedLinkFromAtom03()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/atom03.xml')
        );
        $this->assertEquals('http://www.example.com/feed/atom', $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/atom10.xml')
        );
        $this->assertEquals('http://www.example.com/feed/atom', $feed->getFeedLink());
    }

    /**
     * Implements Countable
     */

    public function testCountableInterface()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/atom10.xml')
        );
        $this->assertEquals(0, count($feed));
    }
}
