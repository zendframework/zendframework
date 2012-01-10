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
 * @subpackage Schema
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Ldap\Node\Schema;

use Zend\Ldap\Node\Schema,
    Zend\Ldap;

/**
 * Zend_Ldap_Node_Schema_ActiveDirectory provides a simple data-container for the Schema node of
 * an Active Directory server.
 *
 * @uses       \Zend\Ldap\Ldap
 * @uses       \Zend\Ldap\Node\Schema
 * @uses       \Zend\Ldap\Node\Schema\AttributeType\ActiveDirectory
 * @uses       \Zend\Ldap\Node\Schema\ObjectClass\ActiveDirectory
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage Schema
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ActiveDirectory extends Schema
{
    /**
     * The attribute Types
     *
     * @var array
     */
    protected $_attributeTypes = array();
    /**
     * The object classes
     *
     * @var array
     */
    protected $_objectClasses = array();

    /**
     * Parses the schema
     *
     * @param  \Zend\Ldap\Dn $dn
     * @param  \Zend\Ldap\Ldap    $ldap
     * @return \Zend\Ldap\Node\Schema Provides a fluid interface
     */
    protected function _parseSchema(Ldap\Dn $dn, Ldap\Ldap $ldap)
    {
        parent::_parseSchema($dn, $ldap);
        foreach ($ldap->search('(objectClass=classSchema)', $dn,
                Ldap\Ldap::SEARCH_SCOPE_ONE) as $node) {
            $val = new ObjectClass\ActiveDirectory($node);
            $this->_objectClasses[$val->getName()] = $val;
        }
        foreach ($ldap->search('(objectClass=attributeSchema)', $dn,
                Ldap\Ldap::SEARCH_SCOPE_ONE) as $node) {
            $val = new AttributeType\ActiveDirectory($node);
            $this->_attributeTypes[$val->getName()] = $val;
        }
        return $this;
    }

    /**
     * Gets the attribute Types
     *
     * @return array
     */
    public function getAttributeTypes()
    {
        return $this->_attributeTypes;
    }

    /**
     * Gets the object classes
     *
     * @return array
     */
    public function getObjectClasses()
    {
        return $this->_objectClasses;
    }
}
