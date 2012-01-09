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
 * @subpackage GApps
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\GData\GApps;

use Zend\GData\GApps;

/**
 * Data model class for a Google Apps User Entry.
 *
 * Each user entry describes a single user within a Google Apps hosted
 * domain.
 *
 * To transfer user entries to and from the Google Apps servers, including
 * creating new entries, refer to the Google Apps service class,
 * Zend_Gdata_GApps.
 *
 * This class represents <atom:entry> in the Google Data protocol.
 *
 * @uses       \Zend\GData\Entry
 * @uses       \Zend\GData\Extension\FeedLink
 * @uses       \Zend\GData\GApps
 * @uses       \Zend\GData\GApps\Extension\Login
 * @uses       \Zend\GData\GApps\Extension\Name
 * @uses       \Zend\GData\GApps\Extension\Quota
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage GApps
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class UserEntry extends \Zend\GData\Entry
{

    protected $_entryClassName = 'Zend\GData\GApps\UserEntry';

    /**
     * <apps:login> element containing information about this user's
     * account, including their username and permissions.
     *
     * @var \Zend\GData\GApps\Extension\Login
     */
    protected $_login = null;

    /**
     * <apps:name> element containing the user's actual name.
     *
     * @var \Zend\GData\GApps\Extension\Name
     */
    protected $_name = null;

    /**
     * <apps:quotq> element describing any storage quotas in place for
     * this user.
     *
     * @var \Zend\GData\GApps\Extension\Quota
     */
    protected $_quota = null;

    /**
     * <gd:feedLink> element containing information about other feeds
     * relevant to this entry.
     *
     * @var \Zend\GData\Extension\FeedLink
     */
    protected $_feedLink = array();

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
        if ($this->_login !== null) {
            $element->appendChild($this->_login->getDOM($element->ownerDocument));
        }
        if ($this->_name !== null) {
            $element->appendChild($this->_name->getDOM($element->ownerDocument));
        }
        if ($this->_quota !== null) {
            $element->appendChild($this->_quota->getDOM($element->ownerDocument));
        }
        foreach ($this->_feedLink as $feedLink) {
            $element->appendChild($feedLink->getDOM($element->ownerDocument));
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
            case $this->lookupNamespace('apps') . ':' . 'login';
                $login = new Extension\Login();
                $login->transferFromDOM($child);
                $this->_login = $login;
                break;
            case $this->lookupNamespace('apps') . ':' . 'name';
                $name = new Extension\Name();
                $name->transferFromDOM($child);
                $this->_name = $name;
                break;
            case $this->lookupNamespace('apps') . ':' . 'quota';
                $quota = new Extension\Quota();
                $quota->transferFromDOM($child);
                $this->_quota = $quota;
                break;
            case $this->lookupNamespace('gd') . ':' . 'feedLink';
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
     * Get the value of the login property for this object.
     *
     * @see setLogin
     * @return \Zend\GData\GApps\Extension\Login The requested object.
     */
    public function getLogin()
    {
        return $this->_login;
    }

    /**
     * Set the value of the login property for this object. This property
     * is used to store the username address of the current user.
     *
     * @param \Zend\GData\GApps\Extension\Login $value The desired value for
     *          this instance's login property.
     * @return \Zend\GData\GApps\UserEntry Provides a fluent interface.
     */
    public function setLogin($value)
    {
        $this->_login = $value;
        return $this;
    }

    /**
     * Get the value of the name property for this object.
     *
     * @see setName
     * @return \Zend\GData\GApps\Extension\Name The requested object.
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set the value of the name property for this object. This property
     * is used to store the full name of the current user.
     *
     * @param \Zend\GData\GApps\Extension\Name $value The desired value for
     *          this instance's name property.
     * @return \Zend\GData\GApps\UserEntry Provides a fluent interface.
     */
    public function setName($value)
    {
        $this->_name = $value;
        return $this;
    }

    /**
     * Get the value of the quota property for this object.
     *
     * @see setQuota
     * @return \Zend\GData\GApps\Extension\Quota The requested object.
     */
    public function getQuota()
    {
        return $this->_quota;
    }

    /**
     * Set the value of the quota property for this object. This property
     * is used to store the amount of storage available for the current
     * user. Quotas may not be modifiable depending on the domain used.
     *
     * @param \Zend\GData\GApps\Extension\Quota $value The desired value for
     *          this instance's quota property.
     * @return \Zend\GData\GApps\UserEntry Provides a fluent interface.
     */
    public function setQuota($value)
    {
        $this->_quota = $value;
        return $this;
    }

    /**
     * Returns all feed links for this entry, or if a rel value is
     * specified, the feed link associated with that value is returned.
     *
     * @param string $rel The rel value of the link to be found. If null,
     *          the array of links is returned instead.
     * @return mixed Either an array of \Zend\GData\Extension\FeedLink
     *          objects if $rel is null, a single
     *          Zend_Gdata_Extension_FeedLink object if $rel is specified
     *          and a matching feed link is found, or null if $rel is
     *          specified and no matching feed link is found.
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
     * Set the value of the feed link property for this object. This property
     * is used to provide links to alternative feeds relevant to this entry.
     *
     * @param array $value A collection of
     *          Zend_Gdata_GApps_Extension_FeedLink objects.
     * @return Zend_Gdata_GApps_EventEntry Provides a fluent interface.
     */
    public function setFeedLink($value)
    {
        $this->_feedLink = $value;
        return $this;
    }

}
