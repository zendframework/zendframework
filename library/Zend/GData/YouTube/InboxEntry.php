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
 * @subpackage YouTube
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\GData\YouTube;

use Zend\GData\YouTube,
    Zend\GData\App;

/**
 * Represents the YouTube message flavor of an Atom entry
 *
 * @uses       \Zend\GData\App\VersionException
 * @uses       \Zend\GData\Extension\Comments
 * @uses       \Zend\GData\Extension\Rating
 * @uses       \Zend\GData\Media\Entry
 * @uses       \Zend\GData\YouTube
 * @uses       \Zend\GData\YouTube\Extension\Description
 * @uses       \Zend\GData\YouTube\Extension\Statistics
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage YouTube
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
     * The yt:description element of this entry.
     *
     * @var \Zend\GData\YouTube\Extension\Description
     */
    protected $_description = null;

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
        if ($this->_description != null) {
            $element->appendChild(
                $this->_description->getDOM($element->ownerDocument));
        }
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
            case $this->lookupNamespace('yt') . ':' . 'description':
                $description = new Extension\Description();
                $description->transferFromDOM($child);
                $this->_description = $description;
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
     * Get the yt:description
     *
     * @throws \Zend\GData\App\VersionException
     * @return \Zend\GData\YouTube\Extension\Description|null
     */
    public function getDescription()
    {
        if ($this->getMajorProtocolVersion() == 2) {
            throw new App\VersionException('The getDescription ' .
                ' method is only supported in version 1 of the YouTube ' .
                'API.');
        } else {
            return $this->_description;
        }
    }

    /**
     * Sets the yt:description element for a new inbox entry.
     *
     * @param \Zend\GData\YouTube\Extension\Description $description The
     *        description.
     * @throws \Zend\GData\App\VersionException
     * @return \Zend\GData\YouTube\InboxEntry Provides a fluent interface
     */
    public function setDescription($description = null)
    {
        if ($this->getMajorProtocolVersion() == 2) {
            throw new App\VersionException('The setDescription ' .
                ' method is only supported in version 1 of the YouTube ' .
                'API.');
        } else {
            $this->_description = $description;
            return $this;
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
