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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_DeveloperGarden_SendSmsTest::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * @see Zend_Service_DeveloperGarden_SendSms
 */
require_once 'Zend/Service/DeveloperGarden/SendSms.php';

/**
 * Zend_Service_DeveloperGarden test case
 *
 * @category   Zend
 * @package    Zend_Service_DeveloperGarden
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Zend_Service_DeveloperGarden_SendSmsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Service_DeveloperGarden_SendSms_Mock
     */
    protected $_service = null;

    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        PHPUnit_TextUI_TestRunner::run($suite);
    }

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
        $this->assertType(
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

        $this->assertType(
            'Zend_Service_DeveloperGarden_Request_SendSms_SendSMS',
            $sms
        );

        $this->assertType(
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

        $this->assertType(
            'Zend_Service_DeveloperGarden_Request_SendSms_SendFlashSMS',
            $sms
        );

        $this->assertType(
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

        $this->assertType(
            'Zend_Service_DeveloperGarden_Request_SendSms_SendSMS',
            $sms
        );

        $result = $this->service->send($sms);
        $this->assertType(
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

        $this->assertType(
            'Zend_Service_DeveloperGarden_Request_SendSms_SendFlashSMS',
            $sms
        );

        $result = $this->service->send($sms);
        $this->assertType(
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
        $this->assertType(
            'Zend_Service_DeveloperGarden_Request_SendSms_WrongSmsType',
            $sms->setNumber('+49-171-2345678')
        );
        $this->assertType(
            'Zend_Service_DeveloperGarden_Request_SendSms_WrongSmsType',
            $sms->setMessage('Zend Framework is very cool')
        );
        $this->assertType(
            'Zend_Service_DeveloperGarden_Request_SendSms_WrongSmsType',
            $sms->setOriginator('ZFTest')
        );

        $this->assertType(
            'Zend_Service_DeveloperGarden_Request_SendSms_WrongSmsType',
            $sms
        );

        $this->assertNull($this->service->send($sms));
    }
}

class Zend_Service_DeveloperGarden_Request_SendSms_WrongSmsType
    extends Zend_Service_DeveloperGarden_Request_SendSms_SendSmsAbstract
{
    protected $_smsType = 999999;
}

class Zend_Service_DeveloperGarden_SendSms_Mock
    extends Zend_Service_DeveloperGarden_SendSms
{

}

if (PHPUnit_MAIN_METHOD == 'Zend_Service_DeveloperGarden_SendSmsTest::main') {
    Zend_Service_DeveloperGarden_SendSmsTest::main();
}
