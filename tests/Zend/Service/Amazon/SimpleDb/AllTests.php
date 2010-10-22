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
 * @package    Zend_Service_Amazon_SimpleDb
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: AllTests.php 23004 2010-09-24 15:14:34Z matthew $
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_Amazon_SimpleDb_AllTests::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../../TestHelper.php';

/**
 * Exclude from code coverage report
 */
PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * @see Zend_Service_Amazon_SimpleDb_OfflineTest
 */
require_once 'Zend/Service/Amazon/SimpleDb/OfflineTest.php';

/**
 * @see Zend_Service_Amazon_SimpleDb_OnlineTest
 */
require_once 'Zend/Service/Amazon/SimpleDb/OnlineTest.php';

/**
 * @category   Zend
 * @package    Zend_Service_Amazon_SimpleDb
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Amazon_SimpleDb_AllTests
{
    /**
     * Runs this test suite
     *
     * @return void
     */
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    /**
     * Creates and returns this test suite
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Service - Amazon - SimpleDB');

        if (defined('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ENABLED')
            && constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ENABLED')
            && defined('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ACCESSKEY')
            && defined('TESTS_ZEND_SERVICE_AMAZON_ONLINE_SECRETKEY')
        ) {
            $suite->addTestSuite('Zend_Service_Amazon_SimpleDb_OnlineTest');
        } else {
            $suite->addTestSuite('Zend_Service_Amazon_SimpleDb_OfflineTest');
        }

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Service_Amazon_SimpleDb_AllTests::main') {
    Zend_Service_Amazon_SimpleDb_AllTests::main();
}
