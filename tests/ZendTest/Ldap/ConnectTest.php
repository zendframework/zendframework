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
class ConnectTest extends \PHPUnit_Framework_TestCase
{
    protected $options = null;

    public function setUp()
    {
        if (!constant('TESTS_ZEND_LDAP_ONLINE_ENABLED')) {
            $this->markTestSkipped("Zend_Ldap online tests are not enabled");
        }

        $this->options = array('host' => TESTS_ZEND_LDAP_HOST);
        if (defined('TESTS_ZEND_LDAP_PORT') && TESTS_ZEND_LDAP_PORT != 389) {
            $this->options['port'] = TESTS_ZEND_LDAP_PORT;
        }
        if (defined('TESTS_ZEND_LDAP_USE_SSL')) {
            $this->options['useSsl'] = TESTS_ZEND_LDAP_USE_SSL;
        }
    }

    public function testEmptyOptionsConnect()
    {
        $ldap = new Ldap\Ldap(array());
        try {
            $ldap->connect();
            $this->fail('Expected exception for empty options');
        } catch (Exception\LdapException $zle) {
            $this->assertContains('host parameter is required', $zle->getMessage());
        }
    }

    public function testUnknownHostConnect()
    {
        $ldap = new Ldap\Ldap(array('host' => 'bogus.example.com'));
        try {
            // connect doesn't actually try to connect until bind is called
            $ldap->connect()->bind('CN=ignored,DC=example,DC=com', 'ignored');
            $this->fail('Expected exception for unknown host');
        } catch (Exception\LdapException $zle) {
            $this->assertContains('Can\'t contact LDAP server', $zle->getMessage());
        }
    }

    public function testPlainConnect()
    {
        $ldap = new Ldap\Ldap($this->options);
        try {
            // Connect doesn't actually try to connect until bind is called
            // but if we get 'Invalid credentials' then we know the connect
            // succeeded.
            $ldap->connect()->bind('CN=ignored,DC=example,DC=com', 'ignored');
            $this->fail('Expected exception for invalid username');
        } catch (Exception\LdapException $zle) {
            $this->assertContains('Invalid credentials', $zle->getMessage());
        }
    }

    public function testNetworkTimeoutConnect()
    {
        $networkTimeout = 1;
        $ldap           = new Ldap\Ldap(array_merge($this->options, array('networkTimeout' => $networkTimeout)));

        $ldap->connect();
        ldap_get_option($ldap->getResource(), LDAP_OPT_NETWORK_TIMEOUT, $actual);
        $this->assertEquals($networkTimeout, $actual);
    }

    public function testExplicitParamsConnect()
    {
        $host = TESTS_ZEND_LDAP_HOST;
        $port = 0;
        if (defined('TESTS_ZEND_LDAP_PORT') && TESTS_ZEND_LDAP_PORT != 389) {
            $port = TESTS_ZEND_LDAP_PORT;
        }
        $useSsl = false;
        if (defined('TESTS_ZEND_LDAP_USE_SSL')) {
            $useSsl = TESTS_ZEND_LDAP_USE_SSL;
        }

        $ldap = new Ldap\Ldap();
        try {
            $ldap->connect($host, $port, $useSsl)
                ->bind('CN=ignored,DC=example,DC=com', 'ignored');
            $this->fail('Expected exception for invalid username');
        } catch (Exception\LdapException $zle) {
            $this->assertContains('Invalid credentials', $zle->getMessage());
        }
    }

    public function testExplicitPortConnect()
    {
        $port = 389;
        if (defined('TESTS_ZEND_LDAP_PORT') && TESTS_ZEND_LDAP_PORT) {
            $port = TESTS_ZEND_LDAP_PORT;
        }
        if (defined('TESTS_ZEND_LDAP_USE_SSL') && TESTS_ZEND_LDAP_USE_SSL) {
            $port = 636;
        }

        $ldap = new Ldap\Ldap($this->options);
        try {
            $ldap->connect(null, $port)
                ->bind('CN=ignored,DC=example,DC=com', 'ignored');
            $this->fail('Expected exception for invalid username');
        } catch (Exception\LdapException $zle) {
            $this->assertContains('Invalid credentials', $zle->getMessage());
        }
    }

