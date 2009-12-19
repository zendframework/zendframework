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
 * @package    Zend_Json
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'Zend/Feed/Writer/Renderer/Feed/Rss.php';
require_once 'Zend/Feed/Reader.php';
require_once 'Zend/Version.php';

/**
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @group      Zend_Feed
 * @group      Zend_Feed_Writer
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Feed_Writer_Renderer_Feed_RssTest extends PHPUnit_Framework_TestCase
{

    protected $_validWriter = null;

    public function setUp()
    {
        $this->_validWriter = new Zend_Feed_Writer_Feed;
        $this->_validWriter->setTitle('This is a test feed.');
        $this->_validWriter->setDescription('This is a test description.');
        $this->_validWriter->setLink('http://www.example.com');
        
        $this->_validWriter->setType('rss');
    }

    public function tearDown()
    {
        $this->_validWriter = null;
    }

    public function testSetsWriterInConstructor()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $feed = new Zend_Feed_Writer_Renderer_Feed_Rss($writer);
        $this->assertTrue($feed->getDataContainer() instanceof Zend_Feed_Writer_Feed);
    }

    public function testBuildMethodRunsMinimalWriterContainerProperlyBeforeICheckRssCompliance()
    {
        $feed = new Zend_Feed_Writer_Renderer_Feed_Rss($this->_validWriter);
        try {
            $feed->render();
        } catch (Zend_Feed_Exception $e) {
            $this->fail('Valid Writer object caused an exception when building which should never happen');
        }
    }

    public function testFeedEncodingHasBeenSet()
    {
        $this->_validWriter->setEncoding('iso-8859-1');
        $rssFeed = new Zend_Feed_Writer_Renderer_Feed_Rss($this->_validWriter);
        $rssFeed->render();
        $feed = Zend_Feed_Reader::importString($rssFeed->saveXml());
        $this->assertEquals('iso-8859-1', $feed->getEncoding());
    }

    public function testFeedEncodingDefaultIsUsedIfEncodingNotSetByHand()
    {
        $rssFeed = new Zend_Feed_Writer_Renderer_Feed_Rss($this->_validWriter);
        $rssFeed->render();
        $feed = Zend_Feed_Reader::importString($rssFeed->saveXml());
        $this->assertEquals('UTF-8', $feed->getEncoding());
    }

    public function testFeedTitleHasBeenSet()
    {
        $rssFeed = new Zend_Feed_Writer_Renderer_Feed_Rss($this->_validWriter);
        $rssFeed->render();
        $feed = Zend_Feed_Reader::importString($rssFeed->saveXml());
        $this->assertEquals('This is a test feed.', $feed->getTitle());
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testFeedTitleIfMissingThrowsException()
    {
        $rssFeed = new Zend_Feed_Writer_Renderer_Feed_Rss($this->_validWriter);
        $this->_validWriter->remove('title');
        $rssFeed->render();
    }

    public function testFeedDescriptionHasBeenSet()
    {
        $rssFeed = new Zend_Feed_Writer_Renderer_Feed_Rss($this->_validWriter);
        $rssFeed->render();
        $feed = Zend_Feed_Reader::importString($rssFeed->saveXml());
        $this->assertEquals('This is a test description.', $feed->getDescription());
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testFeedDescriptionThrowsExceptionIfMissing()
    {
        $rssFeed = new Zend_Feed_Writer_Renderer_Feed_Rss($this->_validWriter);
        $this->_validWriter->remove('description');
        $rssFeed->render();
    }

    public function testFeedUpdatedDateHasBeenSet()
    {
        $this->_validWriter->setDateModified(1234567890);
        $rssFeed = new Zend_Feed_Writer_Renderer_Feed_Rss($this->_validWriter);
        $rssFeed->render();
        $feed = Zend_Feed_Reader::importString($rssFeed->saveXml());
        $this->assertEquals(1234567890, $feed->getDateModified()->get(Zend_Date::TIMESTAMP));
    }
    
    public function testFeedUpdatedDateIfMissingThrowsNoException()
    {
        $rssFeed = new Zend_Feed_Writer_Renderer_Feed_Rss($this->_validWriter);
        $this->_validWriter->remove('dateModified');
        $rssFeed->render();
    }

    public function testFeedGeneratorHasBeenSet()
    {
        $this->_validWriter->setGenerator('FooFeedBuilder', '1.00', 'http://www.example.com');
        $rssFeed = new Zend_Feed_Writer_Renderer_Feed_Rss($this->_validWriter);
        $rssFeed->render();
        $feed = Zend_Feed_Reader::importString($rssFeed->saveXml());
        $this->assertEquals('FooFeedBuilder 1.00 (http://www.example.com)', $feed->getGenerator());
    }
    
    public function testFeedGeneratorIfMissingThrowsNoException()
    {
        $rssFeed = new Zend_Feed_Writer_Renderer_Feed_Rss($this->_validWriter);
        $this->_validWriter->remove('generator');
        $rssFeed->render();
    }

    public function testFeedGeneratorDefaultIsUsedIfGeneratorNotSetByHand()
    {
        $rssFeed = new Zend_Feed_Writer_Renderer_Feed_Rss($this->_validWriter);
        $rssFeed->render();
        $feed = Zend_Feed_Reader::importString($rssFeed->saveXml());
        $this->assertEquals('Zend_Feed_Writer ' . Zend_Version::VERSION . ' (http://framework.zend.com)', $feed->getGenerator());
    }

    public function testFeedLanguageHasBeenSet()
    {
        $this->_validWriter->setLanguage('fr');
        $rssFeed = new Zend_Feed_Writer_Renderer_Feed_Rss($this->_validWriter);
        $rssFeed->render();
        $feed = Zend_Feed_Reader::importString($rssFeed->saveXml());
        $this->assertEquals('fr', $feed->getLanguage());
    }
    
    public function testFeedLanguageIfMissingThrowsNoException()
    {
        $rssFeed = new Zend_Feed_Writer_Renderer_Feed_Rss($this->_validWriter);
        $this->_validWriter->remove('language');
        $rssFeed->render();
    }

    public function testFeedLanguageDefaultIsUsedIfGeneratorNotSetByHand()
    {
        $rssFeed = new Zend_Feed_Writer_Renderer_Feed_Rss($this->_validWriter);
        $rssFeed->render();
        $feed = Zend_Feed_Reader::importString($rssFeed->saveXml());
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testFeedIncludesLinkToHtmlVersionOfFeed()
    {
        $rssFeed = new Zend_Feed_Writer_Renderer_Feed_Rss($this->_validWriter);
        $rssFeed->render();
        $feed = Zend_Feed_Reader::importString($rssFeed->saveXml());
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testFeedLinkToHtmlVersionOfFeedIfMissingThrowsException()
    {
        $rssFeed = new Zend_Feed_Writer_Renderer_Feed_Rss($this->_validWriter);
        $this->_validWriter->remove('link');
        $rssFeed->render();
    }

    public function testFeedIncludesLinkToXmlRssWhereTheFeedWillBeAvailable()
    {
        $this->_validWriter->setFeedLink('http://www.example.com/rss', 'rss');
        $rssFeed = new Zend_Feed_Writer_Renderer_Feed_Rss($this->_validWriter);
        $rssFeed->render();
        $feed = Zend_Feed_Reader::importString($rssFeed->saveXml());
        $this->assertEquals('http://www.example.com/rss', $feed->getFeedLink());
    }
    
    public function testFeedLinkToXmlRssWhereTheFeedWillBeAvailableIfMissingThrowsNoException()
    {
        $rssFeed = new Zend_Feed_Writer_Renderer_Feed_Rss($this->_validWriter);
        $this->_validWriter->remove('feedLinks');
        $rssFeed->render();
    }
    
    public function testBaseUrlCanBeSet()
    {
        $this->_validWriter->setBaseUrl('http://www.example.com/base');
        $rssFeed = new Zend_Feed_Writer_Renderer_Feed_Rss($this->_validWriter);
        $rssFeed->render();
        $feed = Zend_Feed_Reader::importString($rssFeed->saveXml());
        $this->assertEquals('http://www.example.com/base', $feed->getBaseUrl());
    }
    
    /**
     * @group ZFW003
     */
    public function testFeedHoldsAnyAuthorAdded()
    {
        $this->_validWriter->addAuthor('Joe', 'joe@example.com', 'http://www.example.com/joe');
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Rss($this->_validWriter);
        $atomFeed->render();
        $feed = Zend_Feed_Reader::importString($atomFeed->saveXml());
        $author = $feed->getAuthor();
        $this->assertEquals('Joe', $feed->getAuthor());
    }
    
    public function testCopyrightCanBeSet()
    {
        $this->_validWriter->setCopyright('Copyright © 2009 Paddy');
        $rssFeed = new Zend_Feed_Writer_Renderer_Feed_Rss($this->_validWriter);
        $rssFeed->render();
        $feed = Zend_Feed_Reader::importString($rssFeed->saveXml());
        $this->assertEquals('Copyright © 2009 Paddy', $feed->getCopyright());
    }
    
    public function testCategoriesCanBeSet()
    {
        $this->_validWriter->addCategories(array(
            array('term'=>'cat_dog', 'label' => 'Cats & Dogs', 'scheme' => 'http://example.com/schema1'),
            array('term'=>'cat_dog2')
        ));
        $rssFeed = new Zend_Feed_Writer_Renderer_Feed_Rss($this->_validWriter);
        $rssFeed->render();
        $feed = Zend_Feed_Reader::importString($rssFeed->saveXml());
        $expected = array(
            array('term'=>'cat_dog', 'label' => 'cat_dog', 'scheme' => 'http://example.com/schema1'),
            array('term'=>'cat_dog2', 'label' => 'cat_dog2', 'scheme' => null)
        );
        $this->assertEquals($expected, (array) $feed->getCategories());
    }
    
    public function testHubsCanBeSet()
    {
        $this->_validWriter->addHubs(
            array('http://www.example.com/hub', 'http://www.example.com/hub2')
        );
        $rssFeed = new Zend_Feed_Writer_Renderer_Feed_Rss($this->_validWriter);
        $rssFeed->render();
        $feed = Zend_Feed_Reader::importString($rssFeed->saveXml());
        $expected = array(
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        );
        $this->assertEquals($expected, (array) $feed->getHubs());
    }

}
