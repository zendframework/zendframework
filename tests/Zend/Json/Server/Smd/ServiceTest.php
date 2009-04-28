<?php
// Call Zend_Json_Server_Smd_ServiceTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Json_Server_Smd_ServiceTest::main");
}

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

require_once 'Zend/Json/Server/Smd/Service.php';
require_once 'Zend/Json/Server/Smd.php';
require_once 'Zend/Json.php';

/**
 * Test class for Zend_Json_Server_Smd_Service
 */
class Zend_Json_Server_Smd_ServiceTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Json_Server_Smd_ServiceTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->service = new Zend_Json_Server_Smd_Service('foo');
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
    }

    public function testConstructorShouldThrowExceptionWhenNoNameSet()
    {
        try {
            $service = new Zend_Json_Server_Smd_Service(null);
            $this->fail('Should throw exception when no name set');
        } catch (Zend_Json_Server_Exception $e) {
            $this->assertContains('requires a name', $e->getMessage());
        }

        try {
            $service = new Zend_Json_Server_Smd_Service(array());
            $this->fail('Should throw exception when no name set');
        } catch (Zend_Json_Server_Exception $e) {
            $this->assertContains('requires a name', $e->getMessage());
        }
    }

    public function testSettingNameShouldThrowExceptionWhenContainingInvalidFormat()
    {
        try {
            $this->service->setName('0ab-?');
            $this->fail('Invalid name should throw exception');
        } catch (Zend_Json_Server_Exception $e) {
            $this->assertContains('Invalid name', $e->getMessage());
        }
        try {
            $this->service->setName('ab-?');
            $this->fail('Invalid name should throw exception');
        } catch (Zend_Json_Server_Exception $e) {
            $this->assertContains('Invalid name', $e->getMessage());
        }
    }

    public function testNameAccessorsShouldWorkWithNormalInput()
    {
        $this->assertEquals('foo', $this->service->getName());
        $this->service->setName('bar');
        $this->assertEquals('bar', $this->service->getName());
    }

    public function testTransportShouldDefaultToPost()
    {
        $this->assertEquals('POST', $this->service->getTransport());
    }

    public function testTransportShouldBeLimitedToPost()
    {
        try {
            $this->service->setTransport('GET');
            $this->fail('Invalid transport should throw exception');
        } catch (Zend_Json_Server_Exception $e) {
            $this->assertContains('Invalid transport', $e->getMessage());
        }
        try {
            $this->service->setTransport('REST');
            $this->fail('Invalid transport should throw exception');
        } catch (Zend_Json_Server_Exception $e) {
            $this->assertContains('Invalid transport', $e->getMessage());
        }
    }

    public function testTransportAccessorsShouldWorkUnderNormalInput()
    {
        $this->service->setTransport('POST');
        $this->assertEquals('POST', $this->service->getTransport());
    }

    public function testTargetShouldBeNullInitially()
    {
        $this->assertNull($this->service->getTarget());
    }

    public function testTargetAccessorsShouldWorkUnderNormalInput()
    {
        $this->testTargetShouldBeNullInitially();
        $this->service->setTarget('foo');
        $this->assertEquals('foo', $this->service->getTarget());
    }

    public function testTargetAccessorsShouldNormalizeToString()
    {
        $this->testTargetShouldBeNullInitially();
        $this->service->setTarget(123);
        $value = $this->service->getTarget();
        $this->assertTrue(is_string($value));
        $this->assertEquals((string) 123, $value);
    }

    public function testEnvelopeShouldBeJsonRpc1CompliantByDefault()
    {
        $this->assertEquals(Zend_Json_Server_Smd::ENV_JSONRPC_1, $this->service->getEnvelope());
    }

    public function testEnvelopeShouldOnlyComplyWithJsonRpc1And2()
    {
        $this->testEnvelopeShouldBeJsonRpc1CompliantByDefault();
        $this->service->setEnvelope(Zend_Json_Server_Smd::ENV_JSONRPC_2);
        $this->assertEquals(Zend_Json_Server_Smd::ENV_JSONRPC_2, $this->service->getEnvelope());
        $this->service->setEnvelope(Zend_Json_Server_Smd::ENV_JSONRPC_1);
        $this->assertEquals(Zend_Json_Server_Smd::ENV_JSONRPC_1, $this->service->getEnvelope());
        try {
            $this->service->setEnvelope('JSON-P');
            $this->fail('Should not be able to set non-JSON-RPC spec envelopes');
        } catch (Zend_Json_Server_Exception $e) {
            $this->assertContains('Invalid envelope', $e->getMessage());
        }
    }

    public function testShouldHaveNoParamsByDefault()
    {
        $params = $this->service->getParams();
        $this->assertTrue(empty($params));
    }

    public function testShouldBeAbleToAddParamsByTypeOnly()
    {
        $this->service->addParam('integer');
        $params = $this->service->getParams();
        $this->assertEquals(1, count($params));
        $param = array_shift($params);
        $this->assertEquals('integer', $param['type']);
    }

    public function testParamsShouldAcceptArrayOfTypes()
    {
        $type   = array('integer', 'string');
        $this->service->addParam($type);
        $params = $this->service->getParams();
        $param  = array_shift($params);
        $test   = $param['type'];
        $this->assertTrue(is_array($test));
        $this->assertEquals($type, $test);
    }

    public function testInvalidParamTypeShouldThrowException()
    {
        try {
            $this->service->addParam(new stdClass);
            $this->fail('Invalid param type should throw exception');
        } catch (Zend_Json_Server_Exception $e) {
            $this->assertContains('Invalid param type', $e->getMessage());
        }
    }

    public function testShouldBeAbleToOrderParams()
    {
        $this->service->addParam('integer', array(), 4)
                      ->addParam('string')
                      ->addParam('boolean', array(), 3);
        $params = $this->service->getParams();

        $this->assertEquals(3, count($params));

        $param = array_shift($params);
        $this->assertEquals('string', $param['type'], var_export($params, 1));
        $param = array_shift($params);
        $this->assertEquals('boolean', $param['type'], var_export($params, 1));
        $param = array_shift($params);
        $this->assertEquals('integer', $param['type'], var_export($params, 1));
    }

    public function testShouldBeAbleToAddArbitraryParamOptions()
    {
        $this->service->addParam(
            'integer', 
            array(
                'name'        => 'foo',
                'optional'    => false,
                'default'     => 1,
                'description' => 'Foo parameter',
            )
        );
        $params = $this->service->getParams();
        $param  = array_shift($params);
        $this->assertEquals('foo', $param['name']);
        $this->assertFalse($param['optional']);
        $this->assertEquals(1, $param['default']);
        $this->assertEquals('Foo parameter', $param['description']);
    }

    public function testShouldBeAbleToAddMultipleParamsAtOnce()
    {
        $this->service->addParams(array(
            array('type' => 'integer', 'order' => 4),
            array('type' => 'string', 'name' => 'foo'),
            array('type' => 'boolean', 'order' => 3),
        ));
        $params = $this->service->getParams();

        $this->assertEquals(3, count($params));
        $param = array_shift($params);
        $this->assertEquals('string', $param['type']);
        $this->assertEquals('foo', $param['name']);

        $param = array_shift($params);
        $this->assertEquals('boolean', $param['type']);

        $param = array_shift($params);
        $this->assertEquals('integer', $param['type']);
    }

    public function testSetparamsShouldOverwriteExistingParams()
    {
        $this->testShouldBeAbleToAddMultipleParamsAtOnce();
        $params = $this->service->getParams();
        $this->assertEquals(3, count($params));

        $this->service->setParams(array(
            array('type' => 'string'),
            array('type' => 'integer'),
        ));
        $test = $this->service->getParams();
        $this->assertNotEquals($params, $test);
        $this->assertEquals(2, count($test));
    }

    public function testReturnShouldBeNullByDefault()
    {
        $this->assertNull($this->service->getReturn());
    }

    public function testReturnAccessorsShouldWorkWithNormalInput()
    {
        $this->testReturnShouldBeNullByDefault();
        $this->service->setReturn('integer');
        $this->assertEquals('integer', $this->service->getReturn());
    }

    public function testReturnAccessorsShouldAllowArrayOfTypes()
    {
        $this->testReturnShouldBeNullByDefault();
        $type = array('integer', 'string');
        $this->service->setReturn($type);
        $this->assertEquals($type, $this->service->getReturn());
    }

    public function testInvalidReturnTypeShouldThrowException()
    {
        try {
            $this->service->setReturn(new stdClass);
            $this->fail('Invalid return type should throw exception');
        } catch (Zend_Json_Server_Exception $e) {
            $this->assertContains('Invalid param type', $e->getMessage());
        }
    }

    public function testToArrayShouldCreateSmdCompatibleHash()
    {
        $this->setupSmdValidationObject();
        $smd = $this->service->toArray();
        $this->validateSmdArray($smd);
    }

    public function testTojsonShouldEmitJson()
    {
        $this->setupSmdValidationObject();
        $json = $this->service->toJson();
        $smd  = Zend_Json::decode($json);

        $this->assertTrue(array_key_exists('foo', $smd));
        $this->assertTrue(is_array($smd['foo']));

        $this->validateSmdArray($smd['foo']);
    }

    public function setupSmdValidationObject()
    {
        $this->service->setName('foo')
                      ->setTransport('POST')
                      ->setTarget('/foo')
                      ->setEnvelope(Zend_Json_Server_Smd::ENV_JSONRPC_2)
                      ->addParam('boolean')
                      ->addParam('array')
                      ->addParam('object')
                      ->setReturn('boolean');
    }

    public function validateSmdArray(array $smd)
    {
        $this->assertTrue(array_key_exists('transport', $smd));
        $this->assertEquals('POST', $smd['transport']);

        $this->assertTrue(array_key_exists('envelope', $smd));
        $this->assertEquals(Zend_Json_Server_Smd::ENV_JSONRPC_2, $smd['envelope']);

        $this->assertTrue(array_key_exists('parameters', $smd));
        $params = $smd['parameters'];
        $this->assertEquals(3, count($params));
        $param = array_shift($params);
        $this->assertEquals('boolean', $param['type']);
        $param = array_shift($params);
        $this->assertEquals('array', $param['type']);
        $param = array_shift($params);
        $this->assertEquals('object', $param['type']);

        $this->assertTrue(array_key_exists('returns', $smd));
        $this->assertEquals('boolean', $smd['returns']);
    }
}

// Call Zend_Json_Server_Smd_ServiceTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Json_Server_Smd_ServiceTest::main") {
    Zend_Json_Server_Smd_ServiceTest::main();
}
