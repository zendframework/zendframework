<?php
// Call Zend_Json_Server_ErrorTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Json_Server_ErrorTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';

require_once 'Zend/Json/Server/Error.php';
require_once 'Zend/Json.php';

/**
 * Test class for Zend_Json_Server_Error
 */
class Zend_Json_Server_ErrorTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Json_Server_ErrorTest");
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
        $this->error = new Zend_Json_Server_Error();
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

    public function testCodeShouldBeErrOtherByDefault()
    {
        $this->assertEquals(Zend_Json_Server_Error::ERROR_OTHER, $this->error->getCode());
    }

    public function testSetCodeShouldCastToInteger()
    {
        $this->error->setCode('-32768');
        $this->assertEquals(-32768, $this->error->getCode());
    }

    public function testCodeShouldBeLimitedToStandardIntegers()
    {
        foreach (array(true, 'foo', array(), new stdClass, 2.0, 25) as $code) {
            $this->error->setCode($code);
            $this->assertEquals(Zend_Json_Server_Error::ERROR_OTHER, $this->error->getCode());
        }
    }

    public function testCodeShouldAllowArbitraryAppErrorCodesInXmlRpcErrorCodeRange()
    {
        foreach (range(-32099, -32000) as $code) {
            $this->error->setCode($code);
            $this->assertEquals($code, $this->error->getCode());
        }
    }

    public function testMessageShouldBeNullByDefault()
    {
        $this->assertNull($this->error->getMessage());
    }

    public function testSetMessageShouldCastToString()
    {
        foreach (array(true, 2.0, 25) as $message) {
            $this->error->setMessage($message);
            $this->assertEquals((string) $message, $this->error->getMessage());
        }
    }

    public function testSetMessageToNonScalarShouldSilentlyFail()
    {
        foreach (array(array(), new stdClass) as $message) {
            $this->error->setMessage($message);
            $this->assertNull($this->error->getMessage());
        }
    }

    public function testDataShouldBeNullByDefault()
    {
        $this->assertNull($this->error->getData());
    }

    public function testShouldAllowArbitraryData()
    {
        foreach (array(true, 'foo', 2, 2.0, array(), new stdClass) as $datum) {
            $this->error->setData($datum);
            $this->assertEquals($datum, $this->error->getData());
        }
    }

    public function testShouldBeAbleToCastToArray()
    {
        $this->setupError();
        $array = $this->error->toArray();
        $this->validateArray($array);
    }

    public function testShouldBeAbleToCastToJson()
    {
        $this->setupError();
        $json = $this->error->toJson();
        $this->validateArray(Zend_Json::decode($json));
    }

    public function testCastingToStringShouldCastToJson()
    {
        $this->setupError();
        $json = $this->error->__toString();
        $this->validateArray(Zend_Json::decode($json));
    }

    public function setupError()
    {
        $this->error->setCode(Zend_Json_Server_Error::ERROR_OTHER)
                    ->setMessage('Unknown Error')
                    ->setData(array('foo' => 'bar'));
    }

    public function validateArray($error)
    {
        $this->assertTrue(is_array($error));
        $this->assertTrue(array_key_exists('code', $error));
        $this->assertTrue(array_key_exists('message', $error));
        $this->assertTrue(array_key_exists('data', $error));

        $this->assertTrue(is_int($error['code']));
        $this->assertTrue(is_string($error['message']));
        $this->assertTrue(is_array($error['data']));

        $this->assertEquals($this->error->getCode(), $error['code']);
        $this->assertEquals($this->error->getMessage(), $error['message']);
        $this->assertSame($this->error->getData(), $error['data']);
    }
}

// Call Zend_Json_Server_ErrorTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Json_Server_ErrorTest::main") {
    Zend_Json_Server_ErrorTest::main();
}
