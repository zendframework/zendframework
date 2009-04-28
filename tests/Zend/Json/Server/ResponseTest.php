<?php
// Call Zend_Json_Server_ResponseTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Json_Server_ResponseTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';

require_once 'Zend/Json/Server/Response.php';
require_once 'Zend/Json/Server/Error.php';
require_once 'Zend/Json.php';

/**
 * Test class for Zend_Json_Server_Response
 */
class Zend_Json_Server_ResponseTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Json_Server_ResponseTest");
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
        $this->response = new Zend_Json_Server_Response();
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

    public function testResultShouldBeNullByDefault()
    {
        $this->assertNull($this->response->getResult());
    }

    public function testResultAccessorsShouldWorkWithNormalInput()
    {
        foreach (array(true, 'foo', 2, 2.0, array(), array('foo' => 'bar')) as $result) {
            $this->response->setResult($result);
            $this->assertEquals($result, $this->response->getResult());
        }
    }

    public function testResultShouldNotBeErrorByDefault()
    {
        $this->assertFalse($this->response->isError());
    }

    public function testSettingErrorShouldMarkRequestAsError()
    {
        $error = new Zend_Json_Server_Error();
        $this->response->setError($error);
        $this->assertTrue($this->response->isError());
    }

    public function testShouldBeAbleToRetrieveErrorObject()
    {
        $error = new Zend_Json_Server_Error();
        $this->response->setError($error);
        $this->assertSame($error, $this->response->getError());
    }

    public function testIdShouldBeNullByDefault()
    {
        $this->assertNull($this->response->getId());
    }

    public function testIdAccesorsShouldWorkWithNormalInput()
    {
        $this->response->setId('foo');
        $this->assertEquals('foo', $this->response->getId());
    }

    public function testVersionShouldBeNullByDefault()
    {
        $this->assertNull($this->response->getVersion());
    }

    public function testVersionShouldBeLimitedToV2()
    {
        $this->response->setVersion('2.0');
        $this->assertEquals('2.0', $this->response->getVersion());
        foreach (array('a', 1, '1.0', array(), true) as $version) {
            $this->response->setVersion($version);
            $this->assertNull($this->response->getVersion());
        }
    }

    public function testResponseShouldBeAbleToCastToJson()
    {
        $this->response->setResult(true)
                       ->setId('foo')
                       ->setVersion('2.0');
        $json = $this->response->toJson();
        $test = Zend_Json::decode($json);

        $this->assertTrue(is_array($test));
        $this->assertTrue(array_key_exists('result', $test));
        $this->assertFalse(array_key_exists('error', $test));
        $this->assertTrue(array_key_exists('id', $test));
        $this->assertTrue(array_key_exists('jsonrpc', $test));

        $this->assertTrue($test['result']);
        $this->assertEquals($this->response->getId(), $test['id']);
        $this->assertEquals($this->response->getVersion(), $test['jsonrpc']);
    }

    public function testResponseShouldCastErrorToJsonIfIsError()
    {
        $error = new Zend_Json_Server_Error();
        $error->setCode(Zend_Json_Server_Error::ERROR_INTERNAL)
              ->setMessage('error occurred');
        $this->response->setId('foo')
                       ->setResult(true)
                       ->setError($error);
        $json = $this->response->toJson();
        $test = Zend_Json::decode($json);

        $this->assertTrue(is_array($test));
        $this->assertFalse(array_key_exists('result', $test));
        $this->assertTrue(array_key_exists('id', $test));
        $this->assertFalse(array_key_exists('jsonrpc', $test));

        $this->assertEquals($this->response->getId(), $test['id']);
        $this->assertEquals($error->getCode(), $test['error']['code']);
        $this->assertEquals($error->getMessage(), $test['error']['message']);
    }

    public function testCastToStringShouldCastToJson()
    {
        $this->response->setResult(true)
                       ->setId('foo');
        $json = $this->response->__toString();
        $test = Zend_Json::decode($json);

        $this->assertTrue(is_array($test));
        $this->assertTrue(array_key_exists('result', $test));
        $this->assertFalse(array_key_exists('error', $test));
        $this->assertTrue(array_key_exists('id', $test));
        $this->assertFalse(array_key_exists('jsonrpc', $test));

        $this->assertTrue($test['result']);
        $this->assertEquals($this->response->getId(), $test['id']);
    }
}

// Call Zend_Json_Server_ResponseTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Json_Server_ResponseTest::main") {
    Zend_Json_Server_ResponseTest::main();
}
