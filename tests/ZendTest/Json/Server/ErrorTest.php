<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Json\Server;

use Zend\Json\Server;
use Zend\Json;

/**
 * Test class for Zend\JSON\Server\Error
 *
 * @group      Zend_JSON
 * @group      Zend_JSON_Server
 */
class ErrorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->error = new Server\Error();
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
        $this->assertEquals(Server\Error::ERROR_OTHER, $this->error->getCode());
    }

    public function testSetCodeShouldCastToInteger()
    {
        $this->error->setCode('-32700');
        $this->assertEquals(-32700, $this->error->getCode());
    }

    public function testCodeShouldBeLimitedToStandardIntegers()
    {
        foreach (array(null, true, 'foo', array(), new \stdClass, 2.0) as $code) {
            $this->error->setCode($code);
            $this->assertEquals(Server\Error::ERROR_OTHER, $this->error->getCode());
        }
    }

    public function testCodeShouldAllowArbitraryAppErrorCodesInXmlRpcErrorCodeRange()
    {
        foreach (range(-32099, -32000) as $code) {
            $this->error->setCode($code);
            $this->assertEquals($code, $this->error->getCode());
        }
    }

    public function arbitraryErrorCodes()
    {
        return array(
            '1000'  => array(1000),
            '404'   => array(404),
            '-3000' => array(-3000),
        );
    }

    /**
     * @dataProvider arbitraryErrorCodes
     */
    public function testCodeShouldAllowArbitraryErrorCode($code)
    {
        $this->error->setCode($code);
        $this->assertEquals($code, $this->error->getCode());
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
        foreach (array(array(), new \stdClass) as $message) {
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
        foreach (array(true, 'foo', 2, 2.0, array(), new \stdClass) as $datum) {
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

    public function testShouldBeAbleToCastToJSON()
    {
        $this->setupError();
        $json = $this->error->toJSON();
        $this->validateArray(Json\Json::decode($json, Json\Json::TYPE_ARRAY));
    }

    public function testCastingToStringShouldCastToJSON()
    {
        $this->setupError();
        $json = $this->error->__toString();
        $this->validateArray(Json\Json::decode($json, Json\Json::TYPE_ARRAY));
    }

    public function setupError()
    {
        $this->error->setCode(Server\Error::ERROR_OTHER)
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
