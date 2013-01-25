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
class LautDeRdfTest extends \PHPUnit_Framework_TestCase
{

    protected $feedSamplePath = null;

    public function setup()
    {
        Reader\Reader::reset();
        $this->feedSamplePath = dirname(__FILE__) . '/_files/laut.de-rdf.xml';
    }

    /**
     * Feed level testing
     */

    public function testGetsTitle()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('laut.de - news', $feed->getTitle());
    }

    public function testGetsAuthors()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals(array(array('name'=>'laut.de')), (array) $feed->getAuthors());
    }

    public function testGetsSingleAuthor()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals(array('name'=>'laut.de'), $feed->getAuthor());
    }

    public function testGetsCopyright()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('Copyright © 2004 laut.de', $feed->getCopyright());
    }

    public function testGetsDescription()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('laut.de: aktuelle News', $feed->getDescription());
    }

    public function testGetsLanguage()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLink()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('http://www.laut.de', $feed->getLink());
    }

    public function testGetsEncoding()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('ISO-8859-1', $feed->getEncoding());
    }



    /**
     * Entry level testing
     */

    public function testGetsEntryId()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.laut.de/vorlaut/news/2009/07/04/22426/index.htm', $entry->getId());
    }

    public function testGetsEntryTitle()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('Angelika Express: MySpace-Aus wegen Sido-Werbung', $entry->getTitle());
    }

    public function testGetsEntryAuthors()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals(array(array('name'=>'laut.de')), (array) $entry->getAuthors());
    }

    public function testGetsEntrySingleAuthor()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals(array('name'=>'laut.de'), $entry->getAuthor());
    }

    // Technically, the next two tests should not pass. However the source feed has an encoding
    // problem - it's stated as ISO-8859-1 but sent as UTF-8. The result is that a) it's
    // broken itself, or b) We should consider a fix in the future for similar feeds such
    // as using a more limited XML based decoding method (not html_entity_decode())

    public function testGetsEntryDescription()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('Schon lÃ¤nger haderten die KÃ¶lner mit der Plattform des "fiesen Rupert Murdoch". Das Fass zum Ãberlaufen brachte aber ein Werbebanner von Deutschrapper Sido.', $entry->getDescription());
    }

    public function testGetsEntryContent()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('Schon lÃ¤nger haderten die KÃ¶lner mit der Plattform des "fiesen Rupert Murdoch". Das Fass zum Ãberlaufen brachte aber ein Werbebanner von Deutschrapper Sido.', $entry->getContent());
    }

    public function testGetsEntryLinks()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals(array('http://www.laut.de/vorlaut/news/2009/07/04/22426/index.htm'), $entry->getLinks());
    }

    public function testGetsEntryLink()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.laut.de/vorlaut/news/2009/07/04/22426/index.htm', $entry->getLink());
    }

    public function testGetsEntryPermaLink()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.laut.de/vorlaut/news/2009/07/04/22426/index.htm',
            $entry->getPermaLink());
    }

    public function testGetsEntryEncoding()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('ISO-8859-1', $entry->getEncoding());
    }

}
