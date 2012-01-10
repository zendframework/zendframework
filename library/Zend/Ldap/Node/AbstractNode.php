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
namespace Zend\Ldap\Node;
use Zend\Ldap;

/**
 * Zend_Ldap_Node_Abstract provides a bas eimplementation for LDAP nodes
 *
 * @uses       ArrayAccess
 * @uses       BadMethodCallException
 * @uses       Countable
 * @uses       \Zend\Ldap\Attribute
 * @uses       \Zend\Ldap\Dn
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage Node
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractNode implements \ArrayAccess, \Countable
{
    protected static $_systemAttributes=array('createtimestamp', 'creatorsname',
        'entrycsn', 'entrydn', 'entryuuid', 'hassubordinates', 'modifiersname',
        'modifytimestamp', 'structuralobjectclass', 'subschemasubentry',
        'distinguishedname', 'instancetype', 'name', 'objectcategory', 'objectguid',
        'usnchanged', 'usncreated', 'whenchanged', 'whencreated');

    /**
     * Holds the node's DN.
     *
     * @var \Zend\Ldap\Dn
     */
    protected $_dn;

    /**
     * Holds the node's current data.
     *
     * @var array
     */
    protected $_currentData;

    /**
     * Constructor.
     *
     * Constructor is protected to enforce the use of factory methods.
     *
     * @param  \Zend\Ldap\Dn $dn
     * @param  array        $data
     * @param  boolean      $fromDataSource
     */
    protected function __construct(Ldap\Dn $dn, array $data, $fromDataSource)
    {
        $this->_dn = $dn;
        $this->_loadData($data, $fromDataSource);
    }

    /**
     * @param  array   $data
     * @param  boolean $fromDataSource
     * @throws \Zend\Ldap\Exception
     */
    protected function _loadData(array $data, $fromDataSource)
    {
        if (array_key_exists('dn', $data)) {
            unset($data['dn']);
        }
        ksort($data, SORT_STRING);
        $this->_currentData = $data;
    }

    /**
     * Reload node attributes from LDAP.
     *
     * This is an online method.
     *
     * @param  \Zend\Ldap\Ldap $ldap
     * @return \Zend\Ldap\Node\AbstractNode Provides a fluid interface
     * @throws \Zend\Ldap\Exception
     */
    public function reload(Ldap\Ldap $ldap = null)
    {
        if ($ldap !== null) {
            $data = $ldap->getEntry($this->_getDn(), array('*', '+'), true);
            $this->_loadData($data, true);
        }
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
        return $this->_dn;
    }

    /**
     * Gets the DN of the current node as a Zend_Ldap_Dn.
     * The method returns a clone of the node's DN to prohibit modification.
     *
     * This is an offline method.
     *
     * @return \Zend\Ldap\Dn
     */
    public function getDn()
    {
        $dn = clone $this->_getDn();
        return $dn;
    }

    /**
     * Gets the DN of the current node as a string.
     *
     * This is an offline method.
     *
     * @param  string $caseFold
     * @return string
     */
    public function getDnString($caseFold = null)
    {
        return $this->_getDn()->toString($caseFold);
    }

    /**
     * Gets the DN of the current node as an array.
     *
     * This is an offline method.
     *
     * @param  string $caseFold
     * @return array
     */
    public function getDnArray($caseFold = null)
    {
        return $this->_getDn()->toArray($caseFold);
    }

    /**
     * Gets the RDN of the current node as a string.
     *
     * This is an offline method.
     *
     * @param  string $caseFold
     * @return string
     */
    public function getRdnString($caseFold = null)
    {
        return $this->_getDn()->getRdnString($caseFold);
    }

    /**
     * Gets the RDN of the current node as an array.
     *
     * This is an offline method.
     *
     * @param  string $caseFold
     * @return array
     */
    public function getRdnArray($caseFold = null)
    {
        return $this->_getDn()->getRdn($caseFold);
    }

    /**
     * Gets the objectClass of the node
     *
     * @return array
     */
    public function getObjectClass()
    {
        return $this->getAttribute('objectClass', null);
    }

    /**
     * Gets all attributes of node.
     *
     * The collection contains all attributes.
     *
     * This is an offline method.
     *
     * @param  boolean $includeSystemAttributes
     * @return array
     */
    public function getAttributes($includeSystemAttributes = true)
    {
        $data = array();
        foreach ($this->getData($includeSystemAttributes) as $name => $value) {
            $data[$name] = $this->getAttribute($name, null);
        }
        return $data;
    }

    /**
     * Returns the DN of the current node. {@see getDnString()}
     *
     * @return string
     */
    public function toString()
    {
        return $this->getDnString();
    }

    /**
     * Cast to string representation {@see toString()}
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Returns an array representation of the current node
     *
     * @param  boolean $includeSystemAttributes
     * @return array
     */
    public function toArray($includeSystemAttributes = true)
    {
        $attributes = $this->getAttributes($includeSystemAttributes);
        return array_merge(array('dn' => $this->getDnString()), $attributes);
    }

    /**
     * Returns a JSON representation of the current node
     *
     * @param  boolean $includeSystemAttributes
     * @return string
     */
    public function toJson($includeSystemAttributes = true)
    {
        return json_encode($this->toArray($includeSystemAttributes));
    }

    /**
     * Gets node attributes.
     *
     * The array contains all attributes in its internal format (no conversion).
     *
     * This is an offline method.
     *
     * @param  boolean $includeSystemAttributes
     * @return array
     */
    public function getData($includeSystemAttributes = true)
    {
        if ($includeSystemAttributes === false) {
            $data = array();
            foreach ($this->_currentData as $key => $value) {
                if (!in_array($key, self::$_systemAttributes)) {
                    $data[$key] = $value;
                }
            }
            return $data;
        } else {
            return $this->_currentData;
        }
    }

    /**
     * Checks whether a given attribute exists.
     *
     * If $emptyExists is false empty attributes (containing only array()) are
     * treated as non-existent returning false.
     * If $emptyExists is true empty attributes are treated as existent returning
     * true. In this case method returns false only if the attribute name is
     * missing in the key-collection.
     *
     * @param  string  $name
     * @param  boolean $emptyExists
     * @return boolean
     */
    public function existsAttribute($name, $emptyExists = false)
    {
        $name = strtolower($name);
        if (isset($this->_currentData[$name])) {
            if ($emptyExists) return true;
            return count($this->_currentData[$name])>0;
        }
        else return false;
    }

    /**
     * Checks if the given value(s) exist in the attribute
     *
     * @param  string      $attribName
     * @param  mixed|array $value
     * @return boolean
     */
    public function attributeHasValue($attribName, $value)
    {
        return Ldap\Attribute::attributeHasValue($this->_currentData, $attribName, $value);
    }

    /**
     * Gets a LDAP attribute.
     *
     * This is an offline method.
     *
     * @param  string  $name
     * @param  integer $index
     * @return mixed
     * @throws \Zend\Ldap\Exception
     */
    public function getAttribute($name, $index = null)
    {
        if ($name == 'dn') {
            return $this->getDnString();
        }
        else {
            return Ldap\Attribute::getAttribute($this->_currentData, $name, $index);
        }
    }

    /**
     * Gets a LDAP date/time attribute.
     *
     * This is an offline method.
     *
     * @param  string  $name
     * @param  integer $index
     * @return array|integer
     * @throws \Zend\Ldap\Exception
     */
    public function getDateTimeAttribute($name, $index = null)
    {
        return Ldap\Attribute::getDateTimeAttribute($this->_currentData, $name, $index);
    }

    /**
     * Sets a LDAP attribute.
     *
     * This is an offline method.
     *
     * @param  string $name
     * @param  mixed  $value
     * @return null
     * @throws BadMethodCallException
     */
    public function __set($name, $value)
    {
        throw new \BadMethodCallException();
    }

    /**
     * Gets a LDAP attribute.
     *
     * This is an offline method.
     *
     * @param  string $name
     * @return array
     * @throws \Zend\Ldap\Exception
     */
    public function __get($name)
    {
        return $this->getAttribute($name, null);
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
     * @throws BadMethodCallException
     */
    public function __unset($name)
    {
        throw new \BadMethodCallException();
    }

    /**
     * Checks whether a given attribute exists.
     *
     * Empty attributes will be treated as non-existent.
     *
     * @param  string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return $this->existsAttribute($name, false);
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
     * @throws BadMethodCallException
     */
    public function offsetSet($name, $value)
    {
        throw new \BadMethodCallException();
    }

    /**
     * Gets a LDAP attribute.
     * Implements ArrayAccess.
     *
     * This is an offline method.
     *
     * @param  string $name
     * @return array
     * @throws \Zend\Ldap\Exception
     */
    public function offsetGet($name)
    {
        return $this->getAttribute($name, null);
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
     * @throws BadMethodCallException
     */
    public function offsetUnset($name)
    {
        throw new \BadMethodCallException();
    }

    /**
     * Checks whether a given attribute exists.
     * Implements ArrayAccess.
     *
     * Empty attributes will be treated as non-existent.
     *
     * @param  string $name
     * @return boolean
     */
    public function offsetExists($name)
    {
        return $this->existsAttribute($name, false);
    }

    /**
     * Returns the number of attributes in node.
     * Implements Countable
     *
     * @return int
     */
    public function count()
    {
        return count($this->_currentData);
    }
}
