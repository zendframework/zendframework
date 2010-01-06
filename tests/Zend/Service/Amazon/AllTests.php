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
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_Amazon_AllTests::main');
}

require_once 'Zend/Service/Amazon/OfflineTest.php';
require_once 'Zend/Service/Amazon/OnlineTest.php';
require_once 'Zend/Service/Amazon/Ec2/AllTests.php';
require_once 'Zend/Service/Amazon/S3/AllTests.php';
require_once 'Zend/Service/Amazon/Sqs/AllTests.php';

/**
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Amazon
 */
class Zend_Service_Amazon_AllTests
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
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Service_Amazon');

        $suite->addTestSuite('Zend_Service_Amazon_OfflineTest');
        if (defined('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ENABLED') &&
            constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ENABLED') &&
            defined('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ACCESSKEYID')) {
            $suite->addTestSuite('Zend_Service_Amazon_OnlineTest');
        } else {
            $suite->addTestSuite('Zend_Service_Amazon_OnlineTest_Skip');
        }

        $suite->addTest(Zend_Service_Amazon_Ec2_AllTests::suite());
        $suite->addTest(Zend_Service_Amazon_S3_AllTests::suite());
        $suite->addTest(Zend_Service_Amazon_Sqs_AllTests::suite());

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Service_Amazon_AllTests::main') {
    Zend_Service_Amazon_AllTests::main();
}
