<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\YouTube;

use Zend\GData\YouTube;

/**
 * The YouTube contacts flavor of an Atom Entry with media support
 * Represents a an individual contact
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage YouTube
 */
class ContactEntry extends UserProfileEntry
{

    /**
     * The classname for individual feed elements.
     *
     * @var string
     */
    protected $_entryClassName = 'Zend\GData\YouTube\ContactEntry';

    /**
     * Status of the user as a contact
     *
     * @var string
     */
    protected $_status = null;

    /**
     * Constructs a new Contact Entry object, to represent
     * an individual contact for a user
     *
     * @param DOMElement $element (optional) DOMElement from which this
     *          object should be constructed.
     */
    public function __construct($element = null)
    {
        $this->registerAllNamespaces(YouTube::$namespaces);
        parent::__construct($element);
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
        if ($this->_status != null) {
            $element->appendChild($this->_status->getDOM($element->ownerDocument));
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
        case $this->lookupNamespace('yt') . ':' . 'status':
            $status = new Extension\Status();
            $status->transferFromDOM($child);
            $this->_status = $status;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    /**
     * Sets the status
     *
     * @param \Zend\GData\YouTube\Extension\Status $status The status
     * @return \Zend\GData\YouTube\ContactEntry Provides a fluent interface
     */
    public function setStatus($status = null)
    {
        $this->_status = $status;
        return $this;
    }

    /**
     * Returns the status
     *
     * @return \Zend\GData\YouTube\Extension\Status  The status
     */
    public function getStatus()
    {
        return $this->_status;
    }

}
