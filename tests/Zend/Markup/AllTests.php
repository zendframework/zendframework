<?php

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Markup_AllTests::main');
}

require_once dirname(__FILE__) . '/../../TestHelper.php';

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
require_once 'Zend/Markup/BbcodeAndHtmlTest.php';
require_once 'Zend/Markup/TextileAndHtmlTest.php';
require_once 'Zend/Markup/ParserIntegrityTest.php';
require_once 'Zend/Markup/FactoryTest.php';


class Zend_Markup_AllTests
{
    public static function main()
    {
        $parameters = array();

        if (TESTS_GENERATE_REPORT && extension_loaded('xdebug')) {
            $parameters['reportDirectory'] = TESTS_GENERATE_REPORT_TARGET;
        }

        PHPUnit_TextUI_TestRunner::run(self::suite(), $parameters);
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Markup');

        $suite->addTestSuite('Zend_Markup_BbcodeAndHtmlTest');
        $suite->addTestSuite('Zend_Markup_TextileAndHtmlTest');
        $suite->addTestSuite('Zend_Markup_ParserIntegrityTest');
        $suite->addTestSuite('Zend_Markup_FactoryTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Markup_AllTests::main') {
    Zend_Markup_AllTests::main();
}
