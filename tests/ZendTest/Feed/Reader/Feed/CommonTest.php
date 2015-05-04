<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Feed\Reader\Feed;

use Zend\Feed\Reader;

/**
* @group Zend_Feed
* @group Reader\Reader
*/
class CommonTest extends \PHPUnit_Framework_TestCase
{
    protected $feedSamplePath = null;

    public function setup()
    {
        Reader\Reader::reset();
        $this->feedSamplePath = dirname(__FILE__) . '/_files/Common';
    }

    /**
     * Check DOM Retrieval and Information Methods
     */
    public function testGetsDomDocumentObject()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/atom.xml')
        );
        $this->assertInstanceOf('DOMDocument', $feed->getDomDocument());
    }

    public function testGetsDomXpathObject()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/atom.xml')
        );
        $this->assertInstanceOf('DOMXPath', $feed->getXpath());
    }

    public function testGetsXpathPrefixString()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/atom.xml')
        );
        $this->assertEquals('/atom:feed', $feed->getXpathPrefix());
    }

    public function testGetsDomElementObject()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/atom.xml')
        );
        $this->assertInstanceOf('DOMElement', $feed->getElement());
    }

    public function testSaveXmlOutputsXmlStringForFeed()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/atom.xml')
        );
        $expected = file_get_contents($this->feedSamplePath.'/atom_rewrittenbydom.xml');
        $expected = str_replace("\r\n", "\n", $expected);
        $this->assertEquals($expected, $feed->saveXml());
    }

    public function testGetsNamedExtension()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/atom.xml')
        );
        $this->assertInstanceOf('Zend\Feed\Reader\Extension\Atom\Feed', $feed->getExtension('Atom'));
    }

    public function testReturnsNullIfExtensionDoesNotExist()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/atom.xml')
        );
        $this->assertEquals(null, $feed->getExtension('Foo'));
    }

    /**
     * @group ZF-8213
     */
    public function testReturnsEncodingOfFeed()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/atom.xml')
        );
        $this->assertEquals('UTF-8', $feed->getEncoding());
    }

    /**
     * @group ZF-8213
     */
    public function testReturnsEncodingOfFeedAsUtf8IfUndefined()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/atom_noencodingdefined.xml')
        );
        $this->assertEquals('UTF-8', $feed->getEncoding());
    }
}
