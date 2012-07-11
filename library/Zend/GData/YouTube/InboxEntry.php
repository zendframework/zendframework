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

use Zend\GData\App;
use Zend\GData\YouTube;

/**
 * Represents the YouTube message flavor of an Atom entry
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage YouTube
 */
class InboxEntry extends \Zend\GData\Media\Entry
{

    protected $_entryClassName = 'Zend\GData\YouTube\InboxEntry';

    /**
     * The gd:comments element of this entry.
     *
     * @var \Zend\GData\Extension\Comments
     */
    protected $_comments = null;

    /**
     * The gd:rating element of this entry.
     *
     * @var \Zend\GData\Extension\Rating
     */
    protected $_rating = null;

    /**
     * The yt:statistics element of this entry.
     *
     * @var \Zend\GData\YouTube\Extension\Statistics
     */
    protected $_statistics = null;

    /**
     * Creates a subscription entry, representing an individual subscription
     * in a list of subscriptions, usually associated with an individual user.
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
        if ($this->_rating != null) {
            $element->appendChild(
                $this->_rating->getDOM($element->ownerDocument));
        }
        if ($this->_statistics != null) {
            $element->appendChild(
                $this->_statistics->getDOM($element->ownerDocument));
        }
        if ($this->_comments != null) {
            $element->appendChild(
                $this->_comments->getDOM($element->ownerDocument));
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
            case $this->lookupNamespace('gd') . ':' . 'comments':
                $comments = new \Zend\GData\Extension\Comments();
                $comments->transferFromDOM($child);
                $this->_comments = $comments;
                break;
            case $this->lookupNamespace('gd') . ':' . 'rating':
                $rating = new \Zend\GData\Extension\Rating();
                $rating->transferFromDOM($child);
                $this->_rating = $rating;
                break;
            case $this->lookupNamespace('yt') . ':' . 'statistics':
                $statistics = new Extension\Statistics();
                $statistics->transferFromDOM($child);
                $this->_statistics = $statistics;
                break;
            default:
                parent::takeChildFromDOM($child);
                break;
        }
    }

    /**
     * Get the gd:rating element for the inbox entry
     *
     * @return \Zend\GData\Extension\Rating|null
     */
    public function getRating()
    {
        return $this->_rating;
    }

    /**
     * Sets the gd:rating element for the inbox entry
     *
     * @param \Zend\GData\Extension\Rating $rating The rating for the video in
     *        the message
     * @return \Zend\GData\YouTube\InboxEntry Provides a fluent interface
     */
    public function setRating($rating = null)
    {
        $this->_rating = $rating;
        return $this;
    }

    /**
     * Get the gd:comments element of the inbox entry.
     *
     * @return \Zend\GData\Extension\Comments|null
     */
    public function getComments()
    {
        return $this->_comments;
    }

    /**
     * Sets the gd:comments element for the inbox entry
     *
     * @param \Zend\GData\Extension\Comments $comments The comments feed link
     * @return \Zend\GData\YouTube\InboxEntry Provides a fluent interface
     */
    public function setComments($comments = null)
    {
        $this->_comments = $comments;
        return $this;
    }

    /**
     * Get the yt:statistics element for the inbox entry
     *
     * @return \Zend\GData\YouTube\Extension\Statistics|null
     */
    public function getStatistics()
    {
        return $this->_statistics;
    }

    /**
     * Sets the yt:statistics element for the inbox entry
     *
     * @param \Zend\GData\YouTube\Extension\Statistics $statistics The
     *        statistics element for the video in the message
     * @return \Zend\GData\YouTube\InboxEntry Provides a fluent interface
     */
    public function setStatistics($statistics = null)
    {
        $this->_statistics = $statistics;
        return $this;
    }


}