    public function testExplicitNetworkTimeoutConnect()
    {
        $networkTimeout = 1;
        $host           = TESTS_ZEND_LDAP_HOST;
        $port           = 0;
        if (defined('TESTS_ZEND_LDAP_PORT') && TESTS_ZEND_LDAP_PORT != 389) {
            $port = TESTS_ZEND_LDAP_PORT;
        }
        $useSsl = false;
        if (defined('TESTS_ZEND_LDAP_USE_SSL')) {
            $useSsl = TESTS_ZEND_LDAP_USE_SSL;
        }

        $ldap = new Ldap\Ldap();
        $ldap->connect($host, $port, $useSsl, null, $networkTimeout);
        ldap_get_option($ldap->getResource(), LDAP_OPT_NETWORK_TIMEOUT, $actual);
        $this->assertEquals($networkTimeout, $actual);
    }

    public function testBadPortConnect()
    {
        $options         = $this->options;
        $options['port'] = 10;

        $ldap = new Ldap\Ldap($options);
        try {
            $ldap->connect()->bind('CN=ignored,DC=example,DC=com', 'ignored');
            $this->fail('Expected exception for unknown username');
        } catch (Exception\LdapException $zle) {
            $this->assertContains('Can\'t contact LDAP server', $zle->getMessage());
        }
    }

    public function testSetOptionsConnect()
    {
        $ldap = new Ldap\Ldap();
        $ldap->setOptions($this->options);
        try {
            $ldap->connect()->bind('CN=ignored,DC=example,DC=com', 'ignored');
            $this->fail('Expected exception for invalid username');
        } catch (Exception\LdapException $zle) {
            $this->assertContains('Invalid credentials', $zle->getMessage());
        }
    }

    public function testMultiConnect()
    {
        $ldap = new Ldap\Ldap($this->options);
        for ($i = 0; $i < 3; $i++) {
            try {
                $ldap->connect()->bind('CN=ignored,DC=example,DC=com', 'ignored');
                $this->fail('Expected exception for unknown username');
            } catch (Exception\LdapException $zle) {
                $this->assertContains('Invalid credentials', $zle->getMessage());
            }
        }
    }

    public function testDisconnect()
    {
        $ldap = new Ldap\Ldap($this->options);
        for ($i = 0; $i < 3; $i++) {
            $ldap->disconnect();
            try {
                $ldap->connect()->bind('CN=ignored,DC=example,DC=com', 'ignored');
                $this->fail('Expected exception for unknown username');
            } catch (Exception\LdapException $zle) {
                $this->assertContains('Invalid credentials', $zle->getMessage());
            }
        }
    }

    public function testGetErrorCode()
    {
        $ldap = new Ldap\Ldap($this->options);
        try {
            // Connect doesn't actually try to connect until bind is called
            // but if we get 'Invalid credentials' then we know the connect
            // succeeded.
            $ldap->connect()->bind('CN=ignored,DC=example,DC=com', 'ignored');
            $this->fail('Expected exception for invalid username');
        } catch (Exception\LdapException $zle) {
            $this->assertContains('Invalid credentials', $zle->getMessage());

            $this->assertEquals(0x31, $zle->getCode());
            $this->assertEquals(0x0, $ldap->getLastErrorCode());
        }
    }

    /**
     * @group ZF-8274
     */
    public function testConnectWithUri()
    {
        $host = TESTS_ZEND_LDAP_HOST;
        $port = 0;
        if (defined('TESTS_ZEND_LDAP_PORT') && TESTS_ZEND_LDAP_PORT != 389) {
            $port = TESTS_ZEND_LDAP_PORT;
        }
        $useSsl = false;
        if (defined('TESTS_ZEND_LDAP_USE_SSL')) {
            $useSsl = TESTS_ZEND_LDAP_USE_SSL;
        }
        if ($useSsl) {
            $host = 'ldaps://' . $host;
        } else {
            $host = 'ldap://' . $host;
        }
        if ($port) {
            $host = $host . ':' . $port;
        }

        $ldap = new Ldap\Ldap();
        try {
            $ldap->connect($host)
                ->bind('CN=ignored,DC=example,DC=com', 'ignored');
            $this->fail('Expected exception for invalid username');
        } catch (Exception\LdapException $zle) {
            $this->assertContains('Invalid credentials', $zle->getMessage());
        }
    }
}
