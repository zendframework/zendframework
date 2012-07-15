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
 * Zend_Service_DeveloperGarden test case
 *
 * @category   Zend
 * @package    Zend_Service_DeveloperGarden
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_DeveloperGarden
 */
class Zend_Service_DeveloperGarden_OfflineIpLocationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Service_DeveloperGarden_OfflineIpLocation_Mock
     */
    protected $_service = null;

    public function setUp()
    {
        if (!defined('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN')) {
            define('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN', 'Unknown');
        }
        if (!defined('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_PASSWORD')) {
            define('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_PASSWORD', 'Unknown');
        }
        $config = array(
            'username' => TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN,
            'password' => TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_PASSWORD,
        );
        $this->service = new Zend_Service_DeveloperGarden_OfflineIpLocation_Mock($config);
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Exception
     */
    public function testIpAddressVersion()
    {
        $ip = new Zend_Service_DeveloperGarden_IpLocation_IpAddress('127.0.0.1', 4);
        $this->assertNotNull($ip->setVersion(6));
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Exception
     */
    public function testIpAddressInValid()
    {
        $ip = new Zend_Service_DeveloperGarden_IpLocation_IpAddress('266.266.266.266');
    }
}

class Zend_Service_DeveloperGarden_OfflineIpLocation_Mock
    extends Zend_Service_DeveloperGarden_IpLocation
{

}
