<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Feed
 */

namespace ZendTest\Feed\Writer\Renderer\Feed;

use DateTime;
use Zend\Feed\Writer;
use Zend\Feed\Writer\Renderer;
use Zend\Feed\Reader;

/**
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @group      Zend_Feed
 * @group      Zend_Feed_Writer
 */
class RssTest extends \PHPUnit_Framework_TestCase
{

    protected $validWriter = null;

    public function setUp()
    {
        $this->validWriter = new Writer\Feed;
        $this->validWriter->setTitle('This is a test feed.');
        $this->validWriter->setDescription('This is a test description.');
        $this->validWriter->setLink('http://www.example.com');

        $this->validWriter->setType('rss');
    }

    public function tearDown()
    {
        $this->validWriter = null;
    }

    public function testSetsWriterInConstructor()
    {
        $writer = new Writer\Feed;
        $feed   = new Renderer\Feed\Rss($writer);
        $this->assertTrue($feed->getDataContainer() instanceof Writer\Feed);
    }

    public function testBuildMethodRunsMinimalWriterContainerProperlyBeforeICheckRssCompliance()
    {
        $feed = new Renderer\Feed\Rss($this->validWriter);
        $feed->render();
    }

    public function testFeedEncodingHasBeenSet()
    {
        $this->validWriter->setEncoding('iso-8859-1');
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('iso-8859-1', $feed->getEncoding());
    }

    public function testFeedEncodingDefaultIsUsedIfEncodingNotSetByHand()
    {
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('UTF-8', $feed->getEncoding());
    }

