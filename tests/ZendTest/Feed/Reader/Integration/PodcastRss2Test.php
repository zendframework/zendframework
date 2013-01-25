<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Feed
 */

namespace ZendTest\Feed\Reader\Integration;

use Zend\Feed\Reader;

/**
* @category Zend
* @package Zend_Feed
* @subpackage UnitTests
* @group Zend_Feed
* @group Zend_Feed_Reader
*/
class PodcastRss2Test extends \PHPUnit_Framework_TestCase
{

    protected $feedSamplePath = null;

    public function setup()
    {
        Reader\Reader::reset();
        $this->feedSamplePath = dirname(__FILE__) . '/_files/podcast.xml';
    }

    /**
     * Feed level testing
     */

    public function testGetsNewFeedUrl()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('http://newlocation.com/example.rss', $feed->getNewFeedUrl());
    }

    public function testGetsOwner()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('john.doe@example.com (John Doe)', $feed->getOwner());
    }

    public function testGetsCategories()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals(array(
            'Technology' => array(
                'Gadgets' => null
            ),
            'TV & Film' => null
        ), $feed->getItunesCategories());
    }

    public function testGetsTitle()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('All About Everything', $feed->getTitle());
    }

    public function testGetsCastAuthor()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('John Doe', $feed->getCastAuthor());
    }

    public function testGetsFeedBlock()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('no', $feed->getBlock());
    }

    public function testGetsCopyright()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('℗ & © 2005 John Doe & Family', $feed->getCopyright());
    }

    public function testGetsDescription()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('All About Everything is a show about everything.
            Each week we dive into any subject known to man and talk
            about it as much as we can. Look for our Podcast in the
            iTunes Store', $feed->getDescription());
    }

    public function testGetsLanguage()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('en-us', $feed->getLanguage());
    }

    public function testGetsLink()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('http://www.example.com/podcasts/everything/index.html', $feed->getLink());
    }

    public function testGetsEncoding()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('UTF-8', $feed->getEncoding());
    }

    public function testGetsFeedExplicit()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('yes', $feed->getExplicit());
    }

    public function testGetsEntryCount()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals(3, $feed->count());
    }

    public function testGetsImage()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('http://example.com/podcasts/everything/AllAboutEverything.jpg', $feed->getItunesImage());
    }

    /**
     * Entry level testing
     */

    public function testGetsEntryBlock()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('yes', $entry->getBlock());
    }

    public function testGetsEntryId()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('http://example.com/podcasts/archive/aae20050615.m4a', $entry->getId());
    }

    public function testGetsEntryTitle()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('Shake Shake Shake Your Spices', $entry->getTitle());
    }

    public function testGetsEntryCastAuthor()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('John Doe', $entry->getCastAuthor());
    }

    public function testGetsEntryExplicit()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('no', $entry->getExplicit());
    }

    public function testGetsSubtitle()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('A short primer on table spices
            ', $entry->getSubtitle());
    }

    public function testGetsSummary()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('This week we talk about salt and pepper
                shakers, comparing and contrasting pour rates,
                construction materials, and overall aesthetics. Come and
                join the party!', $entry->getSummary());
    }

    public function testGetsDuration()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('7:04', $entry->getDuration());
    }

    public function testGetsKeywords()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('salt, pepper, shaker, exciting
            ', $entry->getKeywords());
    }

    public function testGetsEntryEncoding()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('UTF-8', $entry->getEncoding());
    }

    public function testGetsEnclosure()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();

        $expected = new \stdClass();
        $expected->url    = 'http://example.com/podcasts/everything/AllAboutEverythingEpisode3.m4a';
        $expected->length = '8727310';
        $expected->type   = 'audio/x-m4a';

        $this->assertEquals($expected, $entry->getEnclosure());
    }
}
