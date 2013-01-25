<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Feed
 */

namespace ZendTest\Feed\Reader\Entry;

use DateTime;
use Zend\Feed\Reader;

/**
* @category Zend
* @package Zend_Feed
* @subpackage UnitTests
* @group Zend_Feed
* @group Zend_Feed_Reader
*/
class AtomTest extends \PHPUnit_Framework_TestCase
{

    protected $feedSamplePath = null;

    protected $expectedCats = array();

    protected $expectedCatsDc = array();

    public function setup()
    {
        Reader\Reader::reset();
        $this->feedSamplePath = dirname(__FILE__) . '/_files/Atom';
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

    /**
     * Get Id (Unencoded Text)
     * @group ZFR003
     */
    public function testGetsIdFromAtom03()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/id/plain/atom03.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('1', $entry->getId());
    }

    public function testGetsIdFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/id/plain/atom10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('1', $entry->getId());
    }

    /**
     * Get creation date (Unencoded Text)
     */
    public function testGetsDateCreatedFromAtom03()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/datecreated/plain/atom03.xml')
        );
        $entry = $feed->current();
        $edate = DateTime::createFromFormat(DateTime::ISO8601, '2009-03-07T08:03:50Z');
        $this->assertEquals($edate, $entry->getDateCreated());
    }

    public function testGetsDateCreatedFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/datecreated/plain/atom10.xml')
        );
        $entry = $feed->current();
        $edate = DateTime::createFromFormat(DateTime::ISO8601, '2009-03-07T08:03:50Z');
        $this->assertEquals($edate, $entry->getDateCreated());
    }

    /**
     * Get modification date (Unencoded Text)
     */
    public function testGetsDateModifiedFromAtom03()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/datemodified/plain/atom03.xml')
        );
        $entry = $feed->current();
        $edate = DateTime::createFromFormat(DateTime::ISO8601, '2009-03-07T08:03:50Z');
        $this->assertEquals($edate, $entry->getDateModified());
    }

    public function testGetsDateModifiedFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/datemodified/plain/atom10.xml')
        );
        $entry = $feed->current();
        $edate = DateTime::createFromFormat(DateTime::ISO8601, '2009-03-07T08:03:50Z');
        $this->assertEquals($edate, $entry->getDateModified());
    }

    /**
     * Get Title (Unencoded Text)
     */
    public function testGetsTitleFromAtom03()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/atom03.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getTitle());
    }

    public function testGetsTitleFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/atom10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getTitle());
    }

    /**
     * Get Authors (Unencoded Text)
     */
    public function testGetsAuthorsFromAtom03()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/atom03.xml')
        );

        $authors = array(
            array('email'=>'joe@example.com','name'=>'Joe Bloggs','uri'=>'http://www.example.com'),
            array('name'=>'Joe Bloggs','uri'=>'http://www.example.com'),
            array('name'=>'Joe Bloggs'),
            array('email'=>'joe@example.com','uri'=>'http://www.example.com'),
            array('uri'=>'http://www.example.com'),
            array('email'=>'joe@example.com')
        );

        $entry = $feed->current();
        $this->assertEquals($authors, (array) $entry->getAuthors());
    }

    public function testGetsAuthorsFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/atom10.xml')
        );

        $authors = array(
            array('email'=>'joe@example.com','name'=>'Joe Bloggs','uri'=>'http://www.example.com'),
            array('name'=>'Joe Bloggs','uri'=>'http://www.example.com'),
            array('name'=>'Joe Bloggs'),
            array('email'=>'joe@example.com','uri'=>'http://www.example.com'),
            array('uri'=>'http://www.example.com'),
            array('email'=>'joe@example.com')
        );

        $entry = $feed->current();
        $this->assertEquals($authors, (array) $entry->getAuthors());
    }

    /**
     * Get Author (Unencoded Text)
     */
    public function testGetsAuthorFromAtom03()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/atom03.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array('name'=>'Joe Bloggs','email'=>'joe@example.com','uri'=>'http://www.example.com'), $entry->getAuthor());
    }

    public function testGetsAuthorFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/atom10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array('name'=>'Joe Bloggs','email'=>'joe@example.com','uri'=>'http://www.example.com'), $entry->getAuthor());
    }

    /**
     * Get Description (Unencoded Text)
     */
    public function testGetsDescriptionFromAtom03()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/atom03.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getDescription());
    }

    public function testGetsDescriptionFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/atom10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getDescription());
    }

    /**
     * Get enclosure
     */
    public function testGetsEnclosureFromAtom03()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/enclosure/plain/atom03.xml')
        );
        $entry = $feed->current();

        $expected = new \stdClass();
        $expected->url    = 'http://www.example.org/myaudiofile.mp3';
        $expected->length = '1234';
        $expected->type   = 'audio/mpeg';

        $this->assertEquals($expected, $entry->getEnclosure());
    }

    public function testGetsEnclosureFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/enclosure/plain/atom10.xml')
        );
        $entry = $feed->current();

        $expected = new \stdClass();
        $expected->url    = 'http://www.example.org/myaudiofile.mp3';
        $expected->length = '1234';
        $expected->type   = 'audio/mpeg';

        $this->assertEquals($expected, $entry->getEnclosure());
    }

    /**
     * Get Content (Unencoded Text)
     */
    public function testGetsContentFromAtom03()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/content/plain/atom03.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Content', $entry->getContent());
    }

    /**
     * TEXT
     * @group ZFRATOMCONTENT
     */
    public function testGetsContentFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/content/plain/atom10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Content &amp;', $entry->getContent());
    }

    /**
     * HTML Escaped
     * @group ZFRATOMCONTENT
     */
    public function testGetsContentFromAtom10Html()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/content/plain/atom10_Html.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('<p>Entry Content &amp;</p>', $entry->getContent());
    }

    /**
     * HTML CDATA Escaped
     * @group ZFRATOMCONTENT
     */
    public function testGetsContentFromAtom10HtmlCdata()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/content/plain/atom10_HtmlCdata.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('<p>Entry Content &amp;</p>', $entry->getContent());
    }

    /**
     * XHTML
     * @group ZFRATOMCONTENT
     */
    public function testGetsContentFromAtom10XhtmlNamespaced()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/content/plain/atom10_Xhtml.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('<p class="x:"><em>Entry Content &amp;x:</em></p>', $entry->getContent());
    }

    /**
     * Get Link (Unencoded Text)
     */
    public function testGetsLinkFromAtom03()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/link/plain/atom03.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry', $entry->getLink());
    }

    public function testGetsLinkFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/link/plain/atom10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry', $entry->getLink());
    }

    public function testGetsLinkFromAtom10_WithNoRelAttribute()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/link/plain/atom10-norel.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry', $entry->getLink());
    }

    public function testGetsLinkFromAtom10_WithRelativeUrl()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/link/plain/atom10-relative.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry', $entry->getLink());
    }

    /**
     * Get Base Uri
     */
    public function testGetsBaseUriFromAtom10_FromFeedElement()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/baseurl/plain/atom10-feedlevel.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com', $entry->getBaseUrl());
    }

    public function testGetsBaseUriFromAtom10_FromEntryElement()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/baseurl/plain/atom10-entrylevel.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/', $entry->getBaseUrl());
    }

    /**
     * Get Comment HTML Link
     */
    public function testGetsCommentLinkFromAtom03()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/commentlink/plain/atom03.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry/comments', $entry->getCommentLink());
    }

    public function testGetsCommentLinkFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/commentlink/plain/atom10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry/comments', $entry->getCommentLink());
    }

    public function testGetsCommentLinkFromAtom10_RelativeLinks()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/commentlink/plain/atom10-relative.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry/comments', $entry->getCommentLink());
    }

    /**
     * Get category data
     */

    // Atom 1.0 (Atom 0.3 never supported categories except via Atom 1.0/Dublin Core extensions)

    public function testGetsCategoriesFromAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/category/plain/atom10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals($this->expectedCats, (array) $entry->getCategories());
        $this->assertEquals(array('topic1','Cat & Dog'), array_values($entry->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromAtom03_Atom10Extension()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/category/plain/atom03.xml')
        );
        $entry = $feed->current();
        $this->assertEquals($this->expectedCats, (array) $entry->getCategories());
        $this->assertEquals(array('topic1','Cat & Dog'), array_values($entry->getCategories()->getValues()));
    }

    // DC 1.0/1.1 for Atom 0.3

    public function testGetsCategoriesFromAtom03_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/category/plain/dc10/atom03.xml')
        );
        $entry = $feed->current();
        $this->assertEquals($this->expectedCatsDc, (array) $entry->getCategories());
        $this->assertEquals(array('topic1','topic2'), array_values($entry->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromAtom03_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/category/plain/dc11/atom03.xml')
        );
        $entry = $feed->current();
        $this->assertEquals($this->expectedCatsDc, (array) $entry->getCategories());
        $this->assertEquals(array('topic1','topic2'), array_values($entry->getCategories()->getValues()));
    }

    // No Categories In Entry

    public function testGetsCategoriesFromAtom10_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/category/plain/none/atom10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array(), (array) $entry->getCategories());
        $this->assertEquals(array(), array_values($entry->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromAtom03_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/category/plain/none/atom03.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array(), (array) $entry->getCategories());
        $this->assertEquals(array(), array_values($entry->getCategories()->getValues()));
    }

}
