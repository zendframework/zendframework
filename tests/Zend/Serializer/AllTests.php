<?php

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Serializer_AllTests::main');
}

/** @see Zend_Serializer_Adapter_AllTests */
require_once dirname(__FILE__) . '/Adapter/AllTests.php';

/** @see Zend_Serializer_SerializerTest */
require_once dirname(__FILE__) . '/SerializerTest.php';

class Zend_Serializer_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend');

        /*
         * Performe Zend_Serializer_Adapter tests
         */
        $suite->addTest(Zend_Serializer_Adapter_AllTests::suite());

        /**
         * Performe Zend_Serializer tests
         */
        $suite->addTestSuite('Zend_Serializer_SerializerTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Serializer_AllTests::main') {
    Zend_Serializer_AllTests::main();
}
