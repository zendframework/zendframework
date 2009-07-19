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
 * @package    Zend
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_AllTests::main');
}

require_once 'Zend/AclTest.php';
require_once 'Zend/Amf/AllTests.php';
require_once 'Zend/Application/AllTests.php';
require_once 'Zend/Auth/AllTests.php';
require_once 'Zend/Cache/AllTests.php';
require_once 'Zend/Captcha/AllTests.php';
require_once 'Zend/CodeGenerator/Php/AllTests.php';
require_once 'Zend/Db/AllTests.php';
require_once 'Zend/Dom/AllTests.php';
require_once 'Zend/ConfigTest.php';
require_once 'Zend/Config/AllTests.php';
require_once 'Zend/Console/GetoptTest.php';
require_once 'Zend/Controller/AllTests.php';
require_once 'Zend/CurrencyTest.php';
require_once 'Zend/DateTest.php';
require_once 'Zend/Date/AllTests.php';
require_once 'Zend/DebugTest.php';
require_once 'Zend/Feed/AllTests.php';
require_once 'Zend/File/AllTests.php';
require_once 'Zend/FilterTest.php';
require_once 'Zend/Filter/AllTests.php';
require_once 'Zend/Form/AllTests.php';
require_once 'Zend/Gdata/AllTests.php';
require_once 'Zend/Http/AllTests.php';
require_once 'Zend/InfoCard/AllTests.php';
require_once 'Zend/JsonTest.php';
require_once 'Zend/Json/AllTests.php';
require_once 'Zend/Layout/AllTests.php';
require_once 'Zend/Ldap/AllTests.php';
require_once 'Zend/LoaderTest.php';
require_once 'Zend/Loader/AllTests.php';
require_once 'Zend/LocaleTest.php';
require_once 'Zend/Locale/AllTests.php';
require_once 'Zend/Log/AllTests.php';
require_once 'Zend/MailTest.php';
require_once 'Zend/Mail/AllTests.php';
require_once 'Zend/Measure/AllTests.php';
require_once 'Zend/Memory/AllTests.php';
require_once 'Zend/MimeTest.php';
require_once 'Zend/Mime/AllTests.php';
require_once 'Zend/OpenIdTest.php';
require_once 'Zend/OpenId/AllTests.php';
require_once 'Zend/Paginator/AllTests.php';
require_once 'Zend/Pdf/AllTests.php';
require_once 'Zend/RegistryTest.php';
require_once 'Zend/Rest/AllTests.php';
require_once 'Zend/Queue/AllTests.php';
require_once 'Zend/Search/Lucene/AllTests.php';
require_once 'Zend/Server/AllTests.php';
require_once 'Zend/Service/AllTests.php';
require_once 'Zend/Session/AllTests.php';
require_once 'Zend/Soap/AllTests.php';
require_once 'Zend/Tag/AllTests.php';
require_once 'Zend/Test/AllTests.php';
require_once 'Zend/Text/AllTests.php';
require_once 'Zend/TimeSyncTest.php';
require_once 'Zend/TranslateTest.php';
require_once 'Zend/Translate/Adapter/AllTests.php';
require_once 'Zend/UriTest.php';
require_once 'Zend/Uri/AllTests.php';
require_once 'Zend/ValidateTest.php';
require_once 'Zend/Validate/AllTests.php';
require_once 'Zend/VersionTest.php';
require_once 'Zend/ViewTest.php';
require_once 'Zend/View/AllTests.php';
if (PHP_OS != 'AIX') {
    require_once 'Zend/Wildfire/AllTests.php';
}
require_once 'Zend/XmlRpc/AllTests.php';

