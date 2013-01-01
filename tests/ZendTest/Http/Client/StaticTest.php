<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Http
 */

namespace ZendTest\Http\Client;

use Zend\Uri\Http as UriHttp;
use Zend\Http\Client as HTTPClient;
use Zend\Http;
use Zend\Http\Header\SetCookie;
use Zend\Http\Request;

/**
 * This Testsuite includes all Zend_Http_Client tests that do not rely
 * on performing actual requests to an HTTP server. These tests can be
 * executed once, and do not need to be tested with different servers /
 * client setups.
 *
 * @category   Zend
 * @package    Zend_Http_Client
 * @subpackage UnitTests
 * @group      Zend_Http
 * @group      Zend_Http_Client
 */
class StaticTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Common HTTP client
     *
     * @var \Zend\Http\Client
     */
    protected $_client = null;

    /**
     * Set up the test suite before each test
     *
     */
    public function setUp()
    {
        $this->_client = new MockClient('http://www.example.com');
    }

    /**
     * Clean up after running a test
     *
     */
    public function tearDown()
    {
        $this->_client = null;
    }

    /**
     * URI Tests
     */

    /**
     * Test we can SET and GET a URI as string
     *
     */
    public function testSetGetUriString()
    {
        $uristr = 'http://www.zend.com:80/';

        $this->_client->setUri($uristr);

        $uri = $this->_client->getUri();
        $this->assertTrue($uri instanceof UriHttp, 'Returned value is not a Uri object as expected');
        $this->assertEquals($uri->__toString(), $uristr, 'Returned Uri object does not hold the expected URI');

        $uri = $this->_client->getUri()->toString();
        $this->assertTrue(is_string($uri), 'Returned value expected to be a string, ' . gettype($uri) . ' returned');
        $this->assertEquals($uri, $uristr, 'Returned string is not the expected URI');
    }

    /**
     * Test we can SET and GET a URI as object
     *
     */
    public function testSetGetUriObject()
    {
        $uriobj = new UriHttp('http://www.zend.com:80/');

        $this->_client->setUri($uriobj);

        $uri = $this->_client->getUri();
        $this->assertTrue($uri instanceof UriHttp, 'Returned value is not a Uri object as expected');
        $this->assertEquals($uri, $uriobj, 'Returned object is not the excepted Uri object');
    }

    /**
     * Test that setting the same parameter twice in the query string does not
     * get reduced to a single value only.
     *
     */
    public function testDoubleGetParameter()
    {
        $qstr = 'foo=bar&foo=baz';

        $this->_client->setUri('http://example.com/test/?' . $qstr);
        $this->_client->setAdapter('\\Zend\\Http\\Client\\Adapter\\Test');
        $this->_client->setMethod('GET');
        $res = $this->_client->send();

        $this->assertContains($qstr, $this->_client->getLastRawRequest(),
            'Request is expected to contain the entire query string');
    }

    /**
     * Header Tests
     */

    /**
     * Test we can get already set headers
     *
     */
    public function testGetHeader()
    {
        $this->_client->setHeaders(array(
            'Accept-encoding' => 'gzip,deflate',
            'Accept-language' => 'en,de,*',
        ));

        $this->assertEquals($this->_client->getHeader('Accept-encoding'), 'gzip, deflate', 'Returned value of header is not as expected');
        $this->assertEquals($this->_client->getHeader('X-Fake-Header'), null, 'Non-existing header should not return a value');
    }

    /**
     * Authentication tests
     */

    /**
     * Test setAuth (dynamic method) fails when trying to use an unsupported
     * authentication scheme
     *
     */
    public function testExceptUnsupportedAuthDynamic()
    {
        $this->setExpectedException(
            'Zend\Http\Exception\InvalidArgumentException',
            'Invalid or not supported authentication type: \'SuperStrongAlgo\'');

        $this->_client->setAuth('shahar', '1234', 'SuperStrongAlgo');
    }

    /**
     * Cookie and Cookie Jar tests
     */

    /**
     * Test we can properly set a new cookies
     *
     */
    public function testSetNewCookies()
    {
        $this->_client->addCookie('cookie', 'value');
        $this->_client->addCookie('chocolate', 'chips');
        $cookies = $this->_client->getCookies();

        // Check we got the right cookiejar
        $this->assertTrue((is_array($cookies) && $cookies['chocolate'] instanceof SetCookie), '$cookie is not an array of Zend\Http\Header\SetCookie');
        $this->assertEquals(count($cookies), 2, '$cookies does not contain 2 SetCokie as expected');
    }

    /**
     * Test we can unset a cookie jar
     *
     */
    public function testUnsetCookies()
    {
        // Set the cookie jar just like in testSetNewCookieJar
        $this->_client->addCookie('cookie', 'value');
        $this->_client->addCookie('chocolate', 'chips');
        $cookies = $this->_client->getCookies();

        // Try unsetting the cookies
        $this->_client->clearCookies();
        $cookies = $this->_client->getCookies();

        $this->assertTrue(empty($cookies), 'Cookies is expected to be null but it is not');
    }

    /**
     * Make sure using an invalid cookie jar object throws an exception
     *
     */
    public function testSetInvalidCookies()
    {
        $this->setExpectedException(
            'Zend\Http\Exception\InvalidArgumentException',
            'Invalid parameter type passed as Cookie');

        $this->_client->addCookie('cookie');
    }

    /**
     * Configuration Handling
     */

    /**
     * Test that we can set a valid configuration array with some options
     *
     */
    public function testConfigSetAsArray()
    {

        $config = array(
            'timeout'    => 500,
            'someoption' => 'hasvalue'
        );

        $this->_client->setOptions($config);

        $hasConfig = $this->_client->config;

        foreach ($config as $k => $v) {
            $this->assertEquals($v, $hasConfig[$k]);
        }
    }

    /**
     * Test that a Zend_Config object can be used to set configuration
     *
     * @link http://framework.zend.com/issues/browse/ZF-5577
     */
    public function testConfigSetAsZendConfig()
    {

        $config = new \Zend\Config\Config(array(
            'timeout'  => 400,
            'nested'   => array(
                'item' => 'value',
            )
        ));

        $this->_client->setOptions($config);

        $hasConfig = $this->_client->config;
        $this->assertEquals($config->timeout, $hasConfig['timeout']);
        $this->assertEquals($config->nested->item, $hasConfig['nested']['item']);
    }

    /**
     * Test that passing invalid variables to setConfig() causes an exception
     *
     * @dataProvider      invalidConfigProvider
     */
    public function testConfigSetInvalid($config)
    {
        $this->setExpectedException(
            'Zend\Http\Client\Exception\InvalidArgumentException',
            'Config parameter is not valid');

        $this->_client->setOptions($config);
    }

    /**
     * Test that configuration options are passed to the adapter after the
     * adapter is instantiated
     *
     * @group ZF-4557
     */
    public function testConfigPassToAdapterZF4557()
    {

        $adapter = new MockAdapter();

        // test that config passes when we set the adapter
        $this->_client->setOptions(array('param' => 'value1'));
        $this->_client->setAdapter($adapter);
        $adapterCfg = $adapter->config;
        $this->assertEquals('value1', $adapterCfg['param']);

        // test that adapter config value changes when we set client config
        $this->_client->setOptions(array('param' => 'value2'));
        $adapterCfg = $adapter->config;
        $this->assertEquals('value2', $adapterCfg['param']);
    }

    /**
     * Other Tests
     */

    /**
     * Test the getLastRawResponse() method actually returns the last response
     *
     */
    public function testGetLastRawResponse()
    {
        // First, make sure we get null before the request
        $this->assertEquals(null, $this->_client->getLastRawResponse(),
            'getLastRawResponse() is still expected to return null');

        // Now, test we get a proper response after the request
        $this->_client->setUri('http://example.com/foo/bar');
        $this->_client->setAdapter('Zend\Http\Client\Adapter\Test');

        $response = $this->_client->send();
        $this->assertTrue(($response === $this->_client->getResponse()),
            'Response is expected to be identical to the result of getResponse()');
    }

    /**
     * Test that getLastRawResponse returns null when not storing
     *
     */
    public function testGetLastRawResponseWhenNotStoring()
    {
        // Now, test we get a proper response after the request
        $this->_client->setUri('http://example.com/foo/bar');
        $this->_client->setAdapter('Zend\Http\Client\Adapter\Test');
        $this->_client->setOptions(array('storeresponse' => false));

        $response = $this->_client->send();

        $this->assertNull($this->_client->getLastRawResponse(),
            'getLastRawResponse is expected to be null when not storing');
    }

    /**
     * Check we get an exception when trying to send a POST request with an
     * invalid content-type header
     *
     */
    public function testInvalidPostContentType()
    {
        if (!constant('TESTS_ZEND_HTTP_CLIENT_ONLINE')) {
            $this->markTestSkipped('Zend\Http\Client online tests are not enabled');
        }
        $this->setExpectedException(
            'Zend\Http\Exception\RuntimeException',
            'Cannot handle content type \'x-foo/something-fake\' automatically');

        $this->_client->setEncType('x-foo/something-fake');
        $this->_client->setParameterPost(array('parameter' => 'value'));
        $this->_client->setMethod('POST');
        // This should throw an exception
        $this->_client->send();
    }

    /**
     * Check we get an exception if there's an error in the socket
     *
     */
    public function testSocketErrorException()
    {
        if (!constant('TESTS_ZEND_HTTP_CLIENT_ONLINE')) {
            $this->markTestSkipped('Zend\Http\Client online tests are not enabled');
        }
        $this->setExpectedException(
            'Zend\Http\Client\Adapter\Exception\RuntimeException',
            'Unable to Connect to tcp://255.255.255.255:80');

        // Try to connect to an invalid host
        $this->_client->setUri('http://255.255.255.255');

        // Reduce timeout to 3 seconds to avoid waiting
        $this->_client->setOptions(array('timeout' => 3));

        // This call should cause an exception
        $this->_client->send();
    }

    /**
     * Check that an exception is thrown if non-word characters are used in
     * the request method.
     *
     * @dataProvider invalidMethodProvider
     */
    public function testSettingInvalidMethodThrowsException($method)
    {
        $this->setExpectedException(
            'Zend\Http\Exception\InvalidArgumentException',
            'Invalid HTTP method passed');

        $this->_client->setMethod($method);
    }

    /**
     * Test that POST data with mutli-dimentional array is properly encoded as
     * multipart/form-data
     *
     */
    public function testFormDataEncodingWithMultiArrayZF7038()
    {
        if (!constant('TESTS_ZEND_HTTP_CLIENT_ONLINE')) {
            $this->markTestSkipped('Zend\Http\Client online tests are not enabled');
        }
        $this->_client->setAdapter('Zend\Http\Client\Adapter\Test');
        $this->_client->setUri('http://example.com');
        $this->_client->setEncType(HTTPClient::ENC_FORMDATA);

        $this->_client->setParameterPost(array(
            'test' => array(
                        'v0.1',
                        'v0.2',
                        'k1' => 'v1.0',
                        'k2' => array(
                            'v2.1',
                            'k2.1' => 'v2.1.0'
                         ))
        ));

        $this->_client->setMethod('POST');
        $this->_client->send();

        $expectedLines = file(__DIR__ . '/_files/ZF7038-multipartarrayrequest.txt');

        $gotLines = explode("\n", $this->_client->getLastRawRequest());

        $this->assertEquals(count($expectedLines), count($gotLines));

        while (($expected = array_shift($expectedLines)) &&
               ($got = array_shift($gotLines))) {

            $expected = trim($expected);
            $got = trim($got);
            $this->assertRegExp("/^$expected$/", $got);
        }
    }

    /**
     * Test that we properly calculate the content-length of multibyte-encoded
     * request body
     *
     * This may file in case that mbstring overloads the substr and strlen
     * functions, and the mbstring internal encoding is a multibyte encoding.
     *
     * @link http://framework.zend.com/issues/browse/ZF-2098
     */
    public function testMultibyteRawPostDataZF2098()
    {
        if (!constant('TESTS_ZEND_HTTP_CLIENT_ONLINE')) {
            $this->markTestSkipped('Zend\Http\Client online tests are not enabled');
        }
        $this->_client->setAdapter('Zend\Http\Client\Adapter\Test');
        $this->_client->setUri('http://example.com');

        $bodyFile = __DIR__ . '/_files/ZF2098-multibytepostdata.txt';

        $this->_client->setRawBody(file_get_contents($bodyFile));
        $this->_client->setEncType('text/plain');
        $this->_client->setMethod('POST');
        $this->_client->send();
        $request = $this->_client->getLastRawRequest();

        if (! preg_match('/^content-length:\s+(\d+)/mi', $request, $match)) {
            $this->fail("Unable to find content-length header in request");
        }

        $this->assertEquals(filesize($bodyFile), (int) $match[1]);
    }

    /**
     * Testing if the connection isn't closed
     *
     * @group ZF-9685
     */
    public function testOpenTempStreamWithValidFileDoesntThrowsException()
    {
        if (!constant('TESTS_ZEND_HTTP_CLIENT_ONLINE')) {
            $this->markTestSkipped('Zend\Http\Client online tests are not enabled');
        }
        $url = 'http://www.example.com/';
        $config = array (
            'outputstream' => realpath(__DIR__ . '/_files/zend_http_client_stream.file'),
        );
        $client = new HTTPClient($url, $config);

        $result = $client->send();

        // we can safely return until we can verify link is still active
        // @todo verify link is still active
        return;
    }

    /**
     * Testing if the connection can be closed
     *
     * @group ZF-9685
     */
    public function testOpenTempStreamWithBogusFileClosesTheConnection()
    {
        if (!constant('TESTS_ZEND_HTTP_CLIENT_ONLINE')) {
            $this->markTestSkipped('Zend\Http\Client online tests are not enabled');
        }
        $this->setExpectedException(
            'Zend\Http\Exception\RuntimeException',
            'Could not open temp file /path/to/bogus/file.ext');

        $url = 'http://www.example.com';
        $config = array (
            'outputstream' => '/path/to/bogus/file.ext',
        );
        $client = new HTTPClient($url, $config);
        $client->setMethod('GET');
        $result = $client->send();
    }

    /**
     * Test sending cookie with encoded value
     *
     * @group fix-double-encoding-problem-about-cookie-value
     */
    public function testEncodedCookiesInRequestHeaders()
    {
        if (!constant('TESTS_ZEND_HTTP_CLIENT_ONLINE')) {
            $this->markTestSkipped('Zend\Http\Client online tests are not enabled');
        }
        $this->_client->addCookie('foo', 'bar=baz');
        $this->_client->send();
        $cookieValue = 'Cookie: foo='.urlencode('bar=baz');
        $this->assertContains($cookieValue, $this->_client->getLastRawRequest(),
            'Request is expected to contain the entire cookie "keyname=encoded_value"');
    }

    /**
     * Test sending cookie header with raw value
     *
     * @group fix-double-encoding-problem-about-cookie-value
     */
    public function testRawCookiesInRequestHeaders()
    {
        if (!constant('TESTS_ZEND_HTTP_CLIENT_ONLINE')) {
            $this->markTestSkipped('Zend\Http\Client online tests are not enabled');
        }
        $this->_client->setOptions(array('encodecookies' => false));
        $this->_client->addCookie('foo', 'bar=baz');
        $this->_client->send();
        $cookieValue = 'Cookie: foo=bar=baz';
        $this->assertContains($cookieValue, $this->_client->getLastRawRequest(),
            'Request is expected to contain the entire cookie "keyname=raw_value"');
    }

    /**
     * Data providers
     */

    /**
     * Data provider of valid non-standard HTTP methods
     *
     * @return array
     */
    public static function validMethodProvider()
    {
        return array(
            array('OPTIONS'),
            array('POST'),
            array('DOSOMETHING'),
            array('PROPFIND'),
            array('Some_Characters'),
            array('X-MS-ENUMATTS')
        );
    }

    /**
     * Data provider of invalid HTTP methods
     *
     * @return array
     */
    public static function invalidMethodProvider()
    {
        return array(
            array('N@5TYM3T#0D'),
            array('TWO WORDS'),
            array('GET http://foo.com/?'),
            array("Injected\nnewline")
        );
    }

    /**
     * Data provider for invalid configuration containers
     *
     * @return array
     */
    public static function invalidConfigProvider()
    {
        return array(
            array(false),
            array('foo => bar'),
            array(null),
            array(new \stdClass),
            array(55)
        );
    }
}

class MockClient extends HTTPClient
{
    public $config = array(
        'maxredirects'    => 5,
        'strictredirects' => false,
        'useragent'       => 'Zend_Http_Client',
        'timeout'         => 10,
        'adapter'         => 'Zend\\Http\\Client\\Adapter\\Socket',
        'httpversion'     => Request::VERSION_11,
        'keepalive'       => false,
        'storeresponse'   => true,
        'strict'          => true,
        'outputstream'   => false,
        'encodecookies'   => true,
    );
}

class MockAdapter extends \Zend\Http\Client\Adapter\Test
{
    public $config = array();
}
