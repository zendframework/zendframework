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
 * Data model class for a User Entry.
 *
 * To transfer user entries to and from the servers, including
 * creating new entries, refer to the service class,
 * Zend_Gdata_Photos.
 *
 * This class represents <atom:entry> in the Google Data protocol.
 *
 * @uses       \Zend\GData\App\Extension\Category
 * @uses       \Zend\GData\Entry
 * @uses       \Zend\GData\GApps
 * @uses       \Zend\GData\Photos
 * @uses       \Zend\GData\Photos\Extension\MaxPhotosPerAlbum
 * @uses       \Zend\GData\Photos\Extension\Nickname
 * @uses       \Zend\GData\Photos\Extension\QuotaCurrent
 * @uses       \Zend\GData\Photos\Extension\QuotaLimit
 * @uses       \Zend\GData\Photos\Extension\Thumbnail
 * @uses       \Zend\GData\Photos\Extension\User
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Photos
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class UserEntry extends \Zend\GData\Entry
{

    protected $_entryClassName = 'Zend\GData\Photos\UserEntry';

    /**
     * gphoto:nickname element
     *
     * @var \Zend\GData\Photos\Extension\Nickname
     */
    protected $_gphotoNickname = null;

    /**
     * gphoto:user element
     *
     * @var \Zend\GData\Photos\Extension\User
     */
    protected $_gphotoUser = null;

    /**
     * gphoto:thumbnail element
     *
     * @var \Zend\GData\Photos\Extension\Thumbnail
     */
    protected $_gphotoThumbnail = null;

    /**
     * gphoto:quotalimit element
     *
     * @var \Zend\GData\Photos\Extension\QuotaLimit
     */
    protected $_gphotoQuotaLimit = null;

    /**
     * gphoto:quotacurrent element
     *
     * @var \Zend\GData\Photos\Extension\QuotaCurrent
     */
    protected $_gphotoQuotaCurrent = null;

    /**
     * gphoto:maxPhotosPerAlbum element
     *
     * @var \Zend\GData\Photos\Extension\MaxPhotosPerAlbum
     */
    protected $_gphotoMaxPhotosPerAlbum = null;

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
            'http://schemas.google.com/photos/2007#user',
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
        if ($this->_gphotoNickname !== null) {
            $element->appendChild($this->_gphotoNickname->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoThumbnail !== null) {
            $element->appendChild($this->_gphotoThumbnail->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoUser !== null) {
            $element->appendChild($this->_gphotoUser->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoQuotaCurrent !== null) {
            $element->appendChild($this->_gphotoQuotaCurrent->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoQuotaLimit !== null) {
            $element->appendChild($this->_gphotoQuotaLimit->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoMaxPhotosPerAlbum !== null) {
            $element->appendChild($this->_gphotoMaxPhotosPerAlbum->getDOM($element->ownerDocument));
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
            case $this->lookupNamespace('gphoto') . ':' . 'nickname';
                $nickname = new Extension\Nickname();
                $nickname->transferFromDOM($child);
                $this->_gphotoNickname = $nickname;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'thumbnail';
                $thumbnail = new Extension\Thumbnail();
                $thumbnail->transferFromDOM($child);
                $this->_gphotoThumbnail = $thumbnail;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'user';
                $user = new Extension\User();
                $user->transferFromDOM($child);
                $this->_gphotoUser = $user;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'quotacurrent';
                $quotaCurrent = new Extension\QuotaCurrent();
                $quotaCurrent->transferFromDOM($child);
                $this->_gphotoQuotaCurrent = $quotaCurrent;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'quotalimit';
                $quotaLimit = new Extension\QuotaLimit();
                $quotaLimit->transferFromDOM($child);
                $this->_gphotoQuotaLimit = $quotaLimit;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'maxPhotosPerAlbum';
                $maxPhotosPerAlbum = new Extension\MaxPhotosPerAlbum();
                $maxPhotosPerAlbum->transferFromDOM($child);
                $this->_gphotoMaxPhotosPerAlbum = $maxPhotosPerAlbum;
                break;
            default:
                parent::takeChildFromDOM($child);
                break;
        }
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
     * Get the value for this element's gphoto:thumbnail attribute.
     *
     * @see setGphotoThumbnail
     * @return string The requested attribute.
     */
    public function getGphotoThumbnail()
    {
        return $this->_gphotoThumbnail;
    }

    /**
     * Set the value for this element's gphoto:thumbnail attribute.
     *
     * @param string $value The desired value for this attribute.
     * @return \Zend\GData\Photos\Extension\Thumbnail The element being modified.
     */
    public function setGphotoThumbnail($value)
    {
        $this->_gphotoThumbnail = $value;
        return $this;
    }

    /**
     * Get the value for this element's gphoto:quotacurrent attribute.
     *
     * @see setGphotoQuotaCurrent
     * @return string The requested attribute.
     */
    public function getGphotoQuotaCurrent()
    {
        return $this->_gphotoQuotaCurrent;
    }

    /**
     * Set the value for this element's gphoto:quotacurrent attribute.
     *
     * @param string $value The desired value for this attribute.
     * @return \Zend\GData\Photos\Extension\QuotaCurrent The element being modified.
     */
    public function setGphotoQuotaCurrent($value)
    {
        $this->_gphotoQuotaCurrent = $value;
        return $this;
    }

    /**
     * Get the value for this element's gphoto:quotalimit attribute.
     *
     * @see setGphotoQuotaLimit
     * @return string The requested attribute.
     */
    public function getGphotoQuotaLimit()
    {
        return $this->_gphotoQuotaLimit;
    }

    /**
     * Set the value for this element's gphoto:quotalimit attribute.
     *
     * @param string $value The desired value for this attribute.
     * @return \Zend\GData\Photos\Extension\QuotaLimit The element being modified.
     */
    public function setGphotoQuotaLimit($value)
    {
        $this->_gphotoQuotaLimit = $value;
        return $this;
    }

    /**
     * Get the value for this element's gphoto:maxPhotosPerAlbum attribute.
     *
     * @see setGphotoMaxPhotosPerAlbum
     * @return string The requested attribute.
     */
    public function getGphotoMaxPhotosPerAlbum()
    {
        return $this->_gphotoMaxPhotosPerAlbum;
    }

    /**
     * Set the value for this element's gphoto:maxPhotosPerAlbum attribute.
     *
     * @param string $value The desired value for this attribute.
     * @return \Zend\GData\Photos\Extension\MaxPhotosPerAlbum The element being modified.
     */
    public function setGphotoMaxPhotosPerAlbum($value)
    {
        $this->_gphotoMaxPhotosPerAlbum = $value;
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

}
