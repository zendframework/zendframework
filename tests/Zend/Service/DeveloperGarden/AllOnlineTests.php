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
class Zend_Service_DeveloperGarden_AllOnlineTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Service - DeveloperGarden - Online');

        $suite->addTestSuite('Zend_Service_DeveloperGarden_SecurityTokenServerTest');
        $suite->addTestSuite('Zend_Service_DeveloperGarden_BaseUserServiceTest');
        $suite->addTestSuite('Zend_Service_DeveloperGarden_IpLocationTest');
        $suite->addTestSuite('Zend_Service_DeveloperGarden_LocalSearchTest');
        $suite->addTestSuite('Zend_Service_DeveloperGarden_SendSmsTest');
        $suite->addTestSuite('Zend_Service_DeveloperGarden_SmsValidationTest');
        $suite->addTestSuite('Zend_Service_DeveloperGarden_VoiceCallTest');
        $suite->addTestSuite('Zend_Service_DeveloperGarden_ConferenceCallTest');

        return $suite;
    }
}
