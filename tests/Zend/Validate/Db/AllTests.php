<?php


require_once dirname(__FILE__) . '/../../../TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Validate_Db_AllTests::main');
}

require_once 'Zend/Validate/Db/RecordExistsTest.php';
require_once 'Zend/Validate/Db/NoRecordExistsTest.php';

class Zend_Validate_Db_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Validate_Db');

        $suite->addTestSuite('Zend_Validate_Db_RecordExistsTest');
        $suite->addTestSuite('Zend_Validate_Db_NoRecordExistsTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Validate_Db_AllTests::main') {
    Zend_Validate_File_AllTests::main();
}
