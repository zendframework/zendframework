<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\App;

use Zend\GData\App;

/**
 * Atom feed class
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage App
 */
abstract class AbstractFeedSourceParent extends AbstractFeedEntryParent
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
     * Set the active service instance for this feed and all enclosed entries.
     * This will be used to perform network requests, such as when calling
     * save() and delete().
     *
     * @param \Zend\GData\App $instance The new service instance.
     * @return AbstractFeedEntryParent Provides a fluent interface.
     */
    public function setService(App $instance = null)
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
     * @return Extension\Generator
     */
    public function getGenerator()
    {
        return $this->_generator;
    }

    /**
     * @param Extension\Generator $value
     * @return AbstractFeedSourceParent Provides a fluent interface
     */
    public function setGenerator($value)
    {
        $this->_generator = $value;
        return $this;
    }

    /**
     * @return Extension\Icon
     */
    public function getIcon()
    {
        return $this->_icon;
    }

    /**
     * @param Extension\Icon $value
     * @return AbstractFeedSourceParent Provides a fluent interface
     */
    public function setIcon($value)
    {
        $this->_icon = $value;
        return $this;
    }

    /**
     * @return Extension\logo
     */
    public function getlogo()
    {
        return $this->_logo;
    }

    /**
     * @param Extension\logo $value
     * @return AbstractFeedSourceParent Provides a fluent interface
     */
    public function setlogo($value)
    {
        $this->_logo = $value;
        return $this;
    }

    /**
     * @return Extension\Subtitle
     */
    public function getSubtitle()
    {
        return $this->_subtitle;
    }

    /**
     * @param Extension\Subtitle $value
     * @return AbstractFeedSourceParent Provides a fluent interface
     */
    public function setSubtitle($value)
    {
        $this->_subtitle = $value;
        return $this;
    }

}
