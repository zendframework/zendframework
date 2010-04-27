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
 * @package    Zend_LDAP
 * @subpackage Schema
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\LDAP\Node\Schema;
use Zend\LDAP;
use Zend\LDAP\Node\RootDSE;

/**
 * Zend_LDAP_Node_Schema provides a simple data-container for the Schema node.
 *
 * @uses       \Zend\LDAP\Node\AbstractNode
 * @uses       \Zend\LDAP\Node\RootDSE\RootDSE
 * @uses       \Zend\LDAP\Node\RootDSE\ActiveDirectory
 * @uses       \Zend\LDAP\Node\Schema\ActiveDirectory
 * @category   Zend
 * @package    Zend_LDAP
 * @subpackage Schema
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Schema extends LDAP\Node\AbstractNode
{
    const OBJECTCLASS_TYPE_UNKNOWN    = 0;
    const OBJECTCLASS_TYPE_STRUCTURAL = 1;
    const OBJECTCLASS_TYPE_ABSTRACT   = 3;
    const OBJECTCLASS_TYPE_AUXILIARY  = 4;

    /**
     * Factory method to create the Schema node.
     *
     * @param  \Zend\LDAP\LDAP $ldap
     * @return \Zend\LDAP\Node\Schema\Schema
     * @throws \Zend\LDAP\Exception
     */
    public static function create(LDAP\LDAP $ldap)
    {
        $dn = $ldap->getRootDse()->getSchemaDn();
        $data = $ldap->getEntry($dn, array('*', '+'), true);
        switch ($ldap->getRootDse()->getServerType()) {
            case RootDSE\RootDSE::SERVER_TYPE_ACTIVEDIRECTORY:
                return new ActiveDirectory($dn, $data, $ldap);
            case RootDSE\RootDSE::SERVER_TYPE_OPENLDAP:
                return new OpenLDAP($dn, $data, $ldap);
            case RootDSE\RootDSE::SERVER_TYPE_EDIRECTORY:
            default:
                return new self($dn, $data, $ldap);
        }
    }

    /**
     * Constructor.
     *
     * Constructor is protected to enforce the use of factory methods.
     *
     * @param  \Zend\LDAP\DN $dn
     * @param  array        $data
     * @param  \Zend\LDAP\LDAP    $ldap
     */
    protected function __construct(LDAP\DN $dn, array $data, LDAP\LDAP $ldap)
    {
        parent::__construct($dn, $data, true);
        $this->_parseSchema($dn, $ldap);
    }

    /**
     * Parses the schema
     *
     * @param  \Zend\LDAP\DN $dn
     * @param  \Zend\LDAP\LDAP    $ldap
     * @return \Zend\LDAP\Node\Schema\Schema Provides a fluid interface
     */
    protected function _parseSchema(LDAP\DN $dn, LDAP\LDAP $ldap)
    {
        return $this;
    }

    /**
     * Gets the attribute Types
     *
     * @return array
     */
    public function getAttributeTypes()
    {
        return array();
    }

    /**
     * Gets the object classes
     *
     * @return array
     */
    public function getObjectClasses()
    {
        return array();
    }
}
