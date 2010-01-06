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
 * @package    Zend_Service
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_AllTests::main');
}

require_once 'Zend/Service/AkismetTest.php';
require_once 'Zend/Service/Amazon/AllTests.php';
require_once 'Zend/Service/Audioscrobbler/AllTests.php';
require_once 'Zend/Service/Delicious/AllTests.php';
require_once 'Zend/Service/Flickr/AllTests.php';
require_once 'Zend/Service/LiveDocx/AllTests.php';
require_once 'Zend/Service/Nirvanix/AllTests.php';
require_once 'Zend/Service/ReCaptcha/AllTests.php';
require_once 'Zend/Service/Simpy/AllTests.php';
require_once 'Zend/Service/SlideShareTest.php';
require_once 'Zend/Service/StrikeIron/AllTests.php';
require_once 'Zend/Service/Technorati/AllTests.php';
require_once 'Zend/Service/TwitterTest.php';
require_once 'Zend/Service/TwitterSearchTest.php';
require_once 'Zend/Service/WindowsAzure/AllTests.php';
require_once 'Zend/Service/Yahoo/AllTests.php';

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 */
class Zend_Service_AllTests
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
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Service');

        $suite->addTestSuite('Zend_Service_AkismetTest');
        $suite->addTest(Zend_Service_Amazon_AllTests::suite());
        $suite->addTest(Zend_Service_Audioscrobbler_AllTests::suite());
        $suite->addTest(Zend_Service_Delicious_AllTests::suite());
        $suite->addTest(Zend_Service_Flickr_AllTests::suite());
        $suite->addTest(Zend_Service_LiveDocx_AllTests::suite());
        $suite->addTest(Zend_Service_Nirvanix_AllTests::suite());
        $suite->addTest(Zend_Service_ReCaptcha_AllTests::suite());
        $suite->addTest(Zend_Service_Simpy_AllTests::suite());
        $suite->addTestSuite('Zend_Service_SlideShareTest');
        $suite->addTest(Zend_Service_StrikeIron_AllTests::suite());
        $suite->addTest(Zend_Service_Technorati_AllTests::suite());
        $suite->addTestSuite('Zend_Service_TwitterTest');
        $suite->addTestSuite('Zend_Service_TwitterSearchTest');
        $suite->addTest(Zend_Service_WindowsAzure_AllTests::suite());
        $suite->addTest(Zend_Service_Yahoo_AllTests::suite());

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Service_AllTests::main') {
    Zend_Service_AllTests::main();
}
