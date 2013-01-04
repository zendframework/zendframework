<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Feed
 */

namespace ZendTest\Feed\Reader;

use Zend\Http\Client as HttpClient;
use Zend\Http\Client\Adapter\Test as TestAdapter;
use Zend\Http\Response as HttpResponse;
use Zend\Feed\Reader;

/**
* @category Zend
* @package Zend_Feed
* @subpackage UnitTests
* @group Zend_Feed
* @group Zend_Feed_Reader
*/
class ReaderTest extends \PHPUnit_Framework_TestCase
{

    protected $feedSamplePath = null;

    public function setup()
    {
        $this->feedSamplePath = dirname(__FILE__) . '/_files';
    }

    public function tearDown()
    {
        Reader\Reader::reset();
    }

    public function testStringImportTrimsContentToAllowSlightlyInvalidXml()
    {
        $feed = Reader\Reader::importString(
            '   ' . file_get_contents($this->feedSamplePath.'/Reader/rss20.xml')
        );
    }

    public function testDetectsFeedIsRss20()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/Reader/rss20.xml'));
        $type = Reader\Reader::detectType($feed);
        $this->assertEquals(Reader\Reader::TYPE_RSS_20, $type);
    }

    public function testDetectsFeedIsRss094()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/Reader/rss094.xml'));
        $type = Reader\Reader::detectType($feed);
        $this->assertEquals(Reader\Reader::TYPE_RSS_094, $type);
    }

    public function testDetectsFeedIsRss093()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/Reader/rss093.xml'));
        $type = Reader\Reader::detectType($feed);
        $this->assertEquals(Reader\Reader::TYPE_RSS_093, $type);
    }

    public function testDetectsFeedIsRss092()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/Reader/rss092.xml'));
        $type = Reader\Reader::detectType($feed);
        $this->assertEquals(Reader\Reader::TYPE_RSS_092, $type);
    }

    public function testDetectsFeedIsRss091()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/Reader/rss091.xml'));
        $type = Reader\Reader::detectType($feed);
        $this->assertEquals(Reader\Reader::TYPE_RSS_091, $type);
    }

    public function testDetectsFeedIsRss10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/Reader/rss10.xml'));
        $type = Reader\Reader::detectType($feed);
        $this->assertEquals(Reader\Reader::TYPE_RSS_10, $type);
    }

    public function testDetectsFeedIsRss090()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/Reader/rss090.xml'));
        $type = Reader\Reader::detectType($feed);
        $this->assertEquals(Reader\Reader::TYPE_RSS_090, $type);
    }

    public function testDetectsFeedIsAtom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/Reader/atom10.xml'));
        $type = Reader\Reader::detectType($feed);
        $this->assertEquals(Reader\Reader::TYPE_ATOM_10, $type);
    }

    public function testDetectsFeedIsAtom03()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/Reader/atom03.xml'));
        $type = Reader\Reader::detectType($feed);
        $this->assertEquals(Reader\Reader::TYPE_ATOM_03, $type);
    }

    /**
     * @group ZF-9723
     */
    public function testDetectsTypeFromStringOrToRemindPaddyAboutForgettingATestWhichLetsAStupidTypoSurviveUnnoticedForMonths()
    {
        $feed = '<?xml version="1.0" encoding="utf-8" ?><rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns="http://purl.org/rss/1.0/"><channel></channel></rdf:RDF>';
        $type = Reader\Reader::detectType($feed);
        $this->assertEquals(Reader\Reader::TYPE_RSS_10, $type);
    }

    public function testGetEncoding()
    {
        $feed = Reader\Reader::importString(
            file_get_contents(dirname(__FILE__) . '/Entry/_files/Atom/title/plain/atom10.xml')
        );

        $this->assertEquals('utf-8', $feed->getEncoding());
        $this->assertEquals('utf-8', $feed->current()->getEncoding());
    }

    public function testImportsFile()
    {
        $feed = Reader\Reader::importFile(
            dirname(__FILE__) . '/Entry/_files/Atom/title/plain/atom10.xml'
        );
        $this->assertInstanceOf('Zend\Feed\Reader\Feed\FeedInterface', $feed);
    }

    public function testImportsUri()
    {
        if (!constant('TESTS_ZEND_FEED_READER_ONLINE_ENABLED')) {
            $this->markTestSkipped('testImportsUri() requires a network connection');
        }

        Reader\Reader::import('http://www.planet-php.net/rdf/');
    }

    /**
     * @group ZF-8328
     * @expectedException Zend\Feed\Reader\Exception\RuntimeException
     */
    public function testImportsUriAndThrowsExceptionIfNotAFeed()
    {
        if (!constant('TESTS_ZEND_FEED_READER_ONLINE_ENABLED')) {
            $this->markTestSkipped('testImportsUri() requires a network connection');
        }

        Reader\Reader::import('http://twitter.com/alganet');
    }

    public function testGetsFeedLinksAsValueObject()
    {
        if (!constant('TESTS_ZEND_FEED_READER_ONLINE_ENABLED')) {
            $this->markTestSkipped('testGetsFeedLinksAsValueObject() requires a network connection');
        }

        $links = Reader\Reader::findFeedLinks('http://www.planet-php.net');

        $this->assertEquals('http://www.planet-php.org/rss/', $links->rss);
    }

    public function testCompilesLinksAsArrayObject()
    {
        if (!constant('TESTS_ZEND_FEED_READER_ONLINE_ENABLED')) {
            $this->markTestSkipped('testGetsFeedLinksAsValueObject() requires a network connection');
        }
        $links = Reader\Reader::findFeedLinks('http://www.planet-php.net');
        $this->assertTrue($links instanceof Reader\FeedSet);
        $this->assertEquals(array(
            'rel' => 'alternate', 'type' => 'application/rss+xml', 'href' => 'http://www.planet-php.org/rss/'
        ), (array) $links->getIterator()->current());
    }

    public function testFeedSetLoadsFeedObjectWhenFeedArrayKeyAccessed()
    {
        if (!constant('TESTS_ZEND_FEED_READER_ONLINE_ENABLED')) {
            $this->markTestSkipped('testGetsFeedLinksAsValueObject() requires a network connection');
        }
        $links = Reader\Reader::findFeedLinks('http://www.planet-php.net');
        $link = $links->getIterator()->current();
        $this->assertTrue($link['feed'] instanceof Reader\Feed\Rss);
    }

    public function testZeroCountFeedSetReturnedFromEmptyList()
    {
        if (!constant('TESTS_ZEND_FEED_READER_ONLINE_ENABLED')) {
            $this->markTestSkipped('testGetsFeedLinksAsValueObject() requires a network connection');
        }
        $links = Reader\Reader::findFeedLinks('http://www.example.com');
        $this->assertEquals(0, count($links));
    }

    /**
     * @group ZF-8327
     */
    public function testGetsFeedLinksAndTrimsNewlines()
    {
        if (!constant('TESTS_ZEND_FEED_READER_ONLINE_ENABLED')) {
            $this->markTestSkipped('testGetsFeedLinksAsValueObject() requires a network connection');
        }

        $links = Reader\Reader::findFeedLinks('http://www.infopod.com.br');
        $this->assertEquals('http://feeds.feedburner.com/jonnyken/infoblog', $links->rss);
    }

    /**
     * @group ZF-8330
     */
    public function testGetsFeedLinksAndNormalisesRelativeUrls()
    {
        if (!constant('TESTS_ZEND_FEED_READER_ONLINE_ENABLED')) {
            $this->markTestSkipped('testGetsFeedLinksAsValueObject() requires a network connection');
        }

        $links = Reader\Reader::findFeedLinks('http://meiobit.com');
        $this->assertEquals('http://mbit.disqus.com/latest.rss', $links->rss);
    }

    /**
     * @group ZF-8330
     */
    public function testGetsFeedLinksAndNormalisesRelativeUrlsOnUriWithPath()
    {
        $currClient = Reader\Reader::getHttpClient();

        $testAdapter = new TestAdapter();
        $response = new HttpResponse();
        $response->setStatusCode(200);
        $response->setContent('<!DOCTYPE html><html><head><link rel="alternate" type="application/rss+xml" href="../test.rss"><link rel="alternate" type="application/atom+xml" href="/test.atom"></head><body></body></html>');
        $testAdapter->setResponse($response);
        Reader\Reader::setHttpClient(new HttpClient(null, array('adapter' => $testAdapter)));

        $links = Reader\Reader::findFeedLinks('http://foo/bar');

        Reader\Reader::setHttpClient($currClient);

        $this->assertEquals('http://foo/test.rss', $links->rss);
        $this->assertEquals('http://foo/test.atom', $links->atom);
    }

    public function testRegistersUserExtension()
    {
        require_once __DIR__ . '/_files/My/Extension/JungleBooks/Entry.php';
        require_once __DIR__ . '/_files/My/Extension/JungleBooks/Feed.php';
        $manager = Reader\Reader::getExtensionManager();
        $manager->setInvokableClass('JungleBooks\Entry', 'My\Extension\JungleBooks\Entry');
        $manager->setInvokableClass('JungleBooks\Feed', 'My\Extension\JungleBooks\Feed');
        Reader\Reader::registerExtension('JungleBooks');

        $this->assertTrue(Reader\Reader::isRegistered('JungleBooks'));
    }

    public function testXxePreventionOnFeedParsing()
    {
        $this->setExpectedException('Zend\Feed\Reader\Exception\InvalidArgumentException');
        $string = file_get_contents($this->feedSamplePath.'/Reader/xxe-atom10.xml');
        $string = str_replace('XXE_URI', $this->feedSamplePath.'/Reader/xxe-info.txt', $string);
        $feed = Reader\Reader::importString($string);
        //$this->assertEquals('info:', $feed->getTitle());
    }

    protected function _getTempDirectory()
    {
        $tmpdir = array();
        foreach (array($_ENV, $_SERVER) as $tab) {
            foreach (array('TMPDIR', 'TEMP', 'TMP', 'windir', 'SystemRoot') as $key) {
                if (isset($tab[$key])) {
                    if (($key == 'windir') or ($key == 'SystemRoot')) {
                        $dir = realpath($tab[$key] . '\\temp');
                    } else {
                        $dir = realpath($tab[$key]);
                    }
                    if ($this->_isGoodTmpDir($dir)) {
                        return $dir;
                    }
                }
            }
        }
        if (function_exists('sys_get_temp_dir')) {
            $dir = sys_get_temp_dir();
            if ($this->_isGoodTmpDir($dir)) {
                return $dir;
            }
        }
        $tempFile = tempnam(md5(uniqid(rand(), TRUE)), '');
        if ($tempFile) {
            $dir = realpath(dirname($tempFile));
            unlink($tempFile);
            if ($this->_isGoodTmpDir($dir)) {
                return $dir;
            }
        }
        if ($this->_isGoodTmpDir('/tmp')) {
            return '/tmp';
        }
        if ($this->_isGoodTmpDir('\\temp')) {
            return '\\temp';
        }
    }

    protected function _isGoodTmpDir($dir)
    {
        return (is_readable($dir) && is_writable($dir));
    }

}
