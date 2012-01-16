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
 * @package    Zend_JSON_Server
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Json\Server;
use Zend\Json\Server,
    Zend\Json;

/**
 * Test class for Zend_JSON_Server_Response
 *
 * @category   Zend
 * @package    Zend_JSON_Server
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_JSON
 * @group      Zend_JSON_Server
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->response = new \Zend\Json\Server\Response();
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
        $error = new Server\Error();
        $this->response->setError($error);
        $this->assertTrue($this->response->isError());
    }

    public function testShouldBeAbleToRetrieveErrorObject()
    {
        $error = new Server\Error();
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

    public function testShouldBeAbleToLoadResponseFromJSONString()
    {
        $options = $this->getOptions();
        $json    = Json\Json::encode($options);
        $this->response->loadJSON($json);

        $this->assertEquals('foobar', $this->response->getId());
        $this->assertEquals($options['result'], $this->response->getResult());
    }

    public function testLoadingFromJSONShouldSetJSONRpcVersionWhenPresent()
    {
        $options = $this->getOptions();
        $options['jsonrpc'] = '2.0';
        $json    = Json\Json::encode($options);
        $this->response->loadJSON($json);
        $this->assertEquals('2.0', $this->response->getVersion());
    }

    public function testResponseShouldBeAbleToCastToJSON()
    {
        $this->response->setResult(true)
                       ->setId('foo')
                       ->setVersion('2.0');
        $json = $this->response->toJSON();
        $test = Json\Json::decode($json, Json\Json::TYPE_ARRAY);

        $this->assertTrue(is_array($test));
        $this->assertTrue(array_key_exists('result', $test));
        $this->assertFalse(array_key_exists('error', $test), "'error' may not coexist with 'result'");
        $this->assertTrue(array_key_exists('id', $test));
        $this->assertTrue(array_key_exists('jsonrpc', $test));

        $this->assertTrue($test['result']);
        $this->assertEquals($this->response->getId(), $test['id']);
        $this->assertEquals($this->response->getVersion(), $test['jsonrpc']);
    }

    public function testResponseShouldCastErrorToJSONIfIsError()
    {
        $error = new Server\Error();
        $error->setCode(Server\Error::ERROR_INTERNAL)
              ->setMessage('error occurred');
        $this->response->setId('foo')
                       ->setResult(true)
                       ->setError($error);
        $json = $this->response->toJSON();
        $test = Json\Json::decode($json, Json\Json::TYPE_ARRAY);

        $this->assertTrue(is_array($test));
        $this->assertFalse(array_key_exists('result', $test), "'result' may not coexist with 'error'");
        $this->assertTrue(array_key_exists('error', $test));
        $this->assertTrue(array_key_exists('id', $test));
        $this->assertFalse(array_key_exists('jsonrpc', $test));

        $this->assertEquals($this->response->getId(), $test['id']);
        $this->assertEquals($error->getCode(), $test['error']['code']);
        $this->assertEquals($error->getMessage(), $test['error']['message']);
    }

    public function testCastToStringShouldCastToJSON()
    {
        $this->response->setResult(true)
                       ->setId('foo');
        $json = $this->response->__toString();
        $test = Json\Json::decode($json, Json\Json::TYPE_ARRAY);

        $this->assertTrue(is_array($test));
        $this->assertTrue(array_key_exists('result', $test));
        $this->assertFalse(array_key_exists('error', $test), "'error' may not coexist with 'result'");
        $this->assertTrue(array_key_exists('id', $test));
        $this->assertFalse(array_key_exists('jsonrpc', $test));

        $this->assertTrue($test['result']);
        $this->assertEquals($this->response->getId(), $test['id']);
    }

    public function getOptions()
    {
        return array(
            'result' => array(
                5,
                'four',
                true,
            ),
            'id'  => 'foobar'
        );
    }
}