/**
 * @category   Zend
 * @package    Zend
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend');

        $suite->addTestSuite('Zend_AclTest');
        $suite->addTest(Zend_Amf_AllTests::suite());
        $suite->addTest(Zend_Application_AllTests::suite());
        $suite->addTest(Zend_Auth_AllTests::suite());
        $suite->addTest(Zend_Cache_AllTests::suite());
        $suite->addTest(Zend_Captcha_AllTests::suite());
        $suite->addTest(Zend_CodeGenerator_Php_AllTests::suite());
        $suite->addTestSuite('Zend_ConfigTest');
        $suite->addTest(Zend_Config_AllTests::suite());
        $suite->addTestSuite('Zend_Console_GetoptTest');
        $suite->addTest(Zend_Controller_AllTests::suite());
        $suite->addTestSuite('Zend_CurrencyTest');
        $suite->addTestSuite('Zend_DateTest');
        $suite->addTest(Zend_Date_AllTests::suite());
        $suite->addTest(Zend_Db_AllTests::suite());
        $suite->addTestSuite('Zend_DebugTest');
        $suite->addTest(Zend_Dom_AllTests::suite());
        $suite->addTest(Zend_Feed_AllTests::suite());
        $suite->addTest(Zend_File_AllTests::suite());
        $suite->addTestSuite('Zend_FilterTest');
        $suite->addTest(Zend_Filter_AllTests::suite());
        $suite->addTest(Zend_Form_AllTests::suite());
        $suite->addTest(Zend_Gdata_AllTests::suite());
        $suite->addTest(Zend_Http_AllTests::suite());
        $suite->addTest(Zend_InfoCard_AllTests::suite());
        $suite->addTestSuite('Zend_JsonTest');
        $suite->addTest(Zend_Json_AllTests::suite());
        $suite->addTest(Zend_Layout_AllTests::suite());
        $suite->addTest(Zend_Ldap_AllTests::suite());
        $suite->addTestSuite('Zend_LoaderTest');
        $suite->addTest(Zend_Loader_AllTests::suite());
        $suite->addTestSuite('Zend_LocaleTest');
        $suite->addTest(Zend_Locale_AllTests::suite());
        $suite->addTest(Zend_Log_AllTests::suite());
        $suite->addTestSuite('Zend_MailTest');
        $suite->addTest(Zend_Measure_AllTests::suite());
        $suite->addTest(Zend_Memory_AllTests::suite());
        $suite->addTestSuite('Zend_MimeTest');
        $suite->addTest(Zend_Mime_AllTests::suite());
        $suite->addTestSuite('Zend_OpenIdTest');
        $suite->addTest(Zend_OpenId_AllTests::suite());
        $suite->addTest(Zend_Paginator_AllTests::suite());
        $suite->addTest(Zend_Pdf_AllTests::suite());
        $suite->addTestSuite('Zend_RegistryTest');
        $suite->addTest(Zend_Rest_AllTests::suite());
        $suite->addTest(Zend_Queue_AllTests::suite());
        $suite->addTest(Zend_Search_Lucene_AllTests::suite());
        $suite->addTest(Zend_Server_AllTests::suite());
        $suite->addTest(Zend_Service_AllTests::suite());
// Commenting out temporarily; Zend_Session tests fail unless output buffering is enabled, 
// but ob masks other issues, leads to large memory usage, and gives no 
// feedback when segfaults happen.
//        $suite->addTest(Zend_Session_AllTests::suite());
        $suite->addTest(Zend_Soap_AllTests::suite());
        $suite->addTest(Zend_Tag_AllTests::suite());
        $suite->addTest(Zend_Test_AllTests::suite());
        $suite->addTest(Zend_Text_AllTests::suite());
        $suite->addTestSuite('Zend_TimeSyncTest');
        $suite->addTestSuite('Zend_TranslateTest');
        $suite->addTest(Zend_Translate_Adapter_AllTests::suite());
        $suite->addTestSuite('Zend_UriTest');
        $suite->addTest(Zend_Uri_AllTests::suite());
        $suite->addTestSuite('Zend_ValidateTest');
        $suite->addTest(Zend_Validate_AllTests::suite());
        $suite->addTestSuite('Zend_ViewTest');
        $suite->addTest(Zend_View_AllTests::suite());
        $suite->addTestSuite('Zend_VersionTest');
        if (PHP_OS != 'AIX') {
            $suite->addTest(Zend_Wildfire_AllTests::suite());
        }
        $suite->addTest(Zend_XmlRpc_AllTests::suite());

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_AllTests::main') {
    Zend_AllTests::main();
}
