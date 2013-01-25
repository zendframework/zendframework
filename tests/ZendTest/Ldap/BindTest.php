<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Ldap
 */

namespace ZendTest\Ldap;

use Zend\Ldap;
use Zend\Ldap\Exception;

/* Note: The ldap_connect function does not actually try to connect. This
 * is why many tests attempt to bind with invalid credentials. If the
 * bind returns 'Invalid credentials' we know the transport related work
 * was successful.
 */

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @group      Zend_Ldap
 */
class BindTest extends \PHPUnit_Framework_TestCase
{
    protected $options = null;
    protected $principalName = TESTS_ZEND_LDAP_PRINCIPAL_NAME;
    protected $altUsername = TESTS_ZEND_LDAP_ALT_USERNAME;
    protected $bindRequiresDn = false;

    public function setUp()
    {
        if (!constant('TESTS_ZEND_LDAP_ONLINE_ENABLED')) {
            $this->markTestSkipped("Zend_Ldap online tests are not enabled");
        }

        $this->options = array(
            'host'     => TESTS_ZEND_LDAP_HOST,
            'username' => TESTS_ZEND_LDAP_USERNAME,
            'password' => TESTS_ZEND_LDAP_PASSWORD,
            'baseDn'   => TESTS_ZEND_LDAP_BASE_DN,
        );
        if (defined('TESTS_ZEND_LDAP_PORT')) {
            $this->options['port'] = TESTS_ZEND_LDAP_PORT;
        }
        if (defined('TESTS_ZEND_LDAP_USE_START_TLS')) {
            $this->options['useStartTls'] = TESTS_ZEND_LDAP_USE_START_TLS;
        }
        if (defined('TESTS_ZEND_LDAP_USE_SSL')) {
            $this->options['useSsl'] = TESTS_ZEND_LDAP_USE_SSL;
        }
        if (defined('TESTS_ZEND_LDAP_BIND_REQUIRES_DN')) {
            $this->options['bindRequiresDn'] = TESTS_ZEND_LDAP_BIND_REQUIRES_DN;
        }
        if (defined('TESTS_ZEND_LDAP_ACCOUNT_FILTER_FORMAT')) {
            $this->options['accountFilterFormat'] = TESTS_ZEND_LDAP_ACCOUNT_FILTER_FORMAT;
        }
        if (defined('TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME')) {
            $this->options['accountDomainName'] = TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME;
        }
        if (defined('TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME_SHORT')) {
            $this->options['accountDomainNameShort'] = TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME_SHORT;
        }
        if (defined('TESTS_ZEND_LDAP_ALT_USERNAME')) {
            $this->altUsername = TESTS_ZEND_LDAP_ALT_USERNAME;
        }

        if (isset($this->options['bindRequiresDn'])) {
            $this->bindRequiresDn = $this->options['bindRequiresDn'];
        }
    }

    public function testEmptyOptionsBind()
    {
        $ldap = new Ldap\Ldap(array());
        try {
            $ldap->bind();
            $this->fail('Expected exception for empty options');
        } catch (Exception\LdapException $zle) {
            $this->assertContains('A host parameter is required', $zle->getMessage());
        }
    }

    public function testAnonymousBind()
    {
        $options = $this->options;
        unset($options['password']);

        $ldap = new Ldap\Ldap($options);
        try {
            $ldap->bind();
        } catch (Exception\LdapException $zle) {
            // or I guess the server doesn't allow unauthenticated binds
            $this->assertContains('unauthenticated bind', $zle->getMessage());
        }
    }

    public function testNoBaseDnBind()
    {
        $options = $this->options;
        unset($options['baseDn']);
        $options['bindRequiresDn'] = true;

        $ldap = new Ldap\Ldap($options);
        try {
            $ldap->bind('invalid', 'ignored');
            $this->fail('Expected exception for baseDn missing');
        } catch (Exception\LdapException $zle) {
            $this->assertContains('Base DN not set', $zle->getMessage());
        }
    }

    public function testNoDomainNameBind()
    {
        $options = $this->options;
        unset($options['accountDomainName']);
        $options['bindRequiresDn']       = false;
        $options['accountCanonicalForm'] = Ldap\Ldap::ACCTNAME_FORM_PRINCIPAL;

        $ldap = new Ldap\Ldap($options);
        try {
            $ldap->bind('invalid', 'ignored');
            $this->fail('Expected exception for missing accountDomainName');
        } catch (Exception\LdapException $zle) {
            $this->assertContains('Option required: accountDomainName', $zle->getMessage());
        }
    }

