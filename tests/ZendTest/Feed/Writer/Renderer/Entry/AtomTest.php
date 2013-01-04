<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Feed
 */

namespace ZendTest\Feed\Writer\Renderer\Entry;

use Zend\Feed\Writer\Renderer;
use Zend\Feed\Writer;
use Zend\Feed\Reader;

/**
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @group      Zend_Feed
 * @group      Zend_Feed_Writer
 */
class AtomTest extends \PHPUnit_Framework_TestCase
{

    protected $validWriter = null;
    protected $validEntry = null;

    public function setUp()
    {
        $this->validWriter = new Writer\Feed;

        $this->validWriter->setType('atom');

        $this->validWriter->setTitle('This is a test feed.');
        $this->validWriter->setDescription('This is a test description.');
        $this->validWriter->setDateModified(1234567890);
        $this->validWriter->setLink('http://www.example.com');
        $this->validWriter->setFeedLink('http://www.example.com/atom', 'atom');
        $this->validWriter->addAuthor(array('name' => 'Joe',
                                             'email'=> 'joe@example.com',
                                             'uri'  => 'http://www.example.com/joe'));
        $this->validEntry = $this->validWriter->createEntry();
        $this->validEntry->setTitle('This is a test entry.');
        $this->validEntry->setDescription('This is a test entry description.');
        $this->validEntry->setDateModified(1234567890);
        $this->validEntry->setDateCreated(1234567000);
        $this->validEntry->setLink('http://www.example.com/1');
        $this->validEntry->addAuthor(array('name' => 'Jane',
                                            'email'=> 'jane@example.com',
                                            'uri'  => 'http://www.example.com/jane'));
        $this->validEntry->setContent('<p class="xhtml:">This is test content for <em>xhtml:</em></p>');
        $this->validWriter->addEntry($this->validEntry);
    }

    public function tearDown()
    {
        $this->validWriter = null;
        $this->validEntry  = null;
    }

    public function testRenderMethodRunsMinimalWriterContainerProperlyBeforeICheckAtomCompliance()
    {
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $renderer->render();
    }

    public function testEntryEncodingHasBeenSet()
    {
        $this->validWriter->setEncoding('iso-8859-1');
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals('iso-8859-1', $entry->getEncoding());
    }

    public function testEntryEncodingDefaultIsUsedIfEncodingNotSetByHand()
    {
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals('UTF-8', $entry->getEncoding());
    }

