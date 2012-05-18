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

namespace Zend\Ldap\Node\Schema;

use Zend\Ldap;
use Zend\Ldap\Node;

/**
 * Zend\Ldap\Node\Schema\ActiveDirectory provides a simple data-container for the Schema node of
 * an Active Directory server.
 *
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage Schema
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ActiveDirectory extends Node\Schema
{
    /**
     * The attribute Types
     *
     * @var array
     */
    protected $attributeTypes = array();
    /**
     * The object classes
     *
     * @var array
     */
    protected $objectClasses = array();

    /**
     * Parses the schema
     *
     * @param \Zend\Ldap\Dn   $dn
     * @param \Zend\Ldap\Ldap $ldap
     * @return ActiveDirectory Provides a fluid interface
     */
    protected function parseSchema(Ldap\Dn $dn, Ldap\Ldap $ldap)
    {
        parent::parseSchema($dn, $ldap);
        foreach ($ldap->search(
            '(objectClass=classSchema)', $dn,
            Ldap\Ldap::SEARCH_SCOPE_ONE
        ) as $node) {
            $val                                  = new ObjectClass\ActiveDirectory($node);
            $this->objectClasses[$val->getName()] = $val;
        }
        foreach ($ldap->search(
            '(objectClass=attributeSchema)', $dn,
            Ldap\Ldap::SEARCH_SCOPE_ONE
        ) as $node) {
            $val                                   = new AttributeType\ActiveDirectory($node);
            $this->attributeTypes[$val->getName()] = $val;
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
        return $this->attributeTypes;
    }

    /**
     * Gets the object classes
     *
     * @return array
     */
    public function getObjectClasses()
    {
        return $this->objectClasses;
    }
}
