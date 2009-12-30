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
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Feed_AllTests::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * Exclude from code coverage report
 */
PHPUnit_Util_Filter::addFileToFilter(__FILE__);


require_once 'Zend/Barcode/FactoryTest.php';

require_once 'Zend/Barcode/Object/Code39Test.php';
require_once 'Zend/Barcode/Object/Code25Test.php';
require_once 'Zend/Barcode/Object/Int25Test.php';
require_once 'Zend/Barcode/Object/Itf14Test.php';
require_once 'Zend/Barcode/Object/IdentcodeTest.php';
require_once 'Zend/Barcode/Object/LeitcodeTest.php';
require_once 'Zend/Barcode/Object/ErrorTest.php';

require_once 'Zend/Barcode/Renderer/ImageTest.php';
require_once 'Zend/Barcode/Renderer/PdfTest.php';

/**
 * @category   Zend
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Feed
 */
class Zend_Barcode_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Barcode');

        $suite->addTestSuite('Zend_Barcode_FactoryTest');

        $suite->addTestSuite('Zend_Barcode_Object_Code39Test');
        $suite->addTestSuite('Zend_Barcode_Object_Code25Test');
        $suite->addTestSuite('Zend_Barcode_Object_Int25Test');
        $suite->addTestSuite('Zend_Barcode_Object_Itf14Test');
        $suite->addTestSuite('Zend_Barcode_Object_IdentcodeTest');
        $suite->addTestSuite('Zend_Barcode_Object_LeitcodeTest');
        $suite->addTestSuite('Zend_Barcode_Object_ErrorTest');

        $suite->addTestSuite('Zend_Barcode_Renderer_ImageTest');
        $suite->addTestSuite('Zend_Barcode_Renderer_PdfTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Barcode_AllTests::main') {
    Zend_Barcode_AllTests::main();
}
