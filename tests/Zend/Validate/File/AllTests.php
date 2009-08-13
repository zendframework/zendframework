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
 * @package    Zend_Validate_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Validate_File_AllTests::main');
}

require_once 'Zend/Validate/File/CountTest.php';
require_once 'Zend/Validate/File/Crc32Test.php';
require_once 'Zend/Validate/File/ExcludeExtensionTest.php';
require_once 'Zend/Validate/File/ExcludeMimeTypeTest.php';
require_once 'Zend/Validate/File/ExistsTest.php';
require_once 'Zend/Validate/File/ExtensionTest.php';
require_once 'Zend/Validate/File/FilesSizeTest.php';
require_once 'Zend/Validate/File/HashTest.php';
require_once 'Zend/Validate/File/ImageSizeTest.php';
require_once 'Zend/Validate/File/IsCompressedTest.php';
require_once 'Zend/Validate/File/IsImageTest.php';
require_once 'Zend/Validate/File/Md5Test.php';
require_once 'Zend/Validate/File/MimeTypeTest.php';
require_once 'Zend/Validate/File/NotExistsTest.php';
require_once 'Zend/Validate/File/Sha1Test.php';
require_once 'Zend/Validate/File/SizeTest.php';
require_once 'Zend/Validate/File/UploadTest.php';

/**
 * @category   Zend
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 * @group      Zend_Validate_File
 */
class Zend_Validate_File_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Validate_File');

        $suite->addTestSuite('Zend_Validate_File_CountTest');
        $suite->addTestSuite('Zend_Validate_File_Crc32Test');
        $suite->addTestSuite('Zend_Validate_File_ExcludeExtensionTest');
        $suite->addTestSuite('Zend_Validate_File_ExistsTest');
        $suite->addTestSuite('Zend_Validate_File_ExtensionTest');
        $suite->addTestSuite('Zend_Validate_File_FilesSizeTest');
        $suite->addTestSuite('Zend_Validate_File_HashTest');
        $suite->addTestSuite('Zend_Validate_File_ImageSizeTest');
        $suite->addTestSuite('Zend_Validate_File_IsCompressedTest');
        $suite->addTestSuite('Zend_Validate_File_IsImageTest');
        $suite->addTestSuite('Zend_Validate_File_Md5Test');
        $suite->addTestSuite('Zend_Validate_File_MimeTypeTest');
        $suite->addTestSuite('Zend_Validate_File_NotExistsTest');
        $suite->addTestSuite('Zend_Validate_File_Sha1Test');
        $suite->addTestSuite('Zend_Validate_File_SizeTest');
        $suite->addTestSuite('Zend_Validate_File_UploadTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Validate_File_AllTests::main') {
    Zend_Validate_File_AllTests::main();
}
