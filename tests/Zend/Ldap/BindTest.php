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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * Zend_Ldap
 */
require_once 'Zend/Ldap.php';

/* Note: The ldap_connect function does not actually try to connect. This
 * is why many tests attempt to bind with invalid credentials. If the
 * bind returns 'Invalid credentials' we know the transport related work
 * was successful.
 */

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Ldap
 */
class Zend_Ldap_BindTest extends PHPUnit_Framework_TestCase
{
    protected $_options = null;
    protected $_principalName = TESTS_ZEND_LDAP_PRINCIPAL_NAME;
    protected $_altUsername = TESTS_ZEND_LDAP_ALT_USERNAME;
    protected $_bindRequiresDn = false;

    public function setUp()
    {
        $this->_options = array(
            'host' => TESTS_ZEND_LDAP_HOST,
            'username' => TESTS_ZEND_LDAP_USERNAME,
            'password' => TESTS_ZEND_LDAP_PASSWORD,
            'baseDn' => TESTS_ZEND_LDAP_BASE_DN,
        );
        if (defined('TESTS_ZEND_LDAP_PORT'))
            $this->_options['port'] = TESTS_ZEND_LDAP_PORT;
        if (defined('TESTS_ZEND_LDAP_USE_START_TLS'))
            $this->_options['useStartTls'] = TESTS_ZEND_LDAP_USE_START_TLS;
        if (defined('TESTS_ZEND_LDAP_USE_SSL'))
            $this->_options['useSsl'] = TESTS_ZEND_LDAP_USE_SSL;
        if (defined('TESTS_ZEND_LDAP_BIND_REQUIRES_DN'))
            $this->_options['bindRequiresDn'] = TESTS_ZEND_LDAP_BIND_REQUIRES_DN;
        if (defined('TESTS_ZEND_LDAP_ACCOUNT_FILTER_FORMAT'))
            $this->_options['accountFilterFormat'] = TESTS_ZEND_LDAP_ACCOUNT_FILTER_FORMAT;
        if (defined('TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME'))
            $this->_options['accountDomainName'] = TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME;
        if (defined('TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME_SHORT'))
            $this->_options['accountDomainNameShort'] = TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME_SHORT;
        if (defined('TESTS_ZEND_LDAP_ALT_USERNAME'))
            $this->_altUsername = TESTS_ZEND_LDAP_ALT_USERNAME;

        if (isset($this->_options['bindRequiresDn']))
            $this->_bindRequiresDn = $this->_options['bindRequiresDn'];
    }

    public function testEmptyOptionsBind()
    {
        $ldap = new Zend_Ldap(array());
        try {
            $ldap->bind();
            $this->fail('Expected exception for empty options');
        } catch (Zend_Ldap_Exception $zle) {
            $this->assertContains('A host parameter is required', $zle->getMessage());
        }
    }
    public function testAnonymousBind()
    {
        $options = $this->_options;
        unset($options['password']);

        $ldap = new Zend_Ldap($options);
        try {
            $ldap->bind();
        } catch (Zend_Ldap_Exception $zle) {
            // or I guess the server doesn't allow unauthenticated binds
            $this->assertContains('unauthenticated bind', $zle->getMessage());
        }
    }
    public function testNoBaseDnBind()
    {
        $options = $this->_options;
        unset($options['baseDn']);
        $options['bindRequiresDn'] = true;

        $ldap = new Zend_Ldap($options);
        try {
            $ldap->bind('invalid', 'ignored');
            $this->fail('Expected exception for baseDn missing');
        } catch (Zend_Ldap_Exception $zle) {
            $this->assertContains('Base DN not set', $zle->getMessage());
        }
    }
    public function testNoDomainNameBind()
    {
        $options = $this->_options;
        unset($options['accountDomainName']);
        $options['bindRequiresDn'] = false;
        $options['accountCanonicalForm'] = Zend_Ldap::ACCTNAME_FORM_PRINCIPAL;

        $ldap = new Zend_Ldap($options);
        try {
            $ldap->bind('invalid', 'ignored');
            $this->fail('Expected exception for missing accountDomainName');
        } catch (Zend_Ldap_Exception $zle) {
            $this->assertContains('Option required: accountDomainName', $zle->getMessage());
        }
    }
    public function testPlainBind()
    {
        $ldap = new Zend_Ldap($this->_options);
        $ldap->bind();
        $this->assertNotNull($ldap->getResource());
    }
    public function testConnectBind()
    {
        $ldap = new Zend_Ldap($this->_options);
        $ldap->connect()->bind();
        $this->assertNotNull($ldap->getResource());
    }
    public function testExplicitParamsBind()
    {
        $options = $this->_options;
        $username = $options['username'];
        $password = $options['password'];

        unset($options['username']);
        unset($options['password']);

        $ldap = new Zend_Ldap($options);
        $ldap->bind($username, $password);
        $this->assertNotNull($ldap->getResource());
    }
    public function testRequiresDnBind()
    {
        $options = $this->_options;

        $options['bindRequiresDn'] = true;

        $ldap = new Zend_Ldap($options);
        try {
            $ldap->bind($this->_altUsername, 'invalid');
            $this->fail('Expected exception not thrown');
        } catch (Zend_Ldap_Exception $zle) {
            $this->assertContains('Invalid credentials', $zle->getMessage());
        }
    }
    public function testRequiresDnWithoutDnBind()
    {
        $options = $this->_options;

        $options['bindRequiresDn'] = true;

        unset($options['username']);

        $ldap = new Zend_Ldap($options);
        try {
            $ldap->bind($this->_principalName);
            $this->fail('Expected exception not thrown');
        } catch (Zend_Ldap_Exception $zle) {
            /* Note that if your server actually allows anonymous binds this test will fail.
             */
            $this->assertContains('Failed to retrieve DN', $zle->getMessage());
        }
    }

