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
 * Zend_Ldap_TestCase
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'TestCase.php';
/**
 * @see Zend_Ldap
 */
require_once 'Zend/Ldap.php';

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Ldap
 */
abstract class Zend_Ldap_OnlineTestCase extends Zend_Ldap_TestCase
{
    /**
     * @var Zend_Ldap
     */
    private $_ldap;

    /**
     * @var array
     */
    private $_nodes;

    /**
     * @return Zend_Ldap
     */
    protected function _getLdap()
    {
        return $this->_ldap;
    }

    protected function setUp()
    {
        if (!TESTS_ZEND_LDAP_ONLINE_ENABLED) {
            $this->markTestSkipped("Test skipped due to test configuration");
            return;
        }

        $options = array(
            'host'     => TESTS_ZEND_LDAP_HOST,
            'username' => TESTS_ZEND_LDAP_USERNAME,
            'password' => TESTS_ZEND_LDAP_PASSWORD,
            'baseDn'   => TESTS_ZEND_LDAP_WRITEABLE_SUBTREE,
        );
        if (defined('TESTS_ZEND_LDAP_PORT') && TESTS_ZEND_LDAP_PORT != 389)
            $options['port'] = TESTS_ZEND_LDAP_PORT;
        if (defined('TESTS_ZEND_LDAP_USE_START_TLS'))
            $options['useStartTls'] = TESTS_ZEND_LDAP_USE_START_TLS;
        if (defined('TESTS_ZEND_LDAP_USE_SSL'))
            $options['useSsl'] = TESTS_ZEND_LDAP_USE_SSL;
        if (defined('TESTS_ZEND_LDAP_BIND_REQUIRES_DN'))
            $options['bindRequiresDn'] = TESTS_ZEND_LDAP_BIND_REQUIRES_DN;
        if (defined('TESTS_ZEND_LDAP_ACCOUNT_FILTER_FORMAT'))
            $options['accountFilterFormat'] = TESTS_ZEND_LDAP_ACCOUNT_FILTER_FORMAT;
        if (defined('TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME'))
            $options['accountDomainName'] = TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME;
        if (defined('TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME_SHORT'))
            $options['accountDomainNameShort'] = TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME_SHORT;

        $this->_ldap=new Zend_Ldap($options);
        $this->_ldap->bind();
    }

    protected function tearDown()
    {
        if ($this->_ldap!==null) {
            $this->_ldap->disconnect();
            $this->_ldap=null;
        }
    }

    protected function _createDn($dn)
    {
        if (substr($dn, -1)!==',') {
            $dn.=',';
        }
        $dn = $dn . TESTS_ZEND_LDAP_WRITEABLE_SUBTREE;
        return Zend_Ldap_Dn::fromString($dn)->toString(Zend_Ldap_Dn::ATTR_CASEFOLD_LOWER);
    }

    protected function _prepareLdapServer()
    {
        $this->_nodes=array(
            $this->_createDn('ou=Node,') =>
                array("objectClass" => "organizationalUnit", "ou" => "Node"),
            $this->_createDn('ou=Test1,ou=Node,') =>
                array("objectClass" => "organizationalUnit", "ou" => "Test1"),
            $this->_createDn('ou=Test2,ou=Node,') =>
                array("objectClass" => "organizationalUnit", "ou" => "Test2"),
            $this->_createDn('ou=Test1,') =>
                array("objectClass" => "organizationalUnit", "ou" => "Test1", "l" => "e"),
            $this->_createDn('ou=Test2,') =>
                array("objectClass" => "organizationalUnit", "ou" => "Test2", "l" => "d"),
            $this->_createDn('ou=Test3,') =>
                array("objectClass" => "organizationalUnit", "ou" => "Test3", "l" => "c"),
            $this->_createDn('ou=Test4,') =>
                array("objectClass" => "organizationalUnit", "ou" => "Test4", "l" => "b"),
            $this->_createDn('ou=Test5,') =>
                array("objectClass" => "organizationalUnit", "ou" => "Test5", "l" => "a"),
        );

        $ldap=$this->_ldap->getResource();
        foreach ($this->_nodes as $dn => $entry) {
            ldap_add($ldap, $dn, $entry);
        }
    }

    protected function _cleanupLdapServer()
    {
        $ldap=$this->_ldap->getResource();
        foreach (array_reverse($this->_nodes) as $dn => $entry) {
            ldap_delete($ldap, $dn);
        }
    }
}
