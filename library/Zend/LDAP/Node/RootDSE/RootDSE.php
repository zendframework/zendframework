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
 * @subpackage RootDSE
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\LDAP\Node\RootDSE;
use Zend\LDAP;

/**
 * Zend_LDAP_Node_RootDse provides a simple data-container for the RootDSE node.
 *
 * @uses       \Zend\LDAP\DN
 * @uses       \Zend\LDAP\Node\AbstractNode
 * @uses       \Zend\LDAP\Node\RootDSE\ActiveDirectory
 * @uses       \Zend\LDAP\Node\RootDSE\eDirectory
 * @uses       \Zend\LDAP\Node\RootDSE\OpenLDAP
 * @category   Zend
 * @package    Zend_LDAP
 * @subpackage RootDSE
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class RootDSE extends LDAP\Node\AbstractNode
{
    const SERVER_TYPE_GENERIC         = 1;
    const SERVER_TYPE_OPENLDAP        = 2;
    const SERVER_TYPE_ACTIVEDIRECTORY = 3;
    const SERVER_TYPE_EDIRECTORY      = 4;

    /**
     * Factory method to create the RootDSE.
     *
     * @param  \Zend\LDAP\LDAP $ldap
     * @return \Zend\LDAP\Node\RootDSE\RootDSE
     * @throws \Zend\LDAP\Exception
     */
    public static function create(LDAP\LDAP $ldap)
    {
        $dn = LDAP\DN::fromString('');
        $data = $ldap->getEntry($dn, array('*', '+'), true);
        if (isset($data['domainfunctionality'])) {
            return new ActiveDirectory($dn, $data);
        } else if (isset($data['dsaname'])) {
            return new eDirectory($dn, $data);
        } else if (isset($data['structuralobjectclass']) &&
                $data['structuralobjectclass'][0] === 'OpenLDAProotDSE') {
            return new OpenLDAP($dn, $data);
        } else {
            return new self($dn, $data);
        }
    }

    /**
     * Constructor.
     *
     * Constructor is protected to enforce the use of factory methods.
     *
     * @param  \Zend\LDAP\DN $dn
     * @param  array        $data
     */
    protected function __construct(LDAP\DN $dn, array $data)
    {
        parent::__construct($dn, $data, true);
    }

    /**
     * Gets the namingContexts.
     *
     * @return array
     */
    public function getNamingContexts()
    {
        return $this->getAttribute('namingContexts', null);
    }

    /**
     * Gets the subschemaSubentry.
     *
     * @return string|null
     */
    public function getSubschemaSubentry()
    {
        return $this->getAttribute('subschemaSubentry', 0);
    }

    /**
     * Determines if the version is supported
     *
     * @param  string|int|array $versions version(s) to check
     * @return boolean
     */
    public function supportsVersion($versions)
    {
        return $this->attributeHasValue('supportedLDAPVersion', $versions);
    }

    /**
     * Determines if the sasl mechanism is supported
     *
     * @param  string|array $mechlist SASL mechanisms to check
     * @return boolean
     */
    public function supportsSaslMechanism($mechlist)
    {
        return $this->attributeHasValue('supportedSASLMechanisms', $mechlist);
    }

    /**
     * Gets the server type
     *
     * @return int
     */
    public function getServerType()
    {
        return self::SERVER_TYPE_GENERIC;
    }

    /**
     * Returns the schema DN
     *
     * @return \Zend\LDAP\DN
     */
    public function getSchemaDn()
    {
        $schemaDn = $this->getSubschemaSubentry();
        return LDAP\DN::fromString($schemaDn);
    }
}
