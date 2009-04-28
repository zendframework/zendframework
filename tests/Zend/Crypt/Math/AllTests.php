<?php

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Crypt_Math_AllTests::main');
}

require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'BigInteger/BcmathTest.php';
require_once 'BigInteger/GmpTest.php';

class Zend_Crypt_Math_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Math');

        $suite->addTestSuite('Zend_Crypt_Math_BigInteger_BcmathTest');
        $suite->addTestSuite('Zend_Crypt_Math_BigInteger_GmpTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Crypt_Math_AllTests::main') {
    Zend_Crypt_Math_AllTests::main();
}
