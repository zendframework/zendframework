<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_Amazon_Ec2_AllTests::main');
}
/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../../TestHelper.php';

require_once 'Zend/Service/Amazon/Ec2/AvailabilityzonesTest.php';
require_once 'Zend/Service/Amazon/Ec2/EbsTest.php';
require_once 'Zend/Service/Amazon/Ec2/Ec2Test.php';
require_once 'Zend/Service/Amazon/Ec2/ElasticipTest.php';
require_once 'Zend/Service/Amazon/Ec2/ImageTest.php';
require_once 'Zend/Service/Amazon/Ec2/InstanceTest.php';
require_once 'Zend/Service/Amazon/Ec2/KeypairTest.php';
require_once 'Zend/Service/Amazon/Ec2/RegionTest.php';
require_once 'Zend/Service/Amazon/Ec2/SecuritygroupsTest.php';

/**
 * Static test suite.
 */
class Zend_Service_Amazon_Ec2_AllTests
{
    /**
     * Runs this test suite
     *
     * @return void
     */
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    /**
     * Constructs the test suite handler.
     */
    public function __construct()
    {
    }

    /**
     * Creates the suite.
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Service_Amazon - Ec2');

        $suite->addTestSuite('Zend_Service_Amazon_Ec2_AvailabilityzonesTest');
        $suite->addTestSuite('Zend_Service_Amazon_Ec2_EbsTest');
        $suite->addTestSuite('Zend_Service_Amazon_Ec2_Ec2Test');
        $suite->addTestSuite('Zend_Service_Amazon_Ec2_ElasticipTest');
        $suite->addTestSuite('Zend_Service_Amazon_Ec2_ImageTest');
        $suite->addTestSuite('Zend_Service_Amazon_Ec2_InstanceTest');
        $suite->addTestSuite('Zend_Service_Amazon_Ec2_KeypairTest');
        $suite->addTestSuite('Zend_Service_Amazon_Ec2_RegionTest');
        $suite->addTestSuite('Zend_Service_Amazon_Ec2_SecuritygroupsTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Service_Amazon_Ec2_AllTests::main') {
    Zend_Service_Amazon_Ec2_AllTests::main();
}
