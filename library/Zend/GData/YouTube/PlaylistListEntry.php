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
 * Represents the YouTube video playlist flavor of an Atom entry
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage YouTube
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
        return $this->_countHint;
    }

    /**
     * Returns the Id relating to the playlist.
     *
     * @throws \Zend\GData\App\VersionException
     * @return \Zend\GData\YouTube\Extension\PlaylistId  The id of this playlist.
     */
    public function getPlaylistId()
    {
        return $this->_playlistId;
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
        return $this->getContent()->getSrc();
    }

}
