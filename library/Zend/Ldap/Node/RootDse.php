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
 * @subpackage RootDse
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Ldap\Node;
use Zend\Ldap;

/**
 * Zend_Ldap_Node_RootDse provides a simple data-container for the RootDse node.
 *
 * @uses       \Zend\Ldap\Dn
 * @uses       \Zend\Ldap\Node\AbstractNode
 * @uses       \Zend\Ldap\Node\RootDse\ActiveDirectory
 * @uses       \Zend\Ldap\Node\RootDse\eDirectory
 * @uses       \Zend\Ldap\Node\RootDse\OpenLdap
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage RootDse
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class RootDse extends AbstractNode
{
    const SERVER_TYPE_GENERIC         = 1;
    const SERVER_TYPE_OPENLDAP        = 2;
    const SERVER_TYPE_ACTIVEDIRECTORY = 3;
    const SERVER_TYPE_EDIRECTORY      = 4;

    /**
     * Factory method to create the RootDse.
     *
     * @param  \Zend\Ldap\Ldap $ldap
     * @return \Zend\Ldap\Node\RootDse
     * @throws \Zend\Ldap\Exception
     */
    public static function create(Ldap\Ldap $ldap)
    {
        $dn = Ldap\Dn::fromString('');
        $data = $ldap->getEntry($dn, array('*', '+'), true);
        if (isset($data['domainfunctionality'])) {
            return new RootDse\ActiveDirectory($dn, $data);
        } else if (isset($data['dsaname'])) {
            return new RootDse\eDirectory($dn, $data);
        } else if (isset($data['structuralobjectclass']) &&
                $data['structuralobjectclass'][0] === 'OpenLDAProotDSE') {
            return new RootDse\OpenLdap($dn, $data);
        } else {
            return new self($dn, $data);
        }
    }

    /**
     * Constructor.
     *
     * Constructor is protected to enforce the use of factory methods.
     *
     * @param  \Zend\Ldap\Dn $dn
     * @param  array        $data
     */
    protected function __construct(Ldap\Dn $dn, array $data)
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
     * @return \Zend\Ldap\Dn
     */
    public function getSchemaDn()
    {
        $schemaDn = $this->getSubschemaSubentry();
        return Ldap\Dn::fromString($schemaDn);
    }
}