    public function testPlainBind()
    {
        $ldap = new Ldap\Ldap($this->options);
        $ldap->bind();
        $this->assertNotNull($ldap->getResource());
    }

    public function testConnectBind()
    {
        $ldap = new Ldap\Ldap($this->options);
        $ldap->connect()->bind();
        $this->assertNotNull($ldap->getResource());
    }

    public function testExplicitParamsBind()
    {
        $options  = $this->options;
        $username = $options['username'];
        $password = $options['password'];

        unset($options['username']);
        unset($options['password']);

        $ldap = new Ldap\Ldap($options);
        $ldap->bind($username, $password);
        $this->assertNotNull($ldap->getResource());
    }

    public function testRequiresDnBind()
    {
        $options = $this->options;

        $options['bindRequiresDn'] = true;

        $ldap = new Ldap\Ldap($options);
        try {
            $ldap->bind($this->altUsername, 'invalid');
            $this->fail('Expected exception not thrown');
        } catch (Exception\LdapException $zle) {
            $this->assertContains('Invalid credentials', $zle->getMessage());
        }
    }

    public function testRequiresDnWithoutDnBind()
    {
        $options = $this->options;

        $options['bindRequiresDn'] = true;

        unset($options['username']);

        $ldap = new Ldap\Ldap($options);
        try {
            $ldap->bind($this->principalName);
            $this->fail('Expected exception not thrown');
        } catch (Exception\LdapException $zle) {
            /* Note that if your server actually allows anonymous binds this test will fail.
             */
            $this->assertContains('Failed to retrieve DN', $zle->getMessage());
        }
    }

    public function testBindWithEmptyPassword()
    {
        $options                       = $this->options;
        $options['allowEmptyPassword'] = false;
        $ldap                          = new Ldap\Ldap($options);
        try {
            $ldap->bind($this->altUsername, '');
            $this->fail('Expected exception for empty password');
        } catch (Exception\LdapException $zle) {
            $this->assertContains('Empty password not allowed - see allowEmptyPassword option.',
                $zle->getMessage()
            );
        }

        $options['allowEmptyPassword'] = true;
        $ldap                          = new Ldap\Ldap($options);
        try {
            $ldap->bind($this->altUsername, '');
        } catch (Exception\LdapException $zle) {
            if ($zle->getMessage() ===
                'Empty password not allowed - see allowEmptyPassword option.'
            ) {
                $this->fail('Exception for empty password');
            } else {
                $message = $zle->getMessage();
                $this->assertTrue(strstr($message, 'Invalid credentials')
                        || strstr($message, 'Server is unwilling to perform')
                );
                return;
            }
        }
        $this->assertNotNull($ldap->getResource());
    }

    public function testBindWithoutDnUsernameAndDnRequired()
    {
        $options                   = $this->options;
        $options['username']       = TESTS_ZEND_LDAP_ALT_USERNAME;
        $options['bindRequiresDn'] = true;
        $ldap                      = new Ldap\Ldap($options);
        try {
            $ldap->bind();
            $this->fail('Expected exception for empty password');
        } catch (Exception\LdapException $zle) {
            $this->assertContains('Binding requires username in DN form',
                $zle->getMessage()
            );
        }
    }

    /**
     * @group ZF-8259
     */
    public function testBoundUserIsFalseIfNotBoundToLDAP()
    {
        $ldap = new Ldap\Ldap($this->options);
        $this->assertFalse($ldap->getBoundUser());
    }

    /**
     * @group ZF-8259
     */
    public function testBoundUserIsReturnedAfterBinding()
    {
        $ldap = new Ldap\Ldap($this->options);
        $ldap->bind();
        $this->assertEquals(TESTS_ZEND_LDAP_USERNAME, $ldap->getBoundUser());
    }

    /**
     * @group ZF-8259
     */
    public function testResourceIsAlwaysReturned()
    {
        $ldap = new Ldap\Ldap($this->options);
        $this->assertNotNull($ldap->getResource());
        $this->assertTrue(is_resource($ldap->getResource()));
        $this->assertEquals(TESTS_ZEND_LDAP_USERNAME, $ldap->getBoundUser());
    }
}
