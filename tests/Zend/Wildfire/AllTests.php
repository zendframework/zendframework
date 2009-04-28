<?php

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Wildfire_AllTests::main');
}

require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'Zend/Wildfire/WildfireTest.php';


class Zend_Wildfire_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Wildfire');

        $suite->addTestSuite('Zend_Wildfire_WildfireTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Wildfire_AllTests::main') {
    Zend_Wildfire_AllTests::main();
}
