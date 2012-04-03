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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Ldap;

use Zend\Ldap;

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Ldap
 */
abstract class AbstractOnlineTestCase extends AbstractTestCase
{
    /**
     * @var Ldap\Ldap
     */
    private $ldap;

    /**
     * @var array
     */
    private $nodes;

    /**
     * @return Ldap\Ldap
     */
    protected function getLDAP()
    {
        return $this->ldap;
    }

    protected function setUp()
    {
        if (!constant('TESTS_ZEND_LDAP_ONLINE_ENABLED')) {
            $this->markTestSkipped("Zend_Ldap online tests are not enabled");
        }

        $options = array(
            'host'     => TESTS_ZEND_LDAP_HOST,
            'username' => TESTS_ZEND_LDAP_USERNAME,
            'password' => TESTS_ZEND_LDAP_PASSWORD,
            'baseDn'   => TESTS_ZEND_LDAP_WRITEABLE_SUBTREE,
        );
        if (defined('TESTS_ZEND_LDAP_PORT') && TESTS_ZEND_LDAP_PORT != 389) {
            $options['port'] = TESTS_ZEND_LDAP_PORT;
        }
        if (defined('TESTS_ZEND_LDAP_USE_START_TLS')) {
            $options['useStartTls'] = TESTS_ZEND_LDAP_USE_START_TLS;
        }
        if (defined('TESTS_ZEND_LDAP_USE_SSL')) {
            $options['useSsl'] = TESTS_ZEND_LDAP_USE_SSL;
        }
        if (defined('TESTS_ZEND_LDAP_BIND_REQUIRES_DN')) {
            $options['bindRequiresDn'] = TESTS_ZEND_LDAP_BIND_REQUIRES_DN;
        }
        if (defined('TESTS_ZEND_LDAP_ACCOUNT_FILTER_FORMAT')) {
            $options['accountFilterFormat'] = TESTS_ZEND_LDAP_ACCOUNT_FILTER_FORMAT;
        }
        if (defined('TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME')) {
            $options['accountDomainName'] = TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME;
        }
        if (defined('TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME_SHORT')) {
            $options['accountDomainNameShort'] = TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME_SHORT;
        }

        $this->ldap = new Ldap\Ldap($options);
        $this->ldap->bind();
    }

    protected function tearDown()
    {
        if ($this->ldap !== null) {
            $this->ldap->disconnect();
            $this->ldap = null;
        }
    }

    protected function createDn($dn)
    {
        if (substr($dn, -1) !== ',') {
            $dn .= ',';
        }
        $dn = $dn . TESTS_ZEND_LDAP_WRITEABLE_SUBTREE;

        return Ldap\Dn::fromString($dn)->toString(Ldap\Dn::ATTR_CASEFOLD_LOWER);
    }

    protected function prepareLDAPServer()
    {
        $this->nodes = array(
            $this->createDn('ou=Node,')          =>
            array("objectClass" => "organizationalUnit",
                  "ou"          => "Node",
                  "postalCode"  => "1234"),
            $this->createDn('ou=Test1,ou=Node,') =>
            array("objectClass" => "organizationalUnit",
                  "ou"          => "Test1"),
            $this->createDn('ou=Test2,ou=Node,') =>
            array("objectClass" => "organizationalUnit",
                  "ou"          => "Test2"),
            $this->createDn('ou=Test1,')         =>
            array("objectClass" => "organizationalUnit",
                  "ou"          => "Test1",
                  "l"           => "e"),
            $this->createDn('ou=Test2,')         =>
            array("objectClass" => "organizationalUnit",
                  "ou"          => "Test2",
                  "l"           => "d"),
            $this->createDn('ou=Test3,')         =>
            array("objectClass" => "organizationalUnit",
                  "ou"          => "Test3",
                  "l"           => "c"),
            $this->createDn('ou=Test4,')         =>
            array("objectClass" => "organizationalUnit",
                  "ou"          => "Test4",
                  "l"           => "b"),
            $this->createDn('ou=Test5,')         =>
            array("objectClass" => "organizationalUnit",
                  "ou"          => "Test5",
                  "l"           => "a"),
        );

        $ldap = $this->ldap->getResource();
        foreach ($this->nodes as $dn => $entry) {
            ldap_add($ldap, $dn, $entry);
        }
    }

    protected function cleanupLDAPServer()
    {
        if (!constant('TESTS_ZEND_LDAP_ONLINE_ENABLED')) {
            return;
        }
        $ldap = $this->ldap->getResource();
        foreach (array_reverse($this->nodes) as $dn => $entry) {
            ldap_delete($ldap, $dn);
        }
    }
}
