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
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Validate_AllTests::main');
}


/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * @see Zend_Validate_AbstractTest
 */
require_once 'Zend/Validate/AbstractTest.php';

/**
 * @see Zend_Validate_AlnumTest
 */
require_once 'Zend/Validate/AlnumTest.php';

/**
 * @see Zend_Validate_AlphaTest
 */
require_once 'Zend/Validate/AlphaTest.php';

/**
 * @see Zend_Validate_BarcodeTest
 */
require_once 'Zend/Validate/BarcodeTest.php';

/**
 * @see Zend_Validate_BetweenTest
 */
require_once 'Zend/Validate/BetweenTest.php';

/**
 * @see Zend_Validate_CcnumTest
 */
require_once 'Zend/Validate/CcnumTest.php';

/**
 * @see Zend_Validate_DateTest
 */
require_once 'Zend/Validate/DateTest.php';

/**
 * @see Zend_Validate_Db_AllTests
 */
require_once 'Zend/Validate/Db/AllTests.php';

/**
 * @see Zend_Validate_DigitsTest
 */
require_once 'Zend/Validate/DigitsTest.php';

/**
 * @see Zend_Validate_EmailAddressTest
 */
require_once 'Zend/Validate/EmailAddressTest.php';

/**
 * @see Zend_Validate_File_AllTests
 */
require_once 'Zend/Validate/File/AllTests.php';

/**
 * @see Zend_Validate_FloatTest
 */
require_once 'Zend/Validate/FloatTest.php';

/**
 * @see Zend_Validate_GreaterThanTest
 */
require_once 'Zend/Validate/GreaterThanTest.php';

/**
 * @see Zend_Validate_HexTest
 */
require_once 'Zend/Validate/HexTest.php';

/**
 * @see Zend_Validate_HostnameTest
 */
require_once 'Zend/Validate/HostnameTest.php';

/**
 * @see Zend_Validate_IdenticalTest
 */
require_once 'Zend/Validate/IdenticalTest.php';

/**
 * @see Zend_Validate_InArrayTest
 */
require_once 'Zend/Validate/InArrayTest.php';

/**
 * @see Zend_Validate_IntTest
 */
require_once 'Zend/Validate/IntTest.php';

/**
 * @see Zend_Validate_IpTest
 */
require_once 'Zend/Validate/IpTest.php';

/**
 * @see Zend_Validate_LessThanTest
 */
require_once 'Zend/Validate/LessThanTest.php';

/**
 * @see Zend_Validate_MessageTest
 */
require_once 'Zend/Validate/MessageTest.php';

/**
 * @see Zend_Validate_NotEmptyTest
 */
require_once 'Zend/Validate/NotEmptyTest.php';

/**
 * @see Zend_Validate_RegexTest
 */
require_once 'Zend/Validate/RegexTest.php';

/**
 * @see Zend_Validate_Sitemap_AllTests
 */
require_once 'Zend/Validate/Sitemap/AllTests.php';

/**
 * @see Zend_Validate_StringLengthTest
 */
require_once 'Zend/Validate/StringLengthTest.php';


/**
 * @category   Zend
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Validate_AllTests
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
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Validate');

        $suite->addTestSuite('Zend_Validate_AbstractTest');
        $suite->addTestSuite('Zend_Validate_AlnumTest');
        $suite->addTestSuite('Zend_Validate_AlphaTest');
        $suite->addTestSuite('Zend_Validate_BarcodeTest');
        $suite->addTestSuite('Zend_Validate_BetweenTest');
        $suite->addTestSuite('Zend_Validate_CcnumTest');
        $suite->addTestSuite('Zend_Validate_DateTest');
        $suite->addTest(Zend_Validate_Db_AllTests::suite());
        $suite->addTestSuite('Zend_Validate_DigitsTest');
        $suite->addTestSuite('Zend_Validate_EmailAddressTest');
        $suite->addTest(Zend_Validate_File_AllTests::suite());
        $suite->addTestSuite('Zend_Validate_FloatTest');
        $suite->addTestSuite('Zend_Validate_GreaterThanTest');
        $suite->addTestSuite('Zend_Validate_HexTest');
        $suite->addTestSuite('Zend_Validate_HostnameTest');
        $suite->addTestSuite('Zend_Validate_IdenticalTest');
        $suite->addTestSuite('Zend_Validate_InArrayTest');
        $suite->addTestSuite('Zend_Validate_IntTest');
        $suite->addTestSuite('Zend_Validate_IpTest');
        $suite->addTestSuite('Zend_Validate_LessThanTest');
        $suite->addTestSuite('Zend_Validate_MessageTest');
        $suite->addTestSuite('Zend_Validate_NotEmptyTest');
        $suite->addTestSuite('Zend_Validate_RegexTest');
        $suite->addTest(Zend_Validate_Sitemap_AllTests::suite());
        $suite->addTestSuite('Zend_Validate_StringLengthTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Validate_AllTests::main') {
    Zend_Validate_AllTests::main();
}