    public function testBindWithEmptyPassword()
    {
        $options = $this->_options;
        $options['allowEmptyPassword'] = false;
        $ldap = new Zend_Ldap($options);
        try {
            $ldap->bind($this->_altUsername, '');
            $this->fail('Expected exception for empty password');
        } catch (Zend_Ldap_Exception $zle) {
            $this->assertContains('Empty password not allowed - see allowEmptyPassword option.',
                $zle->getMessage());
        }

        $options['allowEmptyPassword'] = true;
        $ldap = new Zend_Ldap($options);
        try {
            $ldap->bind($this->_altUsername, '');
        } catch (Zend_Ldap_Exception $zle) {
            if ($zle->getMessage() ===
                    'Empty password not allowed - see allowEmptyPassword option.') {
                $this->fail('Exception for empty password');
            } else {
                $message = $zle->getMessage();
                $this->assertTrue(strstr($message, 'Invalid credentials') ||
                    strstr($message, 'Server is unwilling to perform'));
                return;
            }
        }
        $this->assertNotNull($ldap->getResource());
    }

    public function testBindWithoutDnUsernameAndDnRequired()
    {
        $options = $this->_options;
        $options['username'] = TESTS_ZEND_LDAP_ALT_USERNAME;
        $options['bindRequiresDn'] = true;
        $ldap = new Zend_Ldap($options);
        try {
            $ldap->bind();
            $this->fail('Expected exception for empty password');
        } catch (Zend_Ldap_Exception $zle) {
            $this->assertContains('Binding requires username in DN form',
                $zle->getMessage());
        }
    }

    /**
     * @group ZF-8259
     */
    public function testBoundUserIsFalseIfNotBoundToLDAP()
    {
        $ldap = new Zend_Ldap($this->_options);
        $this->assertFalse($ldap->getBoundUser());
    }

    /**
     * @group ZF-8259
     */
    public function testBoundUserIsReturnedAfterBinding()
    {
        $ldap = new Zend_Ldap($this->_options);
        $ldap->bind();
        $this->assertEquals(TESTS_ZEND_LDAP_USERNAME, $ldap->getBoundUser());
    }

    /**
     * @group ZF-8259
     */
    public function testResourceIsAlwaysReturned()
    {
        $ldap = new Zend_Ldap($this->_options);
        $this->assertNotNull($ldap->getResource());
        $this->assertTrue(is_resource($ldap->getResource()));
        $this->assertEquals(TESTS_ZEND_LDAP_USERNAME, $ldap->getBoundUser());
    }
}
