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
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Reflection_AllTests::main');
}

require_once 'Zend/Reflection/ClassTest.php';
require_once 'Zend/Reflection/Docblock/AllTests.php';
require_once 'Zend/Reflection/DocblockTest.php';
require_once 'Zend/Reflection/ExtensionTest.php';
require_once 'Zend/Reflection/FileTest.php';
require_once 'Zend/Reflection/FunctionTest.php';
require_once 'Zend/Reflection/MethodTest.php';
require_once 'Zend/Reflection/ParameterTest.php';
require_once 'Zend/Reflection/PropertyTest.php';

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Reflection
 */
class Zend_Reflection_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Reflection');

        $suite->addTestSuite('Zend_Reflection_ClassTest');
        $suite->addTest(Zend_Reflection_Docblock_AllTests::suite());
        $suite->addTestSuite('Zend_Reflection_DocblockTest');
        $suite->addTestSuite('Zend_Reflection_ExtensionTest');
        $suite->addTestSuite('Zend_Reflection_FileTest');
        $suite->addTestSuite('Zend_Reflection_FunctionTest');
        $suite->addTestSuite('Zend_Reflection_MethodTest');
        $suite->addTestSuite('Zend_Reflection_ParameterTest');
        $suite->addTestSuite('Zend_Reflection_PropertyTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Reflection_AllTests::main') {
    Zend_Reflection_AllTests::main();
}
