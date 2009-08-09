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
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Zend_Ldap_OnlineTestCase
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'OnlineTestCase.php';

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class Zend_Ldap_ChangePasswordTest extends Zend_Ldap_OnlineTestCase
{
    public function testAddNewUserWithPasswordOpenLdap()
    {
        if ($this->_getLdap()->getRootDse()->getServerType() !==
                Zend_Ldap_Node_RootDse::SERVER_TYPE_OPENLDAP) {
            $this->markTestSkipped('Test can only be run on an OpenLDAP server');
        }

        $dn = $this->_createDn('uid=newuser,');
        $data = array();
        $password = 'pa$$w0rd';
        Zend_Ldap_Attribute::setAttribute($data, 'uid', 'newuser', false);
        Zend_Ldap_Attribute::setAttribute($data, 'objectClass', 'account', true);
        Zend_Ldap_Attribute::setAttribute($data, 'objectClass', 'simpleSecurityObject', true);
        Zend_Ldap_Attribute::setPassword($data, $password,
            Zend_Ldap_Attribute::PASSWORD_HASH_SSHA, 'userPassword');

        try {
            $this->_getLdap()->add($dn, $data);

            $this->assertType('Zend_Ldap', $this->_getLdap()->bind($dn, $password));

            $this->_getLdap()->bind();
            $this->_getLdap()->delete($dn);
        } catch (Zend_Ldap_Exception $e) {
            $this->_getLdap()->bind();
            if ($this->_getLdap()->exists($dn)) {
                $this->_getLdap()->delete($dn);
            }
            $this->fail($e->getMessage());
        }
    }

    public function testChangePasswordWithUserAccountOpenLdap()
    {
        if ($this->_getLdap()->getRootDse()->getServerType() !==
                Zend_Ldap_Node_RootDse::SERVER_TYPE_OPENLDAP) {
            $this->markTestSkipped('Test can only be run on an OpenLDAP server');
        }

        $dn = $this->_createDn('uid=newuser,');
        $data = array();
        $password = 'pa$$w0rd';
        Zend_Ldap_Attribute::setAttribute($data, 'uid', 'newuser', false);
        Zend_Ldap_Attribute::setAttribute($data, 'objectClass', 'account', true);
        Zend_Ldap_Attribute::setAttribute($data, 'objectClass', 'simpleSecurityObject', true);
        Zend_Ldap_Attribute::setPassword($data, $password,
            Zend_Ldap_Attribute::PASSWORD_HASH_SSHA, 'userPassword');

        try {
            $this->_getLdap()->add($dn, $data);

            $this->_getLdap()->bind($dn, $password);

            $newPasswd = 'newpasswd';
            $newData = array();
            Zend_Ldap_Attribute::setPassword($newData, $newPasswd,
                Zend_Ldap_Attribute::PASSWORD_HASH_SHA, 'userPassword');
            $this->_getLdap()->update($dn, $newData);

            try {
                $this->_getLdap()->bind($dn, $password);
                $this->fail('Expected exception not thrown');
            } catch (Zend_Ldap_Exception $zle) {
                $message = $zle->getMessage();
                $this->assertTrue(strstr($message, 'Invalid credentials') ||
                    strstr($message, 'Server is unwilling to perform'));
            }

            $this->assertType('Zend_Ldap', $this->_getLdap()->bind($dn, $newPasswd));

            $this->_getLdap()->bind();
            $this->_getLdap()->delete($dn);
        } catch (Zend_Ldap_Exception $e) {
            $this->_getLdap()->bind();
            if ($this->_getLdap()->exists($dn)) {
                $this->_getLdap()->delete($dn);
            }
            $this->fail($e->getMessage());
        }
    }

    public function testAddNewUserWithPasswordActiveDirectory()
    {
        if ($this->_getLdap()->getRootDse()->getServerType() !==
                Zend_Ldap_Node_RootDse::SERVER_TYPE_ACTIVEDIRECTORY) {
            $this->markTestSkipped('Test can only be run on an ActiveDirectory server');
        }
        $options = $this->_getLdap()->getOptions();
        if ($options['useSsl'] !== true && $options['useStartTls'] !== true) {
            $this->markTestSkipped('Test can only be run on an SSL or TLS secured connection');
        }

        $dn = $this->_createDn('cn=New User,');
        $data = array();
        $password = 'pa$$w0rd';
        Zend_Ldap_Attribute::setAttribute($data, 'cn', 'New User', false);
        Zend_Ldap_Attribute::setAttribute($data, 'displayName', 'New User', false);
        Zend_Ldap_Attribute::setAttribute($data, 'sAMAccountName', 'newuser', false);
        Zend_Ldap_Attribute::setAttribute($data, 'userAccountControl', 512, false);
        Zend_Ldap_Attribute::setAttribute($data, 'objectClass', 'person', true);
        Zend_Ldap_Attribute::setAttribute($data, 'objectClass', 'organizationalPerson', true);
        Zend_Ldap_Attribute::setAttribute($data, 'objectClass', 'user', true);
        Zend_Ldap_Attribute::setPassword($data, $password,
            Zend_Ldap_Attribute::PASSWORD_UNICODEPWD, 'unicodePwd');

        try {
            $this->_getLdap()->add($dn, $data);

            $this->assertType('Zend_Ldap', $this->_getLdap()->bind($dn, $password));

            $this->_getLdap()->bind();
            $this->_getLdap()->delete($dn);
        } catch (Zend_Ldap_Exception $e) {
            $this->_getLdap()->bind();
            if ($this->_getLdap()->exists($dn)) {
                $this->_getLdap()->delete($dn);
            }
            $this->fail($e->getMessage());
        }
    }

    public function testChangePasswordWithUserAccountActiveDirectory()
    {
        if ($this->_getLdap()->getRootDse()->getServerType() !==
                Zend_Ldap_Node_RootDse::SERVER_TYPE_ACTIVEDIRECTORY) {
            $this->markTestSkipped('Test can only be run on an ActiveDirectory server');
        }
        $options = $this->_getLdap()->getOptions();
        if ($options['useSsl'] !== true && $options['useStartTls'] !== true) {
            $this->markTestSkipped('Test can only be run on an SSL or TLS secured connection');
        }

        $dn = $this->_createDn('cn=New User,');
        $data = array();
        $password = 'pa$$w0rd';
        Zend_Ldap_Attribute::setAttribute($data, 'cn', 'New User', false);
        Zend_Ldap_Attribute::setAttribute($data, 'displayName', 'New User', false);
        Zend_Ldap_Attribute::setAttribute($data, 'sAMAccountName', 'newuser', false);
        Zend_Ldap_Attribute::setAttribute($data, 'userAccountControl', 512, false);
        Zend_Ldap_Attribute::setAttribute($data, 'objectClass', 'person', true);
        Zend_Ldap_Attribute::setAttribute($data, 'objectClass', 'organizationalPerson', true);
        Zend_Ldap_Attribute::setAttribute($data, 'objectClass', 'user', true);
        Zend_Ldap_Attribute::setPassword($data, $password,
            Zend_Ldap_Attribute::PASSWORD_UNICODEPWD, 'unicodePwd');

        try {
            $this->_getLdap()->add($dn, $data);

            $this->_getLdap()->bind($dn, $password);

            $newPasswd = 'newpasswd';
            $newData = array();
            Zend_Ldap_Attribute::setPassword($newData, $newPasswd, Zend_Ldap_Attribute::PASSWORD_UNICODEPWD);
            $this->_getLdap()->update($dn, $newData);

            try {
                $this->_getLdap()->bind($dn, $password);
                $this->fail('Expected exception not thrown');
            } catch (Zend_Ldap_Exception $zle) {
                $message = $zle->getMessage();
                $this->assertTrue(strstr($message, 'Invalid credentials') ||
                    strstr($message, 'Server is unwilling to perform'));
            }

            $this->assertType('Zend_Ldap', $this->_getLdap()->bind($dn, $newPasswd));

            $this->_getLdap()->bind();
            $this->_getLdap()->delete($dn);
        } catch (Zend_Ldap_Exception $e) {
            $this->_getLdap()->bind();
            if ($this->_getLdap()->exists($dn)) {
                $this->_getLdap()->delete($dn);
            }
            $this->fail($e->getMessage());
        }
    }
}