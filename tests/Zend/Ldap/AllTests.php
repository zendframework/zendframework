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

require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Ldap_AllTests::main');
}

require_once 'Zend/Ldap/OfflineTest.php';
require_once 'Zend/Ldap/AttributeTest.php';
require_once 'Zend/Ldap/ConverterTest.php';
require_once 'Zend/Ldap/Dn/AllTests.php';
require_once 'Zend/Ldap/FilterTest.php';
require_once 'Zend/Ldap/Node/AllTests.php';
require_once 'Zend/Ldap/Ldif/AllTests.php';

if (defined('TESTS_ZEND_LDAP_ONLINE_ENABLED')
    && constant('TESTS_ZEND_LDAP_ONLINE_ENABLED')) {
    require_once 'Zend/Ldap/ConnectTest.php';
    require_once 'Zend/Ldap/BindTest.php';
    require_once 'Zend/Ldap/CanonTest.php';
    require_once 'Zend/Ldap/SearchTest.php';
    require_once 'Zend/Ldap/CrudTest.php';
    require_once 'Zend/Ldap/CopyRenameTest.php';
    require_once 'Zend/Ldap/ChangePasswordTest.php';
}

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Ldap
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

        $suite->addTestSuite('Zend_Ldap_OfflineTest');
        $suite->addTestSuite('Zend_Ldap_AttributeTest');
        $suite->addTestSuite('Zend_Ldap_ConverterTest');
        $suite->addTest(Zend_Ldap_Dn_AllTests::suite());
        $suite->addTestSuite('Zend_Ldap_FilterTest');
        $suite->addTest(Zend_Ldap_Node_AllTests::suite());
        $suite->addTest(Zend_Ldap_Ldif_AllTests::suite());

        if (defined('TESTS_ZEND_LDAP_ONLINE_ENABLED')
            && constant('TESTS_ZEND_LDAP_ONLINE_ENABLED')) {
            $suite->addTestSuite('Zend_Ldap_ConnectTest');
            $suite->addTestSuite('Zend_Ldap_BindTest');
            $suite->addTestSuite('Zend_Ldap_CanonTest');
            $suite->addTestSuite('Zend_Ldap_SearchTest');
            $suite->addTestSuite('Zend_Ldap_CrudTest');
            $suite->addTestSuite('Zend_Ldap_CopyRenameTest');
            $suite->addTestSuite('Zend_Ldap_ChangePasswordTest');
        } else {
            $suite->addTest(new Zend_Ldap_SkipOnlineTests());
        }

        return $suite;
    }
}

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Ldap
 */
class Zend_Ldap_SkipOnlineTests extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->markTestSkipped('Zend_Ldap online tests not enabled in TestConfiguration.php');
    }

    /**
     * @group      Zend_Ldap
     */
    public function testNothing()
    {
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Ldap_AllTests::main') {
    Zend_Ldap_AllTests::main();
}

