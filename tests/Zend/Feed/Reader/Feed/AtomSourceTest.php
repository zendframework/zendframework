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
 * @version    $Id: AtomTest.php 19168 2009-11-21 17:17:18Z padraic $
 */

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Zend/Feed/Reader.php';
require_once 'Zend/Feed/Reader/FeedAbstract.php';

/**
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Feed
 * @group      Zend_Feed_Reader
 */
class Zend_Feed_Reader_Feed_AtomSourceTest extends PHPUnit_Framework_TestCase
{

    protected $_feedSamplePath = null;
    
    protected $_options = array();
    
    protected $_expectedCats = array();
    
    protected $_expectedCatsDc = array();

    public function setup()
    {
        if (Zend_Registry::isRegistered('Zend_Locale')) {
            $registry = Zend_Registry::getInstance();
            unset($registry['Zend_Locale']);
        }
        $this->_feedSamplePath = dirname(__FILE__) . '/_files/AtomSource';
        $this->_options = Zend_Date::setOptions();
        foreach($this->_options as $k=>$v) {
            if (is_null($v)) {
                unset($this->_options[$k]);
            }
        }
        Zend_Date::setOptions(array('format_type'=>'iso'));
        $this->_expectedCats = array(
            array(
                'term' => 'topic1',
                'scheme' => 'http://example.com/schema1',
                'label' => 'topic1'
            ),
            array(
                'term' => 'topic1',
                'scheme' => 'http://example.com/schema2',
                'label' => 'topic1'
            ),
            array(
                'term' => 'cat_dog',
                'scheme' => 'http://example.com/schema1',
                'label' => 'Cat & Dog'
            )
        );
        $this->_expectedCatsDc = array(
            array(
                'term' => 'topic1',
                'scheme' => null,
                'label' => 'topic1'
            ),
            array(
                'term' => 'topic2',
                'scheme' => null,
                'label' => 'topic2'
            )
        );
    }
    
    public function teardown()
    {
        Zend_Date::setOptions($this->_options);
    }
    
    public function testGetsSourceFromEntry()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/atom10.xml')
        );
        $source = $feed->current()->getSource();
        $this->assertTrue($source instanceof Zend_Feed_Reader_Feed_Atom_Source);  
    }

    /**
     * Get Title (Unencoded Text)
     */

    public function testGetsTitleFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/atom10.xml')
        );
        $source = $feed->current()->getSource();
        $this->assertEquals('My Title', $source->getTitle());
    }

    /**
     * Get Authors (Unencoded Text)
     */

    public function testGetsAuthorArrayFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/atom10.xml')
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

    public function testGetsSingleAuthorFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/atom10.xml')
        );

        $this->assertEquals('joe@example.com (Joe Bloggs)', $feed->getAuthor());
    }

    /**
     * Get creation date (Unencoded Text)
     */

    public function testGetsDateCreatedFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/datecreated/atom10.xml')
        );

        $edate = new Zend_Date;
        $edate->set('2009-03-07T08:03:50Z', Zend_Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateCreated()));
    }

    /**
     * Get modification date (Unencoded Text)
     */

    public function testGetsDateModifiedFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/datemodified/atom10.xml')
        );

        $edate = new Zend_Date;
        $edate->set('2009-03-07T08:03:50Z', Zend_Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    /**
     * Get Generator (Unencoded Text)
     */

    public function testGetsGeneratorFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/atom10.xml')
        );
        $this->assertEquals('Zend_Feed', $feed->getGenerator());
    }

    /**
     * Get Copyright (Unencoded Text)
     */

    public function testGetsCopyrightFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/atom10.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    /**
     * Get Description (Unencoded Text)
     */

    public function testGetsDescriptionFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/atom10.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    /**
     * Get Id (Unencoded Text)
     */

    public function testGetsIdFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/atom10.xml')
        );
        $this->assertEquals('123', $feed->getId());
    }

    /**
     * Get Language (Unencoded Text)
     */

    public function testGetsLanguageFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/atom10.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    /**
     * Get Link (Unencoded Text)
     */

    public function testGetsLinkFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/atom10.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromAtom10WithNoRelAttribute()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/atom10-norel.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromAtom10WithRelativeUrl()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/atom10-relative.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    /**
     * Get Base Uri
     */
    public function testGetsBaseUriFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/atom10-relative.xml')
        );
        $this->assertEquals('http://www.example.com/', $feed->getBaseUrl());
    }

    /**
     * Get Feed Link (Unencoded Text)
     */

    public function testGetsFeedLinkFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/atom10.xml')
        );
        $this->assertEquals('http://www.example.com/feed/atom', $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromAtom10IfRelativeUri()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/atom10-relative.xml')
        );
        $this->assertEquals('http://www.example.com/feed/atom', $feed->getFeedLink());
    }

    /**
     * Get Pubsubhubbub Hubs
     */

    public function testGetsHubsFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/atom10.xml')
        );
        $this->assertEquals(array(
            'http://www.example.com/hub1',
            'http://www.example.com/hub2'
        ), $feed->getHubs());
    }
    
    /**
     * Get category data
     */
    
    public function testGetsCategoriesFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/atom10.xml')
        );
        $this->assertEquals($this->_expectedCats, (array) $feed->getCategories());
        $this->assertEquals(array('topic1','Cat & Dog'), array_values($feed->getCategories()->getValues()));
    }

}
