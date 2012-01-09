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
 * Data model for a collection of photo entries, usually
 * provided by the Picasa servers.
 *
 * For information on requesting this feed from a server, see the
 * service class, Zend_Gdata_Photos.
 *
 * @uses       \Zend\GData\App\Exception
 * @uses       \Zend\GData\Feed
 * @uses       \Zend\GData\Photos
 * @uses       \Zend\GData\Photos\PhotoEntry
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Photos
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PhotoFeed extends \Zend\GData\Feed
{

    /**
     * gphoto:id element
     *
     * @var \Zend\GData\Photos\Extension\Id
     */
    protected $_gphotoId = null;

    /**
     * gphoto:albumid element
     *
     * @var \Zend\GData\Photos\Extension\AlbumId
     */
    protected $_gphotoAlbumId = null;

    /**
     * gphoto:version element
     *
     * @var \Zend\GData\Photos\Extension\Version
     */
    protected $_gphotoVersion = null;

    /**
     * gphoto:width element
     *
     * @var \Zend\GData\Photos\Extension\Width
     */
    protected $_gphotoWidth = null;

    /**
     * gphoto:height element
     *
     * @var \Zend\GData\Photos\Extension\Height
     */
    protected $_gphotoHeight = null;

    /**
     * gphoto:size element
     *
     * @var \Zend\GData\Photos\Extension\Size
     */
    protected $_gphotoSize = null;

    /**
     * gphoto:client element
     *
     * @var \Zend\GData\Photos\Extension\Client
     */
    protected $_gphotoClient = null;

    /**
     * gphoto:checksum element
     *
     * @var \Zend\GData\Photos\Extension\Checksum
     */
    protected $_gphotoChecksum = null;

    /**
     * gphoto:timestamp element
     *
     * @var \Zend\GData\Photos\Extension\Timestamp
     */
    protected $_gphotoTimestamp = null;

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
     * @var \Zend\GData\Media\Extension\MediaGroup
     */
    protected $_mediaGroup = null;

    protected $_entryClassName = 'Zend\GData\Photos\PhotoEntry';
    protected $_feedClassName = 'Zend\GData\Photos\PhotoFeed';

    protected $_entryKindClassMapping = array(
        'http://schemas.google.com/photos/2007#comment' => 'Zend\GData\Photos\CommentEntry',
        'http://schemas.google.com/photos/2007#tag' => 'Zend\GData\Photos\TagEntry'
    );

    public function __construct($element = null)
    {
        $this->registerAllNamespaces(Photos::$namespaces);
        parent::__construct($element);
    }

    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        if ($this->_gphotoId != null) {
            $element->appendChild($this->_gphotoId->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoVersion != null) {
            $element->appendChild($this->_gphotoVersion->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoWidth != null) {
            $element->appendChild($this->_gphotoWidth->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoHeight != null) {
            $element->appendChild($this->_gphotoHeight->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoSize != null) {
            $element->appendChild($this->_gphotoSize->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoClient != null) {
            $element->appendChild($this->_gphotoClient->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoChecksum != null) {
            $element->appendChild($this->_gphotoChecksum->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoTimestamp != null) {
            $element->appendChild($this->_gphotoTimestamp->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoCommentingEnabled != null) {
            $element->appendChild($this->_gphotoCommentingEnabled->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoCommentCount != null) {
            $element->appendChild($this->_gphotoCommentCount->getDOM($element->ownerDocument));
        }
        if ($this->_mediaGroup != null) {
            $element->appendChild($this->_mediaGroup->getDOM($element->ownerDocument));
        }

        return $element;
    }

    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;

        switch ($absoluteNodeName) {
            case $this->lookupNamespace('gphoto') . ':' . 'id';
                $id = new Extension\Id();
                $id->transferFromDOM($child);
                $this->_gphotoId = $id;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'version';
                $version = new Extension\Version();
                $version->transferFromDOM($child);
                $this->_gphotoVersion = $version;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'albumid';
                $albumid = new Extension\AlbumId();
                $albumid->transferFromDOM($child);
                $this->_gphotoAlbumId = $albumid;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'width';
                $width = new Extension\Width();
                $width->transferFromDOM($child);
                $this->_gphotoWidth = $width;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'height';
                $height = new Extension\Height();
                $height->transferFromDOM($child);
                $this->_gphotoHeight = $height;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'size';
                $size = new Extension\Size();
                $size->transferFromDOM($child);
                $this->_gphotoSize = $size;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'client';
                $client = new Extension\Client();
                $client->transferFromDOM($child);
                $this->_gphotoClient = $client;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'checksum';
                $checksum = new Extension\Checksum();
                $checksum->transferFromDOM($child);
                $this->_gphotoChecksum = $checksum;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'timestamp';
                $timestamp = new Extension\Timestamp();
                $timestamp->transferFromDOM($child);
                $this->_gphotoTimestamp = $timestamp;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'commentingEnabled';
                $commentingEnabled = new Extension\CommentingEnabled();
                $commentingEnabled->transferFromDOM($child);
                $this->_gphotoCommentingEnabled = $commentingEnabled;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'commentCount';
                $commentCount = new Extension\CommentCount();
                $commentCount->transferFromDOM($child);
                $this->_gphotoCommentCount = $commentCount;
                break;
            case $this->lookupNamespace('media') . ':' . 'group';
                $mediaGroup = new \Zend\GData\Media\Extension\MediaGroup();
                $mediaGroup->transferFromDOM($child);
                $this->_mediaGroup = $mediaGroup;
                break;
            case $this->lookupNamespace('atom') . ':' . 'entry':
                $entryClassName = $this->_entryClassName;
                $tmpEntry = new \Zend\GData\App\Entry($child);
                $categories = $tmpEntry->getCategory();
                foreach ($categories as $category) {
                    if ($category->scheme == Photos::KIND_PATH &&
                        $this->_entryKindClassMapping[$category->term] != "") {
                            $entryClassName = $this->_entryKindClassMapping[$category->term];
                            break;
                    } else {
                        throw new \Zend\GData\App\Exception('Entry is missing kind declaration.');
                    }
                }

                $newEntry = new $entryClassName($child);
                $newEntry->setHttpClient($this->getHttpClient());
                $this->_entry[] = $newEntry;
                break;
            default:
                parent::takeChildFromDOM($child);
                break;
        }
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
     * Get the value for this element's gphoto:version attribute.
     *
     * @see setGphotoVersion
     * @return string The requested attribute.
     */
    public function getGphotoVersion()
    {
        return $this->_gphotoVersion;
    }

    /**
     * Set the value for this element's gphoto:version attribute.
     *
     * @param string $value The desired value for this attribute.
     * @return \Zend\GData\Photos\Extension\Version The element being modified.
     */
    public function setGphotoVersion($value)
    {
        $this->_gphotoVersion = $value;
        return $this;
    }

    /**
     * Get the value for this element's gphoto:albumid attribute.
     *
     * @see setGphotoAlbumId
     * @return string The requested attribute.
     */
    public function getGphotoAlbumId()
    {
        return $this->_gphotoAlbumId;
    }

    /**
     * Set the value for this element's gphoto:albumid attribute.
     *
     * @param string $value The desired value for this attribute.
     * @return \Zend\GData\Photos\Extension\AlbumId The element being modified.
     */
    public function setGphotoAlbumId($value)
    {
        $this->_gphotoAlbumId = $value;
        return $this;
    }

    /**
     * Get the value for this element's gphoto:width attribute.
     *
     * @see setGphotoWidth
     * @return string The requested attribute.
     */
    public function getGphotoWidth()
    {
        return $this->_gphotoWidth;
    }

    /**
     * Set the value for this element's gphoto:width attribute.
     *
     * @param string $value The desired value for this attribute.
     * @return \Zend\GData\Photos\Extension\Width The element being modified.
     */
    public function setGphotoWidth($value)
    {
        $this->_gphotoWidth = $value;
        return $this;
    }

    /**
     * Get the value for this element's gphoto:height attribute.
     *
     * @see setGphotoHeight
     * @return string The requested attribute.
     */
    public function getGphotoHeight()
    {
        return $this->_gphotoHeight;
    }

    /**
     * Set the value for this element's gphoto:height attribute.
     *
     * @param string $value The desired value for this attribute.
     * @return \Zend\GData\Photos\Extension\Height The element being modified.
     */
    public function setGphotoHeight($value)
    {
        $this->_gphotoHeight = $value;
        return $this;
    }

    /**
     * Get the value for this element's gphoto:size attribute.
     *
     * @see setGphotoSize
     * @return string The requested attribute.
     */
    public function getGphotoSize()
    {
        return $this->_gphotoSize;
    }

    /**
     * Set the value for this element's gphoto:size attribute.
     *
     * @param string $value The desired value for this attribute.
     * @return \Zend\GData\Photos\Extension\Size The element being modified.
     */
    public function setGphotoSize($value)
    {
        $this->_gphotoSize = $value;
        return $this;
    }

    /**
     * Get the value for this element's gphoto:client attribute.
     *
     * @see setGphotoClient
     * @return string The requested attribute.
     */
    public function getGphotoClient()
    {
        return $this->_gphotoClient;
    }

    /**
     * Set the value for this element's gphoto:client attribute.
     *
     * @param string $value The desired value for this attribute.
     * @return \Zend\GData\Photos\Extension\Client The element being modified.
     */
    public function setGphotoClient($value)
    {
        $this->_gphotoClient = $value;
        return $this;
    }

    /**
     * Get the value for this element's gphoto:checksum attribute.
     *
     * @see setGphotoChecksum
     * @return string The requested attribute.
     */
    public function getGphotoChecksum()
    {
        return $this->_gphotoChecksum;
    }

    /**
     * Set the value for this element's gphoto:checksum attribute.
     *
     * @param string $value The desired value for this attribute.
     * @return \Zend\GData\Photos\Extension\Checksum The element being modified.
     */
    public function setGphotoChecksum($value)
    {
        $this->_gphotoChecksum = $value;
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

}
