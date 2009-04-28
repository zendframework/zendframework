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
 * @package    Zend_Locale
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Locale_AllTests::main');
}

if (!defined('TESTS_ZEND_LOCALE_FORMAT_SETLOCALE')) {
    /**
     * Read in user-defined test configuration if available; otherwise, read default test configuration.
     * This facilitates running "php AllTests.php" in this subdirectory or "phpunit Zend_Locale_AllTests".
     */
    $_test_configuration = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'
        . DIRECTORY_SEPARATOR .  'TestConfiguration.php';
    if (is_readable($_test_configuration)) {
        include_once $_test_configuration;
    } else if (is_readable("$_test_configuration.dist")) {
        include_once "$_test_configuration.dist";
    }
}

require_once dirname(__FILE__) . '/../../TestHelper.php';

// define('TESTS_ZEND_LOCALE_BCMATH_ENABLED', false); // uncomment to disable use of bcmath extension by Zend_Date

require_once 'Zend/Locale/DataTest.php';
require_once 'Zend/Locale/FormatTest.php';
require_once 'Zend/Locale/MathTest.php';

// echo "BCMATH is ", Zend_Locale_Math::isBcmathDisabled() ? 'disabled':'not disabled', "\n";

class Zend_Locale_AllTests
{
    public static function main()
    {
        if (defined('TESTS_ZEND_LOCALE_FORMAT_SETLOCALE') && TESTS_ZEND_LOCALE_FORMAT_SETLOCALE) {
            // run all tests in a special locale
            setlocale(LC_ALL, TESTS_ZEND_LOCALE_FORMAT_SETLOCALE);
        }

        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Locale');

        $suite->addTestSuite('Zend_Locale_DataTest');
        $suite->addTestSuite('Zend_Locale_FormatTest');
        $suite->addTestSuite('Zend_Locale_MathTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Locale_AllTests::main') {
    Zend_Locale_AllTests::main();
}
