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
 * @see Zend_Service_DeveloperGarden_BaseUserService
 */

/**
 * Zend_Service_DeveloperGarden test case
 *
 * @category   Zend
 * @package    Zend_Service_DeveloperGarden
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_DeveloperGarden
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_DeveloperGarden_BaseUserServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @todo add more tests for the AbstractClient
     */

    /**
     * @var Zend_Service_DeveloperGarden_BaseUserService_Mock
     */
    protected $_service = null;

    protected $_limit = 10;

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
        $this->service = new Zend_Service_DeveloperGarden_BaseUserService_Mock($config);
    }

    public function testSmsQuotaSandbox()
    {
        $resp = $this->service->getSmsQuotaInformation(Zend_Service_DeveloperGarden_BaseUserService_Mock::ENV_SANDBOX);
        $this->assertTrue($resp->isValid());
        $this->assertFalse($resp->hasError());
    }

    public function testSmsQuotaProduction()
    {
        $resp = $this->service->getSmsQuotaInformation(Zend_Service_DeveloperGarden_BaseUserService_Mock::ENV_PRODUCTION);
        $this->assertTrue($resp->isValid());
        $this->assertFalse($resp->hasError());
    }

    public function testVoiceCallQuotaSandbox()
    {
        $resp = $this->service->getVoiceCallQuotaInformation(Zend_Service_DeveloperGarden_BaseUserService_Mock::ENV_SANDBOX);
        $this->assertTrue($resp->isValid());
        $this->assertFalse($resp->hasError());
    }

    public function testVoiceCallQuotaProduction()
    {
        $resp = $this->service->getVoiceCallQuotaInformation(Zend_Service_DeveloperGarden_BaseUserService_Mock::ENV_PRODUCTION);
        $this->assertTrue($resp->isValid());
        $this->assertFalse($resp->hasError());
    }

    public function testConfernceCallQuotaSandbox()
    {
        $resp = $this->service->getConfernceCallQuotaInformation(Zend_Service_DeveloperGarden_BaseUserService_Mock::ENV_SANDBOX);
        $this->assertTrue($resp->isValid());
        $this->assertFalse($resp->hasError());
    }

    public function testConfernceCallQuotaProduction()
    {
        $resp = $this->service->getConfernceCallQuotaInformation(Zend_Service_DeveloperGarden_BaseUserService_Mock::ENV_PRODUCTION);
        $this->assertTrue($resp->isValid());
        $this->assertFalse($resp->hasError());
    }

    public function testLocalSearchQuotaSandbox()
    {
        $resp = $this->service->getLocalSearchQuotaInformation(Zend_Service_DeveloperGarden_BaseUserService_Mock::ENV_SANDBOX);
        $this->assertTrue($resp->isValid());
        $this->assertFalse($resp->hasError());
    }

    public function testLocalSearchQuotaProduction()
    {
        $resp = $this->service->getLocalSearchQuotaInformation(Zend_Service_DeveloperGarden_BaseUserService_Mock::ENV_PRODUCTION);
        $this->assertTrue($resp->isValid());
        $this->assertFalse($resp->hasError());
    }

    public function testIPLocationQuotaSandbox()
    {
        $resp = $this->service->getIPLocationQuotaInformation(Zend_Service_DeveloperGarden_BaseUserService_Mock::ENV_SANDBOX);
        $this->assertTrue($resp->isValid());
        $this->assertFalse($resp->hasError());
    }

    public function testIPLocationQuotaProduction()
    {
        $resp = $this->service->getIPLocationQuotaInformation(Zend_Service_DeveloperGarden_BaseUserService_Mock::ENV_PRODUCTION);
        $this->assertTrue($resp->isValid());
        $this->assertFalse($resp->hasError());
    }

    public function testChangeSmsQuotaPoolProduction()
    {
        $this->service->changeSmsQuotaPool($this->_limit, Zend_Service_DeveloperGarden_BaseUserService_Mock::ENV_PRODUCTION);
    }

    public function testChangeSmsQuotaPoolSandbox()
    {
        $this->service->changeSmsQuotaPool($this->_limit, Zend_Service_DeveloperGarden_BaseUserService_Mock::ENV_SANDBOX);
    }

    public function testChangeVoiceCallQuotaPoolProduction()
    {
        $this->service->changeVoiceCallQuotaPool($this->_limit, Zend_Service_DeveloperGarden_BaseUserService_Mock::ENV_PRODUCTION);
    }

    public function testChangeVoiceCallQuotaPoolSandbox()
    {
        $this->service->changeVoiceCallQuotaPool($this->_limit, Zend_Service_DeveloperGarden_BaseUserService_Mock::ENV_SANDBOX);
    }

    public function testChangeIPLocationQuotaPoolProduction()
    {
        $this->service->changeIPLocationQuotaPool($this->_limit, Zend_Service_DeveloperGarden_BaseUserService_Mock::ENV_PRODUCTION);
    }

    public function testChangeIPLocationQuotaPoolSandbox()
    {
        $this->service->changeIPLocationQuotaPool($this->_limit, Zend_Service_DeveloperGarden_BaseUserService_Mock::ENV_SANDBOX);
    }

    public function testChangeConferenceCallQuotaPoolProduction()
    {
        $this->service->changeConferenceCallQuotaPool($this->_limit, Zend_Service_DeveloperGarden_BaseUserService_Mock::ENV_PRODUCTION);
    }

    public function testChangeConferenceCallQuotaPoolSandbox()
    {
        $this->service->changeConferenceCallQuotaPool($this->_limit, Zend_Service_DeveloperGarden_BaseUserService_Mock::ENV_SANDBOX);
    }

    public function testChangeLocalSearchQuotaPoolProduction()
    {
        $this->service->changeLocalSearchQuotaPool($this->_limit, Zend_Service_DeveloperGarden_BaseUserService_Mock::ENV_PRODUCTION);
    }

    public function testChangeLocalSearchQuotaPoolSandbox()
    {
        $this->service->changeLocalSearchQuotaPool($this->_limit, Zend_Service_DeveloperGarden_BaseUserService_Mock::ENV_SANDBOX);
    }

    public function testAccountBalance()
    {
        $result = $this->service->getAccountBalance();
        $this->assertEquals('0000', $result->getErrorCode());
        $this->assertInternalType('array', $result->Account);
    }

    public function testAccountBalanceLoop()
    {
        $result = $this->service->getAccountBalance();
        $this->assertEquals('0000', $result->getErrorCode());
        $this->assertInternalType('array', $result->Account);
        foreach ($result->Account as $k => $v) {
            $this->assertInstanceOf(
                'Zend_Service_DeveloperGarden_BaseUserService_AccountBalance',
                $v
            );
            $this->assertInternalType('string', $v->getAccount());
            $this->assertInternalType('integer', $v->getCredits());
        }
    }
}

class Zend_Service_DeveloperGarden_BaseUserService_Mock
    extends Zend_Service_DeveloperGarden_BaseUserService
{
    /**
     * returns the correct module string
     *
     * @param string $module
     * @param integer $environment
     * @return string
     */
    public function getBuildModuleString($module, $environment)
    {
        return $this->_buildModuleString($module, $environment);
    }

    public function checkModuleId($moduleId)
    {
        return $this->_checkModuleId($moduleId);
    }

    public function buildModuleString($module, $environment)
    {
        return $this->_buildModuleString($module, $environment);
    }
}
