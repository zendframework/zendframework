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

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Ldap_AllTests::main');
}

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Ldap_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Ldap');

        /**
         * @see Zend_Ldap_OfflineTest
         */
        require_once 'Zend/Ldap/OfflineTest.php';
        $suite->addTestSuite('Zend_Ldap_OfflineTest');
        /**
         * @see Zend_Ldap_AttributeTest
         */
        require_once 'Zend/Ldap/AttributeTest.php';
        $suite->addTestSuite('Zend_Ldap_AttributeTest');
        /**
         * @see Zend_Ldap_ConverterTest
         */
        require_once 'Zend/Ldap/ConverterTest.php';
        $suite->addTestSuite('Zend_Ldap_ConverterTest');
        /**
         * @see Zend_Ldap_Dn_AllTests
         */
        require_once 'Zend/Ldap/Dn/AllTests.php';
        $suite->addTest(Zend_Ldap_Dn_AllTests::suite());
        /**
         * @see Zend_Ldap_FilterTest
         */
        require_once 'Zend/Ldap/FilterTest.php';
        $suite->addTestSuite('Zend_Ldap_FilterTest');
        /**
         * @see Zend_Ldap_Node_AllTests
         */
        require_once 'Zend/Ldap/Node/AllTests.php';
        $suite->addTest(Zend_Ldap_Node_AllTests::suite());
        /**
         * @see Zend_Ldap_Ldif_AllTests
         */
        require_once 'Zend/Ldap/Ldif/AllTests.php';
        $suite->addTest(Zend_Ldap_Ldif_AllTests::suite());

        if (defined('TESTS_ZEND_LDAP_ONLINE_ENABLED')
            && constant('TESTS_ZEND_LDAP_ONLINE_ENABLED')) {
            /**
             * @see Zend_Ldap_ConnectTest
             */
            require_once 'Zend/Ldap/ConnectTest.php';
            $suite->addTestSuite('Zend_Ldap_ConnectTest');
            /**
             * @see Zend_Ldap_BindTest
             */
            require_once 'Zend/Ldap/BindTest.php';
            $suite->addTestSuite('Zend_Ldap_BindTest');
            /**
             * @see Zend_Ldap_CanonTest
             */
            require_once 'Zend/Ldap/CanonTest.php';
            $suite->addTestSuite('Zend_Ldap_CanonTest');
            /**
             * @see Zend_Ldap_SearchTest
             */
            require_once 'Zend/Ldap/SearchTest.php';
            $suite->addTestSuite('Zend_Ldap_SearchTest');
            /**
             * @see Zend_Ldap_CrudTest
             */
            require_once 'Zend/Ldap/CrudTest.php';
            $suite->addTestSuite('Zend_Ldap_CrudTest');
            /**
             * @see Zend_Ldap_CopyRenameTest
             */
            require_once 'Zend/Ldap/CopyRenameTest.php';
            $suite->addTestSuite('Zend_Ldap_CopyRenameTest');
            /**
             * @see Zend_Ldap_ChangePasswordTest
             */
            require_once 'Zend/Ldap/ChangePasswordTest.php';
            $suite->addTestSuite('Zend_Ldap_ChangePasswordTest');

        } else {
            $suite->addTest(new Zend_Ldap_SkipOnlineTests());
        }

        return $suite;
    }
}

class Zend_Ldap_SkipOnlineTests extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->markTestSkipped('Zend_Ldap online tests not enabled in TestConfiguration.php');
    }

    public function testNothing()
    {
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Ldap_AllTests::main') {
    Zend_Ldap_AllTests::main();
}
