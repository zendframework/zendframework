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

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Ldap_OriginalBindTest extends PHPUnit_Framework_TestCase
{
    protected $_options = null;
    protected $_principalName = TESTS_ZEND_LDAP_PRINCIPAL_NAME;
    protected $_altUsername = TESTS_ZEND_LDAP_PRINCIPAL_NAME;
    protected $_bindRequiresDn = false;

    public function setUp()
    {
        $this->_options = array(
            'host' => TESTS_ZEND_LDAP_HOST,
            'username' => TESTS_ZEND_LDAP_USERNAME,
            'password' => TESTS_ZEND_LDAP_PASSWORD,
            'baseDn' => TESTS_ZEND_LDAP_BASE_DN,
        );
        if (defined('TESTS_ZEND_LDAP_PORT') && TESTS_ZEND_LDAP_PORT != 389)
            $this->_options['port'] = TESTS_ZEND_LDAP_PORT;
        if (defined('TESTS_ZEND_LDAP_USE_START_TLS'))
            $this->_options['useStartTls'] = TESTS_ZEND_LDAP_USE_START_TLS;
        if (defined('TESTS_ZEND_LDAP_USE_SSL'))
            $this->_options['useSsl'] = TESTS_ZEND_LDAP_USE_SSL;
        if (defined('TESTS_ZEND_LDAP_BIND_REQUIRES_DN'))
            $this->_options['bindRequiresDn'] = TESTS_ZEND_LDAP_BIND_REQUIRES_DN;
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
        unset($options['baseDn']);
        $options['bindRequiresDn'] = false;

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
    }
    public function testConnectBind()
    {
        $ldap = new Zend_Ldap($this->_options);
        $ldap->connect()->bind();
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
    }
    public function testRequiresDnBind()
    {
        $options = $this->_options;

        /* Fixup filter since bindRequiresDn is used to determine default accountFilterFormat
         */
        if (!isset($options['accountFilterFormat']) && $this->_bindRequiresDn === false)
            $options['accountFilterFormat'] = '(&(objectClass=user)(sAMAccountName=%s))';

        $options['bindRequiresDn'] = true;

        $ldap = new Zend_Ldap($options);
        try {
            $ldap->bind($this->_altUsername, 'invalid');
        } catch (Zend_Ldap_Exception $zle) {
            $message = str_replace("\n", " ", $zle->getMessage());
            $this->assertContains('Invalid credentials', $message);
        }
    }
    public function testRequiresDnWithoutDnBind()
    {
        $options = $this->_options;

        /* Fixup filter since bindRequiresDn is used to determine default accountFilterFormat
         */
        if (!isset($options['accountFilterFormat']) && !$this->_bindRequiresDn)
            $options['accountFilterFormat'] = '(&(objectClass=user)(sAMAccountName=%s))';

        $options['bindRequiresDn'] = true;

        unset($options['username']);

        $ldap = new Zend_Ldap($options);
        try {
            $ldap->bind($this->_principalName);
        } catch (Zend_Ldap_Exception $zle) {
            /* Note that if your server actually allows anonymous binds this test will fail.
             */
            $this->assertContains('Failed to retrieve DN', $zle->getMessage());
        }
    }
}
