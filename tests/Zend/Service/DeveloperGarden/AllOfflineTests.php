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
 * @package    Zend_Service_DeveloperGarden
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_DeveloperGarden_AllTests::main');
}

/**
 * Test helper
 */


/**
 * Zend_Service_DeveloperGarden test suite
 *
 * @category   Zend
 * @package    Zend_Service_DeveloperGarden
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Zend_Service_DeveloperGarden_AllOfflineTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Service - DeveloperGarden - Offline');

        $suite->addTestSuite('Zend_Service_DeveloperGarden_OfflineClientTest');
        $suite->addTestSuite('Zend_Service_DeveloperGarden_OfflineCredentialTest');
        $suite->addTestSuite('Zend_Service_DeveloperGarden_OfflineConferenceCallTest');
        $suite->addTestSuite('Zend_Service_DeveloperGarden_OfflineSecurityTokenServerTest');
        $suite->addTestSuite('Zend_Service_DeveloperGarden_OfflineBaseUserServiceTest');
        $suite->addTestSuite('Zend_Service_DeveloperGarden_OfflineIpLocationTest');
        $suite->addTestSuite('Zend_Service_DeveloperGarden_OfflineLocalSearchParametersTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Service_DeveloperGarden_AllOfflineTests::main') {
    Zend_Service_DeveloperGarden_AllOfflineTests::main();
}
