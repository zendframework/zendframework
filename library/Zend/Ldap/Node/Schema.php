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
namespace Zend\Ldap\Node;

use Zend\Ldap;
use Zend\Ldap\Node\RootDSE;

/**
 * Zend_Ldap_Node_Schema provides a simple data-container for the Schema node.
 *
 * @uses       \Zend\Ldap\Node\AbstractNode
 * @uses       \Zend\Ldap\Node\RootDSE\RootDSE
 * @uses       \Zend\Ldap\Node\RootDSE\ActiveDirectory
 * @uses       \Zend\Ldap\Node\Schema\ActiveDirectory
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage Schema
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Schema extends AbstractNode
{
    const OBJECTCLASS_TYPE_UNKNOWN    = 0;
    const OBJECTCLASS_TYPE_STRUCTURAL = 1;
    const OBJECTCLASS_TYPE_ABSTRACT   = 3;
    const OBJECTCLASS_TYPE_AUXILIARY  = 4;

    /**
     * Factory method to create the Schema node.
     *
     * @param  \Zend\Ldap\Ldap $ldap
     * @return \Zend\Ldap\Node\Schema
     * @throws \Zend\Ldap\Exception
     */
    public static function create(Ldap\Ldap $ldap)
    {
        $dn = $ldap->getRootDse()->getSchemaDn();
        $data = $ldap->getEntry($dn, array('*', '+'), true);
        switch ($ldap->getRootDse()->getServerType()) {
            case RootDSE::SERVER_TYPE_ACTIVEDIRECTORY:
                return new Schema\ActiveDirectory($dn, $data, $ldap);
            case RootDSE::SERVER_TYPE_OPENLDAP:
                return new Schema\OpenLdap($dn, $data, $ldap);
            case RootDSE::SERVER_TYPE_EDIRECTORY:
            default:
                return new self($dn, $data, $ldap);
        }
    }

    /**
     * Constructor.
     *
     * Constructor is protected to enforce the use of factory methods.
     *
     * @param  \Zend\Ldap\Dn $dn
     * @param  array        $data
     * @param  \Zend\Ldap\Ldap    $ldap
     */
    protected function __construct(Ldap\Dn $dn, array $data, Ldap\Ldap $ldap)
    {
        parent::__construct($dn, $data, true);
        $this->_parseSchema($dn, $ldap);
    }

    /**
     * Parses the schema
     *
     * @param  \Zend\Ldap\Dn $dn
     * @param  \Zend\Ldap\Ldap    $ldap
     * @return \Zend\Ldap\Node\Schema Provides a fluid interface
     */
    protected function _parseSchema(Ldap\Dn $dn, Ldap\Ldap $ldap)
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
