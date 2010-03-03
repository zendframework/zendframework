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
 * @package    Zend_Amf
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * This class implements authentication against XML file with roles for Flex Builder.
 *
 * @uses       Zend_Acl
 * @uses       Zend_Amf_Auth_Abstract
 * @uses       Zend_Auth_Result
 * @uses       Zend_Auth_Adapter_Exception
 * @package    Zend_Amf
 * @subpackage Adobe
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Amf_Adobe_Auth extends Zend_Amf_Auth_Abstract
{

    /**
     * ACL for authorization
     *
     * @var Zend_Acl
     */
    protected $_acl;

    /**
     * Username/password array
     *
     * @var array
     */
    protected $_users = array();

    /**
     * Create auth adapter
     *
     * @param string $rolefile File containing XML with users and roles
     */
    public function __construct($rolefile)
    {
        $this->_acl = new Zend_Acl();
        $xml = simplexml_load_file($rolefile);
/*
Roles file format:
 <roles>
   <role id=”admin”>
        <user name=”user1” password=”pwd”/>
    </role>
   <role id=”hr”>
        <user name=”user2” password=”pwd2”/>
    </role>
</roles>
*/
        foreach($xml->role as $role) {
            $this->_acl->addRole(new Zend_Acl_Role((string)$role["id"]));
            foreach($role->user as $user) {
                $this->_users[(string)$user["name"]] = array("password" => (string)$user["password"],
                                                             "role" => (string)$role["id"]);
            }
        }
    }

    /**
     * Get ACL with roles from XML file
     *
     * @return Zend_Acl
     */
    public function getAcl()
    {
        return $this->_acl;
    }

    /**
     * Perform authentication
     *
     * @throws Zend_Auth_Adapter_Exception
     * @return Zend_Auth_Result
     * @see Zend_Auth_Adapter_Interface#authenticate()
     */
    public function authenticate()
    {
        if (empty($this->_username) ||
            empty($this->_password)) {
            throw new Zend_Auth_Adapter_Exception('Username/password should be set');
        }

        if(!isset($this->_users[$this->_username])) {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND,
                null,
                array('Username not found')
                );
        }

        $user = $this->_users[$this->_username];
        if($user["password"] != $this->_password) {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,
                null,
                array('Authentication failed')
                );
        }

        $id = new stdClass();
        $id->role = $user["role"];
        $id->name = $this->_username;
        return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $id);
    }
}
