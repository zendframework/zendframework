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
 * @package    Zend_Ldap
 * @subpackage Node
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Ldap;

/**
 * Zend_Ldap_Node provides an object oriented view into a LDAP node.
 *
 * @uses       \Zend\Ldap\Ldap
 * @uses       \Zend\Ldap\Attribute
 * @uses       \Zend\Ldap\Dn
 * @uses       \Zend\Ldap\Exception
 * @uses       \Zend\Ldap\Ldif\Encoder
 * @uses       \Zend\Ldap\Node\AbstractNode
 * @uses       \Zend\Ldap\Node\ChildrenIterator
 * @uses       \Zend\Ldap\Node\Collection
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage Node
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Node extends Node\AbstractNode implements \Iterator, \RecursiveIterator
{
    /**
     * Holds the node's new Dn if node is renamed.
     *
     * @var \Zend\Ldap\Dn
     */
    protected $_newDn;
    /**
     * Holds the node's orginal attributes (as loaded).
     *
     * @var array
     */
    protected $_originalData;
    /**
     * This node will be added
     *
     * @var boolean
     */
    protected $_new;
    /**
     * This node will be deleted
     *
     * @var boolean
     */
    protected $_delete;
    /**
     * Holds the connection to the LDAP server if in connected mode.
     *
     * @var \Zend\Ldap\Ldap
     */
    protected $_ldap;

    /**
     * Holds an array of the current node's children.
     *
     * @var array
     */
    protected $_children;

    /**
     * Controls iteration status
     *
     * @var boolean
     */
    private $_iteratorRewind = false;

    /**
     * Constructor.
     *
     * Constructor is protected to enforce the use of factory methods.
     *
     * @param  \Zend\Ldap\Dn $dn
     * @param  array        $data
     * @param  boolean      $fromDataSource
     * @param  \Zend\Ldap\Ldap    $ldap
     * @throws \Zend\Ldap\Exception
     */
    protected function __construct(Dn $dn, array $data, $fromDataSource, Ldap $ldap = null)
    {
        parent::__construct($dn, $data, $fromDataSource);
        if ($ldap !== null) $this->attachLdap($ldap);
        else $this->detachLdap();
    }

    /**
     * Serialization callback
     *
     * Only Dn and attributes will be serialized.
     *
     * @return array
     */
    public function __sleep()
    {
        return array('_dn', '_currentData', '_newDn', '_originalData',
            '_new', '_delete', '_children');
    }

    /**
     * Deserialization callback
     *
     * Enforces a detached node.
     *
     * @return null
     */
    public function __wakeup()
    {
        $this->detachLdap();
    }

    /**
     * Gets the current LDAP connection.
     *
     * @return \Zend\Ldap\Ldap
     * @throws \Zend\Ldap\Exception
     */
    public function getLdap()
    {
        if ($this->_ldap === null) {
            throw new Exception(null, 'No LDAP connection specified.', Exception::LDAP_OTHER);
        }
        else return $this->_ldap;
    }

    /**
     * Attach node to an LDAP connection
     *
     * This is an offline method.
     *
     * @uses   \Zend\Ldap\Dn::isChildOf()
     * @param  \Zend\Ldap\Ldap $ldap
     * @return \Zend\Ldap\Node Provides a fluid interface
     * @throws \Zend\Ldap\Exception
     */
    public function attachLdap(Ldap $ldap)
    {
        if (!Dn::isChildOf($this->_getDn(), $ldap->getBaseDn())) {
            throw new Exception(null, 'LDAP connection is not responsible for given node.',
                Exception::LDAP_OTHER);
        }

        if ($ldap !== $this->_ldap) {
            $this->_ldap = $ldap;
            if (is_array($this->_children)) {
                foreach ($this->_children as $child) {
                    $child->attachLdap($ldap);
                }
            }
        }
        return $this;
    }

    /**
     * Detach node from LDAP connection
     *
     * This is an offline method.
     *
     * @return \Zend\Ldap\Node Provides a fluid interface
     */
    public function detachLdap()
    {
        $this->_ldap = null;
        if (is_array($this->_children)) {
            foreach ($this->_children as $child) {
                $child->detachLdap();
            }
        }
        return $this;
    }

    /**
     * Checks if the current node is attached to a LDAP server.
     *
     * This is an offline method.
     *
     * @return boolean
     */
    public function isAttached()
    {
        return ($this->_ldap !== null);
    }

    /**
     * @param  array   $data
     * @param  boolean $fromDataSource
     * @throws \Zend\Ldap\Exception
     */
    protected function _loadData(array $data, $fromDataSource)
    {
        parent::_loadData($data, $fromDataSource);
        if ($fromDataSource === true) {
            $this->_originalData = $data;
        } else {
            $this->_originalData = array();
        }
        $this->_children = null;
        $this->_markAsNew(($fromDataSource === true) ? false : true);
        $this->_markAsToBeDeleted(false);
    }

    /**
     * Factory method to create a new detached Zend_Ldap_Node for a given DN.
     *
     * @param  string|array|\Zend\Ldap\Dn $dn
     * @param  array                     $objectClass
     * @return \Zend\Ldap\Node
     * @throws \Zend\Ldap\Exception
     */
    public static function create($dn, array $objectClass = array())
    {
        if (is_string($dn) || is_array($dn)) {
            $dn = Dn::factory($dn);
        } else if ($dn instanceof Dn) {
            $dn = clone $dn;
        } else {
            throw new Exception(null, '$dn is of a wrong data type.');
        }
        $new = new self($dn, array(), false, null);
        $new->_ensureRdnAttributeValues();
        $new->setAttribute('objectClass', $objectClass);
        return $new;
    }

    /**
     * Factory method to create an attached Zend_Ldap_Node for a given DN.
     *
     * @param  string|array|\Zend\Ldap\Dn $dn
     * @param  \Zend\Ldap\Ldap                 $ldap
     * @return \Zend\Ldap\Node|null
     * @throws \Zend\Ldap\Exception
     */
    public static function fromLdap($dn, Ldap $ldap)
    {
        if (is_string($dn) || is_array($dn)) {
            $dn = Dn::factory($dn);
        } else if ($dn instanceof Dn) {
            $dn = clone $dn;
        } else {
            throw new Exception(null, '$dn is of a wrong data type.');
        }
        $data = $ldap->getEntry($dn, array('*', '+'), true);
        if ($data === null) {
            return null;
        }
        $entry = new self($dn, $data, true, $ldap);
        return $entry;
    }

    /**
     * Factory method to create a detached Zend_Ldap_Node from array data.
     *
     * @param  array   $data
     * @param  boolean $fromDataSource
     * @return \Zend\Ldap\Node
     * @throws \Zend\Ldap\Exception
     */
    public static function fromArray(array $data, $fromDataSource = false)
    {
        if (!array_key_exists('dn', $data)) {
            throw new Exception(null, '\'dn\' key is missing in array.');
        }
        if (is_string($data['dn']) || is_array($data['dn'])) {
            $dn = Dn::factory($data['dn']);
        } else if ($data['dn'] instanceof Dn) {
            $dn = clone $data['dn'];
        } else {
            throw new Exception(null, '\'dn\' key is of a wrong data type.');
        }
        $fromDataSource = ($fromDataSource === true) ? true : false;
        $new = new self($dn, $data, $fromDataSource, null);
        $new->_ensureRdnAttributeValues();
        return $new;
    }

    /**
     * Ensures that teh RDN attributes are correctly set.
     *
     * @return void
     */
    protected function _ensureRdnAttributeValues()
    {
        foreach ($this->getRdnArray() as $key => $value) {
            Attribute::setAttribute($this->_currentData, $key, $value, false);
        }
    }

    /**
     * Marks this node as new.
     *
     * Node will be added (instead of updated) on calling update() if $new is true.
     *
     * @param boolean $new
     */
    protected function _markAsNew($new)
    {
        $this->_new = ($new === false) ? false : true;
    }

    /**
     * Tells if the node is consiedered as new (not present on the server)
     *
     * Please note, that this doesn't tell you if the node is present on the server.
     * Use {@link exits()} to see if a node is already there.
     *
     * @return boolean
     */
    public function isNew()
    {
        return $this->_new;
    }

    /**
     * Marks this node as to be deleted.
     *
     * Node will be deleted on calling update() if $delete is true.
     *
     * @param boolean $delete
     */
    protected function _markAsToBeDeleted($delete)
    {
        $this->_delete = ($delete === true) ? true : false;
    }


    /**
    * Is this node going to be deleted once update() is called?
    *
    * @return boolean
    */
    public function willBeDeleted()
    {
        return $this->_delete;
    }

    /**
     * Marks this node as to be deleted
     *
     * Node will be deleted on calling update() if $delete is true.
     *
     * @return \Zend\Ldap\Node Provides a fluid interface
     */
    public function delete()
    {
        $this->_markAsToBeDeleted(true);
        return $this;
    }

    /**
    * Is this node going to be moved once update() is called?
    *
    * @return boolean
    */
    public function willBeMoved()
    {
        if ($this->isNew() || $this->willBeDeleted()) {
            return false;
        } else if ($this->_newDn !== null) {
            return ($this->_dn != $this->_newDn);
        } else {
            return false;
        }
    }

    /**
     * Sends all pending changes to the LDAP server
     *
     * @param  \Zend\Ldap\Ldap $ldap
     * @return \Zend\Ldap\Node Provides a fluid interface
     * @throws \Zend\Ldap\Exception
     */
    public function update(Ldap $ldap = null)
    {
        if ($ldap !== null) {
            $this->attachLdap($ldap);
        }
        $ldap = $this->getLdap();
        if (!($ldap instanceof Ldap)) {
            throw new Exception(null, 'No LDAP connection available');
        }

        if ($this->willBeDeleted()) {
            if ($ldap->exists($this->_dn)) {
                $ldap->delete($this->_dn);
            }
            return $this;
        }

        if ($this->isNew()) {
            $data = $this->getData();
            $ldap->add($this->_getDn(), $data);
            $this->_loadData($data, true);
            return $this;
        }

        $changedData = $this->getChangedData();
        if ($this->willBeMoved()) {
            $recursive = $this->hasChildren();
            $ldap->rename($this->_dn, $this->_newDn, $recursive, false);
            foreach ($this->_newDn->getRdn() as $key => $value) {
                if (array_key_exists($key, $changedData)) {
                    unset($changedData[$key]);
                }
            }
            $this->_dn = $this->_newDn;
            $this->_newDn = null;
        }
        if (count($changedData) > 0) {
            $ldap->update($this->_getDn(), $changedData);
        }
        $this->_originalData = $this->_currentData;
        return $this;
    }

    /**
     * Gets the DN of the current node as a Zend_Ldap_Dn.
     *
     * This is an offline method.
     *
     * @return \Zend\Ldap\Dn
     */
    protected function _getDn()
    {
        return ($this->_newDn === null) ? parent::_getDn() : $this->_newDn;
    }

    /**
     * Gets the current DN of the current node as a Zend_Ldap_Dn.
     * The method returns a clone of the node's DN to prohibit modification.
     *
     * This is an offline method.
     *
     * @return \Zend\Ldap\Dn
     */
    public function getCurrentDn()
    {
        $dn = clone parent::_getDn();
        return $dn;
    }

    /**
     * Sets the new DN for this node
     *
     * This is an offline method.
     *
     * @param  \Zend\Ldap\Dn|string|array $newDn
     * @throws \Zend\Ldap\Exception
     * @return \Zend\Ldap\Node Provides a fluid interface
     */
    public function setDn($newDn)
    {
        if ($newDn instanceof Dn) {
            $this->_newDn = clone $newDn;
        } else {
            $this->_newDn = Dn::factory($newDn);
        }
        $this->_ensureRdnAttributeValues();
        return $this;
    }

    /**
     * {@see setDn()}
     *
     * This is an offline method.
     *
     * @param  \Zend\Ldap\Dn|string|array $newDn
     * @throws \Zend\Ldap\Exception
     * @return \Zend\Ldap\Node Provides a fluid interface
     */
    public function move($newDn)
    {
        return $this->setDn($newDn);
    }

    /**
     * {@see setDn()}
     *
     * This is an offline method.
     *
     * @param  \Zend\Ldap\Dn|string|array $newDn
     * @throws \Zend\Ldap\Exception
     * @return \Zend\Ldap\Node Provides a fluid interface
     */
    public function rename($newDn)
    {
        return $this->setDn($newDn);
    }

    /**
     * Sets the objectClass.
     *
     * This is an offline method.
     *
     * @param  array|string $value
     * @return \Zend\Ldap\Node Provides a fluid interface
     * @throws \Zend\Ldap\Exception
     */
    public function setObjectClass($value)
    {
        $this->setAttribute('objectClass', $value);
        return $this;
    }

    /**
     * Appends to the objectClass.
     *
     * This is an offline method.
     *
     * @param  array|string $value
     * @return \Zend\Ldap\Node Provides a fluid interface
     * @throws \Zend\Ldap\Exception
     */
    public function appendObjectClass($value)
    {
        $this->appendToAttribute('objectClass', $value);
        return $this;
    }

    /**
     * Returns a LDIF representation of the current node
     *
     * @param  array $options Additional options used during encoding
     * @return string
     */
    public function toLdif(array $options = array())
    {
        $attributes = array_merge(array('dn' => $this->getDnString()), $this->getData(false));
        return Ldif\Encoder::encode($attributes, $options);
    }

    /**
     * Gets changed node data.
     *
     * The array contains all changed attributes.
     * This format can be used in {@link Zend_Ldap::add()} and {@link Zend_Ldap::update()}.
     *
     * This is an offline method.
     *
     * @return array
     */
    public function getChangedData()
    {
        $changed = array();
        foreach ($this->_currentData as $key => $value) {
            if (!array_key_exists($key, $this->_originalData) && !empty($value)) {
                $changed[$key] = $value;
            } else if ($this->_originalData[$key] !== $this->_currentData[$key]) {
                $changed[$key] = $value;
            }
        }
        return $changed;
    }

    /**
     * Returns all changes made.
     *
     * This is an offline method.
     *
     * @return array
     */
    public function getChanges()
    {
        $changes = array(
            'add'     => array(),
            'delete'  => array(),
            'replace' => array());
        foreach ($this->_currentData as $key => $value) {
            if (!array_key_exists($key, $this->_originalData) && !empty($value)) {
                $changes['add'][$key] = $value;
            } else if (count($this->_originalData[$key]) === 0 && !empty($value)) {
                $changes['add'][$key] = $value;
            } else if ($this->_originalData[$key] !== $this->_currentData[$key]) {
                if (empty($value)) {
                    $changes['delete'][$key] = $value;
                } else {
                    $changes['replace'][$key] = $value;
                }
            }
        }
        return $changes;
    }

    /**
     * Sets a LDAP attribute.
     *
     * This is an offline method.
     *
     * @param  string $name
     * @param  mixed  $value
     * @return \Zend\Ldap\Node Provides a fluid interface
     * @throws \Zend\Ldap\Exception
     */
    public function setAttribute($name, $value)
    {
        $this->_setAttribute($name, $value, false);
        return $this;
    }

    /**
     * Appends to a LDAP attribute.
     *
     * This is an offline method.
     *
     * @param  string $name
     * @param  mixed  $value
     * @return \Zend\Ldap\Node Provides a fluid interface
     * @throws \Zend\Ldap\Exception
     */
    public function appendToAttribute($name, $value)
    {
        $this->_setAttribute($name, $value, true);
        return $this;
    }

    /**
     * Checks if the attribute can be set and sets it accordingly.
     *
     * @param  string  $name
     * @param  mixed   $value
     * @param  boolean $append
     * @throws \Zend\Ldap\Exception
     */
    protected function _setAttribute($name, $value, $append)
    {
        $this->_assertChangeableAttribute($name);
        Attribute::setAttribute($this->_currentData, $name, $value, $append);
    }

    /**
     * Sets a LDAP date/time attribute.
     *
     * This is an offline method.
     *
     * @param  string        $name
     * @param  integer|array $value
     * @param  boolean       $utc
     * @return \Zend\Ldap\Node Provides a fluid interface
     * @throws \Zend\Ldap\Exception
     */
    public function setDateTimeAttribute($name, $value, $utc = false)
    {
        $this->_setDateTimeAttribute($name, $value, $utc, false);
        return $this;
    }

    /**
     * Appends to a LDAP date/time attribute.
     *
     * This is an offline method.
     *
     * @param  string        $name
     * @param  integer|array $value
     * @param  boolean       $utc
     * @return \Zend\Ldap\Node Provides a fluid interface
     * @throws \Zend\Ldap\Exception
     */
    public function appendToDateTimeAttribute($name, $value, $utc = false)
    {
        $this->_setDateTimeAttribute($name, $value, $utc, true);
        return $this;
    }

    /**
     * Checks if the attribute can be set and sets it accordingly.
     *
     * @param  string        $name
     * @param  integer|array $value
     * @param  boolean       $utc
     * @param  boolean       $append
     * @throws \Zend\Ldap\Exception
     */
    protected function _setDateTimeAttribute($name, $value, $utc, $append)
    {
        $this->_assertChangeableAttribute($name);
        Attribute::setDateTimeAttribute($this->_currentData, $name, $value, $utc, $append);
    }

    /**
     * Sets a LDAP password.
     *
     * @param  string $password
     * @param  string $hashType
     * @param  string $attribName
     * @return \Zend\Ldap\Node Provides a fluid interface
     * @throws \Zend\Ldap\Exception
     */
    public function setPasswordAttribute($password, $hashType = Attribute::PASSWORD_HASH_MD5,
        $attribName = 'userPassword')
    {
        $this->_assertChangeableAttribute($attribName);
        Attribute::setPassword($this->_currentData, $password, $hashType, $attribName);
        return $this;
    }

    /**
     * Deletes a LDAP attribute.
     *
     * This method deletes the attribute.
     *
     * This is an offline method.
     *
     * @param  string $name
     * @return \Zend\Ldap\Node Provides a fluid interface
     * @throws \Zend\Ldap\Exception
     */
    public function deleteAttribute($name)
    {
        if ($this->existsAttribute($name, true)) {
            $this->_setAttribute($name, null, false);
        }
        return $this;
    }

    /**
     * Removes duplicate values from a LDAP attribute
     *
     * @param  string $attribName
     * @return void
     */
    public function removeDuplicatesFromAttribute($attribName)
    {
        Attribute::removeDuplicatesFromAttribute($this->_currentData, $attribName);
    }

    /**
     * Remove given values from a LDAP attribute
     *
     * @param  string      $attribName
     * @param  mixed|array $value
     * @return void
     */
    public function removeFromAttribute($attribName, $value)
    {
        Attribute::removeFromAttribute($this->_currentData, $attribName, $value);
    }

    /**
     * @param  string $name
     * @return boolean
     * @throws \Zend\Ldap\Exception
     */
    protected function _assertChangeableAttribute($name)
    {
        $name = strtolower($name);
        $rdn = $this->getRdnArray(Dn::ATTR_CASEFOLD_LOWER);
        if ($name == 'dn') {
            throw new Exception(null, 'DN cannot be changed.');
        }
        else if (array_key_exists($name, $rdn)) {
            throw new Exception(null, 'Cannot change attribute because it\'s part of the RDN');
        } else if (in_array($name, self::$_systemAttributes)) {
            throw new Exception(null, 'Cannot change attribute because it\'s read-only');
        }
        else return true;
    }

    /**
     * Sets a LDAP attribute.
     *
     * This is an offline method.
     *
     * @param  string $name
     * @param  mixed  $value
     * @return null
     * @throws \Zend\Ldap\Exception
     */
    public function __set($name, $value)
    {
        $this->setAttribute($name, $value);
    }

    /**
     * Deletes a LDAP attribute.
     *
     * This method deletes the attribute.
     *
     * This is an offline method.
     *
     * @param  string $name
     * @return null
     * @throws \Zend\Ldap\Exception
     */
    public function __unset($name)
    {
        $this->deleteAttribute($name);
    }

    /**
     * Sets a LDAP attribute.
     * Implements ArrayAccess.
     *
     * This is an offline method.
     *
     * @param  string $name
     * @param  mixed  $value
     * @return null
     * @throws \Zend\Ldap\Exception
     */
    public function offsetSet($name, $value)
    {
        $this->setAttribute($name, $value);
    }

    /**
     * Deletes a LDAP attribute.
     * Implements ArrayAccess.
     *
     * This method deletes the attribute.
     *
     * This is an offline method.
     *
     * @param  string $name
     * @return null
     * @throws \Zend\Ldap\Exception
     */
    public function offsetUnset($name)
    {
        $this->deleteAttribute($name);
    }

    /**
     * Check if node exists on LDAP.
     *
     * This is an online method.
     *
     * @param  \Zend\Ldap\Ldap $ldap
     * @return boolean
     * @throws \Zend\Ldap\Exception
     */
    public function exists(Ldap $ldap = null)
    {
        if ($ldap !== null) {
            $this->attachLdap($ldap);
        }
        $ldap = $this->getLdap();
        return $ldap->exists($this->_getDn());
    }

    /**
     * Reload node attributes from LDAP.
     *
     * This is an online method.
     *
     * @param  \Zend\Ldap\Ldap $ldap
     * @return \Zend\Ldap\Node Provides a fluid interface
     * @throws \Zend\Ldap\Exception
     */
    public function reload(Ldap $ldap = null)
    {
        if ($ldap !== null) {
            $this->attachLdap($ldap);
        }
        $ldap = $this->getLdap();
        parent::reload($ldap);
        return $this;
    }

    /**
     * Search current subtree with given options.
     *
     * This is an online method.
     *
     * @param  string|\Zend\Ldap\Filter\AbstractFilter $filter
     * @param  integer                          $scope
     * @param  string                           $sort
     * @return \Zend\Ldap\Node\Collection
     * @throws \Zend\Ldap\Exception
     */
    public function searchSubtree($filter, $scope = Ldap::SEARCH_SCOPE_SUB, $sort = null)
    {
        return $this->getLdap()->search($filter, $this->_getDn(), $scope, array('*', '+'), $sort,
            'Zend\Ldap\Node\Collection');
    }

    /**
     * Count items in current subtree found by given filter.
     *
     * This is an online method.
     *
     * @param  string|\Zend\Ldap\Filter\AbstractFilter $filter
     * @param  integer                          $scope
     * @return integer
     * @throws \Zend\Ldap\Exception
     */
    public function countSubtree($filter, $scope = Ldap::SEARCH_SCOPE_SUB)
    {
        return $this->getLdap()->count($filter, $this->_getDn(), $scope);
    }

    /**
     * Count children of current node.
     *
     * This is an online method.
     *
     * @return integer
     * @throws \Zend\Ldap\Exception
     */
    public function countChildren()
    {
        return $this->countSubtree('(objectClass=*)', Ldap::SEARCH_SCOPE_ONE);
    }

    /**
     * Gets children of current node.
     *
     * This is an online method.
     *
     * @param  string|\Zend\Ldap\Filter\AbstractFilter $filter
     * @param  string                           $sort
     * @return \Zend\Ldap\Node\Collection
     * @throws \Zend\Ldap\Exception
     */
    public function searchChildren($filter, $sort = null)
    {
        return $this->searchSubtree($filter, Ldap::SEARCH_SCOPE_ONE, $sort);
    }

    /**
     * Checks if current node has children.
     * Returns whether the current element has children.
     *
     * Can be used offline but returns false if children have not been retrieved yet.
     *
     * @return boolean
     * @throws \Zend\Ldap\Exception
     */
    public function hasChildren()
    {
        if (!is_array($this->_children)) {
            if ($this->isAttached()) {
                return ($this->countChildren() > 0);
            } else {
                return false;
            }
        } else {
            return (count($this->_children) > 0);
        }
    }

    /**
     * Returns the children for the current node.
     *
     * Can be used offline but returns an empty array if children have not been retrieved yet.
     *
     * @return \Zend\Ldap\Node\ChildrenIterator
     * @throws \Zend\Ldap\Exception
     */
    public function getChildren()
    {
        if (!is_array($this->_children)) {
            $this->_children = array();
            if ($this->isAttached()) {
                $children = $this->searchChildren('(objectClass=*)', null);
                foreach ($children as $child) {
                    $this->_children[$child->getRdnString(Dn::ATTR_CASEFOLD_LOWER)] = $child;
                }
            }
        }
        return new Node\ChildrenIterator($this->_children);
    }

    /**
     * Returns the parent of the current node.
     *
     * @param  \Zend\Ldap\Ldap $ldap
     * @return \Zend\Ldap\Node
     * @throws \Zend\Ldap\Exception
     */
    public function getParent(Ldap $ldap = null)
    {
        if ($ldap !== null) {
            $this->attachLdap($ldap);
        }
        $ldap = $this->getLdap();
        $parentDn = $this->_getDn()->getParentDn(1);
        return self::fromLdap($parentDn, $ldap);
    }

    /**
     * Return the current attribute.
     * Implements Iterator
     *
     * @return array
     */
    public function current()
    {
        return $this;
    }

    /**
     * Return the attribute name.
     * Implements Iterator
     *
     * @return string
     */
    public function key()
    {
        return $this->getRdnString();
    }

    /**
     * Move forward to next attribute.
     * Implements Iterator
     */
    public function next()
    {
        $this->_iteratorRewind = false;
    }

    /**
     * Rewind the Iterator to the first attribute.
     * Implements Iterator
     */
    public function rewind()
    {
        $this->_iteratorRewind = true;
    }

    /**
     * Check if there is a current attribute
     * after calls to rewind() or next().
     * Implements Iterator
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->_iteratorRewind;
    }
}
