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
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
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
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Amazon
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
