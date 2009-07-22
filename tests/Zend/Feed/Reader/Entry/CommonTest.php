<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Zend/Feed/Reader.php';

class Zend_Feed_Reader_Entry_CommonTest extends PHPUnit_Framework_TestCase
{

    protected $_feedSamplePath = null;

    public function setup()
    {
        if (Zend_Registry::isRegistered('Zend_Locale')) {
            $registry = Zend_Registry::getInstance();
            unset($registry['Zend_Locale']);
        }
        $this->_feedSamplePath = dirname(__FILE__) . '/_files/Common';
    }

    /**
     * Check DOM Retrieval and Information Methods
     */
    public function testGetsDomDocumentObject()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/atom.xml')
        );
        $entry = $feed->current();
        $this->assertTrue($entry->getDomDocument() instanceof DOMDocument);
    }

    public function testGetsDomXpathObject()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/atom.xml')
        );
        $entry = $feed->current();
        $this->assertTrue($entry->getXpath() instanceof DOMXPath);
    }

    public function testGetsXpathPrefixString()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/atom.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('//atom:entry[1]', $entry->getXpathPrefix());
    }

    public function testGetsDomElementObject()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/atom.xml')
        );
        $entry = $feed->current();
        $this->assertTrue($entry->getElement() instanceof DOMElement);
    }

    public function testSaveXmlOutputsXmlStringForEntry()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/atom.xml')
        );
        $entry = $feed->current();
        $this->assertEquals($entry->saveXml(), file_get_contents($this->_feedSamplePath.'/atom_rewrittenbydom.xml'));
    }

    public function testGetsNamedExtension()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/atom.xml')
        );
        $entry = $feed->current();
        $this->assertTrue($entry->getExtension('Atom') instanceof Zend_Feed_Reader_Extension_Atom_Entry);
    }

    public function testReturnsNullIfExtensionDoesNotExist()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/atom.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(null, $entry->getExtension('Foo'));
    }


}
