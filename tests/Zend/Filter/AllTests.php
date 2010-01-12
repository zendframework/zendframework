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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Filter_AllTests::main');
}

require_once 'Zend/Filter/AlnumTest.php';
require_once 'Zend/Filter/AlphaTest.php';
require_once 'Zend/Filter/BaseNameTest.php';
require_once 'Zend/Filter/BooleanTest.php';
require_once 'Zend/Filter/CallbackTest.php';
require_once 'Zend/Filter/CompressTest.php';
require_once 'Zend/Filter/DecompressTest.php';
require_once 'Zend/Filter/DecryptTest.php';
require_once 'Zend/Filter/DigitsTest.php';
require_once 'Zend/Filter/DirTest.php';
require_once 'Zend/Filter/EncryptTest.php';
require_once 'Zend/Filter/HtmlEntitiesTest.php';
require_once 'Zend/Filter/InflectorTest.php';
require_once 'Zend/Filter/InputTest.php';
require_once 'Zend/Filter/IntTest.php';
require_once 'Zend/Filter/NullTest.php';
require_once 'Zend/Filter/PregReplaceTest.php';
require_once 'Zend/Filter/RealPathTest.php';
require_once 'Zend/Filter/StringToLowerTest.php';
require_once 'Zend/Filter/StringToUpperTest.php';
require_once 'Zend/Filter/StringTrimTest.php';
require_once 'Zend/Filter/StripNewlinesTest.php';
require_once 'Zend/Filter/StripTagsTest.php';

/*
require_once 'Zend/Filter/Compress/Bz2Test.php';
require_once 'Zend/Filter/Compress/GzTest.php';
require_once 'Zend/Filter/Compress/LzfTest.php';
require_once 'Zend/Filter/Compress/RarTest.php';
require_once 'Zend/Filter/Compress/TarTest.php';
require_once 'Zend/Filter/Compress/ZipTest.php';

require_once 'Zend/Filter/Encrypt/McryptTest.php';
require_once 'Zend/Filter/Encrypt/OpensslTest.php';

require_once 'Zend/Filter/File/DecryptTest.php';
require_once 'Zend/Filter/File/EncryptTest.php';
require_once 'Zend/Filter/File/LowerCaseTest.php';
require_once 'Zend/Filter/File/RenameTest.php';
require_once 'Zend/Filter/File/UpperCaseTest.php';

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
*/

require_once 'Zend/Filter/Compress/AllTests.php';
require_once 'Zend/Filter/Encrypt/AllTests.php';
require_once 'Zend/Filter/File/AllTests.php';
require_once 'Zend/Filter/Word/AllTests.php';

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Filter
 */
class Zend_Filter_AllTests
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
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Filter');

        $suite->addTestSuite('Zend_Filter_AlnumTest');
        $suite->addTestSuite('Zend_Filter_AlphaTest');
        $suite->addTestSuite('Zend_Filter_BaseNameTest');
        $suite->addTestSuite('Zend_Filter_BooleanTest');
        $suite->addTestSuite('Zend_Filter_CallbackTest');
        $suite->addTestSuite('Zend_Filter_CompressTest');
        $suite->addTestSuite('Zend_Filter_DecompressTest');
        $suite->addTestSuite('Zend_Filter_DecryptTest');
        $suite->addTestSuite('Zend_Filter_DigitsTest');
        $suite->addTestSuite('Zend_Filter_DirTest');
        $suite->addTestSuite('Zend_Filter_EncryptTest');
        $suite->addTestSuite('Zend_Filter_HtmlEntitiesTest');
        $suite->addTestSuite('Zend_Filter_InflectorTest');
        $suite->addTestSuite('Zend_Filter_InputTest');
        $suite->addTestSuite('Zend_Filter_IntTest');
        $suite->addTestSuite('Zend_Filter_PregReplaceTest');
        $suite->addTestSuite('Zend_Filter_RealPathTest');
        $suite->addTestSuite('Zend_Filter_StringToLowerTest');
        $suite->addTestSuite('Zend_Filter_StringToUpperTest');
        $suite->addTestSuite('Zend_Filter_StringTrimTest');
        $suite->addTestSuite('Zend_Filter_StripNewlinesTest');
        $suite->addTestSuite('Zend_Filter_StripTagsTest');

/*
        $suite->addTestSuite('Zend_Filter_Compress_Bz2Test');
        $suite->addTestSuite('Zend_Filter_Compress_GzTest');
        $suite->addTestSuite('Zend_Filter_Compress_LzfTest');
        $suite->addTestSuite('Zend_Filter_Compress_RarTest');
        $suite->addTestSuite('Zend_Filter_Compress_TarTest');
        $suite->addTestSuite('Zend_Filter_Compress_ZipTest');
        $suite->addTestSuite('Zend_Filter_Encrypt_McryptTest');
        $suite->addTestSuite('Zend_Filter_Encrypt_OpensslTest');
        $suite->addTestSuite('Zend_Filter_File_DecryptTest');
        $suite->addTestSuite('Zend_Filter_File_EncryptTest');
        $suite->addTestSuite('Zend_Filter_File_LowerCaseTest');
        $suite->addTestSuite('Zend_Filter_File_RenameTest');
        $suite->addTestSuite('Zend_Filter_File_UpperCaseTest');
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
*/

        $suite->addTest(Zend_Filter_Compress_AllTests::suite());
        $suite->addTest(Zend_Filter_Encrypt_AllTests::suite());
        $suite->addTest(Zend_Filter_File_AllTests::suite());
        $suite->addTest(Zend_Filter_Word_AllTests::suite());

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Filter_AllTests::main') {
    Zend_Filter_AllTests::main();
}
