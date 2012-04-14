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
 * @see Zend_Service_DeveloperGarden_VoiceCall
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
class Zend_Service_DeveloperGarden_VoiceCallTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Service_DeveloperGarden_VoiceCall_Mock
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
        $this->service = new Zend_Service_DeveloperGarden_VoiceCall_Mock($config);
    }

    public function testNewCall()
    {
        $aNumber = '+4932-000001';
        $bNumber = '+4932-000002';


        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Client_AbstractClient',
            $this->service->setEnvironment(Zend_Service_DeveloperGarden_VoiceCall_Mock::ENV_MOCK)
        );

        $result = $this->service->newCall($aNumber, $bNumber, 30, 30);
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Response_VoiceButler_NewCallResponse',
            $result
        );

        $this->assertTrue($result->isValid());

        $this->assertNotNull($result->getSessionId());
        $this->assertEquals((string)$result, $result->getSessionId());
        $tmp = $result->return->sessionId;
        $result->return->sessionId = null;
        $this->assertNull($result->getSessionId());
        $result->return->sessionId = $tmp;
    }

    public function testNewCallSequenced()
    {
        $aNumber = '+4932-000001';
        $bNumber = array(
            '+4932-000004',
            '+4932-000002'
        );


        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Client_AbstractClient',
            $this->service->setEnvironment(Zend_Service_DeveloperGarden_VoiceCall_Mock::ENV_MOCK)
        );

        $result = $this->service->newCallSequenced($aNumber, $bNumber, 30, 30, 5);
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Response_VoiceButler_NewCallSequencedResponse',
            $result
        );

        // test getReturn specially here
        $this->assertInstanceOf(
            'stdClass',
            $result->getReturn()
        );

        $this->assertTrue($result->isValid());
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Response_Exception
     */
    public function testNewCallSequencedWaitTimeTooShort()
    {
        $aNumber = '+4932-000001';
        $bNumber = array(
            '+4932-000004',
            '+4932-000002'
        );


        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Client_AbstractClient',
            $this->service->setEnvironment(Zend_Service_DeveloperGarden_VoiceCall_Mock::ENV_MOCK)
        );

        $this->assertNull($this->service->newCallSequenced($aNumber, $bNumber, 30, 30, 2));
    }

    public function testTearDownCall()
    {
        $aNumber = '+4932-000001';
        $bNumber = '+4932-000001';


        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Client_AbstractClient',
            $this->service->setEnvironment(Zend_Service_DeveloperGarden_VoiceCall_Mock::ENV_MOCK)
        );

        $result = $this->service->newCall($aNumber, $bNumber, 30, 30);
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Response_VoiceButler_NewCallResponse',
            $result
        );

        $this->assertTrue($result->isValid());

        $sessionId = $result->getSessionId();
        $this->assertTrue(is_string($sessionId));
        $this->assertNotNull($sessionId);

        $result = $this->service->tearDownCall($sessionId);
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Response_VoiceButler_TearDownCallResponse',
            $result
        );

        $this->assertNotNull($result->getSessionId());
        $tmp = $result->return->sessionId;
        $result->return->sessionId = null;
        $this->assertNull($result->getSessionId());
        $result->return->sessionId = $tmp;
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Response_Exception
     */
    public function testTearDownCallException()
    {
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Client_AbstractClient',
            $this->service->setEnvironment(Zend_Service_DeveloperGarden_VoiceCall_Mock::ENV_MOCK)
        );

        $this->assertNull($this->service->tearDownCall('NotValid'));
    }

    public function testCallStatus()
    {
        $aNumber = '+4932-000001';
        $bNumber = '+4932-000001';


        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Client_AbstractClient',
            $this->service->setEnvironment(Zend_Service_DeveloperGarden_VoiceCall_Mock::ENV_MOCK)
        );

        $result = $this->service->newCall($aNumber, $bNumber, 30, 30);
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Response_VoiceButler_NewCallResponse',
            $result
        );

        $this->assertTrue($result->isValid());

        $sessionId = $result->getSessionId();
        $this->assertTrue(is_string($sessionId));
        $this->assertNotNull($sessionId);

        $result = $this->service->callStatus($sessionId, 30);
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Response_VoiceButler_CallStatusResponse',
            $result
        );

        $this->assertNotNull($result->getBe164());
        $tmp = $result->return->be164;
        $result->return->be164 = null;
        $this->assertNull($result->getBe164());
        $result->return->be164 = $tmp;

        $this->assertNotNull($result->getBNumberIndex());
        $this->assertTrue(0 >= $result->getBNumberIndex());
        $tmp = $result->return->bindex;
        $result->return->bindex = null;
        $this->assertNull($result->getBNumberIndex());
        $result->return->bindex = $tmp;

        $this->assertNotNull($result->getSessionId());
        $this->assertEquals($sessionId, $result->getSessionId());
        $tmp = $result->return->sessionId;
        $result->return->sessionId = null;
        $this->assertNull($result->getSessionId());
        $result->return->sessionId = $tmp;

        $this->assertNotNull($result->getConnectionTimeA());
        $this->assertTrue(0 >= $result->getConnectionTimeA());
        $tmp = $result->return->connectiontimea;
        $result->return->connectiontimea = null;
        $this->assertNull($result->getConnectionTimeA());
        $result->return->connectiontimea = $tmp;

        $this->assertNotNull($result->getConnectionTimeB());
        $this->assertTrue(0 >= $result->getConnectionTimeB());
        $tmp = $result->return->connectiontimeb;
        $result->return->connectiontimeb = null;
        $this->assertNull($result->getConnectionTimeB());
        $result->return->connectiontimeb = $tmp;

        $this->assertNotNull($result->getReasonA());
        $this->assertTrue(0 >= $result->getReasonA());
        $tmp = $result->return->reasona;
        $result->return->reasona = null;
        $this->assertNull($result->getReasonA());
        $result->return->reasona = $tmp;

        $this->assertNotNull($result->getReasonB());
        $this->assertTrue(0 >= $result->getReasonB());
        $tmp = $result->return->reasonb;
        $result->return->reasonb = null;
        $this->assertNull($result->getReasonB());
        $result->return->reasonb = $tmp;

        $this->assertNotNull($result->getStateA());
        $this->assertTrue(0 >= $result->getStateA());
        $tmp = $result->return->statea;
        $result->return->statea = null;
        $this->assertNull($result->getStateA());
        $result->return->statea = $tmp;

        $this->assertNotNull($result->getStateB());
        $this->assertTrue(0 >= $result->getStateB());
        $tmp = $result->return->stateb;
        $result->return->stateb = null;
        $this->assertNull($result->getStateB());
        $result->return->stateb = $tmp;

        $this->assertNotNull($result->getDescriptionA());
        $this->assertTrue(0 >= $result->getDescriptionA());
        $tmp = $result->return->descriptiona;
        $result->return->descriptiona = null;
        $this->assertNull($result->getDescriptionA());
        $result->return->descriptiona = $tmp;

        $this->assertNotNull($result->getDescriptionB());
        $this->assertTrue(0 >= $result->getDescriptionB());
        $tmp = $result->return->descriptionb;
        $result->return->descriptionb = null;
        $this->assertNull($result->getDescriptionB());
        $result->return->descriptionb = $tmp;
    }

    public function testNewCallRequest()
    {
        $request = new Zend_Service_DeveloperGarden_Request_VoiceButler_NewCall(
            Zend_Service_DeveloperGarden_VoiceCall_Mock::ENV_SANDBOX
        );
        $this->assertEquals(
            Zend_Service_DeveloperGarden_VoiceCall_Mock::ENV_SANDBOX,
            $request->getEnvironment()
        );

        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Request_VoiceButler_NewCall',
            $request->setAccount(999999)
        );
        $this->assertEquals(999999, $request->getAccount());

        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Request_VoiceButler_NewCall',
            $request->setANumber('+49-123456')
        );
        $this->assertEquals('+49-123456', $request->getANumber());

        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Request_VoiceButler_NewCall',
            $request->setBNumber('+49-654321')
        );
        $this->assertEquals('+49-654321', $request->getBNumber());

        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Request_VoiceButler_NewCall',
            $request->setBNumber('+49-654321')
        );
        $this->assertEquals('+49-654321', $request->getBNumber());

        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Request_VoiceButler_NewCall',
            $request->setPrivacyA(true)
        );
        $this->assertEquals(true, $request->getPrivacyA());

        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Request_VoiceButler_NewCall',
            $request->setPrivacyB(true)
        );
        $this->assertEquals(true, $request->getPrivacyB());

        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Request_VoiceButler_NewCall',
            $request->setExpiration(30)
        );
        $this->assertEquals(30, $request->getExpiration());

        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Request_VoiceButler_NewCall',
            $request->setMaxDuration(60)
        );
        $this->assertEquals(60, $request->getMaxDuration());

        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Request_VoiceButler_NewCall',
            $request->setGreeter('49-999999')
        );
        $this->assertEquals('49-999999', $request->getGreeter());

        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Request_VoiceButler_NewCall',
            $request->setEnvironment(Zend_Service_DeveloperGarden_VoiceCall_Mock::ENV_MOCK)
        );
        $this->assertEquals(
            Zend_Service_DeveloperGarden_VoiceCall_Mock::ENV_MOCK,
            $request->getEnvironment()
        );
    }

    public function testNewCallSequencedRequest()
    {
        $request = new Zend_Service_DeveloperGarden_Request_VoiceButler_NewCallSequenced(
            Zend_Service_DeveloperGarden_VoiceCall_Mock::ENV_SANDBOX
        );
        $this->assertEquals(
            Zend_Service_DeveloperGarden_VoiceCall_Mock::ENV_SANDBOX,
            $request->getEnvironment()
        );

        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Request_VoiceButler_NewCallSequenced',
            $request->setBNumber(array('+49-654321','+49-123456'))
        );
        $this->assertEquals(
            array('+49-654321','+49-123456'),
            $request->getBNumber()
        );
        $this->assertEquals(
            2,
            count($request->getBNumber())
        );
        $this->assertArrayHasKey(0, array('+49-654321','+49-123456'));
        $this->assertArrayHasKey(1, array('+49-654321','+49-123456'));

        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Request_VoiceButler_NewCallSequenced',
            $request->setMaxWait(9)
        );
        $this->assertEquals(9, $request->getMaxWait());

    }

    public function testTearDownCallRequest()
    {
        $request = new Zend_Service_DeveloperGarden_Request_VoiceButler_TearDownCall(
            Zend_Service_DeveloperGarden_VoiceCall_Mock::ENV_SANDBOX,
            'SESSIONID-987654321'
        );
        $this->assertEquals(
            Zend_Service_DeveloperGarden_VoiceCall_Mock::ENV_SANDBOX,
            $request->getEnvironment()
        );

        $this->assertEquals(
            'SESSIONID-987654321',
            $request->getSessionId()
        );

        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Request_VoiceButler_TearDownCall',
            $request->setSessionId('SESSIONID-8888888888')
        );

        $this->assertEquals(
            'SESSIONID-8888888888',
            $request->getSessionId()
        );
    }
    public function testCallStatusRequest()
    {
        $request = new Zend_Service_DeveloperGarden_Request_VoiceButler_CallStatus(
            Zend_Service_DeveloperGarden_VoiceCall_Mock::ENV_SANDBOX,
            'SESSIONID-987654321',
            123
        );
        $this->assertEquals(
            Zend_Service_DeveloperGarden_VoiceCall_Mock::ENV_SANDBOX,
            $request->getEnvironment()
        );

        $this->assertEquals(
            'SESSIONID-987654321',
            $request->getSessionId()
        );

        $this->assertEquals(
            123,
            $request->getKeepAlive()
        );

        // value changes
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Request_VoiceButler_CallStatus',
            $request->setSessionId('SESSIONID-8888888888')
        );

        $this->assertEquals(
            'SESSIONID-8888888888',
            $request->getSessionId()
        );

        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Request_VoiceButler_CallStatus',
            $request->setKeepAlive(15)
        );

        $this->assertEquals(
            15,
            $request->getKeepAlive()
        );
    }
}

class Zend_Service_DeveloperGarden_VoiceCall_Mock
    extends Zend_Service_DeveloperGarden_VoiceCall
{

}
