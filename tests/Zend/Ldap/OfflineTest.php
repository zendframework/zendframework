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

/**
 * @see Zend_Ldap
 */
/**
 * @see Zend_Ldap_Exception
 */


/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Ldap
 */
class Zend_Ldap_OfflineTest extends PHPUnit_Framework_TestCase
{
    /**
     * Zend_Ldap instance
     *
     * @var Zend_Ldap
     */
    protected $_ldap = null;

    /**
     * Setup operations run prior to each test method:
     *
     * * Creates an instance of Zend_Ldap
     *
     * @return void
     */
    public function setUp()
    {
        if (!extension_loaded('ldap')) {
            $this->markTestSkipped('LDAP is not enabled');
        }
        $this->_ldap = new Zend_Ldap();
    }

    /**
     * @return void
     */
    public function testInvalidOptionResultsInException()
    {
        $optionName = 'invalid';
        try {
            $this->_ldap->setOptions(array($optionName => 'irrelevant'));
            $this->fail('Expected Zend_Ldap_Exception not thrown');
        } catch (Zend_Ldap_Exception $e) {
            $this->assertEquals("Unknown Zend_Ldap option: $optionName", $e->getMessage());
        }
    }

    public function testException()
    {
        $e = new Zend_Ldap_Exception(null, '', 0);
        $this->assertEquals('no exception message', $e->getMessage());
        $this->assertEquals(0, $e->getCode());
        $this->assertEquals(0, $e->getErrorCode());

        $e = new Zend_Ldap_Exception(null, '', 15);
        $this->assertEquals('0xf: no exception message', $e->getMessage());
        $this->assertEquals(15, $e->getCode());
        $this->assertEquals(15, $e->getErrorCode());
    }

    public function testOptionsGetter()
    {
        $options = array(
            'host' => TESTS_ZEND_LDAP_HOST,
            'username' => TESTS_ZEND_LDAP_USERNAME,
            'password' => TESTS_ZEND_LDAP_PASSWORD,
            'baseDn' => TESTS_ZEND_LDAP_BASE_DN,
        );
        $ldap = new Zend_Ldap($options);
        $this->assertEquals(array(
            'host'                   => TESTS_ZEND_LDAP_HOST,
            'port'                   => 0,
            'useSsl'                 => false,
            'username'               => TESTS_ZEND_LDAP_USERNAME,
            'password'               => TESTS_ZEND_LDAP_PASSWORD,
            'bindRequiresDn'         => false,
            'baseDn'                 => TESTS_ZEND_LDAP_BASE_DN,
            'accountCanonicalForm'   => null,
            'accountDomainName'      => null,
            'accountDomainNameShort' => null,
            'accountFilterFormat'    => null,
            'allowEmptyPassword'     => false,
            'useStartTls'            => false,
            'optReferrals'           => false,
            'tryUsernameSplit'       => true
        ), $ldap->getOptions());
    }

    public function testConfigObject()
    {
        /**
         * @see Zend_Config
         */
        $config = new Zend_Config(array(
            'host' => TESTS_ZEND_LDAP_HOST,
            'username' => TESTS_ZEND_LDAP_USERNAME,
            'password' => TESTS_ZEND_LDAP_PASSWORD,
            'baseDn' => TESTS_ZEND_LDAP_BASE_DN,
        ));
        $ldap = new Zend_Ldap($config);
        $this->assertEquals(array(
            'host'                   => TESTS_ZEND_LDAP_HOST,
            'port'                   => 0,
            'useSsl'                 => false,
            'username'               => TESTS_ZEND_LDAP_USERNAME,
            'password'               => TESTS_ZEND_LDAP_PASSWORD,
            'bindRequiresDn'         => false,
            'baseDn'                 => TESTS_ZEND_LDAP_BASE_DN,
            'accountCanonicalForm'   => null,
            'accountDomainName'      => null,
            'accountDomainNameShort' => null,
            'accountFilterFormat'    => null,
            'allowEmptyPassword'     => false,
            'useStartTls'            => false,
            'optReferrals'           => false,
            'tryUsernameSplit'       => true
        ), $ldap->getOptions());
    }
}
