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

use Zend\Feed\Reader\Extension;
use Zend\Feed\Reader;

/**
* @category Zend
* @package Zend_Feed
* @subpackage UnitTests
* @group Zend_Feed
* @group Zend_Feed_Reader
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
        $entry = $feed->current();
        $this->assertTrue($entry->getDomDocument() instanceof \DOMDocument);
    }

    public function testGetsDomXpathObject()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/atom.xml')
        );
        $entry = $feed->current();
        $this->assertTrue($entry->getXpath() instanceof \DOMXPath);
    }

    public function testGetsXpathPrefixString()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/atom.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('//atom:entry[1]', $entry->getXpathPrefix());
    }

    public function testGetsDomElementObject()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/atom.xml')
        );
        $entry = $feed->current();
        $this->assertTrue($entry->getElement() instanceof \DOMElement);
    }

    public function testSaveXmlOutputsXmlStringForEntry()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/atom.xml')
        );
        $entry = $feed->current();
        $this->assertEquals($entry->saveXml(), file_get_contents($this->feedSamplePath.'/atom_rewrittenbydom.xml'));
    }

    public function testGetsNamedExtension()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/atom.xml')
        );
        $entry = $feed->current();
        $this->assertTrue($entry->getExtension('Atom') instanceof Extension\Atom\Entry);
    }

    public function testReturnsNullIfExtensionDoesNotExist()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/atom.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getExtension('Foo'));
    }

    /**
     * @group ZF-8213
     */
    public function testReturnsEncodingOfFeed()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/atom.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('UTF-8', $entry->getEncoding());
    }

    /**
     * @group ZF-8213
     */
    public function testReturnsEncodingOfFeedAsUtf8IfUndefined()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/atom_noencodingdefined.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('UTF-8', $entry->getEncoding());
    }

    /**
    * When not passing the optional argument type
    */
    public function testFeedEntryCanDetectFeedType()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/atom.xml')
        );
        $entry = $feed->current();
        $stub = $this->getMockForAbstractClass(
            'Zend\Feed\Reader\Entry\AbstractEntry',
            array($entry->getElement(), $entry->getId())
        );
        $this->assertEquals($entry->getType(), $stub->getType());
    }

    /**
    * When passing a newly created DOMElement without any DOMDocument assigned
    */
    public function testFeedEntryCanSetAnyType()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath.'/atom.xml')
        );
        $entry = $feed->current();
        $domElement = new \DOMElement($entry->getElement()->tagName);
        $stub = $this->getMockForAbstractClass(
            'Zend\Feed\Reader\Entry\AbstractEntry',
            array($domElement, $entry->getId())
        );
        $this->assertEquals($stub->getType(), Reader\Reader::TYPE_ANY);
    }
}
