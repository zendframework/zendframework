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

/**
* @namespace
*/
namespace ZendTest\Feed\Reader\Feed;
use Zend\Feed\Reader;
use Zend\Date;

/**
* @category Zend
* @package Zend_Feed
* @subpackage UnitTests
* @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
* @license http://framework.zend.com/license/new-bsd New BSD License
* @group Zend_Feed
* @group Zend_Feed_Reader
*/
class AtomTest extends \PHPUnit_Framework_TestCase
{

    protected $_feedSamplePath = null;
    
    protected $_options = array();
    
    protected $_expectedCats = array();
    
    protected $_expectedCatsDc = array();

    public function setup()
    {
        Reader\Reader::reset();
        if (\Zend\Registry::isRegistered('Zend_Locale')) {
            $registry = \Zend\Registry::getInstance();
            unset($registry['Zend_Locale']);
        }
        $this->_feedSamplePath = dirname(__FILE__) . '/_files/Atom';
        $this->_options = Date\Date::setOptions();
        foreach($this->_options as $k=>$v) {
            if (is_null($v)) {
                unset($this->_options[$k]);
            }
        }
        Date\Date::setOptions(array('format_type'=>'iso'));
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
        Date\Date::setOptions($this->_options);
    }

    /**
     * Get Title (Unencoded Text)
     */
    public function testGetsTitleFromAtom03()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/atom03.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/atom10.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    /**
     * Get Authors (Unencoded Text)
     */
    public function testGetsAuthorArrayFromAtom03()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/atom03.xml')
        );

        $authors = array(
            array('email'=>'joe@example.com','name'=>'Joe Bloggs','uri'=>'http://www.example.com'),
            array('name'=>'Joe Bloggs','uri'=>'http://www.example.com'),
            array('name'=>'Joe Bloggs'),
            array('email'=>'joe@example.com','uri'=>'http://www.example.com'),
            array('uri'=>'http://www.example.com'),
            array('email'=>'joe@example.com')
        );

        $this->assertEquals($authors, (array) $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/atom10.xml')
        );

        $authors = array(
            array('email'=>'joe@example.com','name'=>'Joe Bloggs','uri'=>'http://www.example.com'),
            array('name'=>'Joe Bloggs','uri'=>'http://www.example.com'),
            array('name'=>'Joe Bloggs'),
            array('email'=>'joe@example.com','uri'=>'http://www.example.com'),
            array('uri'=>'http://www.example.com'),
            array('email'=>'joe@example.com')
        );

        $this->assertEquals($authors, (array) $feed->getAuthors());
    }

    /**
     * Get Single Author (Unencoded Text)
     */
    public function testGetsSingleAuthorFromAtom03()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/atom03.xml')
        );

        $this->assertEquals(array('name'=>'Joe Bloggs','email'=>'joe@example.com','uri'=>'http://www.example.com'), $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/atom10.xml')
        );

        $this->assertEquals(array('name'=>'Joe Bloggs','email'=>'joe@example.com','uri'=>'http://www.example.com'), $feed->getAuthor());
    }

    /**
     * Get creation date (Unencoded Text)
     */
    public function testGetsDateCreatedFromAtom03()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath . '/datecreated/plain/atom03.xml')
        );
        $edate = new Date\Date;
        $edate->set('2009-03-07T08:03:50Z', Date\Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateCreated()));
    }

    public function testGetsDateCreatedFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath . '/datecreated/plain/atom10.xml')
        );

        $edate = new Date\Date;
        $edate->set('2009-03-07T08:03:50Z', Date\Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateCreated()));
    }

    /**
     * Get modification date (Unencoded Text)
     */
    public function testGetsDateModifiedFromAtom03()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath . '/datemodified/plain/atom03.xml')
        );

        $edate = new Date\Date;
        $edate->set('2009-03-07T08:03:50Z', Date\Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath . '/datemodified/plain/atom10.xml')
        );

        $edate = new Date\Date;
        $edate->set('2009-03-07T08:03:50Z', Date\Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    /**
     * Get Last Build Date (Unencoded Text)
     */
    public function testGetsLastBuildDateAlwaysReturnsNullForAtom()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/atom10.xml')
        );
        $this->assertNull($feed->getLastBuildDate());
    }

    /**
     * Get Generator (Unencoded Text)
     */
    public function testGetsGeneratorFromAtom03()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/atom03.xml')
        );
        $this->assertEquals('Zend_Feed', $feed->getGenerator());
    }

    public function testGetsGeneratorFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/atom10.xml')
        );
        $this->assertEquals('Zend_Feed', $feed->getGenerator());
    }

    /**
     * Get Copyright (Unencoded Text)
     */
    public function testGetsCopyrightFromAtom03()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/atom03.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/atom10.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    /**
     * Get Description (Unencoded Text)
     */
    public function testGetsDescriptionFromAtom03()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/atom03.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/atom10.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    /**
     * Get Id (Unencoded Text)
     */
    public function testGetsIdFromAtom03()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/atom03.xml')
        );
        $this->assertEquals('123', $feed->getId());
    }

    public function testGetsIdFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/plain/atom10.xml')
        );
        $this->assertEquals('123', $feed->getId());
    }

    /**
     * Get Language (Unencoded Text)
     */
    public function testGetsLanguageFromAtom03()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/atom03.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/atom10.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    /**
     * Get Link (Unencoded Text)
     */
    public function testGetsLinkFromAtom03()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/atom03.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/atom10.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromAtom10WithNoRelAttribute()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/atom10-norel.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromAtom10WithRelativeUrl()
    {
        $this->markTestIncomplete('Pending fix for \Zend\URI\URL::validate()');
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/atom10-relative.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    /**
     * Get Base Uri
     */
    public function testGetsBaseUriFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/atom10-relative.xml')
        );
        $this->assertEquals('http://www.example.com/', $feed->getBaseUrl());
    }

    /**
     * Get Feed Link (Unencoded Text)
     */
    public function testGetsFeedLinkFromAtom03()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/atom03.xml')
        );
        $this->assertEquals('http://www.example.com/feed/atom', $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/atom10.xml')
        );
        $this->assertEquals('http://www.example.com/feed/atom', $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromAtom10IfRelativeUri()
    {
        $this->markTestIncomplete('Pending fix for \Zend\URI\URL::validate()');
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/atom10-relative.xml')
        );
        $this->assertEquals('http://www.example.com/feed/atom', $feed->getFeedLink());
    }

    public function testGetsOriginalSourceUriIfFeedLinkNotAvailableFromFeed()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/atom10_NoFeedLink.xml')
        );
        $feed->setOriginalSourceUri('http://www.example.com/feed/atom');
        $this->assertEquals('http://www.example.com/feed/atom', $feed->getFeedLink());
    }

    /**
     * Get Pubsubhubbub Hubs
     */
    public function testGetsHubsFromAtom03()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/plain/atom03.xml')
        );
        $this->assertEquals(array(
            'http://www.example.com/hub1',
            'http://www.example.com/hub2'
        ), $feed->getHubs());
    }

    public function testGetsHubsFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/plain/atom10.xml')
        );
        $this->assertEquals(array(
            'http://www.example.com/hub1',
            'http://www.example.com/hub2'
        ), $feed->getHubs());
    }


    /**
     * Implements Countable
     */

    public function testCountableInterface()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/atom10.xml')
        );
        $this->assertEquals(0, count($feed));
    }
    
    /**
     * Get category data
     */
    
    // Atom 1.0 (Atom 0.3 never supported categories except via Atom 1.0/Dublin Core extensions)
    
    public function testGetsCategoriesFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/atom10.xml')
        );
        $this->assertEquals($this->_expectedCats, (array) $feed->getCategories());
        $this->assertEquals(array('topic1','Cat & Dog'), array_values($feed->getCategories()->getValues()));
    }
    
    public function testGetsCategoriesFromAtom03_Atom10Extension()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/atom03.xml')
        );
        $this->assertEquals($this->_expectedCats, (array) $feed->getCategories());
        $this->assertEquals(array('topic1','Cat & Dog'), array_values($feed->getCategories()->getValues()));
    }
    
    // DC 1.0/1.1 for Atom 0.3
    
    public function testGetsCategoriesFromAtom03_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/dc10/atom03.xml')
        );
        $this->assertEquals($this->_expectedCatsDc, (array) $feed->getCategories());
        $this->assertEquals(array('topic1','topic2'), array_values($feed->getCategories()->getValues()));
    }
    
    public function testGetsCategoriesFromAtom03_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/dc11/atom03.xml')
        );
        $this->assertEquals($this->_expectedCatsDc, (array) $feed->getCategories());
        $this->assertEquals(array('topic1','topic2'), array_values($feed->getCategories()->getValues()));
    }
    
    // No Categories In Entry
    
    public function testGetsCategoriesFromAtom10_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/none/atom10.xml')
        );
        $this->assertEquals(array(), (array) $feed->getCategories());
        $this->assertEquals(array(), array_values($feed->getCategories()->getValues()));
    }
    
    public function testGetsCategoriesFromAtom03_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/none/atom03.xml')
        );
        $this->assertEquals(array(), (array) $feed->getCategories());
        $this->assertEquals(array(), array_values($feed->getCategories()->getValues()));
    }

    /**
     * Get Image (Unencoded Text)
     */
    public function testGetsImageFromAtom03()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/atom03.xml')
        );
        $this->assertEquals(array('uri'=>'http://www.example.com/logo.gif'), $feed->getImage());
    }

    public function testGetsImageFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/atom10.xml')
        );
        $this->assertEquals(array('uri'=>'http://www.example.com/logo.gif'), $feed->getImage());
    }

    /**
     * Get Image (Unencoded Text) When Missing
     */
    public function testGetsImageFromAtom03_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/none/atom03.xml')
        );
        $this->assertEquals(null, $feed->getImage());
    }

    public function testGetsImageFromAtom10_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/none/atom10.xml')
        );
        $this->assertEquals(null, $feed->getImage());
    }
}
