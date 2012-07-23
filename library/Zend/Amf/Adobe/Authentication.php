<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Amf
 */

namespace Zend\Amf\Adobe;

use stdClass;
use Zend\Acl\Acl;
use Zend\Acl\Role;
use Zend\Amf\AbstractAuthentication;
use Zend\Authentication\Adapter as AuthenticationAdapter;
use Zend\Authentication\Result as AuthenticationResult;

/**
 * This class implements authentication against XML file with roles for Flex Builder.
 *
 * @package    Zend_Amf
 * @subpackage Adobe
 */
class Authentication extends AbstractAuthentication
{

    /**
     * ACL for authorization
     *
     * @var Acl
     */
    protected $acl;

    /**
     * Username/password array
     *
     * @var array
     */
    protected $users = array();

    /**
     * Create auth adapter
     *
     * @param string $rolefile File containing XML with users and roles
     */
    public function __construct($rolefile)
    {
        $this->acl = new Acl();
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
            $this->acl->addRole(new Role\GenericRole((string)$role["id"]));
            foreach($role->user as $user) {
                $this->users[(string)$user['name']] = array(
                    'password' => (string)$user['password'],
                    'role'     => (string)$role['id']
                );
            }
        }
    }

    /**
     * Get ACL with roles from XML file
     *
     * @return Acl
     */
    public function getAcl()
    {
        return $this->acl;
    }

    /**
     * Perform authentication
     *
     * @see Zend_Auth_Adapter_Interface#authenticate()
     * @return AuthenticationResult
     * @throws AuthenticationAdapter\Exception\InvalidArgumentException
     */
    public function authenticate()
    {
        if (empty($this->_username)
            || empty($this->_password)
        ) {
            throw new AuthenticationAdapter\Exception\InvalidArgumentException('Username/password should be set');
        }

        if (!isset($this->users[$this->_username])) {
            return new AuthenticationResult(
                AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND,
                null,
                array('Username not found')
            );
        }

        $user = $this->users[$this->_username];
        if($user["password"] != $this->_password) {
            return new AuthenticationResult(
                AuthenticationResult::FAILURE_CREDENTIAL_INVALID,
                null,
                array('Authentication failed')
            );
        }

        $id       = new stdClass();
        $id->role = $user["role"];
        $id->name = $this->_username;
        return new AuthenticationResult(AuthenticationResult::SUCCESS, $id);
    }
}
