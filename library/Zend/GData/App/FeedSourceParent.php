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

/**
 * Atom feed class
 *
 * @uses       \Zend\GData\App\Entry
 * @uses       \Zend\GData\App\Extension\Generator
 * @uses       \Zend\GData\App\Extension\Icon
 * @uses       \Zend\GData\App\Extension\Logo
 * @uses       \Zend\GData\App\Extension\Subtitle
 * @uses       \Zend\GData\App\FeedSourceParent
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage App
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class FeedSourceParent extends FeedEntryParent
{

    /**
     * The classname for individual feed elements.
     *
     * @var string
     */
    protected $_entryClassName = '\Zend\GData\App\Entry';

    /**
     * Root XML element for Atom entries.
     *
     * @var string
     */
    protected $_rootElement = null;

    protected $_generator = null;
    protected $_icon = null;
    protected $_logo = null;
    protected $_subtitle = null;

    /**
     * Set the HTTP client instance
     *
     * Sets the HTTP client object to use for retrieving the feed.
     *
     * @deprecated Deprecated as of Zend Framework 1.7. Use
     *             setService() instead.
     * @param  \Zend\Http\Client $httpClient
     * @return \Zend\GData\App\FeedSourceParent Provides a fluent interface
     */
    public function setHttpClient(\Zend\Http\Client $httpClient)
    {
        parent::setHttpClient($httpClient);
        foreach ($this->_entry as $entry) {
            $entry->setHttpClient($httpClient);
        }
        return $this;
    }

    /**
     * Set the active service instance for this feed and all enclosed entries.
     * This will be used to perform network requests, such as when calling
     * save() and delete().
     *
     * @param \Zend\GData\App $instance The new service instance.
     * @return \Zend\GData\App\FeedEntryParent Provides a fluent interface.
     */
    public function setService($instance)
    {
        parent::setService($instance);
        foreach ($this->_entry as $entry) {
            $entry->setService($instance);
        }
        return $this;
    }

    /**
     * Make accessing some individual elements of the feed easier.
     *
     * Special accessors 'entry' and 'entries' are provided so that if
     * you wish to iterate over an Atom feed's entries, you can do so
     * using foreach ($feed->entries as $entry) or foreach
     * ($feed->entry as $entry).
     *
     * @param  string $var The property to access.
     * @return mixed
     */
    public function __get($var)
    {
        switch ($var) {
            default:
                return parent::__get($var);
        }
    }


    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        if ($this->_generator != null) {
            $element->appendChild($this->_generator->getDOM($element->ownerDocument));
        }
        if ($this->_icon != null) {
            $element->appendChild($this->_icon->getDOM($element->ownerDocument));
        }
        if ($this->_logo != null) {
            $element->appendChild($this->_logo->getDOM($element->ownerDocument));
        }
        if ($this->_subtitle != null) {
            $element->appendChild($this->_subtitle->getDOM($element->ownerDocument));
        }
        return $element;
    }

    /**
     * Creates individual Entry objects of the appropriate type and
     * stores them in the $_entry array based upon DOM data.
     *
     * @param DOMNode $child The DOMNode to process
     */
    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
        case $this->lookupNamespace('atom') . ':' . 'generator':
            $generator = new Extension\Generator();
            $generator->transferFromDOM($child);
            $this->_generator = $generator;
            break;
        case $this->lookupNamespace('atom') . ':' . 'icon':
            $icon = new Extension\Icon();
            $icon->transferFromDOM($child);
            $this->_icon = $icon;
            break;
        case $this->lookupNamespace('atom') . ':' . 'logo':
            $logo = new Extension\Logo();
            $logo->transferFromDOM($child);
            $this->_logo = $logo;
            break;
        case $this->lookupNamespace('atom') . ':' . 'subtitle':
            $subtitle = new Extension\Subtitle();
            $subtitle->transferFromDOM($child);
            $this->_subtitle = $subtitle;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    /**
     * @return \Zend\Gdata\AppExtension\Generator
     */
    public function getGenerator()
    {
        return $this->_generator;
    }

    /**
     * @param \Zend\GData\App\Extension\Generator $value
     * @return \Zend\GData\App\FeedSourceParent Provides a fluent interface
     */
    public function setGenerator($value)
    {
        $this->_generator = $value;
        return $this;
    }

    /**
     * @return \Zend\Gdata\AppExtension\Icon
     */
    public function getIcon()
    {
        return $this->_icon;
    }

    /**
     * @param \Zend\GData\App\Extension\Icon $value
     * @return \Zend\GData\App\FeedSourceParent Provides a fluent interface
     */
    public function setIcon($value)
    {
        $this->_icon = $value;
        return $this;
    }

    /**
     * @return \Zend\Gdata\AppExtension\logo
     */
    public function getlogo()
    {
        return $this->_logo;
    }

    /**
     * @param \Zend\Gdata\AppExtension\logo $value
     * @return \Zend\GData\App\FeedSourceParent Provides a fluent interface
     */
    public function setlogo($value)
    {
        $this->_logo = $value;
        return $this;
    }

    /**
     * @return \Zend\Gdata\AppExtension\Subtitle
     */
    public function getSubtitle()
    {
        return $this->_subtitle;
    }

    /**
     * @param \Zend\GData\App\Extension\Subtitle $value
     * @return \Zend\GData\App\FeedSourceParent Provides a fluent interface
     */
    public function setSubtitle($value)
    {
        $this->_subtitle = $value;
        return $this;
    }

}
