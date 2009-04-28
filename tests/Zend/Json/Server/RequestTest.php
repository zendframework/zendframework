<?php
// Call Zend_Json_Server_RequestTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Json_Server_RequestTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';

require_once 'Zend/Json/Server/Request.php';
require_once 'Zend/Json.php';

/**
 * Test class for Zend_Json_Server_Request
 */
class Zend_Json_Server_RequestTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Json_Server_RequestTest");
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
        $this->request = new Zend_Json_Server_Request();
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

    public function testShouldHaveNoParamsByDefault()
    {
        $params = $this->request->getParams();
        $this->assertTrue(empty($params));
    }

    public function testShouldBeAbleToAddAParamAsValueOnly()
    {
        $this->request->addParam('foo');
        $params = $this->request->getParams();
        $this->assertEquals(1, count($params));
        $test = array_shift($params);
        $this->assertEquals('foo', $test);
    }

    public function testShouldBeAbleToAddAParamAsKeyValuePair()
    {
        $this->request->addParam('bar', 'foo');
        $params = $this->request->getParams();
        $this->assertEquals(1, count($params));
        $this->assertTrue(array_key_exists('foo', $params));
        $this->assertEquals('bar', $params['foo']);
    }

    public function testInvalidKeysShouldBeIgnored()
    {
        $count = 0;
        foreach (array(array('foo', true), array('foo', new stdClass), array('foo', array())) as $spec) {
            $this->request->addParam($spec[0], $spec[1]);
            $this->assertNull($this->request->getParam('foo'));
            $params = $this->request->getParams();
            ++$count;
            $this->assertEquals($count, count($params));
        }
    }

    public function testShouldBeAbleToAddMultipleIndexedParamsAtOnce()
    {
        $params = array(
            'foo',
            'bar',
            'baz',
        );
        $this->request->addParams($params);
        $test = $this->request->getParams();
        $this->assertSame($params, $test);
    }

    public function testShouldBeAbleToAddMultipleNamedParamsAtOnce()
    {
        $params = array(
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => 'bat',
        );
        $this->request->addParams($params);
        $test = $this->request->getParams();
        $this->assertSame($params, $test);
    }

    public function testShouldBeAbleToAddMixedIndexedAndNamedParamsAtOnce()
    {
        $params = array(
            'foo' => 'bar',
            'baz',
            'baz' => 'bat',
        );
        $this->request->addParams($params);
        $test = $this->request->getParams();
        $this->assertEquals(array_values($params), array_values($test));
        $this->assertTrue(array_key_exists('foo', $test));
        $this->assertTrue(array_key_exists('baz', $test));
        $this->assertTrue(in_array('baz', $test));
    }

    public function testSetParamsShouldOverwriteParams()
    {
        $this->testShouldBeAbleToAddMixedIndexedAndNamedParamsAtOnce();
        $params = array(
            'one',
            'two',
            'three',
        );
        $this->request->setParams($params);
        $this->assertSame($params, $this->request->getParams());
    }

    public function testShouldBeAbleToRetrieveParamByKeyOrIndex()
    {
        $this->testShouldBeAbleToAddMixedIndexedAndNamedParamsAtOnce();
        $params = $this->request->getParams();
        $this->assertEquals('bar', $this->request->getParam('foo'), var_export($params, 1));
        $this->assertEquals('baz', $this->request->getParam(1), var_export($params, 1));
        $this->assertEquals('bat', $this->request->getParam('baz'), var_export($params, 1));
    }

    public function testMethodShouldBeNullByDefault()
    {
        $this->assertNull($this->request->getMethod());
    }

    public function testMethodErrorShouldBeFalseByDefault()
    {
        $this->assertFalse($this->request->isMethodError());
    }

    public function testMethodAccessorsShouldWorkUnderNormalInput()
    {
        $this->request->setMethod('foo');
        $this->assertEquals('foo', $this->request->getMethod());
    }

    public function testSettingMethodWithInvalidNameShouldSetError()
    {
        foreach (array('1ad', 'abc-123', 'ad$$832r#@') as $method) {
            $this->request->setMethod($method);
            $this->assertNull($this->request->getMethod());
            $this->assertTrue($this->request->isMethodError());
        }
    }

    public function testIdShouldBeNullByDefault()
    {
        $this->assertNull($this->request->getId());
    }

    public function testIdAccessorsShouldWorkUnderNormalInput()
    {
        $this->request->setId('foo');
        $this->assertEquals('foo', $this->request->getId());
    }

    public function testVersionShouldBeJsonRpcV1ByDefault()
    {
        $this->assertEquals('1.0', $this->request->getVersion());
    }

    public function testVersionShouldBeLimitedToV1AndV2()
    {
        $this->testVersionShouldBeJsonRpcV1ByDefault();
        $this->request->setVersion('2.0');
        $this->assertEquals('2.0', $this->request->getVersion());
        $this->request->setVersion('foo');
        $this->assertEquals('1.0', $this->request->getVersion());
    }

    public function testShouldBeAbleToLoadRequestFromJsonString()
    {
        $options = $this->getOptions();
        $json    = Zend_Json::encode($options);
        $this->request->loadJson($json);

        $this->assertEquals('foo', $this->request->getMethod());
        $this->assertEquals('foobar', $this->request->getId());
        $this->assertEquals($options['params'], $this->request->getParams());
    }

    public function testLoadingFromJsonShouldSetJsonRpcVersionWhenPresent()
    {
        $options = $this->getOptions();
        $options['jsonrpc'] = '2.0';
        $json    = Zend_Json::encode($options);
        $this->request->loadJson($json);
        $this->assertEquals('2.0', $this->request->getVersion());
    }

    public function testShouldBeAbleToCastToJson()
    {
        $options = $this->getOptions();
        $this->request->setOptions($options);
        $json    = $this->request->toJson();
        $this->validateJson($json, $options);
    }

    public function testCastingToStringShouldCastToJson()
    {
        $options = $this->getOptions();
        $this->request->setOptions($options);
        $json    = $this->request->__toString();
        $this->validateJson($json, $options);
    }

    /**
     * @group ZF-6187
     */
    public function testMethodNamesShouldAllowDotNamespacing()
    {
        $this->request->setMethod('foo.bar');
        $this->assertEquals('foo.bar', $this->request->getMethod());
    }

    public function getOptions()
    {
        return array(
            'method' => 'foo',
            'params' => array(
                5,
                'four',
                true,
            ),
            'id'     => 'foobar'
        );
    }

    public function validateJson($json, array $options)
    {
        $test = Zend_Json::decode($json);
        $this->assertTrue(is_array($test), var_export($json, 1));

        $this->assertTrue(array_key_exists('id', $test));
        $this->assertTrue(array_key_exists('method', $test));
        $this->assertTrue(array_key_exists('params', $test));

        $this->assertTrue(is_string($test['id']));
        $this->assertTrue(is_string($test['method']));
        $this->assertTrue(is_array($test['params']));

        $this->assertEquals($options['id'], $test['id']);
        $this->assertEquals($options['method'], $test['method']);
        $this->assertSame($options['params'], $test['params']);
    }
}

// Call Zend_Json_Server_RequestTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Json_Server_RequestTest::main") {
    Zend_Json_Server_RequestTest::main();
}