    public function testEntryTitleHasBeenSet()
    {
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals('This is a test entry.', $entry->getTitle());
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testFeedTitleIfMissingThrowsException()
    {
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $this->validEntry->remove('title');
        $atomFeed->render();
    }

    public function testEntrySummaryDescriptionHasBeenSet()
    {
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals('This is a test entry description.', $entry->getDescription());
    }

    /**
     * @group ZFWATOMCONTENT
     */
    public function testEntryContentHasBeenSet_Xhtml()
    {
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals('<p class="xhtml:">This is test content for <em>xhtml:</em></p>', $entry->getContent());
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testFeedContentIfMissingThrowsExceptionIfThereIsNoLink()
    {
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $this->validEntry->remove('content');
        $this->validEntry->remove('link');
        $atomFeed->render();
    }

    public function testEntryUpdatedDateHasBeenSet()
    {
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals(1234567890, $entry->getDateModified()->getTimestamp());
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testFeedUpdatedDateIfMissingThrowsException()
    {
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $this->validEntry->remove('dateModified');
        $atomFeed->render();
    }

    public function testEntryPublishedDateHasBeenSet()
    {
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals(1234567000, $entry->getDateCreated()->getTimestamp());
    }

    public function testEntryIncludesLinkToHtmlVersionOfFeed()
    {
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals('http://www.example.com/1', $entry->getLink());
    }

    public function testEntryHoldsAnyAuthorAdded()
    {
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $author   = $entry->getAuthor();
        $this->assertEquals(array(
                                 'name' => 'Jane',
                                 'email'=> 'jane@example.com',
                                 'uri'  => 'http://www.example.com/jane'), $entry->getAuthor());
    }

    public function testEntryHoldsAnyEnclosureAdded()
    {
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $this->validEntry->setEnclosure(array(
                                              'type'   => 'audio/mpeg',
                                              'length' => '1337',
                                              'uri'    => 'http://example.com/audio.mp3'
                                         ));
        $feed  = Reader\Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $enc   = $entry->getEnclosure();
        $this->assertEquals('audio/mpeg', $enc->type);
        $this->assertEquals('1337', $enc->length);
        $this->assertEquals('http://example.com/audio.mp3', $enc->url);
    }

    public function testEntryIdHasBeenSet()
    {
        $this->validEntry->setId('urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6');
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals('urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6', $entry->getId());
    }

    public function testEntryIdHasBeenSetUsingSimpleTagUri()
    {
        $this->validEntry->setId('tag:example.org,2010:/foo/bar/');
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals('tag:example.org,2010:/foo/bar/', $entry->getId());
    }

    public function testEntryIdHasBeenSetUsingComplexTagUri()
    {
        $this->validEntry->setId('tag:diveintomark.org,2004-05-27:/archives/2004/05/27/howto-atom-linkblog');
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals('tag:diveintomark.org,2004-05-27:/archives/2004/05/27/howto-atom-linkblog',
                            $entry->getId());
    }

    public function testFeedIdDefaultIsUsedIfNotSetByHand()
    {
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals($entry->getLink(), $entry->getId());
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testFeedIdIfMissingThrowsException()
    {
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $this->validEntry->remove('id');
        $this->validEntry->remove('link');
        $atomFeed->render();
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testFeedIdThrowsExceptionIfNotUri()
    {
        $this->markTestIncomplete('Pending Zend\URI fix for validation');
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $this->validEntry->remove('id');
        $this->validEntry->remove('link');
        $this->validEntry->setId('not-a-uri');
        $atomFeed->render();
    }

    public function testCommentLinkRendered()
    {
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $this->validEntry->setCommentLink('http://www.example.com/id/1');
        $feed  = Reader\Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/id/1', $entry->getCommentLink());
    }

    public function testCommentCountRendered()
    {
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $this->validEntry->setCommentCount(22);
        $feed  = Reader\Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals(22, $entry->getCommentCount());
    }

    public function testCategoriesCanBeSet()
    {
        $this->validEntry->addCategories(array(
                                               array('term'   => 'cat_dog',
                                                     'label'  => 'Cats & Dogs',
                                                     'scheme' => 'http://example.com/schema1'),
                                               array('term'=> 'cat_dog2')
                                          ));
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $atomFeed->render();
        $feed     = Reader\Reader::importString($atomFeed->saveXml());
        $entry    = $feed->current();
        $expected = array(
            array('term'   => 'cat_dog',
                  'label'  => 'Cats & Dogs',
                  'scheme' => 'http://example.com/schema1'),
            array('term'   => 'cat_dog2',
                  'label'  => 'cat_dog2',
                  'scheme' => null)
        );
        $this->assertEquals($expected, (array)$entry->getCategories());
    }

    public function testCommentFeedLinksRendered()
    {
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $this->validEntry->setCommentFeedLinks(array(
                                                     array('uri' => 'http://www.example.com/atom/id/1',
                                                           'type'=> 'atom'),
                                                     array('uri' => 'http://www.example.com/rss/id/1',
                                                           'type'=> 'rss'),
                                                ));
        $feed  = Reader\Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        // Skipped over due to ZFR bug (detects Atom in error when RSS requested)
        //$this->assertEquals('http://www.example.com/rss/id/1', $entry->getCommentFeedLink('rss'));
        $this->assertEquals('http://www.example.com/atom/id/1', $entry->getCommentFeedLink('atom'));
    }

}
