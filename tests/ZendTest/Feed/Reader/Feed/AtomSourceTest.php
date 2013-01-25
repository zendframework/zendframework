<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Feed
 */

namespace ZendTest\Feed\Reader\Feed;

use DateTime;
use Zend\Feed\Reader;

/**
* @category Zend
* @package Zend_Feed
* @subpackage UnitTests
* @group Zend_Feed
* @group Zend_Feed_Reader
*/
class AtomSourceTest extends \PHPUnit_Framework_TestCase
{

    protected $feedSamplePath = null;

    protected $options = array();

    protected $expectedCats = array();

    protected $expectedCatsDc = array();

    public function setup()
    {
        Reader\Reader::reset();
        $this->feedSamplePath = dirname(__FILE__) . '/_files/AtomSource';
        $this->expectedCats = array(
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
        $this->expectedCatsDc = array(
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

    public function testGetsSourceFromEntry()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/title/atom10.xml')
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
            file_get_contents($this->feedSamplePath.'/title/atom10.xml')
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
            file_get_contents($this->feedSamplePath.'/author/atom10.xml')
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
            file_get_contents($this->feedSamplePath.'/author/atom10.xml')
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
            file_get_contents($this->feedSamplePath . '/datecreated/atom10.xml')
        );
        $source = $feed->current()->getSource();

        $edate = DateTime::createFromFormat(DateTime::ISO8601, '2009-03-07T08:03:50Z');
        $this->assertEquals($edate, $source->getDateCreated());
    }

    /**
     * Get modification date (Unencoded Text)
     */

    public function testGetsDateModifiedFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/datemodified/atom10.xml')
        );
        $source = $feed->current()->getSource();

        $edate = DateTime::createFromFormat(DateTime::ISO8601, '2009-03-07T08:03:50Z');
        $this->assertEquals($edate, $source->getDateModified());
    }

    /**
     * Get Generator (Unencoded Text)
     */

    public function testGetsGeneratorFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/generator/atom10.xml')
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
            file_get_contents($this->feedSamplePath.'/copyright/atom10.xml')
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
            file_get_contents($this->feedSamplePath.'/description/atom10.xml')
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
            file_get_contents($this->feedSamplePath.'/id/atom10.xml')
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
            file_get_contents($this->feedSamplePath.'/language/atom10.xml')
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
            file_get_contents($this->feedSamplePath.'/link/atom10.xml')
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
            file_get_contents($this->feedSamplePath.'/feedlink/atom10.xml')
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
            file_get_contents($this->feedSamplePath.'/hubs/atom10.xml')
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
            file_get_contents($this->feedSamplePath.'/category/atom10.xml')
        );
        $source = $feed->current()->getSource();
        $this->assertEquals($this->expectedCats, (array) $source->getCategories());
        $this->assertEquals(array('topic1','Cat & Dog'), array_values($source->getCategories()->getValues()));
    }

}
