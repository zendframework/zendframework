<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Feed
 */

namespace Zend\Feed\Reader;

use DOMDocument;
use DOMElement;
use DOMXPath;

/**
 * @category   Zend
 * @package    Zend_Feed_Reader
 */
abstract class AbstractEntry
{
    /**
     * Feed entry data
     *
     * @var array
     */
    protected $_data = array();

    /**
     * DOM document object
     *
     * @var DOMDocument
     */
    protected $_domDocument = null;

    /**
     * Entry instance
     *
     * @var Zend\Feed\Entry
     */
    protected $_entry = null;

    /**
     * Pointer to the current entry
     *
     * @var int
     */
    protected $_entryKey = 0;

    /**
     * XPath object
     *
     * @var DOMXPath
     */
    protected $_xpath = null;

    /**
     * Registered extensions
     *
     * @var array
     */
    protected $_extensions = array();

    /**
     * Constructor
     *
     * @param  DOMElement $entry
     * @param  int $entryKey
     * @param  string $type
     * @return void
     */
    public function __construct(DOMElement $entry, $entryKey, $type = null)
    {
        $this->_entry       = $entry;
        $this->_entryKey    = $entryKey;
        $this->_domDocument = $entry->ownerDocument;
        if ($type !== null) {
            $this->_data['type'] = $type;
        } else {
            $this->_data['type'] = Reader::detectType($feed);
        }
        $this->_loadExtensions();
    }

    /**
     * Get the DOM
     *
     * @return DOMDocument
     */
    public function getDomDocument()
    {
        return $this->_domDocument;
    }

    /**
     * Get the entry element
     *
     * @return DOMElement
     */
    public function getElement()
    {
        return $this->_entry;
    }

    /**
     * Get the Entry's encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        $assumed = $this->getDomDocument()->encoding;
        if (empty($assumed)) {
            $assumed = 'UTF-8';
        }
        return $assumed;
    }

    /**
     * Get entry as xml
     *
     * @return string
     */
    public function saveXml()
    {
        $dom = new DOMDocument('1.0', $this->getEncoding());
        $entry = $dom->importNode($this->getElement(), true);
        $dom->appendChild($entry);
        return $dom->saveXml();
    }

    /**
     * Get the entry type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_data['type'];
    }

    /**
     * Get the XPath query object
     *
     * @return DOMXPath
     */
    public function getXpath()
    {
        if (!$this->_xpath) {
            $this->setXpath(new DOMXPath($this->getDomDocument()));
        }
        return $this->_xpath;
    }

    /**
     * Set the XPath query
     *
     * @param  DOMXPath $xpath
     * @return Zend\Feed\Reader\AbstractEntry
     */
    public function setXpath(DOMXPath $xpath)
    {
        $this->_xpath = $xpath;
        return $this;
    }

    /**
     * Get registered extensions
     *
     * @return array
     */
    public function getExtensions()
    {
        return $this->_extensions;
    }

    /**
     * Return an Extension object with the matching name (postfixed with _Entry)
     *
     * @param string $name
     * @return \Zend\Feed\Reader\Extension\AbstractEntry
     */
    public function getExtension($name)
    {
        if (array_key_exists($name . '\Entry', $this->_extensions)) {
            return $this->_extensions[$name . '\Entry'];
        }
        return null;
    }

    /**
     * Method overloading: call given method on first extension implementing it
     *
     * @param  string $method
     * @param  array $args
     * @return mixed
     * @throws Exception\BadMethodCallException if no extensions implements the method
     */
    public function __call($method, $args)
    {
        foreach ($this->_extensions as $extension) {
            if (method_exists($extension, $method)) {
                return call_user_func_array(array($extension, $method), $args);
            }
        }
        throw new Exception\BadMethodCallException('Method: ' . $method
            . 'does not exist and could not be located on a registered Extension');
    }

    /**
     * Load extensions from Zend_Feed_Reader
     *
     * @return void
     */
    protected function _loadExtensions()
    {
        $all = Reader::getExtensions();
        $feed = $all['entry'];
        foreach ($feed as $extension) {
            if (in_array($extension, $all['core'])) {
                continue;
            }
            $className = Reader::getPluginLoader()->getClassName($extension);
            $this->_extensions[$extension] = new $className(
                $this->getElement(), $this->_entryKey, $this->_data['type']
            );
        }
    }
}
