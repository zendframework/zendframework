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
namespace Zend\GData\Docs;

use Zend\GData\Gapps;

/**
 * Data model class for a Google Docs ACL Entry.
 *
 * Each acl entry describes a single ACL for a given document
 * domain.
 *
 * This class represents <atom:entry> in the Google Data protocol.
 *
 * @uses       \Zend\GData\Entry
 * @uses       \Zend\GData\Extension\FeedLink
 * @uses       \Zend\GData\GApps
 * @uses       \Zend\GData\Docs\Extension\ACLRole
 * @uses       \Zend\GData\Docs\Extension\ACLScope
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage GApps
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ACLEntry extends \Zend\GData\Entry
{

    protected $_entryClassName = 'Zend\GData\Docs\ACLEntry';

    /**
     * <gAcl:role> element containing information about the
     * role of the ACL.
     *
     * @var \Zend\GData\Docs\Extension\ACLRole
     */
    protected $_role = null;

    /**
     * <gAcl:scope> element containing the information about
     * the scope of the ACL (user type and user id);
     *
     * @var \Zend\GData\Docs\Extension\ACLScope
     */
    protected $_scope = null;

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
    public function getDOM($doc = null, $majorVersion = 3, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        if ($this->_role !== null) {
            $element->appendChild($this->_role->getDOM($element->ownerDocument));
        }
        if ($this->_scope !== null) {
            $element->appendChild($this->_scope->getDOM($element->ownerDocument));
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
            case $this->lookupNamespace('gAcl') . ':' . 'role';
                $role = new Extension\ACLRole();
                $role->transferFromDOM($child);
                $this->_role = $role;
                break;
            case $this->lookupNamespace('gAcl') . ':' . 'scope';
                $scope = new Extension\ACLScope();
                $scope->transferFromDOM($child);
                $this->_scope = $scope;
                break;
            default:
                parent::takeChildFromDOM($child);
                break;
        }
    }

    /**
     * Get the value of the role property for this object.
     *
     * @see setRole
     * @return \Zend\GData\GApps\Extension\Login The requested object.
     */
    public function getRole()
    {
        return $this->_role;
    }

    /**
     * Set the value of the role property for this object.
     *
     * @param \Zend\GData\Docs\Extension\ACLRole $value The desired value for
     *          this instance's login property.
     * @return \Zend\GData\Docs\ACLEntry Provides a fluent interface.
     */
    public function setRole($value)
    {
        $this->_role = $value;
        return $this;
    }

    /**
     * Get the value of the scope property for this object.
     *
     * @see setScope
     * @return \Zend\GData\Docs\Extension\ACLScope The requested object.
     */
    public function getScope()
    {
        return $this->_scope;
    }

    /**
     * Set the value of the scope property for this object.
     *
     * @param \Zend\GData\Docs\Extension\ACLScope $value The desired value for
     *          this instance's name property.
     * @return \Zend\GData\Docs\ACLEntry Provides a fluent interface.
     */
    public function setScope($value)
    {
        $this->_scope = $value;
        return $this;
    }

}
