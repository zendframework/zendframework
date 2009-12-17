<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Zend/Feed/Writer/Renderer/Feed/Atom.php';

require_once 'Zend/Feed/Reader.php';
require_once 'Zend/Version.php';

class Zend_Feed_Writer_Renderer_Entry_AtomTest extends PHPUnit_Framework_TestCase
{

    protected $_validWriter = null;
    protected $_validEntry = null;

    public function setUp()
    {
        $this->_validWriter = new Zend_Feed_Writer_Feed;
        
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
        $this->_validEntry->setContent('This is test entry content.');
        $this->_validWriter->addEntry($this->_validEntry);
    }

    public function tearDown()
    {
        $this->_validWriter = null;
        $this->_validEntry = null;
    }

    public function testRenderMethodRunsMinimalWriterContainerProperlyBeforeICheckAtomCompliance()
    {
        $renderer = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        try {
            $renderer->render();
        } catch (Zend_Feed_Exception $e) {
            $this->fail('Valid Writer object caused an exception when building which should never happen');
        }
    }

    public function testEntryEncodingHasBeenSet()
    {
        $this->_validWriter->setEncoding('iso-8859-1');
        $renderer = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $feed = Zend_Feed_Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals('iso-8859-1', $entry->getEncoding());
    }

    public function testEntryEncodingDefaultIsUsedIfEncodingNotSetByHand()
    {
        $renderer = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $feed = Zend_Feed_Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals('UTF-8', $entry->getEncoding());
    }

    public function testEntryTitleHasBeenSet()
    {
        $renderer = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $feed = Zend_Feed_Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals('This is a test entry.', $entry->getTitle());
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testFeedTitleIfMissingThrowsException()
    {
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $this->_validEntry->remove('title');
        $atomFeed->render();
    }

    public function testEntrySummaryDescriptionHasBeenSet()
    {
        $renderer = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $feed = Zend_Feed_Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals('This is a test entry description.', $entry->getDescription());
    }

    public function testEntryContentHasBeenSet()
    {
        $renderer = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $feed = Zend_Feed_Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals('This is test entry content.', $entry->getContent());
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testFeedContentIfMissingThrowsExceptionIfThereIsNoLink()
    {
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $this->_validEntry->remove('content');
        $this->_validEntry->remove('link');
        $atomFeed->render();
    }

    public function testEntryUpdatedDateHasBeenSet()
    {
        $renderer = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $feed = Zend_Feed_Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals(1234567890, $entry->getDateModified()->get(Zend_Date::TIMESTAMP));
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testFeedUpdatedDateIfMissingThrowsException()
    {
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $this->_validEntry->remove('dateModified');
        $atomFeed->render();
    }

    public function testEntryPublishedDateHasBeenSet()
    {
        $renderer = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $feed = Zend_Feed_Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals(1234567000, $entry->getDateCreated()->get(Zend_Date::TIMESTAMP));
    }

    public function testEntryIncludesLinkToHtmlVersionOfFeed()
    {
        $renderer= new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $feed = Zend_Feed_Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/1', $entry->getLink());
    }

    public function testEntryHoldsAnyAuthorAdded()
    {
        $renderer = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $feed = Zend_Feed_Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $author = $entry->getAuthor();
        $this->assertEquals('jane@example.com (Jane)', $entry->getAuthor());
    }
    
    public function testEntryHoldsAnyEnclosureAdded()
    {
        $renderer = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $this->_validEntry->setEnclosure(array(
            'type' => 'audio/mpeg',
            'length' => '1337',
            'uri' => 'http://example.com/audio.mp3'
        ));
        $feed = Zend_Feed_Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $enc = $entry->getEnclosure();
        $this->assertEquals('audio/mpeg', $enc->type);
        $this->assertEquals('1337', $enc->length);
        $this->assertEquals('http://example.com/audio.mp3', $enc->url);
    }

    public function testEntryIdHasBeenSet()
    {
        $this->_validEntry->setId('urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6');
        $renderer = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $feed = Zend_Feed_Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals('urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6', $entry->getId());
    }

    public function testFeedIdDefaultIsUsedIfNotSetByHand()
    {
        $renderer = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $feed = Zend_Feed_Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals($entry->getLink(), $entry->getId());
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testFeedIdIfMissingThrowsException()
    {
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $this->_validEntry->remove('id');
        $this->_validEntry->remove('link');
        $atomFeed->render();
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testFeedIdThrowsExceptionIfNotUri()
    {
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $this->_validEntry->remove('id');
        $this->_validEntry->remove('link');
        $this->_validEntry->setId('not-a-uri');
        $atomFeed->render();
    }
    
    public function testCommentLinkRendered()
    {
        $renderer = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $this->_validEntry->setCommentLink('http://www.example.com/id/1');
        $feed = Zend_Feed_Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/id/1', $entry->getCommentLink());
    }
    
    public function testCommentCountRendered()
    {
        $renderer = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $this->_validEntry->setCommentCount(22);
        $feed = Zend_Feed_Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals(22, $entry->getCommentCount());
    }
    
    public function testCommentFeedLinksRendered()
    {
        $renderer = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $this->_validEntry->setCommentFeedLinks(array(
            array('uri'=>'http://www.example.com/atom/id/1','type'=>'atom'),
            array('uri'=>'http://www.example.com/rss/id/1','type'=>'rss'),
        ));
        $feed = Zend_Feed_Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        // Skipped over due to ZFR bug (detects Atom in error when RSS requested)
        //$this->assertEquals('http://www.example.com/rss/id/1', $entry->getCommentFeedLink('rss'));
        $this->assertEquals('http://www.example.com/atom/id/1', $entry->getCommentFeedLink('atom'));
    }
    
}
