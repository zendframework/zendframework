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
namespace ZendTest\Feed\Writer\Renderer\Entry;
use Zend\Feed\Writer\Renderer;
use Zend\Feed\Writer;
use Zend\Feed\Reader;
use Zend\Date;

/**
* @category Zend
* @package Zend_Feed
* @subpackage UnitTests
* @group Zend_Feed
* @group Zend_Feed_Writer
* @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
* @license http://framework.zend.com/license/new-bsd New BSD License
*/
class AtomTest extends \PHPUnit_Framework_TestCase
{

    protected $_validWriter = null;
    protected $_validEntry = null;

    public function setUp()
    {
        $this->_validWriter = new Writer\Feed;
        
        $this->_validWriter->setType('atom');
        
        $this->_validWriter->setTitle('This is a test feed.');
        $this->_validWriter->setDescription('This is a test description.');
        $this->_validWriter->setDateModified(1234567890);
        $this->_validWriter->setLink('http://www.example.com');
        $this->_validWriter->setFeedLink('http://www.example.com/atom', 'atom');
        $this->_validWriter->addAuthor('Joe', 'joe@example.com', 'http://www.example.com/joe');
        $this->_validEntry = $this->_validWriter->createEntry();
        $this->_validEntry->setTitle('This is a test entry.');
        $this->_validEntry->setDescription('This is a test entry description.');
        $this->_validEntry->setDateModified(1234567890);
        $this->_validEntry->setDateCreated(1234567000);
        $this->_validEntry->setLink('http://www.example.com/1');
        $this->_validEntry->addAuthor('Jane', 'jane@example.com', 'http://www.example.com/jane');
        $this->_validEntry->setContent('<p class="xhtml:">This is test content for <em>xhtml:</em></p>');
        $this->_validWriter->addEntry($this->_validEntry);
    }

    public function tearDown()
    {
        $this->_validWriter = null;
        $this->_validEntry = null;
    }

    public function testRenderMethodRunsMinimalWriterContainerProperlyBeforeICheckAtomCompliance()
    {
        $renderer = new Renderer\Feed\Atom($this->_validWriter);
        try {
            $renderer->render();
        } catch (Writer\Exception $e) {
            $this->fail('Valid Writer object caused an exception when building which should never happen');
        }
    }

    public function testEntryEncodingHasBeenSet()
    {
        $this->_validWriter->setEncoding('iso-8859-1');
        $renderer = new Renderer\Feed\Atom($this->_validWriter);
        $feed = Reader\Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals('iso-8859-1', $entry->getEncoding());
    }

    public function testEntryEncodingDefaultIsUsedIfEncodingNotSetByHand()
    {
        $renderer = new Renderer\Feed\Atom($this->_validWriter);
        $feed = Reader\Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals('UTF-8', $entry->getEncoding());
    }

    public function testEntryTitleHasBeenSet()
    {
        $renderer = new Renderer\Feed\Atom($this->_validWriter);
        $feed = Reader\Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals('This is a test entry.', $entry->getTitle());
    }
    
    /**
     * @expectedException Zend\Feed\Writer\Exception
     */
    public function testFeedTitleIfMissingThrowsException()
    {
        $atomFeed = new Renderer\Feed\Atom($this->_validWriter);
        $this->_validEntry->remove('title');
        $atomFeed->render();
    }

    public function testEntrySummaryDescriptionHasBeenSet()
    {
        $renderer = new Renderer\Feed\Atom($this->_validWriter);
        $feed = Reader\Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals('This is a test entry description.', $entry->getDescription());
    }

    /**
     * @group ZFWATOMCONTENT
     */
    public function testEntryContentHasBeenSet_Xhtml()
    {
        $renderer = new Renderer\Feed\Atom($this->_validWriter);
        $feed = Reader\Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals('<p class="xhtml:">This is test content for <em>xhtml:</em></p>', $entry->getContent());
    }
    
    /**
     * @expectedException Zend\Feed\Writer\Exception
     */
    public function testFeedContentIfMissingThrowsExceptionIfThereIsNoLink()
    {
        $atomFeed = new Renderer\Feed\Atom($this->_validWriter);
        $this->_validEntry->remove('content');
        $this->_validEntry->remove('link');
        $atomFeed->render();
    }

    public function testEntryUpdatedDateHasBeenSet()
    {
        $renderer = new Renderer\Feed\Atom($this->_validWriter);
        $feed = Reader\Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals(1234567890, $entry->getDateModified()->get(Date\Date::TIMESTAMP));
    }
    
    /**
     * @expectedException Zend\Feed\Writer\Exception
     */
    public function testFeedUpdatedDateIfMissingThrowsException()
    {
        $atomFeed = new Renderer\Feed\Atom($this->_validWriter);
        $this->_validEntry->remove('dateModified');
        $atomFeed->render();
    }

    public function testEntryPublishedDateHasBeenSet()
    {
        $renderer = new Renderer\Feed\Atom($this->_validWriter);
        $feed = Reader\Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals(1234567000, $entry->getDateCreated()->get(Date\Date::TIMESTAMP));
    }

    public function testEntryIncludesLinkToHtmlVersionOfFeed()
    {
        $renderer= new Renderer\Feed\Atom($this->_validWriter);
        $feed = Reader\Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/1', $entry->getLink());
    }

