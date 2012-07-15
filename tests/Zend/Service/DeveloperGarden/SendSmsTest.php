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
class Zend_Service_DeveloperGarden_SendSmsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Service_DeveloperGarden_SendSms_Mock
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
        $this->service = new Zend_Service_DeveloperGarden_SendSms_Mock($config);
        // limit to mock env
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_SendSms_Mock',
            $this->service->setEnvironment(Zend_Service_DeveloperGarden_SendSms_Mock::ENV_MOCK)
        );
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Response_Exception
     */
    public function testSmsMockInValidSender()
    {
        $sms = $this->service->createSms(
            '+49-171-2345678',
            'Zend Framework is very cool',
            'Zend.Framework'
        );

        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Request_SendSms_SendSMS',
            $sms
        );

        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Response_SendSms_SendSMSResponse',
            $this->service->send($sms)
        );
    }


    /**
     * @expectedException Zend_Service_DeveloperGarden_Response_Exception
     */
    public function testFlashSmsMockInValidSender()
    {
        $sms = $this->service->createFlashSms(
            '+49-171-2345678',
            'Zend Framework is very cool',
            'Zend.Framework'
        );

        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Request_SendSms_SendFlashSMS',
            $sms
        );

        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Response_SendSms_SendFlashSMSResponse',
            $this->service->send($sms)
        );
    }

    public function testSmsMockValid()
    {
        $sms = $this->service->createSms(
            '+49-171-2345678',
            'Zend Framework is very cool',
            'ZFTest'
        );

        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Request_SendSms_SendSMS',
            $sms
        );

        $result = $this->service->send($sms);
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Response_SendSms_SendSMSResponse',
            $result
        );

        $this->assertTrue($result->isValid());
    }

    public function testFlashSmsMockValid()
    {
        $sms = $this->service->createFlashSms(
            '+49-171-2345678',
            'Zend Framework is very cool',
            'ZFTest'
        );

        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Request_SendSms_SendFlashSMS',
            $sms
        );

        $result = $this->service->send($sms);
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Response_SendSms_SendFlashSMSResponse',
            $result
        );

        $this->assertTrue($result->isValid());
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Client_Exception
     */
    public function testWrongSmsType()
    {
        $sms = new Zend_Service_DeveloperGarden_Request_SendSms_WrongSmsType(
            $this->service->getEnvironment()
        );
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Request_SendSms_WrongSmsType',
            $sms->setNumber('+49-171-2345678')
        );
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Request_SendSms_WrongSmsType',
            $sms->setMessage('Zend Framework is very cool')
        );
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Request_SendSms_WrongSmsType',
            $sms->setOriginator('ZFTest')
        );

        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Request_SendSms_WrongSmsType',
            $sms
        );

        $this->assertNull($this->service->send($sms));
    }
}

class Zend_Service_DeveloperGarden_Request_SendSms_WrongSmsType
    extends Zend_Service_DeveloperGarden_Request_SendSms_AbstractSendSms
{
    protected $_smsType = 999999;
}

class Zend_Service_DeveloperGarden_SendSms_Mock
    extends Zend_Service_DeveloperGarden_SendSms
{

}
