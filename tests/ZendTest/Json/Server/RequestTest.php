<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Json
 */

namespace ZendTest\Json\Server;

use Zend\Json;

/**
 * @category   Zend
 * @package    Zend_JSON_Server
 * @subpackage UnitTests
 * @group      Zend_JSON
 * @group      Zend_JSON_Server
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->request = new \Zend\Json\Server\Request();
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
        foreach (array(array('foo', true), array('foo', new \stdClass), array('foo', array())) as $spec) {
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

    public function testVersionShouldBeJSONRpcV1ByDefault()
    {
        $this->assertEquals('1.0', $this->request->getVersion());
    }

    public function testVersionShouldBeLimitedToV1AndV2()
    {
        $this->testVersionShouldBeJSONRpcV1ByDefault();
        $this->request->setVersion('2.0');
        $this->assertEquals('2.0', $this->request->getVersion());
        $this->request->setVersion('foo');
        $this->assertEquals('1.0', $this->request->getVersion());
    }

    public function testShouldBeAbleToLoadRequestFromJSONString()
    {
        $options = $this->getOptions();
        $json    = Json\Json::encode($options);
        $this->request->loadJSON($json);

        $this->assertEquals('foo', $this->request->getMethod());
        $this->assertEquals('foobar', $this->request->getId());
        $this->assertEquals($options['params'], $this->request->getParams());
    }

    public function testLoadingFromJSONShouldSetJSONRpcVersionWhenPresent()
    {
        $options = $this->getOptions();
        $options['jsonrpc'] = '2.0';
        $json    = Json\Json::encode($options);
        $this->request->loadJSON($json);
        $this->assertEquals('2.0', $this->request->getVersion());
    }

    public function testShouldBeAbleToCastToJSON()
    {
        $options = $this->getOptions();
        $this->request->setOptions($options);
        $json    = $this->request->toJSON();
        $this->validateJSON($json, $options);
    }

    public function testCastingToStringShouldCastToJSON()
    {
        $options = $this->getOptions();
        $this->request->setOptions($options);
        $json    = $this->request->__toString();
        $this->validateJSON($json, $options);
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

    public function validateJSON($json, array $options)
    {
        $test = Json\Json::decode($json, Json\Json::TYPE_ARRAY);
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
