<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Rest_AllTests::main');

    set_include_path(dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'library' . PATH_SEPARATOR
                 . get_include_path());
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Zend/Rest/ServerTest.php';
require_once 'Zend/Rest/ClientTest.php';
require_once 'Zend/Rest/ResultTest.php';

class Zend_Rest_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Rest');

        $suite->addTestSuite('Zend_Rest_ServerTest');
        $suite->addTestSuite('Zend_Rest_ClientTest');
        $suite->addTestSuite('Zend_Rest_ResultTest');
       
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Rest_AllTests::main') {
    Zend_Rest_AllTests::main();
}
