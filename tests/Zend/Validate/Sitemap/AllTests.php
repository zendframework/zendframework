<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Validate_Sitemap_AllTests::main');
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';

require_once 'Zend/Validate/Sitemap/ChangefreqTest.php';
require_once 'Zend/Validate/Sitemap/LastmodTest.php';
require_once 'Zend/Validate/Sitemap/LocTest.php';
require_once 'Zend/Validate/Sitemap/PriorityTest.php';

class Zend_Validate_Sitemap_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Validate - Sitemap');

        $suite->addTestSuite('Zend_Validate_Sitemap_ChangefreqTest');
        $suite->addTestSuite('Zend_Validate_Sitemap_LastmodTest');
        $suite->addTestSuite('Zend_Validate_Sitemap_LocTest');
        $suite->addTestSuite('Zend_Validate_Sitemap_PriorityTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Validate_Sitemap_AllTests::main') {
    Zend_Validate_Sitemap_AllTests::main();
}
