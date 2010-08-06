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
 * @namespace
 */
namespace Zend\Amf\Adobe;

use Zend\Authentication;

/**
 * This class implements authentication against XML file with roles for Flex Builder.
 *
 * @uses       \Zend\Acl\Acl
 * @uses       \Zend\Amf\AbstractAuthentication
 * @uses       \Zend\Authentication\Result
 * @uses       \Zend\Authentication\Adapter\Exception
 * @package    Zend_Amf
 * @subpackage Adobe
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Authentication extends \Zend\Amf\AbstractAuthentication
{

    /**
     * ACL for authorization
     *
     * @var \Zend\Acl\Acl
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
        $this->_acl = new \Zend\Acl\Acl();
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
            $this->_acl->addRole(new \Zend\Acl\Role\GenericRole((string)$role["id"]));
            foreach($role->user as $user) {
                $this->_users[(string)$user['name']] = array(
                    'password' => (string)$user['password'],
                    'role'     => (string)$role['id']
                );
            }
        }
    }

    /**
     * Get ACL with roles from XML file
     *
     * @return \Zend\Acl\Acl
     */
    public function getAcl()
    {
        return $this->_acl;
    }

    /**
     * Perform authentication
     *
     * @see Zend_Auth_Adapter_Interface#authenticate()
     * @return Zend\Authentication\Result
     * @throws Zend\Authentication\Adapter\Exception
     */
    public function authenticate()
    {
        if (empty($this->_username) 
            || empty($this->_password)
        ) {
            throw new Authentication\Adapter\Exception('Username/password should be set');
        }

        if (!isset($this->_users[$this->_username])) {
            return new Authentication\Result(
                Authentication\Result::FAILURE_IDENTITY_NOT_FOUND,
                null,
                array('Username not found')
            );
        }

        $user = $this->_users[$this->_username];
        if($user["password"] != $this->_password) {
            return new Authentication\Result(
                Authentication\Result::FAILURE_CREDENTIAL_INVALID,
                null,
                array('Authentication failed')
            );
        }

        $id       = new \stdClass();
        $id->role = $user["role"];
        $id->name = $this->_username;
        return new Authentication\Result(Authentication\Result::SUCCESS, $id);
    }
}
