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
 * @version    $Id: AllTests.php 16225 2009-06-21 20:34:55Z thomas $
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Filter_Compress_AllTests::main');
}

require_once 'Zend/Filter/Compress/Bz2Test.php';
require_once 'Zend/Filter/Compress/GzTest.php';
require_once 'Zend/Filter/Compress/LzfTest.php';
require_once 'Zend/Filter/Compress/RarTest.php';
require_once 'Zend/Filter/Compress/TarTest.php';
require_once 'Zend/Filter/Compress/ZipTest.php';

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter_Compress_AllTests
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
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Filter_Compress');

        $suite->addTestSuite('Zend_Filter_Compress_Bz2Test');
        $suite->addTestSuite('Zend_Filter_Compress_GzTest');
        $suite->addTestSuite('Zend_Filter_Compress_LzfTest');
        $suite->addTestSuite('Zend_Filter_Compress_RarTest');
        $suite->addTestSuite('Zend_Filter_Compress_TarTest');
        $suite->addTestSuite('Zend_Filter_Compress_ZipTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Filter_Compress_AllTests::main') {
    Zend_Filter_Compress_AllTests::main();
}
