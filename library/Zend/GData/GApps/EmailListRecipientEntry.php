<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\GApps;

use Zend\GData\GApps;

/**
 * Data model class for a Google Apps Email List Recipient Entry.
 *
 * Each instance of this class represents a recipient of an email list
 * hosted on a Google Apps domain. Each email list may contain multiple
 * recipients. Email lists themselves are described by
 * Zend_Gdata_EmailListEntry. Multiple recipient entries are contained within
 * instances of Zend_Gdata_GApps_EmailListRecipientFeed.
 *
 * To transfer email list recipients to and from the Google Apps servers,
 * including creating new recipients, refer to the Google Apps service class,
 * Zend_Gdata_GApps.
 *
 * This class represents <atom:entry> in the Google Data protocol.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage GApps
 */
class EmailListRecipientEntry extends \Zend\GData\Entry
{

    protected $_entryClassName = 'Zend\GData\GApps\EmailListRecipientEntry';

    /**
     * <gd:who> element used to store the email address of the current
     * recipient. Only the email property of this element should be
     * populated.
     *
     * @var \Zend\GData\Extension\Who
     */
    protected $_who = null;

    /**
     * Create a new instance.
     *
     * @param DOMElement $element (optional) DOMElement from which this
     *          object should be constructed.
     */
    public function __construct($element = null)
    {
        $this->registerAllNamespaces(GApps::$namespaces);
        parent::__construct($element);
    }

    /**
     * Retrieves a DOMElement which corresponds to this element and all
     * child properties.  This is used to build an entry back into a DOM
     * and eventually XML text for application storage/persistence.
     *
     * @param DOMDocument $doc The DOMDocument used to construct DOMElements
     * @return DOMElement The DOMElement representing this element and all
     *          child properties.
     */
    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        if ($this->_who !== null) {
            $element->appendChild($this->_who->getDOM($element->ownerDocument));
        }
        return $element;
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
            case $this->lookupNamespace('gd') . ':' . 'who';
                $who = new \Zend\GData\Extension\Who();
                $who->transferFromDOM($child);
                $this->_who = $who;
                break;
            default:
                parent::takeChildFromDOM($child);
                break;
        }
    }

    /**
     * Get the value of the who property for this object.
     *
     * @see setWho
     * @return \Zend\GData\Extension\Who The requested object.
     */
    public function getWho()
    {
        return $this->_who;
    }

    /**
     * Set the value of the who property for this object. This property
     * is used to store the email address of the current recipient.
     *
     * @param \Zend\GData\Extension\Who $value The desired value for this
     *          instance's who property.
     * @return EmailListRecipientEntry Provides a fluent interface.
     */
    public function setWho($value)
    {
        $this->_who = $value;
        return $this;
    }

}
