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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Ldap
 */
class Zend_Ldap_ConnectTest extends PHPUnit_Framework_TestCase
{
    protected $_options = null;

    public function setUp()
    {
        $this->_options = array('host' => TESTS_ZEND_LDAP_HOST);
        if (defined('TESTS_ZEND_LDAP_PORT') && TESTS_ZEND_LDAP_PORT != 389)
            $this->_options['port'] = TESTS_ZEND_LDAP_PORT;
        if (defined('TESTS_ZEND_LDAP_USE_SSL'))
            $this->_options['useSsl'] = TESTS_ZEND_LDAP_USE_SSL;
    }

    public function testEmptyOptionsConnect()
    {
        $ldap = new Zend_Ldap(array());
        try {
            $ldap->connect();
            $this->fail('Expected exception for empty options');
        } catch (Zend_Ldap_Exception $zle) {
            $this->assertContains('host parameter is required', $zle->getMessage());
        }
    }
    public function testUnknownHostConnect()
    {
        $ldap = new Zend_Ldap(array('host' => 'bogus.example.com'));
        try {
            // connect doesn't actually try to connect until bind is called
            $ldap->connect()->bind('CN=ignored,DC=example,DC=com', 'ignored');
            $this->fail('Expected exception for unknown host');
        } catch (Zend_Ldap_Exception $zle) {
            $this->assertContains('Can\'t contact LDAP server', $zle->getMessage());
        }
    }
    public function testPlainConnect()
    {
        $ldap = new Zend_Ldap($this->_options);
        try {
            // Connect doesn't actually try to connect until bind is called
            // but if we get 'Invalid credentials' then we know the connect
            // succeeded.
            $ldap->connect()->bind('CN=ignored,DC=example,DC=com', 'ignored');
            $this->fail('Expected exception for invalid username');
        } catch (Zend_Ldap_Exception $zle) {
            $this->assertContains('Invalid credentials', $zle->getMessage());
        }
    }
    public function testExplicitParamsConnect()
    {
        $host = TESTS_ZEND_LDAP_HOST;
        $port = 0;
        if (defined('TESTS_ZEND_LDAP_PORT') && TESTS_ZEND_LDAP_PORT != 389)
            $port = TESTS_ZEND_LDAP_PORT;
        $useSsl = false;
        if (defined('TESTS_ZEND_LDAP_USE_SSL'))
            $useSsl = TESTS_ZEND_LDAP_USE_SSL;

        $ldap = new Zend_Ldap();
        try {
            $ldap->connect($host, $port, $useSsl)
                 ->bind('CN=ignored,DC=example,DC=com', 'ignored');
            $this->fail('Expected exception for invalid username');
        } catch (Zend_Ldap_Exception $zle) {
            $this->assertContains('Invalid credentials', $zle->getMessage());
        }
    }
    public function testExplicitPortConnect()
    {
        $port = 389;
        if (defined('TESTS_ZEND_LDAP_PORT') && TESTS_ZEND_LDAP_PORT)
            $port = TESTS_ZEND_LDAP_PORT;
        if (defined('TESTS_ZEND_LDAP_USE_SSL') && TESTS_ZEND_LDAP_USE_SSL)
            $port = 636;

        $ldap = new Zend_Ldap($this->_options);
        try {
            $ldap->connect(null, $port)
                 ->bind('CN=ignored,DC=example,DC=com', 'ignored');
            $this->fail('Expected exception for invalid username');
        } catch (Zend_Ldap_Exception $zle) {
            $this->assertContains('Invalid credentials', $zle->getMessage());
        }
    }
    public function testBadPortConnect()
    {
        $options = $this->_options;
        $options['port'] = 10;

        $ldap = new Zend_Ldap($options);
        try {
            $ldap->connect()->bind('CN=ignored,DC=example,DC=com', 'ignored');
            $this->fail('Expected exception for unknown username');
        } catch (Zend_Ldap_Exception $zle) {
            $this->assertContains('Can\'t contact LDAP server', $zle->getMessage());
        }
    }
    public function testSetOptionsConnect()
    {
        $ldap = new Zend_Ldap();
        $ldap->setOptions($this->_options);
        try {
            $ldap->connect()->bind('CN=ignored,DC=example,DC=com', 'ignored');
            $this->fail('Expected exception for invalid username');
        } catch (Zend_Ldap_Exception $zle) {
            $this->assertContains('Invalid credentials', $zle->getMessage());
        }
    }
    public function testMultiConnect()
    {
        $ldap = new Zend_Ldap($this->_options);
        for ($i = 0; $i < 3; $i++) {
            try {
                $ldap->connect()->bind('CN=ignored,DC=example,DC=com', 'ignored');
                $this->fail('Expected exception for unknown username');
            } catch (Zend_Ldap_Exception $zle) {
                $this->assertContains('Invalid credentials', $zle->getMessage());
            }
        }
    }
    public function testDisconnect()
    {
        $ldap = new Zend_Ldap($this->_options);
        for ($i = 0; $i < 3; $i++) {
            $ldap->disconnect();
            try {
                $ldap->connect()->bind('CN=ignored,DC=example,DC=com', 'ignored');
                $this->fail('Expected exception for unknown username');
            } catch (Zend_Ldap_Exception $zle) {
                $this->assertContains('Invalid credentials', $zle->getMessage());
            }
        }
    }

    public function testGetErrorCode()
    {
        $ldap = new Zend_Ldap($this->_options);
        try {
            // Connect doesn't actually try to connect until bind is called
            // but if we get 'Invalid credentials' then we know the connect
            // succeeded.
            $ldap->connect()->bind('CN=ignored,DC=example,DC=com', 'ignored');
            $this->fail('Expected exception for invalid username');
        } catch (Zend_Ldap_Exception $zle) {
            $this->assertContains('Invalid credentials', $zle->getMessage());

            $this->assertEquals(0x31, $zle->getCode());
            $this->assertEquals(0x0, Zend_Ldap_Exception::getLdapCode($ldap));
            $this->assertEquals(0x0, Zend_Ldap_Exception::getLdapCode(null));
        }
    }

    /**
     * @group ZF-8274
     */
    public function testConnectWithUri()
    {
        $host = TESTS_ZEND_LDAP_HOST;
        $port = 0;
        if (defined('TESTS_ZEND_LDAP_PORT') && TESTS_ZEND_LDAP_PORT != 389) $port = TESTS_ZEND_LDAP_PORT;
        $useSsl = false;
        if (defined('TESTS_ZEND_LDAP_USE_SSL')) $useSsl = TESTS_ZEND_LDAP_USE_SSL;
        if ($useSsl) {
            $host = 'ldaps://' . $host;
        } else {
            $host = 'ldap://' . $host;
        }
        if ($port) {
            $host = $host . ':' . $port;
        }

        $ldap = new Zend_Ldap();
        try {
            $ldap->connect($host)
                 ->bind('CN=ignored,DC=example,DC=com', 'ignored');
            $this->fail('Expected exception for invalid username');
        } catch (Zend_Ldap_Exception $zle) {
            $this->assertContains('Invalid credentials', $zle->getMessage());
        }
    }
}
