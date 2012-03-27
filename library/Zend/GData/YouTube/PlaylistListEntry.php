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
 * Represents the YouTube video playlist flavor of an Atom entry
 *
 * @uses       \Zend\GData\App\VersionException
 * @uses       \Zend\GData\Entry
 * @uses       \Zend\GData\Extension\FeedLink
 * @uses       \Zend\GData\YouTube
 * @uses       \Zend\GData\YouTube\Extension\CountHint
 * @uses       \Zend\GData\YouTube\Extension\Description
 * @uses       \Zend\GData\YouTube\Extension\PlaylistId
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage YouTube
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PlaylistListEntry extends \Zend\GData\Entry
{

    protected $_entryClassName = 'Zend\GData\YouTube\PlaylistListEntry';

    /**
     * Nested feed links
     *
     * @var array
     */
    protected $_feedLink = array();

    /**
     * Description of this playlist
     *
     * @deprecated Deprecated as of version 2 of the YouTube API.
     * @var \Zend\GData\YouTube\Extension\Description
     */
    protected $_description = null;

    /**
     * Id of this playlist
     *
     * @var \Zend\GData\YouTube\Extension\PlaylistId
     */
    protected $_playlistId = null;

    /**
     * CountHint for this playlist.
     *
     * @var \Zend\GData\YouTube\Extension\CountHint
     */
    protected $_countHint = null;

    /**
     * Creates a Playlist list entry, representing an individual playlist
     * in a list of playlists, usually associated with an individual user.
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
            $element->appendChild($this->_description->getDOM($element->ownerDocument));
        }
        if ($this->_countHint != null) {
            $element->appendChild($this->_countHint->getDOM($element->ownerDocument));
        }
        if ($this->_playlistId != null) {
            $element->appendChild($this->_playlistId->getDOM($element->ownerDocument));
        }
        if ($this->_feedLink != null) {
            foreach ($this->_feedLink as $feedLink) {
                $element->appendChild($feedLink->getDOM($element->ownerDocument));
            }
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
        case $this->lookupNamespace('yt') . ':' . 'description':
            $description = new Extension\Description();
            $description->transferFromDOM($child);
            $this->_description = $description;
            break;
        case $this->lookupNamespace('yt') . ':' . 'countHint':
            $countHint = new Extension\CountHint();
            $countHint->transferFromDOM($child);
            $this->_countHint = $countHint;
            break;
        case $this->lookupNamespace('yt') . ':' . 'playlistId':
            $playlistId = new Extension\PlaylistId();
            $playlistId->transferFromDOM($child);
            $this->_playlistId = $playlistId;
            break;
        case $this->lookupNamespace('gd') . ':' . 'feedLink':
            $feedLink = new \Zend\GData\Extension\FeedLink();
            $feedLink->transferFromDOM($child);
            $this->_feedLink[] = $feedLink;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    /**
     * Sets the description relating to the playlist.
     *
     * @deprecated Deprecated as of version 2 of the YouTube API.
     * @param \Zend\GData\YouTube\Extension\Description $description The description relating to the video
     * @return \Zend\GData\YouTube\PlaylistListEntry Provides a fluent interface
     */
    public function setDescription($description = null)
    {
        if ($this->getMajorProtocolVersion() >= 2) {
            $this->setSummary($description);
        } else {
            $this->_description = $description;
        }
        return $this;
    }

    /**
     * Returns the description relating to the video.
     *
     * @return \Zend\GData\YouTube\Extension\Description  The description
     *         relating to the video
     */
    public function getDescription()
    {
        if ($this->getMajorProtocolVersion() >= 2) {
            return $this->getSummary();
        } else {
            return $this->_description;
        }
    }

    /**
     * Returns the countHint relating to the playlist.
     *
     * The countHint is the number of videos on a playlist.
     *
     * @throws \Zend\GData\App\VersionException
     * @return \Zend\GData\YouTube\Extension\CountHint  The count of videos on
     *         a playlist.
     */
    public function getCountHint()
    {
        if (($this->getMajorProtocolVersion() == null) ||
            ($this->getMajorProtocolVersion() == 1)) {
            throw new App\VersionException('The yt:countHint ' .
                'element is not supported in versions earlier than 2.');
        } else {
            return $this->_countHint;
        }
    }

    /**
     * Returns the Id relating to the playlist.
     *
     * @throws \Zend\GData\App\VersionException
     * @return \Zend\GData\YouTube\Extension\PlaylistId  The id of this playlist.
     */
    public function getPlaylistId()
    {
        if (($this->getMajorProtocolVersion() == null) ||
            ($this->getMajorProtocolVersion() == 1)) {
            throw new App\VersionException('The yt:playlistId ' .
                'element is not supported in versions earlier than 2.');
        } else {
            return $this->_playlistId;
        }
    }

    /**
     * Sets the array of embedded feeds related to the playlist
     *
     * @param array $feedLink The array of embedded feeds relating to the video
     * @return \Zend\GData\YouTube\PlaylistListEntry Provides a fluent interface
     */
    public function setFeedLink($feedLink = null)
    {
        $this->_feedLink = $feedLink;
        return $this;
    }

    /**
     * Get the feed link property for this entry.
     *
     * @see setFeedLink
     * @param string $rel (optional) The rel value of the link to be found.
     *          If null, the array of links is returned.
     * @return mixed If $rel is specified, a \Zend\GData\Extension\FeedLink
     *          object corresponding to the requested rel value is returned
     *          if found, or null if the requested value is not found. If
     *          $rel is null or not specified, an array of all available
     *          feed links for this entry is returned, or null if no feed
     *          links are set.
     */
    public function getFeedLink($rel = null)
    {
        if ($rel == null) {
            return $this->_feedLink;
        } else {
            foreach ($this->_feedLink as $feedLink) {
                if ($feedLink->rel == $rel) {
                    return $feedLink;
                }
            }
            return null;
        }
    }

    /**
     * Returns the URL of the playlist video feed
     *
     * @return string The URL of the playlist video feed
     */
    public function getPlaylistVideoFeedUrl()
    {
        if ($this->getMajorProtocolVersion() >= 2) {
            return $this->getContent()->getSrc();
        } else {
            return $this->getFeedLink(YouTube::PLAYLIST_REL)->href;
        }
    }

}
