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
namespace Zend\Ldap\Node\RootDse;

use Zend\Ldap\Node\RootDse;

/**
 * Zend_Ldap_Node_RootDse provides a simple data-container for the RootDse node of
 * an OpenLDAP server.
 *
 * @uses       \Zend\Ldap\Node\RootDse
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage RootDse
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class OpenLdap extends RootDse
{
    /**
     * Gets the configContext.
     *
     * @return string|null
     */
    public function getConfigContext()
    {
        return $this->getAttribute('configContext', 0);
    }

    /**
     * Gets the monitorContext.
     *
     * @return string|null
     */
    public function getMonitorContext()
    {
        return $this->getAttribute('monitorContext', 0);
    }

    /**
     * Determines if the control is supported
     *
     * @param  string|array $oids control oid(s) to check
     * @return boolean
     */
    public function supportsControl($oids)
    {
        return $this->attributeHasValue('supportedControl', $oids);
    }

    /**
     * Determines if the extension is supported
     *
     * @param  string|array $oids oid(s) to check
     * @return boolean
     */
    public function supportsExtension($oids)
    {
        return $this->attributeHasValue('supportedExtension', $oids);
    }

    /**
     * Determines if the feature is supported
     *
     * @param  string|array $oids feature oid(s) to check
     * @return boolean
     */
    public function supportsFeature($oids)
    {
        return $this->attributeHasValue('supportedFeatures', $oids);
    }

    /**
     * Gets the server type
     *
     * @return int
     */
    public function getServerType()
    {
        return self::SERVER_TYPE_OPENLDAP;
    }
}
