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
class CanonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $options;

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
    }

    public function testPlainCanon()
    {
        $ldap = new Ldap\Ldap($this->options);
        /* This test tries to canonicalize each name (uname, uname@example.com,
         * EXAMPLE\uname) to each of the 3 forms (username, principal and backslash)
         * for a total of canonicalizations.
         */
        if (defined('TESTS_ZEND_LDAP_ALT_USERNAME')) {
            $names[Ldap\Ldap::ACCTNAME_FORM_USERNAME] = TESTS_ZEND_LDAP_ALT_USERNAME;
            if (defined('TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME')) {
                $names[Ldap\Ldap::ACCTNAME_FORM_PRINCIPAL]
                    = TESTS_ZEND_LDAP_ALT_USERNAME . '@' . TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME;
            }
            if (defined('TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME_SHORT')) {
                $names[Ldap\Ldap::ACCTNAME_FORM_BACKSLASH]
                    = TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME_SHORT . '\\' . TESTS_ZEND_LDAP_ALT_USERNAME;
            }
        }

        foreach ($names as $form => $name) {
            $ret = $ldap->getCanonicalAccountName($name, $form);
            $this->assertEquals($names[$form], $ret);
        }
    }

    public function testInvalidAccountCanon()
    {
        $ldap = new Ldap\Ldap($this->options);
        try {
            $ldap->bind('invalid', 'invalid');
            $this->fail('Expected exception not thrown');
        } catch (Exception\LdapException $zle) {
            $msg = $zle->getMessage();
            $this->assertTrue(strstr($msg, 'Invalid credentials')
                    || strstr($msg, 'No such object')
                    || strstr($msg, 'No object found')
            );
        }
    }

    public function testDnCanon()
    {
        $ldap = new Ldap\Ldap($this->options);
        $name = $ldap->getCanonicalAccountName(TESTS_ZEND_LDAP_ALT_USERNAME, Ldap\Ldap::ACCTNAME_FORM_DN);
        $this->assertEquals(TESTS_ZEND_LDAP_ALT_DN, $name);
    }

    public function testMismatchDomainBind()
    {
        $ldap = new Ldap\Ldap($this->options);
        try {
            $ldap->bind('BOGUS\\doesntmatter', 'doesntmatter');
            $this->fail('Expected exception not thrown');
        } catch (Exception\LdapException $zle) {
            $this->assertTrue($zle->getCode() == Exception\LdapException::LDAP_X_DOMAIN_MISMATCH);
        }
    }

    public function testAccountCanonization()
    {
        $options = $this->options;
        $ldap    = new Ldap\Ldap($options);

        $canonDn = $ldap->getCanonicalAccountName(TESTS_ZEND_LDAP_ALT_USERNAME,
            Ldap\Ldap::ACCTNAME_FORM_DN
        );
        $this->assertEquals(TESTS_ZEND_LDAP_ALT_DN, $canonDn);
        $canonUsername = $ldap->getCanonicalAccountName(TESTS_ZEND_LDAP_ALT_USERNAME,
            Ldap\Ldap::ACCTNAME_FORM_USERNAME
        );
        $this->assertEquals(TESTS_ZEND_LDAP_ALT_USERNAME, $canonUsername);
        $canonBackslash = $ldap->getCanonicalAccountName(TESTS_ZEND_LDAP_ALT_USERNAME,
            Ldap\Ldap::ACCTNAME_FORM_BACKSLASH
        );
        $this->assertEquals(
            TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME_SHORT . '\\' . TESTS_ZEND_LDAP_ALT_USERNAME,
            $canonBackslash
        );
        $canonPrincipal = $ldap->getCanonicalAccountName(TESTS_ZEND_LDAP_ALT_USERNAME,
            Ldap\Ldap::ACCTNAME_FORM_PRINCIPAL
        );
        $this->assertEquals(
            TESTS_ZEND_LDAP_ALT_USERNAME . '@' . TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME,
            $canonPrincipal
        );

        $options['accountCanonicalForm'] = Ldap\Ldap::ACCTNAME_FORM_USERNAME;
        $ldap->setOptions($options);
        $canon = $ldap->getCanonicalAccountName(TESTS_ZEND_LDAP_ALT_USERNAME);
        $this->assertEquals(TESTS_ZEND_LDAP_ALT_USERNAME, $canon);

        $options['accountCanonicalForm'] = Ldap\Ldap::ACCTNAME_FORM_BACKSLASH;
        $ldap->setOptions($options);
        $canon = $ldap->getCanonicalAccountName(TESTS_ZEND_LDAP_ALT_USERNAME);
        $this->assertEquals(
            TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME_SHORT . '\\' . TESTS_ZEND_LDAP_ALT_USERNAME, $canon
        );

        $options['accountCanonicalForm'] = Ldap\Ldap::ACCTNAME_FORM_PRINCIPAL;
        $ldap->setOptions($options);
        $canon = $ldap->getCanonicalAccountName(TESTS_ZEND_LDAP_ALT_USERNAME);
        $this->assertEquals(
            TESTS_ZEND_LDAP_ALT_USERNAME . '@' . TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME, $canon
        );

        unset($options['accountCanonicalForm']);

        unset($options['accountDomainName']);
        $ldap->setOptions($options);
        $canon = $ldap->getCanonicalAccountName(TESTS_ZEND_LDAP_ALT_USERNAME);
        $this->assertEquals(
            TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME_SHORT . '\\' . TESTS_ZEND_LDAP_ALT_USERNAME, $canon
        );

        unset($options['accountDomainNameShort']);
        $ldap->setOptions($options);
        $canon = $ldap->getCanonicalAccountName(TESTS_ZEND_LDAP_ALT_USERNAME);
        $this->assertEquals(TESTS_ZEND_LDAP_ALT_USERNAME, $canon);

        $options['accountDomainName'] = TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME;
        $ldap->setOptions($options);
        $canon = $ldap->getCanonicalAccountName(TESTS_ZEND_LDAP_ALT_USERNAME);
        $this->assertEquals(
            TESTS_ZEND_LDAP_ALT_USERNAME . '@' . TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME, $canon
        );
    }

    public function testDefaultAccountFilterFormat()
    {
        $options = $this->options;

        unset($options['accountFilterFormat']);
        $options['bindRequiresDn'] = true;
        $ldap                      = new Ldap\Ldap($options);
        try {
            $canon = $ldap->getCanonicalAccountName('invalid', Ldap\Ldap::ACCTNAME_FORM_DN);
            $this->fail('Expected exception not thrown');
        } catch (Exception\LdapException $zle) {
            $this->assertContains('(&(objectClass=posixAccount)(uid=invalid))', $zle->getMessage());
        }

        $options['bindRequiresDn'] = false;
        $ldap                      = new Ldap\Ldap($options);
        try {
            $canon = $ldap->getCanonicalAccountName('invalid', Ldap\Ldap::ACCTNAME_FORM_DN);
            $this->fail('Expected exception not thrown');
        } catch (Exception\LdapException $zle) {
            $this->assertContains('(&(objectClass=user)(sAMAccountName=invalid))', $zle->getMessage());
        }
    }

    public function testPossibleAuthority()
    {
        $options = $this->options;
        $ldap    = new Ldap\Ldap($options);
        try {
            $canon = $ldap->getCanonicalAccountName('invalid\invalid',
                Ldap\Ldap::ACCTNAME_FORM_USERNAME
            );
            $this->fail('Expected exception not thrown');
        } catch (Exception\LdapException $zle) {
            $this->assertContains('Binding domain is not an authority for user: invalid\invalid',
                $zle->getMessage()
            );
        }
        try {
            $canon = $ldap->getCanonicalAccountName('invalid@invalid.tld',
                Ldap\Ldap::ACCTNAME_FORM_USERNAME
            );
            $this->fail('Expected exception not thrown');
        } catch (Exception\LdapException $zle) {
            $this->assertContains('Binding domain is not an authority for user: invalid@invalid.tld',
                $zle->getMessage()
            );
        }

        unset($options['accountDomainName']);
        $ldap  = new Ldap\Ldap($options);
        $canon = $ldap->getCanonicalAccountName(TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME_SHORT . '\invalid',
            Ldap\Ldap::ACCTNAME_FORM_USERNAME
        );
        $this->assertEquals('invalid', $canon);
        try {
            $canon = $ldap->getCanonicalAccountName('invalid@' . TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME,
                Ldap\Ldap::ACCTNAME_FORM_USERNAME
            );
            $this->fail('Expected exception not thrown');
        } catch (Exception\LdapException $zle) {
            $this->assertContains('Binding domain is not an authority for user: invalid@' .
                    TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME,
                $zle->getMessage()
            );
        }

        unset($options['accountDomainNameShort']);
        $options['accountDomainName'] = TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME;
        $ldap                         = new Ldap\Ldap($options);
        try {
            $canon = $ldap->getCanonicalAccountName(TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME_SHORT . '\invalid',
                Ldap\Ldap::ACCTNAME_FORM_USERNAME
            );
            $this->fail('Expected exception not thrown');
        } catch (Exception\LdapException $zle) {
            $this->assertContains('Binding domain is not an authority for user: ' .
                    TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME_SHORT . '\invalid',
                $zle->getMessage()
            );
        }

        $canon = $ldap->getCanonicalAccountName('invalid@' . TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME,
            Ldap\Ldap::ACCTNAME_FORM_USERNAME
        );
        $this->assertEquals('invalid', $canon);

        unset($options['accountDomainName']);
        $ldap  = new Ldap\Ldap($options);
        $canon = $ldap->getCanonicalAccountName(TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME_SHORT . '\invalid',
            Ldap\Ldap::ACCTNAME_FORM_USERNAME
        );
        $this->assertEquals('invalid', $canon);
        $canon = $ldap->getCanonicalAccountName('invalid@' . TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME,
            Ldap\Ldap::ACCTNAME_FORM_USERNAME
        );
        $this->assertEquals('invalid', $canon);
    }

    public function testInvalidAccountName()
    {
        $options = $this->options;
        $ldap    = new Ldap\Ldap($options);

        try {
            $canon = $ldap->getCanonicalAccountName('0@' . TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME,
                Ldap\Ldap::ACCTNAME_FORM_USERNAME
            );
            $this->fail('Expected exception not thrown');
        } catch (Exception\LdapException $zle) {
            $this->assertContains('Invalid account name syntax: 0@' .
                    TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME,
                $zle->getMessage()
            );
        }

        try {
            $canon = $ldap->getCanonicalAccountName(TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME_SHORT . '\\0',
                Ldap\Ldap::ACCTNAME_FORM_USERNAME
            );
            $this->fail('Expected exception not thrown');
        } catch (Exception\LdapException $zle) {
            $this->assertContains('Invalid account name syntax: ' .
                    TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME_SHORT . '\\0',
                $zle->getMessage()
            );
        }
    }

    public function testGetUnknownCanonicalForm()
    {
        $options = $this->options;
        $ldap    = new Ldap\Ldap($options);

        try {
            $canon = $ldap->getCanonicalAccountName(TESTS_ZEND_LDAP_ALT_USERNAME, 99);
            $this->fail('Expected exception not thrown');
        } catch (Exception\LdapException $zle) {
            $this->assertContains('Unknown canonical name form: 99',
                $zle->getMessage()
            );
        }
    }

    public function testGetUnavailableCanoncialForm()
    {
        $options = $this->options;
        unset($options['accountDomainName']);
        $ldap = new Ldap\Ldap($options);
        try {
            $canon = $ldap->getCanonicalAccountName(TESTS_ZEND_LDAP_ALT_USERNAME,
                Ldap\Ldap::ACCTNAME_FORM_PRINCIPAL
            );
            $this->fail('Expected exception not thrown');
        } catch (Exception\LdapException $zle) {
            $this->assertContains('Option required: accountDomainName',
                $zle->getMessage()
            );
        }

        unset($options['accountDomainNameShort']);
        $ldap = new Ldap\Ldap($options);
        try {
            $canon = $ldap->getCanonicalAccountName(TESTS_ZEND_LDAP_ALT_USERNAME,
                Ldap\Ldap::ACCTNAME_FORM_BACKSLASH
            );
            $this->fail('Expected exception not thrown');
        } catch (Exception\LdapException $zle) {
            $this->assertContains('Option required: accountDomainNameShort',
                $zle->getMessage()
            );
        }
    }

    public function testSplittingOption()
    {
        $options = $this->options;
        unset($options['accountDomainName']);
        unset($options['accountDomainNameShort']);
        $options['tryUsernameSplit'] = true;
        $ldap                        = new Ldap\Ldap($options);
        $this->assertEquals('username', $ldap->getCanonicalAccountName('username@example.com',
                Ldap\Ldap::ACCTNAME_FORM_USERNAME
            )
        );
        $this->assertEquals('username', $ldap->getCanonicalAccountName('EXAMPLE\username',
                Ldap\Ldap::ACCTNAME_FORM_USERNAME
            )
        );
        $this->assertEquals('username', $ldap->getCanonicalAccountName('username',
                Ldap\Ldap::ACCTNAME_FORM_USERNAME
            )
        );

        $options['tryUsernameSplit'] = false;
        $ldap                        = new Ldap\Ldap($options);
        $this->assertEquals('username@example.com',
            $ldap->getCanonicalAccountName('username@example.com', Ldap\Ldap::ACCTNAME_FORM_USERNAME)
        );
        $this->assertEquals('example\username', $ldap->getCanonicalAccountName('EXAMPLE\username',
                Ldap\Ldap::ACCTNAME_FORM_USERNAME
            )
        );
        $this->assertEquals('username', $ldap->getCanonicalAccountName('username',
                Ldap\Ldap::ACCTNAME_FORM_USERNAME
            )
        );

    }

    /**
     * ZF-4495
     */
    public function testSpecialCharacterInUsername()
    {
        $options                           = $this->options;
        $options['accountDomainName']      = 'example.com';
        $options['accountDomainNameShort'] = 'EXAMPLE';
        $ldap                              = new Ldap\Ldap($options);

        $this->assertEquals('schäfer', $ldap->getCanonicalAccountName('SCHÄFER@example.com',
                Ldap\Ldap::ACCTNAME_FORM_USERNAME
            )
        );
        $this->assertEquals('schäfer', $ldap->getCanonicalAccountName('EXAMPLE\SCHÄFER',
                Ldap\Ldap::ACCTNAME_FORM_USERNAME
            )
        );
        $this->assertEquals('schäfer', $ldap->getCanonicalAccountName('SCHÄFER',
                Ldap\Ldap::ACCTNAME_FORM_USERNAME
            )
        );

        $this->assertEquals('schäfer@example.com', $ldap->getCanonicalAccountName('SCHÄFER@example.com',
                Ldap\Ldap::ACCTNAME_FORM_PRINCIPAL
            )
        );
        $this->assertEquals('schäfer@example.com', $ldap->getCanonicalAccountName('EXAMPLE\SCHÄFER',
                Ldap\Ldap::ACCTNAME_FORM_PRINCIPAL
            )
        );
        $this->assertEquals('schäfer@example.com', $ldap->getCanonicalAccountName('SCHÄFER',
                Ldap\Ldap::ACCTNAME_FORM_PRINCIPAL
            )
        );

        $this->assertEquals('EXAMPLE\schäfer', $ldap->getCanonicalAccountName('SCHÄFER@example.com',
                Ldap\Ldap::ACCTNAME_FORM_BACKSLASH
            )
        );
        $this->assertEquals('EXAMPLE\schäfer', $ldap->getCanonicalAccountName('EXAMPLE\SCHÄFER',
                Ldap\Ldap::ACCTNAME_FORM_BACKSLASH
            )
        );
        $this->assertEquals('EXAMPLE\schäfer', $ldap->getCanonicalAccountName('SCHÄFER',
                Ldap\Ldap::ACCTNAME_FORM_BACKSLASH
            )
        );
    }
}
