<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\Extension;

use Zend\GData\Extension;

/**
 * Data model class to represent an entry's recurrenceException
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Gdata
 */
class RecurrenceException extends Extension
{

    protected $_rootElement = 'recurrenceException';
    protected $_specialized = null;
    protected $_entryLink = null;
    protected $_originalEvent = null;

    /**
     * Constructs a new Zend_Gdata_Extension_RecurrenceException object.
     * @param bool $specialized (optional) Whether this is a specialized exception or not.
     * @param Zend_Gdata_EntryLink (optional) An Event entry with details about the exception.
     * @param Zend_Gdata_OriginalEvent (optional) The origianl recurrent event this is an exeption to.
     */
    public function __construct($specialized = null, $entryLink = null,
            $originalEvent = null)
    {
        parent::__construct();
        $this->_specialized = $specialized;
        $this->_entryLink = $entryLink;
        $this->_originalEvent = $originalEvent;
    }

    /**
     * Retrieves a DOMElement which corresponds to this element and all
     * child properties.  This is used to build an entry back into a DOM
     * and eventually XML text for sending to the server upon updates, or
     * for application storage/persistence.
     *
     * @param DOMDocument $doc The DOMDocument used to construct DOMElements
     * @return DOMElement The DOMElement representing this element and all
     * child properties.
     */
    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        if ($this->_specialized !== null) {
            $element->setAttribute('specialized', $this->_specialized);
        }
        if ($this->_entryLink !== null) {
            $element->appendChild($this->_entryLink->getDOM($element->ownerDocument));
        }
        if ($this->_originalEvent !== null) {
            $element->appendChild($this->_originalEvent->getDOM($element->ownerDocument));
        }
        return $element;
    }

    /**
     * Given a DOMNode representing an attribute, tries to map the data into
     * instance members.  If no mapping is defined, the name and value are
     * stored in an array.
     *
     * @param DOMNode $attribute The DOMNode attribute needed to be handled
     */
    protected function takeAttributeFromDOM($attribute)
    {
        switch ($attribute->localName) {
        case 'specialized':
            if ($attribute->nodeValue != "true" && $attribute->nodeValue != "false") {
                throw new \Zend\GData\App\InvalidArgumentException(
                    "Expected 'true' or 'false' for gCal:selected#value.");
            }
            $this->_specialized = $attribute->nodeValue;
            break;
        default:
            parent::takeAttributeFromDOM($attribute);
        }
    }

    /**
     * Creates individual Entry objects of the appropriate type and
     * stores them as members of this entry based upon DOM data.
     *
     * @param DOMNode $child The DOMNode to process
     */
    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
        case $this->lookupNamespace('gd') . ':' . 'entryLink':
            $entryLink = new EntryLink();
            $entryLink->transferFromDOM($child);
            $this->_entryLink = $entryLink;
            break;
        case $this->lookupNamespace('gd') . ':' . 'originalEvent':
            $originalEvent = new OriginalEvent();
            $originalEvent->transferFromDOM($child);
            $this->_originalEvent = $originalEvent;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    /**
     * Get the value for this element's Specialized attribute.
     *
     * @return bool The requested attribute.
     */
    public function getSpecialized()
    {
        return $this->_specialized;
    }

    /**
     * Set the value for this element's Specialized attribute.
     *
     * @param bool $value The desired value for this attribute.
     * @return \Zend\GData\Extension\RecurrenceException The element being modified.
     */
    public function setSpecialized($value)
    {
        $this->_specialized = $value;
        return $this;
    }

    /**
     * Get the value for this element's EntryLink attribute.
     *
     * @return \Zend\GData\Extension\EntryLink The requested attribute.
     */
    public function getEntryLink()
    {
        return $this->_entryLink;
    }

    /**
     * Set the value for this element's EntryLink attribute.
     *
     * @param \Zend\GData\Extension\EntryLink $value The desired value for this attribute.
     * @return \Zend\GData\Extension\RecurrenceException The element being modified.
     */
    public function setEntryLink($value)
    {
        $this->_entryLink = $value;
        return $this;
    }

    /**
     * Get the value for this element's Specialized attribute.
     *
     * @return \Zend\GData\Extension\OriginalEvent The requested attribute.
     */
    public function getOriginalEvent()
    {
        return $this->_originalEvent;
    }

    /**
     * Set the value for this element's Specialized attribute.
     *
     * @param \Zend\GData\Extension\OriginalEvent $value The desired value for this attribute.
     * @return \Zend\GData\Extension\RecurrenceException The element being modified.
     */
    public function setOriginalEvent($value)
    {
        $this->_originalEvent = $value;
        return $this;
    }

}