    public function testFeedTitleHasBeenSet()
    {
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('This is a test feed.', $feed->getTitle());
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testFeedTitleIfMissingThrowsException()
    {
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $this->validWriter->remove('title');
        $rssFeed->render();
    }

    /**
     * @group ZFWCHARDATA01
     */
    public function testFeedTitleCharDataEncoding()
    {
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $this->validWriter->setTitle('<>&\'"áéíóú');
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('<>&\'"áéíóú', $feed->getTitle());
    }

    public function testFeedDescriptionHasBeenSet()
    {
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('This is a test description.', $feed->getDescription());
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testFeedDescriptionThrowsExceptionIfMissing()
    {
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $this->validWriter->remove('description');
        $rssFeed->render();
    }

    /**
     * @group ZFWCHARDATA01
     */
    public function testFeedDescriptionCharDataEncoding()
    {
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $this->validWriter->setDescription('<>&\'"áéíóú');
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('<>&\'"áéíóú', $feed->getDescription());
    }

    public function testFeedUpdatedDateHasBeenSet()
    {
        $this->validWriter->setDateModified(1234567890);
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals(1234567890, $feed->getDateModified()->getTimestamp());
    }

    public function testFeedUpdatedDateIfMissingThrowsNoException()
    {
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $this->validWriter->remove('dateModified');
        $rssFeed->render();
    }

    public function testFeedLastBuildDateHasBeenSet()
    {
        $this->validWriter->setLastBuildDate(1234567890);
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals(1234567890, $feed->getLastBuildDate()->getTimestamp());
    }

    public function testFeedGeneratorHasBeenSet()
    {
        $this->validWriter->setGenerator('FooFeedBuilder', '1.00', 'http://www.example.com');
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('FooFeedBuilder 1.00 (http://www.example.com)', $feed->getGenerator());
    }

    public function testFeedGeneratorIfMissingThrowsNoException()
    {
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $this->validWriter->remove('generator');
        $rssFeed->render();
    }

    public function testFeedGeneratorDefaultIsUsedIfGeneratorNotSetByHand()
    {
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals(
            'Zend_Feed_Writer ' . \Zend\Version\Version::VERSION . ' (http://framework.zend.com)', $feed->getGenerator());
    }

    public function testFeedLanguageHasBeenSet()
    {
        $this->validWriter->setLanguage('fr');
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('fr', $feed->getLanguage());
    }

    public function testFeedLanguageIfMissingThrowsNoException()
    {
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $this->validWriter->remove('language');
        $rssFeed->render();
    }

    public function testFeedLanguageDefaultIsUsedIfGeneratorNotSetByHand()
    {
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testFeedIncludesLinkToHtmlVersionOfFeed()
    {
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testFeedLinkToHtmlVersionOfFeedIfMissingThrowsException()
    {
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $this->validWriter->remove('link');
        $rssFeed->render();
    }

    /**
     * @group Issue2605
     */
    public function testFeedIncludesLinkToXmlRssWhereRssAndAtomLinksAreProvided()
    {
        $this->validWriter->setFeedLink('http://www.example.com/rss', 'rss');
        $this->validWriter->setFeedLink('http://www.example.com/atom', 'atom');
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('http://www.example.com/rss', $feed->getFeedLink());
        $xpath = new \DOMXPath($feed->getDomDocument());
        $this->assertEquals(1, $xpath->evaluate('/rss/channel/atom:link[@rel="self"]')->length);
    }

    public function testFeedIncludesLinkToXmlRssWhereTheFeedWillBeAvailable()
    {
        $this->validWriter->setFeedLink('http://www.example.com/rss', 'rss');
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('http://www.example.com/rss', $feed->getFeedLink());
    }

    public function testFeedLinkToXmlRssWhereTheFeedWillBeAvailableIfMissingThrowsNoException()
    {
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $this->validWriter->remove('feedLinks');
        $rssFeed->render();
    }

    public function testBaseUrlCanBeSet()
    {
        $this->validWriter->setBaseUrl('http://www.example.com/base');
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('http://www.example.com/base', $feed->getBaseUrl());
    }

    /**
     * @group ZFW003
     */
    public function testFeedHoldsAnyAuthorAdded()
    {
        $this->validWriter->addAuthor(array('name' => 'Joe',
                                             'email'=> 'joe@example.com',
                                             'uri'  => 'http://www.example.com/joe'));
        $atomFeed = new Renderer\Feed\Rss($this->validWriter);
        $atomFeed->render();
        $feed   = Reader\Reader::importString($atomFeed->saveXml());
        $author = $feed->getAuthor();
        $this->assertEquals(array('name'=> 'Joe'), $feed->getAuthor());
    }

    /**
     * @group ZFWCHARDATA01
     */
    public function testFeedAuthorCharDataEncoding()
    {
        $this->validWriter->addAuthor(array('name' => '<>&\'"áéíóú',
                                            'email'=> 'joe@example.com',
                                            'uri'  => 'http://www.example.com/joe'));
        $atomFeed = new Renderer\Feed\Rss($this->validWriter);
        $atomFeed->render();
        $feed   = Reader\Reader::importString($atomFeed->saveXml());
        $author = $feed->getAuthor();
        $this->assertEquals(array('name'=> '<>&\'"áéíóú'), $feed->getAuthor());
    }

    public function testCopyrightCanBeSet()
    {
        $this->validWriter->setCopyright('Copyright © 2009 Paddy');
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('Copyright © 2009 Paddy', $feed->getCopyright());
    }

    /**
     * @group ZFWCHARDATA01
     */
    public function testCopyrightCharDataEncoding()
    {
        $this->validWriter->setCopyright('<>&\'"áéíóú');
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('<>&\'"áéíóú', $feed->getCopyright());
    }

    public function testCategoriesCanBeSet()
    {
        $this->validWriter->addCategories(array(
                                                array('term'   => 'cat_dog',
                                                      'label'  => 'Cats & Dogs',
                                                      'scheme' => 'http://example.com/schema1'),
                                                array('term'=> 'cat_dog2')
                                           ));
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed     = Reader\Reader::importString($rssFeed->saveXml());
        $expected = array(
            array('term'   => 'cat_dog',
                  'label'  => 'cat_dog',
                  'scheme' => 'http://example.com/schema1'),
            array('term'   => 'cat_dog2',
                  'label'  => 'cat_dog2',
                  'scheme' => null)
        );
        $this->assertEquals($expected, (array)$feed->getCategories());
    }

    /**
     * @group ZFWCHARDATA01
     */
    public function testCategoriesCharDataEncoding()
    {
        $this->validWriter->addCategories(array(
                                                array('term'   => '<>&\'"áéíóú',
                                                      'label'  => 'Cats & Dogs',
                                                      'scheme' => 'http://example.com/schema1'),
                                                array('term'=> 'cat_dog2')
                                           ));
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed     = Reader\Reader::importString($rssFeed->saveXml());
        $expected = array(
            array('term'   => '<>&\'"áéíóú',
                  'label'  => '<>&\'"áéíóú',
                  'scheme' => 'http://example.com/schema1'),
            array('term'   => 'cat_dog2',
                  'label'  => 'cat_dog2',
                  'scheme' => null)
        );
        $this->assertEquals($expected, (array)$feed->getCategories());
    }

    public function testHubsCanBeSet()
    {
        $this->validWriter->addHubs(
            array('http://www.example.com/hub', 'http://www.example.com/hub2')
        );
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed     = Reader\Reader::importString($rssFeed->saveXml());
        $expected = array(
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        );
        $this->assertEquals($expected, (array)$feed->getHubs());
    }

    public function testImageCanBeSet()
    {
        $this->validWriter->setImage(array(
                                           'uri'         => 'http://www.example.com/logo.gif',
                                           'link'        => 'http://www.example.com',
                                           'title'       => 'Image ALT',
                                           'height'      => '400',
                                           'width'       => '144',
                                           'description' => 'Image TITLE'
                                      ));
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed     = Reader\Reader::importString($rssFeed->saveXml());
        $expected = array(
            'uri'         => 'http://www.example.com/logo.gif',
            'link'        => 'http://www.example.com',
            'title'       => 'Image ALT',
            'height'      => '400',
            'width'       => '144',
            'description' => 'Image TITLE'
        );
        $this->assertEquals($expected, $feed->getImage());
    }

    public function testImageCanBeSetWithOnlyRequiredElements()
    {
        $this->validWriter->setImage(array(
                                           'uri'   => 'http://www.example.com/logo.gif',
                                           'link'  => 'http://www.example.com',
                                           'title' => 'Image ALT'
                                      ));
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed     = Reader\Reader::importString($rssFeed->saveXml());
        $expected = array(
            'uri'   => 'http://www.example.com/logo.gif',
            'link'  => 'http://www.example.com',
            'title' => 'Image ALT'
        );
        $this->assertEquals($expected, $feed->getImage());
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testImageThrowsExceptionOnMissingLink()
    {
        $this->validWriter->setImage(array(
                                           'uri'   => 'http://www.example.com/logo.gif',
                                           'title' => 'Image ALT'
                                      ));
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testImageThrowsExceptionOnMissingTitle()
    {
        $this->validWriter->setImage(array(
                                           'uri'  => 'http://www.example.com/logo.gif',
                                           'link' => 'http://www.example.com'
                                      ));
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testImageThrowsExceptionOnMissingUri()
    {
        $this->validWriter->setImage(array(
                                           'link'  => 'http://www.example.com',
                                           'title' => 'Image ALT'
                                      ));
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testImageThrowsExceptionIfOptionalDescriptionInvalid()
    {
        $this->validWriter->setImage(array(
                                           'uri'         => 'http://www.example.com/logo.gif',
                                           'link'        => 'http://www.example.com',
                                           'title'       => 'Image ALT',
                                           'description' => 2
                                      ));
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testImageThrowsExceptionIfOptionalDescriptionEmpty()
    {
        $this->validWriter->setImage(array(
                                           'uri'         => 'http://www.example.com/logo.gif',
                                           'link'        => 'http://www.example.com',
                                           'title'       => 'Image ALT',
                                           'description' => ''
                                      ));
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testImageThrowsExceptionIfOptionalHeightNotAnInteger()
    {
        $this->validWriter->setImage(array(
                                           'uri'    => 'http://www.example.com/logo.gif',
                                           'link'   => 'http://www.example.com',
                                           'title'  => 'Image ALT',
                                           'height' => 'a',
                                           'width'  => 144
                                      ));
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testImageThrowsExceptionIfOptionalHeightEmpty()
    {
        $this->validWriter->setImage(array(
                                           'uri'    => 'http://www.example.com/logo.gif',
                                           'link'   => 'http://www.example.com',
                                           'title'  => 'Image ALT',
                                           'height' => '',
                                           'width'  => 144
                                      ));
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testImageThrowsExceptionIfOptionalHeightGreaterThan400()
    {
        $this->validWriter->setImage(array(
                                           'uri'    => 'http://www.example.com/logo.gif',
                                           'link'   => 'http://www.example.com',
                                           'title'  => 'Image ALT',
                                           'height' => '401',
                                           'width'  => 144
                                      ));
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testImageThrowsExceptionIfOptionalWidthNotAnInteger()
    {
        $this->validWriter->setImage(array(
                                           'uri'    => 'http://www.example.com/logo.gif',
                                           'link'   => 'http://www.example.com',
                                           'title'  => 'Image ALT',
                                           'height' => '400',
                                           'width'  => 'a'
                                      ));
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testImageThrowsExceptionIfOptionalWidthEmpty()
    {
        $this->validWriter->setImage(array(
                                           'uri'    => 'http://www.example.com/logo.gif',
                                           'link'   => 'http://www.example.com',
                                           'title'  => 'Image ALT',
                                           'height' => '400',
                                           'width'  => ''
                                      ));
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testImageThrowsExceptionIfOptionalWidthGreaterThan144()
    {
        $this->validWriter->setImage(array(
                                           'uri'    => 'http://www.example.com/logo.gif',
                                           'link'   => 'http://www.example.com',
                                           'title'  => 'Image ALT',
                                           'height' => '400',
                                           'width'  => '145'
                                      ));
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
    }

    public function testFeedSetDateCreated()
    {
        $this->validWriter->setDateCreated(1234567890);
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $myDate = new DateTime('@' . 1234567890);
        $this->assertEquals($myDate, $feed->getDateCreated());
    }


}
