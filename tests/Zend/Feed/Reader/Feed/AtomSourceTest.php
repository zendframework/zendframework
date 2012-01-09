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
class AtomSourceTest extends \PHPUnit_Framework_TestCase
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
        $this->_feedSamplePath = dirname(__FILE__) . '/_files/AtomSource';
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
    
    public function testGetsSourceFromEntry()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/atom10.xml')
        );
        $source = $feed->current()->getSource();
        $this->assertTrue($source instanceof Reader\Feed\Atom\Source);  
    }

    /**
     * Get Title (Unencoded Text)
     */

    public function testGetsTitleFromAtom10()
    {
        $feed = Reader\Reader::importString(
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
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/atom10.xml')
        );
        $source = $feed->current()->getSource();

        $authors = array(
            array('email'=>'joe@example.com','name'=>'Joe Bloggs','uri'=>'http://www.example.com'),
            array('name'=>'Joe Bloggs','uri'=>'http://www.example.com'),
            array('name'=>'Joe Bloggs'),
            array('email'=>'joe@example.com','uri'=>'http://www.example.com'),
            array('uri'=>'http://www.example.com'),
            array('email'=>'joe@example.com')
        );

        $this->assertEquals($authors, (array) $source->getAuthors());
    }

    /**
     * Get Single Author (Unencoded Text)
     */

    public function testGetsSingleAuthorFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/atom10.xml')
        );
        $source = $feed->current()->getSource();

        $this->assertEquals(array('name'=>'Joe Bloggs','email'=>'joe@example.com','uri'=>'http://www.example.com'), $feed->getAuthor());
    }

    /**
     * Get creation date (Unencoded Text)
     */

    public function testGetsDateCreatedFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath . '/datecreated/atom10.xml')
        );
        $source = $feed->current()->getSource();

        $edate = new Date\Date;
        $edate->set('2009-03-07T08:03:50Z', Date\Date::ISO_8601);
        $this->assertTrue($edate->equals($source->getDateCreated()));
    }

    /**
     * Get modification date (Unencoded Text)
     */

    public function testGetsDateModifiedFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath . '/datemodified/atom10.xml')
        );
        $source = $feed->current()->getSource();

        $edate = new Date\Date;
        $edate->set('2009-03-07T08:03:50Z', Date\Date::ISO_8601);
        $this->assertTrue($edate->equals($source->getDateModified()));
    }

    /**
     * Get Generator (Unencoded Text)
     */

    public function testGetsGeneratorFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/atom10.xml')
        );
        $source = $feed->current()->getSource();
        $this->assertEquals('Zend_Feed', $source->getGenerator());
    }

    /**
     * Get Copyright (Unencoded Text)
     */

    public function testGetsCopyrightFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/atom10.xml')
        );
        $source = $feed->current()->getSource();
        $this->assertEquals('Copyright 2008', $source->getCopyright());
    }

    /**
     * Get Description (Unencoded Text)
     */

    public function testGetsDescriptionFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/atom10.xml')
        );
        $source = $feed->current()->getSource();
        $this->assertEquals('My Description', $source->getDescription());
    }

    /**
     * Get Id (Unencoded Text)
     */

    public function testGetsIdFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/id/atom10.xml')
        );
        $source = $feed->current()->getSource();
        $this->assertEquals('123', $source->getId());
    }

    /**
     * Get Language (Unencoded Text)
     */

    public function testGetsLanguageFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/atom10.xml')
        );
        $source = $feed->current()->getSource();
        $this->assertEquals('en-GB', $source->getLanguage());
    }

    /**
     * Get Link (Unencoded Text)
     */

    public function testGetsLinkFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/atom10.xml')
        );
        $source = $feed->current()->getSource();
        $this->assertEquals('http://www.example.com', $source->getLink());
    }

    /**
     * Get Feed Link (Unencoded Text)
     */

    public function testGetsFeedLinkFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/atom10.xml')
        );
        $source = $feed->current()->getSource();
        $this->assertEquals('http://www.example.com/feed/atom', $source->getFeedLink());
    }
    
    /**
     * Get Pubsubhubbub Hubs
     */
    public function testGetsHubsFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/atom10.xml')
        );
        $source = $feed->current()->getSource();
        $this->assertEquals(array(
            'http://www.example.com/hub1',
            'http://www.example.com/hub2'
        ), $source->getHubs());
    }
    
    /**
     * Get category data
     */
    public function testGetsCategoriesFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/atom10.xml')
        );
        $source = $feed->current()->getSource();
        $this->assertEquals($this->_expectedCats, (array) $source->getCategories());
        $this->assertEquals(array('topic1','Cat & Dog'), array_values($source->getCategories()->getValues()));
    }

}
