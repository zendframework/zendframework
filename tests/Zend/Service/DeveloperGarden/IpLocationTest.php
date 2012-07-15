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
class Zend_Service_DeveloperGarden_IpLocationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Service_DeveloperGarden_IpLocation_Mock
     */
    protected $_service = null;

    public function setUp()
    {
        if (!defined('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_ENABLED') ||
            TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_ENABLED !== true) {
            $this->markTestSkipped('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_ENABLED is not enabled');
        }
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
        $this->service = new Zend_Service_DeveloperGarden_IpLocation_Mock($config);
    }

    public function testLocateValid()
    {
        try {
            $result = $this->service->locateIP('217.7.192.1');
            $this->assertInternalType('array', $result->getIpAddressLocation());
        } catch (Exception $e) {
            if ($e->getMessage() != 'quotas have exceeded') {
                throw $e;
            } else {
                $this->markTestSkipped('Quota exceeded.');
            }
        }
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Response_Exception
     */
    public function testLocateIpNotFound()
    {
        $this->service->locateIP('127.0.0.1');
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Exception
     */
    public function testLocateIpNotFoundShort()
    {
        $this->service->locateIP('127');
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Exception
     */
    public function testLocateIpNotFoundString()
    {
        $this->service->locateIP('someStuff');
    }
}

class Zend_Service_DeveloperGarden_IpLocation_Mock
    extends Zend_Service_DeveloperGarden_IpLocation
{

}
