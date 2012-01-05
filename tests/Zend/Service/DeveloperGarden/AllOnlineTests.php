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
 * @package    Zend_Service_DeveloperGarden
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Zend_Service_DeveloperGarden test suite
 *
 * @category   Zend
 * @package    Zend_Service_DeveloperGarden
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
