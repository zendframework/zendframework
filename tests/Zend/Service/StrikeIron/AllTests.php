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
 * @package    Zend_Service_StrikeIron
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
 
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_StrikeIron_AllTests::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * Exclude from code coverage report
 */
PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * @see Zend_Service_StrikeIron_StrikeIronTest
 */
require_once 'Zend/Service/StrikeIron/StrikeIronTest.php';

/**
 * @see Zend_Service_StrikeIron_BaseTest
 */
require_once 'Zend/Service/StrikeIron/BaseTest.php';

/**
 * @see Zend_Service_StrikeIron_DecoratorTest
 */
require_once 'Zend/Service/StrikeIron/DecoratorTest.php';

/**
 * @see Zend_Service_StrikeIron_ExceptionTest
 */
require_once 'Zend/Service/StrikeIron/ExceptionTest.php';

/**
 * @see Zend_Service_StrikeIron_SalesUseTaxBasicTest
 */
require_once 'Zend/Service/StrikeIron/SalesUseTaxBasicTest.php';

/**
 * @see Zend_Service_StrikeIron_USAddressVerificationBasicTest
 */
require_once 'Zend/Service/StrikeIron/USAddressVerificationTest.php';

/**
 * @see Zend_Service_StrikeIron_ZipCodeInfoTest.php
 */
require_once 'Zend/Service/StrikeIron/ZipCodeInfoTest.php';

/**
 * @see Zend_Service_StrikeIron_NoSoapTest.php
 */
require_once 'Zend/Service/StrikeIron/NoSoapTest.php';


/**
 * @category   Zend
 * @package    Zend_Service_StrikeIron
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_StrikeIron
 */
class Zend_Service_StrikeIron_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Service_StrikeIron');

        $suite->addTestSuite('Zend_Service_StrikeIron_NoSoapTest');

        if (!extension_loaded('soap')) {
            return $suite;
        }

        $suite->addTestSuite('Zend_Service_StrikeIron_StrikeIronTest');
        $suite->addTestSuite('Zend_Service_StrikeIron_DecoratorTest');
        $suite->addTestSuite('Zend_Service_StrikeIron_ExceptionTest');
        $suite->addTestSuite('Zend_Service_StrikeIron_BaseTest');
        $suite->addTestSuite('Zend_Service_StrikeIron_SalesUseTaxBasicTest');
        $suite->addTestSuite('Zend_Service_StrikeIron_USAddressVerificationTest');
        $suite->addTestSuite('Zend_Service_StrikeIron_ZipCodeInfoTest');
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Service_StrikeIron_AllTests::main') {
    Zend_Service_StrikeIron_AllTests::main();
}
