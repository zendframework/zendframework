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

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Ldap
 */
class Zend_Ldap_OriginalCanonTest extends PHPUnit_Framework_TestCase
{
    protected $_options = null;
    protected $_principalName = TESTS_ZEND_LDAP_PRINCIPAL_NAME;
    protected $_names = array();

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
        if (defined('TESTS_ZEND_LDAP_USE_SSL'))
            $this->_options['useSsl'] = TESTS_ZEND_LDAP_USE_SSL;
        if (defined('TESTS_ZEND_LDAP_BIND_REQUIRES_DN'))
            $this->_options['bindRequiresDn'] = TESTS_ZEND_LDAP_BIND_REQUIRES_DN;
        if (defined('TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME'))
            $this->_options['accountDomainName'] = TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME;
        if (defined('TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME_SHORT'))
            $this->_options['accountDomainNameShort'] = TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME_SHORT;
        if (defined('TESTS_ZEND_LDAP_ALT_USERNAME')) {
            $this->_names[Zend_Ldap::ACCTNAME_FORM_USERNAME] = TESTS_ZEND_LDAP_ALT_USERNAME;
            if (defined('TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME')) {
                $this->_names[Zend_Ldap::ACCTNAME_FORM_PRINCIPAL] =
                    TESTS_ZEND_LDAP_ALT_USERNAME . '@' . TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME;
            }
            if (defined('TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME_SHORT')) {
                $this->_names[Zend_Ldap::ACCTNAME_FORM_BACKSLASH] =
                    TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME_SHORT . '\\' . TESTS_ZEND_LDAP_ALT_USERNAME;
            }
        }
    }

    public function testPlainCanon()
    {
        $ldap = new Zend_Ldap($this->_options);

        /* This test tries to canonicalize each name (uname, uname@example.com,
         * EXAMPLE\uname) to each of the 3 forms (username, principal and backslash)
         * for a total of canonicalizations.
         */

        foreach ($this->_names as $_form => $name) {
            foreach ($this->_names as $form => $_name) {
                $ret = $ldap->getCanonicalAccountName($name, $form);
                $this->assertTrue($ret === $this->_names[$form]);
            }
        }
    }
    public function testInvalidAccountCanon()
    {
        $ldap = new Zend_Ldap($this->_options);
        try {
            $ldap->bind('invalid', 'invalid');
        } catch (Zend_Ldap_Exception $zle) {
            $msg = $zle->getMessage();
            $this->assertTrue(strstr($msg, 'Invalid credentials') || strstr($msg, 'No such object'));
        }
    }
    public function testDnCanon()
    {
        $ldap = new Zend_Ldap($this->_options);
        $name = $ldap->getCanonicalAccountName(TESTS_ZEND_LDAP_ALT_DN, Zend_Ldap::ACCTNAME_FORM_DN);
    }
    public function testMismatchDomainBind()
    {
        $ldap = new Zend_Ldap($this->_options);
        try {
            $ldap->bind('BOGUS\\doesntmatter', 'doesntmatter');
        } catch (Zend_Ldap_Exception $zle) {
            $this->assertTrue($zle->getCode() == Zend_Ldap_Exception::LDAP_X_DOMAIN_MISMATCH);
        }
    }
    public function testBindCanon()
    {
        /**
         * @todo test accountCanonicalForm option
         */
    }
}
