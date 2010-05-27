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
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\LDAP;
use Zend\LDAP\Node\RootDSE;
use Zend\LDAP;

/**
 * @category   Zend
 * @package    Zend_LDAP
 * @subpackage UnitTests
 * @group      Zend_LDAP
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class ChangePasswordTest extends OnlineTestCase
{
    public function testAddNewUserWithPasswordOpenLDAP()
    {
        if ($this->_getLDAP()->getRootDse()->getServerType() !==
                RootDSE\RootDSE::SERVER_TYPE_OPENLDAP) {
            $this->markTestSkipped('Test can only be run on an OpenLDAP server');
        }

        $dn = $this->_createDn('uid=newuser,');
        $data = array();
        $password = 'pa$$w0rd';
        LDAP\Attribute::setAttribute($data, 'uid', 'newuser', false);
        LDAP\Attribute::setAttribute($data, 'objectClass', 'account', true);
        LDAP\Attribute::setAttribute($data, 'objectClass', 'simpleSecurityObject', true);
        LDAP\Attribute::setPassword($data, $password,
            LDAP\Attribute::PASSWORD_HASH_SSHA, 'userPassword');

        try {
            $this->_getLDAP()->add($dn, $data);

            $this->assertType('Zend\LDAP\LDAP', $this->_getLDAP()->bind($dn, $password));

            $this->_getLDAP()->bind();
            $this->_getLDAP()->delete($dn);
        } catch (LDAP\Exception $e) {
            $this->_getLDAP()->bind();
            if ($this->_getLDAP()->exists($dn)) {
                $this->_getLDAP()->delete($dn);
            }
            $this->fail($e->getMessage());
        }
    }

    public function testChangePasswordWithUserAccountOpenLDAP()
    {
        if ($this->_getLDAP()->getRootDse()->getServerType() !==
                RootDSE\RootDSE::SERVER_TYPE_OPENLDAP) {
            $this->markTestSkipped('Test can only be run on an OpenLDAP server');
        }

        $dn = $this->_createDn('uid=newuser,');
        $data = array();
        $password = 'pa$$w0rd';
        LDAP\Attribute::setAttribute($data, 'uid', 'newuser', false);
        LDAP\Attribute::setAttribute($data, 'objectClass', 'account', true);
        LDAP\Attribute::setAttribute($data, 'objectClass', 'simpleSecurityObject', true);
        LDAP\Attribute::setPassword($data, $password,
            LDAP\Attribute::PASSWORD_HASH_SSHA, 'userPassword');

        try {
            $this->_getLDAP()->add($dn, $data);

            $this->_getLDAP()->bind($dn, $password);

            $newPasswd = 'newpasswd';
            $newData = array();
            LDAP\Attribute::setPassword($newData, $newPasswd,
                LDAP\Attribute::PASSWORD_HASH_SHA, 'userPassword');
            $this->_getLDAP()->update($dn, $newData);

            try {
                $this->_getLDAP()->bind($dn, $password);
                $this->fail('Expected exception not thrown');
            } catch (LDAP\Exception $zle) {
                $message = $zle->getMessage();
                $this->assertTrue(strstr($message, 'Invalid credentials') ||
                    strstr($message, 'Server is unwilling to perform'));
            }

            $this->assertType('Zend_LDAP', $this->_getLDAP()->bind($dn, $newPasswd));

            $this->_getLDAP()->bind();
            $this->_getLDAP()->delete($dn);
        } catch (LDAP\Exception $e) {
            $this->_getLDAP()->bind();
            if ($this->_getLDAP()->exists($dn)) {
                $this->_getLDAP()->delete($dn);
            }
            $this->fail($e->getMessage());
        }
    }

    public function testAddNewUserWithPasswordActiveDirectory()
    {
        if ($this->_getLDAP()->getRootDse()->getServerType() !==
                RootDSE\RootDSE::SERVER_TYPE_ACTIVEDIRECTORY) {
            $this->markTestSkipped('Test can only be run on an ActiveDirectory server');
        }
        $options = $this->_getLDAP()->getOptions();
        if ($options['useSsl'] !== true && $options['useStartTls'] !== true) {
            $this->markTestSkipped('Test can only be run on an SSL or TLS secured connection');
        }

        $dn = $this->_createDn('cn=New User,');
        $data = array();
        $password = 'pa$$w0rd';
        LDAP\Attribute::setAttribute($data, 'cn', 'New User', false);
        LDAP\Attribute::setAttribute($data, 'displayName', 'New User', false);
        LDAP\Attribute::setAttribute($data, 'sAMAccountName', 'newuser', false);
        LDAP\Attribute::setAttribute($data, 'userAccountControl', 512, false);
        LDAP\Attribute::setAttribute($data, 'objectClass', 'person', true);
        LDAP\Attribute::setAttribute($data, 'objectClass', 'organizationalPerson', true);
        LDAP\Attribute::setAttribute($data, 'objectClass', 'user', true);
        LDAP\Attribute::setPassword($data, $password,
            LDAP\Attribute::PASSWORD_UNICODEPWD, 'unicodePwd');

        try {
            $this->_getLDAP()->add($dn, $data);

            $this->assertType('Zend_LDAP', $this->_getLDAP()->bind($dn, $password));

            $this->_getLDAP()->bind();
            $this->_getLDAP()->delete($dn);
        } catch (LDAP\Exception $e) {
            $this->_getLDAP()->bind();
            if ($this->_getLDAP()->exists($dn)) {
                $this->_getLDAP()->delete($dn);
            }
            $this->fail($e->getMessage());
        }
    }

    public function testChangePasswordWithUserAccountActiveDirectory()
    {
        if ($this->_getLDAP()->getRootDse()->getServerType() !==
                RootDSE\RootDSE::SERVER_TYPE_ACTIVEDIRECTORY) {
            $this->markTestSkipped('Test can only be run on an ActiveDirectory server');
        }
        $options = $this->_getLDAP()->getOptions();
        if ($options['useSsl'] !== true && $options['useStartTls'] !== true) {
            $this->markTestSkipped('Test can only be run on an SSL or TLS secured connection');
        }

        $dn = $this->_createDn('cn=New User,');
        $data = array();
        $password = 'pa$$w0rd';
        LDAP\Attribute::setAttribute($data, 'cn', 'New User', false);
        LDAP\Attribute::setAttribute($data, 'displayName', 'New User', false);
        LDAP\Attribute::setAttribute($data, 'sAMAccountName', 'newuser', false);
        LDAP\Attribute::setAttribute($data, 'userAccountControl', 512, false);
        LDAP\Attribute::setAttribute($data, 'objectClass', 'person', true);
        LDAP\Attribute::setAttribute($data, 'objectClass', 'organizationalPerson', true);
        LDAP\Attribute::setAttribute($data, 'objectClass', 'user', true);
        LDAP\Attribute::setPassword($data, $password,
            LDAP\Attribute::PASSWORD_UNICODEPWD, 'unicodePwd');

        try {
            $this->_getLDAP()->add($dn, $data);

            $this->_getLDAP()->bind($dn, $password);

            $newPasswd = 'newpasswd';
            $newData = array();
            LDAP\Attribute::setPassword($newData, $newPasswd, LDAP\Attribute::PASSWORD_UNICODEPWD);
            $this->_getLDAP()->update($dn, $newData);

            try {
                $this->_getLDAP()->bind($dn, $password);
                $this->fail('Expected exception not thrown');
            } catch (LDAP\Exception $zle) {
                $message = $zle->getMessage();
                $this->assertTrue(strstr($message, 'Invalid credentials') ||
                    strstr($message, 'Server is unwilling to perform'));
            }

            $this->assertType('Zend_LDAP', $this->_getLDAP()->bind($dn, $newPasswd));

            $this->_getLDAP()->bind();
            $this->_getLDAP()->delete($dn);
        } catch (LDAP\Exception $e) {
            $this->_getLDAP()->bind();
            if ($this->_getLDAP()->exists($dn)) {
                $this->_getLDAP()->delete($dn);
            }
            $this->fail($e->getMessage());
        }
    }
}
