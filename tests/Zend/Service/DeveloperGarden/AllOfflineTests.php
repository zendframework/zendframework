<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

/**
 * Zend_Service_DeveloperGarden test suite
 *
 * @category   Zend
 * @package    Zend_Service_DeveloperGarden
 * @subpackage UnitTests
 */
class Zend_Service_DeveloperGarden_AllOfflineTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Service - DeveloperGarden - Offline');

        $suite->addTestSuite('Zend_Service_DeveloperGarden_OfflineClientTest');
        $suite->addTestSuite('Zend_Service_DeveloperGarden_OfflineCredentialTest');
        $suite->addTestSuite('Zend_Service_DeveloperGarden_OfflineConferenceCallTest');
        $suite->addTestSuite('Zend_Service_DeveloperGarden_OfflineSecurityTokenServerTest');
        $suite->addTestSuite('Zend_Service_DeveloperGarden_OfflineBaseUserServiceTest');
        $suite->addTestSuite('Zend_Service_DeveloperGarden_OfflineIpLocationTest');
        $suite->addTestSuite('Zend_Service_DeveloperGarden_OfflineLocalSearchParametersTest');

        return $suite;
    }
}
