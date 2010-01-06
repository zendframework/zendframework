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
 * @package    Zend_Translate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Translate_Adapter_AllTests::main');
}

require_once 'Zend/Translate/Adapter/ArrayTest.php';
require_once 'Zend/Translate/Adapter/CsvTest.php';
require_once 'Zend/Translate/Adapter/GettextTest.php';
require_once 'Zend/Translate/Adapter/IniTest.php';
require_once 'Zend/Translate/Adapter/QtTest.php';
require_once 'Zend/Translate/Adapter/TbxTest.php';
require_once 'Zend/Translate/Adapter/TmxTest.php';
require_once 'Zend/Translate/Adapter/XliffTest.php';
require_once 'Zend/Translate/Adapter/XmlTmTest.php';

/**
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Translate
 */
class Zend_Translate_Adapter_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Translate_Adapter');

        $suite->addTestSuite('Zend_Translate_Adapter_ArrayTest');
        $suite->addTestSuite('Zend_Translate_Adapter_CsvTest');
        $suite->addTestSuite('Zend_Translate_Adapter_GettextTest');
        $suite->addTestSuite('Zend_Translate_Adapter_IniTest');
        $suite->addTestSuite('Zend_Translate_Adapter_QtTest');
        $suite->addTestSuite('Zend_Translate_Adapter_TbxTest');
        $suite->addTestSuite('Zend_Translate_Adapter_TmxTest');
        $suite->addTestSuite('Zend_Translate_Adapter_XliffTest');
        $suite->addTestSuite('Zend_Translate_Adapter_XmlTmTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Translate_Adapter_AllTests::main') {
    Zend_Translate_Adapter_AllTests::main();
}
