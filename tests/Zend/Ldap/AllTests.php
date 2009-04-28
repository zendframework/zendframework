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

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Ldap_AllTests::main');
}

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * @see Zend_Ldap_OfflineTest
 */
require_once 'Zend/Ldap/OfflineTest.php';

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
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

        $suite->addTestSuite('Zend_Ldap_OfflineTest');

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
