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
 * @package    Zend_Gdata
 * @subpackage App
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\GData\App;

use Zend\Http\Header\Etag;

/**
 * Concrete class for working with Atom entries.
 *
 * @uses       \Zend\GData\App\Extension\Content
 * @uses       \Zend\GData\App\Extension\Control
 * @uses       \Zend\GData\App\Extension\Edited
 * @uses       \Zend\GData\App\Extension\Published
 * @uses       \Zend\GData\App\Extension\Source
 * @uses       \Zend\GData\App\Extension\Summary
 * @uses       \Zend\GData\App\FeedEntryParent
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage App
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Entry extends FeedEntryParent
{

    /**
     * Root XML element for Atom entries.
     *
     * @var string
     */
    protected $_rootElement = 'entry';

    /**
     * Class name for each entry in this feed*
     *
     * @var string
     */
    protected $_entryClassName = '\Zend\GData\App\Entry';

    /**
     * atom:content element
     *
     * @var \Zend\GData\App\Extension\Content
     */
    protected $_content = null;

    /**
     * atom:published element
     *
     * @var \Zend\GData\App\Extension\Published
     */
    protected $_published = null;

    /**
     * atom:source element
     *
     * @var \Zend\GData\App\Extension\Source
     */
    protected $_source = null;

    /**
     * atom:summary element
     *
     * @var \Zend\GData\App\Extension\Summary
     */
    protected $_summary = null;

    /**
     * app:control element
     *
     * @var \Zend\GData\App\Extension\Control
     */
    protected $_control = null;

    /**
     * app:edited element
     *
     * @var \Zend\GData\App\Extension\Edited
     */
    protected $_edited = null;

    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        if ($this->_content != null) {
            $element->appendChild($this->_content->getDOM($element->ownerDocument));
        }
        if ($this->_published != null) {
            $element->appendChild($this->_published->getDOM($element->ownerDocument));
        }
        if ($this->_source != null) {
            $element->appendChild($this->_source->getDOM($element->ownerDocument));
        }
        if ($this->_summary != null) {
            $element->appendChild($this->_summary->getDOM($element->ownerDocument));
        }
        if ($this->_control != null) {
            $element->appendChild($this->_control->getDOM($element->ownerDocument));
        }
        if ($this->_edited != null) {
            $element->appendChild($this->_edited->getDOM($element->ownerDocument));
        }
        return $element;
    }

    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
        case $this->lookupNamespace('atom') . ':' . 'content':
            $content = new Extension\Content();
            $content->transferFromDOM($child);
            $this->_content = $content;
            break;
        case $this->lookupNamespace('atom') . ':' . 'published':
            $published = new Extension\Published();
            $published->transferFromDOM($child);
            $this->_published = $published;
            break;
        case $this->lookupNamespace('atom') . ':' . 'source':
            $source = new Extension\Source();
            $source->transferFromDOM($child);
            $this->_source = $source;
            break;
        case $this->lookupNamespace('atom') . ':' . 'summary':
            $summary = new Extension\Summary();
            $summary->transferFromDOM($child);
            $this->_summary = $summary;
            break;
        case $this->lookupNamespace('app') . ':' . 'control':
            $control = new Extension\Control();
            $control->transferFromDOM($child);
            $this->_control = $control;
            break;
        case $this->lookupNamespace('app') . ':' . 'edited':
            $edited = new Extension\Edited();
            $edited->transferFromDOM($child);
            $this->_edited = $edited;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    /**
     * Uploads changes in this entry to the server using \Zend\Gdata\App
     *
     * @param string|null $uri The URI to send requests to, or null if $data
     *        contains the URI.
     * @param string|null $className The name of the class that should we
     *        deserializing the server response. If null, then
     *        '\Zend\Gdata\App\Entry' will be used.
     * @param array $extraHeaders Extra headers to add to the request, as an
     *        array of string-based key/value pairs.
     * @return \Zend\GData\App\Entry The updated entry.
     * @throws \Zend\GData\App\Exception
     */
    public function save($uri = null, $className = null, $extraHeaders = array())
    {
        return $this->getService()->updateEntry($this,
                                                $uri,
                                                $className,
                                                $extraHeaders);
    }

    /**
     * Deletes this entry to the server using the referenced
     * Zend_Http_Client to do a HTTP DELETE to the edit link stored in this
     * entry's link collection.
     *
     * @return void
     * @throws \Zend\GData\App\Exception
     */
    public function delete()
    {
        $this->getService()->delete($this);
    }

    /**
     * Reload the current entry. Returns a new copy of the entry as returned
     * by the server, or null if no changes exist. This does not
     * modify the current entry instance.
     *
     * @param string|null The URI to send requests to, or null if $data
     *        contains the URI.
     * @param string|null The name of the class that should we deserializing
     *        the server response. If null, then '\Zend\Gdata\App\Entry' will
     *        be used.
     * @param array $extraHeaders Extra headers to add to the request, as an
     *        array of string-based key/value pairs.
     * @return mixed A new instance of the current entry with updated data, or
     *         null if the server reports that no changes have been made.
     * @throws \Zend\GData\App\Exception
     */
    public function reload($uri = null, $className = null, $extraHeaders = array())
    {
        // Get URI
        $editLink = $this->getEditLink();
        if (($uri === null) && $editLink != null) {
            $uri = $editLink->getHref();
        }

        // Set classname to current class, if not otherwise set
        if ($className === null) {
            $className = get_class($this);
        }

        // Append ETag, if present (Gdata v2 and above, only) and doesn't
        // conflict with existing headers
        if (($this->_etag instanceof Etag)
                && !array_key_exists('If-Match', $extraHeaders)
                && !array_key_exists('If-None-Match', $extraHeaders)) {
            $extraHeaders['If-None-Match'] = $this->_etag->getFieldValue();
        }

        // If an HTTP 304 status (Not Modified)is returned, then we return
        // null.
        $result = null;
        try {
            $result = $this->service->importUrl($uri, $className, $extraHeaders);
        } catch (HttpException $e) {
            if ($e->getResponse()->getStatusCode() != '304')
                throw $e;
        }

        return $result;
    }

    /**
     * Gets the value of the atom:content element
     *
     * @return \Zend\GData\App\Extension\Content
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * Sets the value of the atom:content element
     *
     * @param \Zend\GData\App\Extension\Content $value
     * @return \Zend\GData\App\Entry Provides a fluent interface
     */
    public function setContent($value)
    {
        $this->_content = $value;
        return $this;
    }

    /**
     * Sets the value of the atom:published element
     * This represents the publishing date for an entry
     *
     * @return \Zend\GData\App\Extension\Published
     */
    public function getPublished()
    {
        return $this->_published;
    }

    /**
     * Sets the value of the atom:published element
     * This represents the publishing date for an entry
     *
     * @param \Zend\GData\App\Extension\Published $value
     * @return \Zend\GData\App\Entry Provides a fluent interface
     */
    public function setPublished($value)
    {
        $this->_published = $value;
        return $this;
    }

    /**
     * Gets the value of the atom:source element
     *
     * @return \Zend\GData\App\Extension\Source
     */
    public function getSource()
    {
        return $this->_source;
    }

    /**
     * Sets the value of the atom:source element
     *
     * @param \Zend\GData\App\Extension\Source $value
     * @return \Zend\GData\App\Entry Provides a fluent interface
     */
    public function setSource($value)
    {
        $this->_source = $value;
        return $this;
    }

    /**
     * Gets the value of the atom:summary element
     * This represents a textual summary of this entry's content
     *
     * @return \Zend\GData\App\Extension\Summary
     */
    public function getSummary()
    {
        return $this->_summary;
    }

    /**
     * Sets the value of the atom:summary element
     * This represents a textual summary of this entry's content
     *
     * @param \Zend\GData\App\Extension\Summary $value
     * @return \Zend\GData\App\Entry Provides a fluent interface
     */
    public function setSummary($value)
    {
        $this->_summary = $value;
        return $this;
    }

    /**
     * Gets the value of the app:control element
     *
     * @return \Zend\GData\App\Extension\Control
     */
    public function getControl()
    {
        return $this->_control;
    }

    /**
     * Sets the value of the app:control element
     *
     * @param \Zend\GData\App\Extension\Control $value
     * @return \Zend\GData\App\Entry Provides a fluent interface
     */
    public function setControl($value)
    {
        $this->_control = $value;
        return $this;
    }

}