    public function testEntryHoldsAnyAuthorAdded()
    {
        $renderer = new Renderer\Feed\Atom($this->_validWriter);
        $feed = Reader\Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $author = $entry->getAuthor();
        $this->assertEquals(array(
            'name'=>'Jane',
            'email'=>'jane@example.com',
            'uri'=>'http://www.example.com/jane'), $entry->getAuthor());
    }
    
    public function testEntryHoldsAnyEnclosureAdded()
    {
        $renderer = new Renderer\Feed\Atom($this->_validWriter);
        $this->_validEntry->setEnclosure(array(
            'type' => 'audio/mpeg',
            'length' => '1337',
            'uri' => 'http://example.com/audio.mp3'
        ));
        $feed = Reader\Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $enc = $entry->getEnclosure();
        $this->assertEquals('audio/mpeg', $enc->type);
        $this->assertEquals('1337', $enc->length);
        $this->assertEquals('http://example.com/audio.mp3', $enc->url);
    }

    public function testEntryIdHasBeenSet()
    {
        $this->_validEntry->setId('urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6');
        $renderer = new Renderer\Feed\Atom($this->_validWriter);
        $feed = Reader\Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals('urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6', $entry->getId());
    }
    
    public function testEntryIdHasBeenSetUsingSimpleTagUri()
    {
        $this->_validEntry->setId('tag:example.org,2010:/foo/bar/');
        $renderer = new Renderer\Feed\Atom($this->_validWriter);
        $feed = Reader\Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals('tag:example.org,2010:/foo/bar/', $entry->getId());
    }
    
    public function testEntryIdHasBeenSetUsingComplexTagUri()
    {
        $this->_validEntry->setId('tag:diveintomark.org,2004-05-27:/archives/2004/05/27/howto-atom-linkblog');
        $renderer = new Renderer\Feed\Atom($this->_validWriter);
        $feed = Reader\Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals('tag:diveintomark.org,2004-05-27:/archives/2004/05/27/howto-atom-linkblog', $entry->getId());
    }

    public function testFeedIdDefaultIsUsedIfNotSetByHand()
    {
        $renderer = new Renderer\Feed\Atom($this->_validWriter);
        $feed = Reader\Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals($entry->getLink(), $entry->getId());
    }
    
    /**
     * @expectedException Zend\Feed\Writer\Exception
     */
    public function testFeedIdIfMissingThrowsException()
    {
        $atomFeed = new Renderer\Feed\Atom($this->_validWriter);
        $this->_validEntry->remove('id');
        $this->_validEntry->remove('link');
        $atomFeed->render();
    }
    
    /**
     * @expectedException Zend\Feed\Writer\Exception
     */
    public function testFeedIdThrowsExceptionIfNotUri()
    {
        $this->markTestIncomplete('Pending Zend\URI fix for validation');
        $atomFeed = new Renderer\Feed\Atom($this->_validWriter);
        $this->_validEntry->remove('id');
        $this->_validEntry->remove('link');
        $this->_validEntry->setId('not-a-uri');
        $atomFeed->render();
    }
    
    public function testCommentLinkRendered()
    {
        $renderer = new Renderer\Feed\Atom($this->_validWriter);
        $this->_validEntry->setCommentLink('http://www.example.com/id/1');
        $feed = Reader\Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/id/1', $entry->getCommentLink());
    }
    
    public function testCommentCountRendered()
    {
        $renderer = new Renderer\Feed\Atom($this->_validWriter);
        $this->_validEntry->setCommentCount(22);
        $feed = Reader\Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals(22, $entry->getCommentCount());
    }
    
    public function testCategoriesCanBeSet()
    {
        $this->_validEntry->addCategories(array(
            array('term'=>'cat_dog', 'label' => 'Cats & Dogs', 'scheme' => 'http://example.com/schema1'),
            array('term'=>'cat_dog2')
        ));
        $atomFeed = new Renderer\Feed\Atom($this->_validWriter);
        $atomFeed->render();
        $feed = Reader\Reader::importString($atomFeed->saveXml());
        $entry = $feed->current();
        $expected = array(
            array('term'=>'cat_dog', 'label' => 'Cats & Dogs', 'scheme' => 'http://example.com/schema1'),
            array('term'=>'cat_dog2', 'label' => 'cat_dog2', 'scheme' => null)
        );
        $this->assertEquals($expected, (array) $entry->getCategories());
    }
    
    public function testCommentFeedLinksRendered()
    {
        $renderer = new Renderer\Feed\Atom($this->_validWriter);
        $this->_validEntry->setCommentFeedLinks(array(
            array('uri'=>'http://www.example.com/atom/id/1','type'=>'atom'),
            array('uri'=>'http://www.example.com/rss/id/1','type'=>'rss'),
        ));
        $feed = Reader\Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        // Skipped over due to ZFR bug (detects Atom in error when RSS requested)
        //$this->assertEquals('http://www.example.com/rss/id/1', $entry->getCommentFeedLink('rss'));
        $this->assertEquals('http://www.example.com/atom/id/1', $entry->getCommentFeedLink('atom'));
    }
    
}
