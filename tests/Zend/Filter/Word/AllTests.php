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
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: AllTests.php 13797 2009-01-25 13:14:31Z thomas $
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Filter_Word_AllTests::main');
}


/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * Word filter tests
 */
require_once 'Zend/Filter/Word/CamelCaseToDashTest.php';
require_once 'Zend/Filter/Word/CamelCaseToSeparatorTest.php';
require_once 'Zend/Filter/Word/CamelCaseToUnderscoreTest.php';
require_once 'Zend/Filter/Word/DashToCamelCaseTest.php';
require_once 'Zend/Filter/Word/DashToSeparatorTest.php';
require_once 'Zend/Filter/Word/DashToUnderscoreTest.php';
require_once 'Zend/Filter/Word/SeparatorToCamelCaseTest.php';
require_once 'Zend/Filter/Word/SeparatorToDashTest.php';
require_once 'Zend/Filter/Word/SeparatorToSeparatorTest.php';
require_once 'Zend/Filter/Word/UnderscoreToCamelCaseTest.php';
require_once 'Zend/Filter/Word/UnderscoreToDashTest.php';
require_once 'Zend/Filter/Word/UnderscoreToSeparatorTest.php';

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Filter
 */
class Zend_Filter_Word_AllTests
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
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Filter_Word');

        $suite->addTestSuite('Zend_Filter_Word_CamelCaseToDashTest');
        $suite->addTestSuite('Zend_Filter_Word_CamelCaseToSeparatorTest');
        $suite->addTestSuite('Zend_Filter_Word_CamelCaseToUnderscoreTest');
        $suite->addTestSuite('Zend_Filter_Word_DashToCamelCaseTest');
        $suite->addTestSuite('Zend_Filter_Word_DashToSeparatorTest');
        $suite->addTestSuite('Zend_Filter_Word_DashToUnderscoreTest');
        $suite->addTestSuite('Zend_Filter_Word_SeparatorToCamelCaseTest');
        $suite->addTestSuite('Zend_Filter_Word_SeparatorToDashTest');
        $suite->addTestSuite('Zend_Filter_Word_SeparatorToSeparatorTest');
        $suite->addTestSuite('Zend_Filter_Word_UnderscoreToCamelCaseTest');
        $suite->addTestSuite('Zend_Filter_Word_UnderscoreToDashTest');
        $suite->addTestSuite('Zend_Filter_Word_UnderscoreToSeparatorTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Filter_Word_AllTests::main') {
    Zend_Filter_Word_AllTests::main();
}
