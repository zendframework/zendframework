<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Mime_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Zend/Mime/PartTest.php';
require_once 'Zend/Mime/MessageTest.php';

class Zend_Mime_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Mime');

        $suite->addTestSuite('Zend_Mime_PartTest');
        $suite->addTestSuite('Zend_Mime_MessageTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Mime_AllTests::main') {
    Zend_Mime_AllTests::main();
}
