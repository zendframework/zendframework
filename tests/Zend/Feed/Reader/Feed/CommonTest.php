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

namespace ZendTest\Feed\Reader\Feed;
use Zend\Feed\Reader;

/**
* @category Zend
* @package Zend_Feed
* @subpackage UnitTests
* @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
* @license http://framework.zend.com/license/new-bsd New BSD License
* @group Zend_Feed
* @group Reader\Reader
*/
class CommonTest extends \PHPUnit_Framework_TestCase
{

    protected $_feedSamplePath = null;

    public function setup()
    {
        Reader\Reader::reset();
        $this->_feedSamplePath = dirname(__FILE__) . '/_files/Common';
    }

    /**
     * Check DOM Retrieval and Information Methods
     */
    public function testGetsDomDocumentObject()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/atom.xml')
        );
        $this->assertTrue($feed->getDomDocument() instanceof \DOMDocument);
    }

    public function testGetsDomXpathObject()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/atom.xml')
        );
        $this->assertTrue($feed->getXpath() instanceof \DOMXPath);
    }

    public function testGetsXpathPrefixString()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/atom.xml')
        );
        $this->assertTrue($feed->getXpathPrefix() == '/atom:feed');
    }

    public function testGetsDomElementObject()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/atom.xml')
        );
        $this->assertTrue($feed->getElement() instanceof \DOMElement);
    }

    public function testSaveXmlOutputsXmlStringForFeed()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/atom.xml')
        );
        $this->assertEquals($feed->saveXml(), file_get_contents($this->_feedSamplePath.'/atom_rewrittenbydom.xml'));
    }

    public function testGetsNamedExtension()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/atom.xml')
        );
        $this->assertTrue($feed->getExtension('Atom') instanceof Reader\Extension\Atom\Feed);
    }

    public function testReturnsNullIfExtensionDoesNotExist()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/atom.xml')
        );
        $this->assertEquals(null, $feed->getExtension('Foo'));
    }
    
    /**
     * @group ZF-8213
     */
    public function testReturnsEncodingOfFeed()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/atom.xml')
        );
        $this->assertEquals('UTF-8', $feed->getEncoding());
    }
    
    /**
     * @group ZF-8213
     */
    public function testReturnsEncodingOfFeedAsUtf8IfUndefined()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/atom_noencodingdefined.xml')
        );
        $this->assertEquals('UTF-8', $feed->getEncoding());
    }


}
