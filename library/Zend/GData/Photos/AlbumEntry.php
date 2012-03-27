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
 * @subpackage Photos
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\GData\Photos;

use Zend\GData\Photos;

/**
 * Data model class for a Photo Album Entry.
 *
 * To transfer user entries to and from the servers, including
 * creating new entries, refer to the service class,
 * Zend_Gdata_Photos.
 *
 * This class represents <atom:entry> in the Google Data protocol.
 *
 * @uses       \Zend\GData\App\Extension\Category
 * @uses       \Zend\GData\Entry
 * @uses       \Zend\GData\Geo\Extension\GeoRssWhere
 * @uses       \Zend\GData\Media\Extension\MediaGroup
 * @uses       \Zend\GData\Photos
 * @uses       \Zend\GData\Photos\Extension\Access
 * @uses       \Zend\GData\Photos\Extension\BytesUsed
 * @uses       \Zend\GData\Photos\Extension\CommentCount
 * @uses       \Zend\GData\Photos\Extension\CommentingEnabled
 * @uses       \Zend\GData\Photos\Extension\Id
 * @uses       \Zend\GData\Photos\Extension\Location
 * @uses       \Zend\GData\Photos\Extension\Name
 * @uses       \Zend\GData\Photos\Extension\NumPhotos
 * @uses       \Zend\GData\Photos\Extension\NumPhotosRemaining
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Photos
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class AlbumEntry extends \Zend\GData\Entry
{

    protected $_entryClassName = 'Zend\GData\Photos\AlbumEntry';

    /**
     * gphoto:id element
     *
     * @var \Zend\GData\Photos\Extension\Id
     */
    protected $_gphotoId = null;

    /**
     * gphoto:access element
     *
     * @var \Zend\GData\Photos\Extension\Access
     */
    protected $_gphotoAccess = null;

    /**
     * gphoto:location element
     *
     * @var \Zend\GData\Photos\Extension\Location
     */
    protected $_gphotoLocation = null;

    /**
     * gphoto:user element
     *
     * @var \Zend\GData\Photos\Extension\User
     */
    protected $_gphotoUser = null;

    /**
     * gphoto:nickname element
     *
     * @var \Zend\GData\Photos\Extension\Nickname
     */
    protected $_gphotoNickname = null;

    /**
     * gphoto:timestamp element
     *
     * @var \Zend\GData\Photos\Extension\Timestamp
     */
    protected $_gphotoTimestamp = null;

    /**
     * gphoto:name element
     *
     * @var \Zend\GData\Photos\Extension\Name
     */
    protected $_gphotoName = null;

    /**
     * gphoto:numphotos element
     *
     * @var \Zend\GData\Photos\Extension\NumPhotos
     */
    protected $_gphotoNumPhotos = null;

    /**
     * gphoto:commentCount element
     *
     * @var \Zend\GData\Photos\Extension\CommentCount
     */
    protected $_gphotoCommentCount = null;

    /**
     * gphoto:commentingEnabled element
     *
     * @var \Zend\GData\Photos\Extension\CommentingEnabled
     */
    protected $_gphotoCommentingEnabled = null;

    /**
     * media:group element
     *
     * @var Zend_Gdata_Media_MediaGroup
     */
    protected $_mediaGroup = null;

    /**
     * georss:where element
     *
     * @var \Zend\GData\Geo\Extension\GeoRssWhere
     */
    protected $_geoRssWhere = null;

    /**
     * Create a new instance.
     *
     * @param DOMElement $element (optional) DOMElement from which this
     *          object should be constructed.
     */
    public function __construct($element = null)
    {
        $this->registerAllNamespaces(Photos::$namespaces);
        parent::__construct($element);

        $category = new \Zend\GData\App\Extension\Category(
            'http://schemas.google.com/photos/2007#album',
            'http://schemas.google.com/g/2005#kind');
        $this->setCategory(array($category));
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
        if ($this->_gphotoTimestamp !== null) {
            $element->appendChild($this->_gphotoTimestamp->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoUser !== null) {
            $element->appendChild($this->_gphotoUser->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoNickname !== null) {
            $element->appendChild($this->_gphotoNickname->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoAccess !== null) {
            $element->appendChild($this->_gphotoAccess->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoLocation !== null) {
            $element->appendChild($this->_gphotoLocation->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoName !== null) {
            $element->appendChild($this->_gphotoName->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoNumPhotos !== null) {
            $element->appendChild($this->_gphotoNumPhotos->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoCommentCount !== null) {
            $element->appendChild($this->_gphotoCommentCount->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoCommentingEnabled !== null) {
            $element->appendChild($this->_gphotoCommentingEnabled->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoId !== null) {
            $element->appendChild($this->_gphotoId->getDOM($element->ownerDocument));
        }
        if ($this->_mediaGroup !== null) {
            $element->appendChild($this->_mediaGroup->getDOM($element->ownerDocument));
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
            case $this->lookupNamespace('gphoto') . ':' . 'access';
                $access = new Extension\Access();
                $access->transferFromDOM($child);
                $this->_gphotoAccess = $access;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'location';
                $location = new Extension\Location();
                $location->transferFromDOM($child);
                $this->_gphotoLocation = $location;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'name';
                $name = new Extension\Name();
                $name->transferFromDOM($child);
                $this->_gphotoName = $name;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'numphotos';
                $numPhotos = new Extension\NumPhotos();
                $numPhotos->transferFromDOM($child);
                $this->_gphotoNumPhotos = $numPhotos;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'commentCount';
                $commentCount = new Extension\CommentCount();
                $commentCount->transferFromDOM($child);
                $this->_gphotoCommentCount = $commentCount;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'commentingEnabled';
                $commentingEnabled = new Extension\CommentingEnabled();
                $commentingEnabled->transferFromDOM($child);
                $this->_gphotoCommentingEnabled = $commentingEnabled;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'id';
                $id = new Extension\Id();
                $id->transferFromDOM($child);
                $this->_gphotoId = $id;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'user';
                $user = new Extension\User();
                $user->transferFromDOM($child);
                $this->_gphotoUser = $user;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'timestamp';
                $timestamp = new Extension\Timestamp();
                $timestamp->transferFromDOM($child);
                $this->_gphotoTimestamp = $timestamp;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'nickname';
                $nickname = new Extension\Nickname();
                $nickname->transferFromDOM($child);
                $this->_gphotoNickname = $nickname;
                break;
            case $this->lookupNamespace('georss') . ':' . 'where';
                $geoRssWhere = new \Zend\GData\Geo\Extension\GeoRssWhere();
                $geoRssWhere->transferFromDOM($child);
                $this->_geoRssWhere = $geoRssWhere;
                break;
            case $this->lookupNamespace('media') . ':' . 'group';
                $mediaGroup = new \Zend\GData\Media\Extension\MediaGroup();
                $mediaGroup->transferFromDOM($child);
                $this->_mediaGroup = $mediaGroup;
                break;
            default:
                parent::takeChildFromDOM($child);
                break;
        }
    }

    /**
     * Get the value for this element's gphoto:access attribute.
     *
     * @see setGphotoAccess
     * @return string The requested attribute.
     */
    public function getGphotoAccess()
    {
        return $this->_gphotoAccess;
    }

    /**
     * Set the value for this element's gphoto:access attribute.
     *
     * @param string $value The desired value for this attribute.
     * @return \Zend\GData\Photos\Extension\Access The element being modified.
     */
    public function setGphotoAccess($value)
    {
        $this->_gphotoAccess = $value;
        return $this;
    }

    /**
     * Get the value for this element's gphoto:location attribute.
     *
     * @see setGphotoLocation
     * @return string The requested attribute.
     */
    public function getGphotoLocation()
    {
        return $this->_gphotoLocation;
    }

    /**
     * Set the value for this element's gphoto:location attribute.
     *
     * @param string $value The desired value for this attribute.
     * @return \Zend\GData\Photos\Extension\Location The element being modified.
     */
    public function setGphotoLocation($value)
    {
        $this->_location = $value;
        return $this;
    }

    /**
     * Get the value for this element's gphoto:name attribute.
     *
     * @see setGphotoName
     * @return string The requested attribute.
     */
    public function getGphotoName()
    {
        return $this->_gphotoName;
    }

    /**
     * Set the value for this element's gphoto:name attribute.
     *
     * @param string $value The desired value for this attribute.
     * @return \Zend\GData\Photos\Extension\Name The element being modified.
     */
    public function setGphotoName($value)
    {
        $this->_gphotoName = $value;
        return $this;
    }

    /**
     * Get the value for this element's gphoto:numphotos attribute.
     *
     * @see setGphotoNumPhotos
     * @return string The requested attribute.
     */
    public function getGphotoNumPhotos()
    {
        return $this->_gphotoNumPhotos;
    }

    /**
     * Set the value for this element's gphoto:numphotos attribute.
     *
     * @param string $value The desired value for this attribute.
     * @return \Zend\GData\Photos\Extension\NumPhotos The element being modified.
     */
    public function setGphotoNumPhotos($value)
    {
        $this->_gphotoNumPhotos = $value;
        return $this;
    }

    /**
     * Get the value for this element's gphoto:commentCount attribute.
     *
     * @see setGphotoCommentCount
     * @return string The requested attribute.
     */
    public function getGphotoCommentCount()
    {
        return $this->_gphotoCommentCount;
    }

    /**
     * Set the value for this element's gphoto:commentCount attribute.
     *
     * @param string $value The desired value for this attribute.
     * @return \Zend\GData\Photos\Extension\CommentCount The element being modified.
     */
    public function setGphotoCommentCount($value)
    {
        $this->_gphotoCommentCount = $value;
        return $this;
    }

    /**
     * Get the value for this element's gphoto:commentingEnabled attribute.
     *
     * @see setGphotoCommentingEnabled
     * @return string The requested attribute.
     */
    public function getGphotoCommentingEnabled()
    {
        return $this->_gphotoCommentingEnabled;
    }

    /**
     * Set the value for this element's gphoto:commentingEnabled attribute.
     *
     * @param string $value The desired value for this attribute.
     * @return \Zend\GData\Photos\Extension\CommentingEnabled The element being modified.
     */
    public function setGphotoCommentingEnabled($value)
    {
        $this->_gphotoCommentingEnabled = $value;
        return $this;
    }

    /**
     * Get the value for this element's gphoto:id attribute.
     *
     * @see setGphotoId
     * @return string The requested attribute.
     */
    public function getGphotoId()
    {
        return $this->_gphotoId;
    }

    /**
     * Set the value for this element's gphoto:id attribute.
     *
     * @param string $value The desired value for this attribute.
     * @return \Zend\GData\Photos\Extension\Id The element being modified.
     */
    public function setGphotoId($value)
    {
        $this->_gphotoId = $value;
        return $this;
    }

    /**
     * Get the value for this element's georss:where attribute.
     *
     * @see setGeoRssWhere
     * @return string The requested attribute.
     */
    public function getGeoRssWhere()
    {
        return $this->_geoRssWhere;
    }

    /**
     * Set the value for this element's georss:where attribute.
     *
     * @param string $value The desired value for this attribute.
     * @return \Zend\GData\Geo\Extension\GeoRssWhere The element being modified.
     */
    public function setGeoRssWhere($value)
    {
        $this->_geoRssWhere = $value;
        return $this;
    }

    /**
     * Get the value for this element's media:group attribute.
     *
     * @see setMediaGroup
     * @return string The requested attribute.
     */
    public function getMediaGroup()
    {
        return $this->_mediaGroup;
    }

    /**
     * Set the value for this element's media:group attribute.
     *
     * @param string $value The desired value for this attribute.
     * @return \Zend\GData\Media\Extension\MediaGroup The element being modified.
     */
    public function setMediaGroup($value)
    {
        $this->_mediaGroup = $value;
        return $this;
    }

    /**
     * Get the value for this element's gphoto:user attribute.
     *
     * @see setGphotoUser
     * @return string The requested attribute.
     */
    public function getGphotoUser()
    {
        return $this->_gphotoUser;
    }

    /**
     * Set the value for this element's gphoto:user attribute.
     *
     * @param string $value The desired value for this attribute.
     * @return \Zend\GData\Photos\Extension\User The element being modified.
     */
    public function setGphotoUser($value)
    {
        $this->_gphotoUser = $value;
        return $this;
    }

    /**
     * Get the value for this element's gphoto:nickname attribute.
     *
     * @see setGphotoNickname
     * @return string The requested attribute.
     */
    public function getGphotoNickname()
    {
        return $this->_gphotoNickname;
    }

    /**
     * Set the value for this element's gphoto:nickname attribute.
     *
     * @param string $value The desired value for this attribute.
     * @return \Zend\GData\Photos\Extension\Nickname The element being modified.
     */
    public function setGphotoNickname($value)
    {
        $this->_gphotoNickname = $value;
        return $this;
    }

    /**
     * Get the value for this element's gphoto:timestamp attribute.
     *
     * @see setGphotoTimestamp
     * @return string The requested attribute.
     */
    public function getGphotoTimestamp()
    {
        return $this->_gphotoTimestamp;
    }

    /**
     * Set the value for this element's gphoto:timestamp attribute.
     *
     * @param string $value The desired value for this attribute.
     * @return \Zend\GData\Photos\Extension\Timestamp The element being modified.
     */
    public function setGphotoTimestamp($value)
    {
        $this->_gphotoTimestamp = $value;
        return $this;
    }
}
