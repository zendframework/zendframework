<?php

require_once realpath(dirname(__FILE__) . '/../../../') . '/TestHelper.php';

require_once 'Zend/Http/Client.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * This Testsuite includes all Zend_Http_Client tests that do not rely
 * on performing actual requests to an HTTP server. These tests can be
 * executed once, and do not need to be tested with different servers /
 * client setups.
 *
 * @category   Zend
 * @package    Zend_Http_Client
 * @subpackage UnitTests
 * @version    $Id$
 * @copyright
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Http_Client_StaticTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Common HTTP client
	 *
	 * @var Zend_Http_Client
	 */
	protected $client = null;

	/**
	 * Set up the test suite before each test
	 *
	 */
	public function setUp()
	{
		$this->client = new Zend_Http_Client('http://www.example.com');
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

		$this->client->setUri($uristr);

		$uri = $this->client->getUri();
		$this->assertTrue($uri instanceof Zend_Uri_Http, 'Returned value is not a Uri object as expected');
		$this->assertEquals($uri->__toString(), $uristr, 'Returned Uri object does not hold the expected URI');

		$uri = $this->client->getUri(true);
		$this->assertTrue(is_string($uri), 'Returned value expected to be a string, ' . gettype($uri) . ' returned');
		$this->assertEquals($uri, $uristr, 'Returned string is not the expected URI');
	}

	/**
	 * Test we can SET and GET a URI as object
	 *
	 */
	public function testSetGetUriObject()
	{
		$uriobj = Zend_Uri::factory('http://www.zend.com:80/');

		$this->client->setUri($uriobj);

		$uri = $this->client->getUri();
		$this->assertTrue($uri instanceof Zend_Uri_Http, 'Returned value is not a Uri object as expected');
		$this->assertEquals($uri, $uriobj, 'Returned object is not the excepted Uri object');
	}

	/**
	 * Test that passing an invalid URI string throws an exception
	 *
	 */
	public function testInvalidUriStringException()
	{
		try {
			$this->client->setUri('httpp://__invalid__.com');
			$this->fail('Excepted invalid URI string exception was not thrown');
		} catch (Zend_Uri_Exception $e) {
			// We're good
		}
	}

	/**
	 * Test that passing an invalid URI object throws an exception
	 *
	 */
	public function testInvalidUriObjectException()
	{
            try {
                $uri = Zend_Uri::factory('mailto:nobody@example.com');
                $this->client->setUri($uri);
                $this->fail('Excepted invalid URI object exception was not thrown');
            } catch (Zend_Http_Client_Exception $e) {
                // We're good
            } catch (Zend_Uri_Exception $e) {
                // URI is currently unimplemented
                $this->markTestIncomplete('Zend_Uri_Mailto is not implemented yet');
            }

	}

	/**
	 * Header Tests
	 */

	/**
	 * Make sure an exception is thrown if an invalid header name is used
	 *
	 */
	public function testInvalidHeaderExcept()
	{
		try {
			$this->client->setHeaders('Ina_lid* Hea%der', 'is not good');
			$this->fail('Expected invalid header name exception was not thrown');
		} catch (Zend_Http_Client_Exception $e) {
			// We're good
		}
	}

	/**
	 * Make sure non-strict mode disables header name validation
	 *
	 */
	public function testInvalidHeaderNonStrictMode()
	{
	    // Disable strict validation
	    $this->client->setConfig(array('strict' => false));

		try {
			$this->client->setHeaders('Ina_lid* Hea%der', 'is not good');
		} catch (Zend_Http_Client_Exception $e) {
			$this->fail('Invalid header names should be allowed in non-strict mode');
		}
	}

	/**
	 * Test we can get already set headers
	 *
	 */
	public function testGetHeader()
	{
		$this->client->setHeaders(array(
			'Accept-encoding' => 'gzip,deflate',
			'Accept-language' => 'en,de,*',
		));

		$this->assertEquals($this->client->getHeader('Accept-encoding'), 'gzip,deflate', 'Returned value of header is not as expected');
		$this->assertEquals($this->client->getHeader('X-Fake-Header'), null, 'Non-existing header should not return a value');
	}

	public function testUnsetHeader()
	{
		$this->client->setHeaders('Accept-Encoding', 'gzip,deflate');
		$this->client->setHeaders('Accept-Encoding', null);
		$this->assertNull($this->client->getHeader('Accept-encoding'), 'Returned value of header is expected to be null');
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
		try {
			$this->client->setAuth('shahar', '1234', 'SuperStrongAlgo');
			$this->fail('Trying to use unknown authentication method, setAuth should throw an exception but it didn\'t');
		} catch (Zend_Http_Client_Exception $e) {
			// We're good!
		}
	}

	/**
	 * Test encodeAuthHeader (static method) fails when trying to use an
	 * unsupported authentication scheme
	 *
	 */
	public function testExceptUnsupportedAuthStatic()
	{
		try {
			Zend_Http_Client::encodeAuthHeader('shahar', '1234', 'SuperStrongAlgo');
			$this->fail('Trying to use unknown authentication method, encodeAuthHeader should throw an exception but it didn\'t');
		} catch (Zend_Http_Client_Exception $e) {
			// We're good!
		}
	}

	/**
	 * Cookie and Cookie Jar tests
	 */

	/**
	 * Test we can properly set a new cookie jar
	 *
	 */
	public function testSetNewCookieJar()
	{
		$this->client->setCookieJar();
		$this->client->setCookie('cookie', 'value');
		$this->client->setCookie('chocolate', 'chips');
		$jar = $this->client->getCookieJar();

		// Check we got the right cookiejar
		$this->assertTrue($jar instanceof Zend_Http_CookieJar, '$jar is not an instance of Zend_Http_CookieJar as expected');
		$this->assertEquals(count($jar->getAllCookies()), 2, '$jar does not contain 2 cookies as expected');
	}

	/**
	 * Test we can properly set an existing cookie jar
	 *
	 */
	public function testSetReadyCookieJar()
	{
		$jar = new Zend_Http_CookieJar();
		$jar->addCookie('cookie=value', 'http://www.example.com');
		$jar->addCookie('chocolate=chips; path=/foo', 'http://www.example.com');

		$this->client->setCookieJar($jar);

		// Check we got the right cookiejar
		$this->assertEquals($jar, $this->client->getCookieJar(), '$jar is not the client\'s cookie jar as expected');
	}

	/**
	 * Test we can unset a cookie jar
	 *
	 */
	public function testUnsetCookieJar()
	{
		// Set the cookie jar just like in testSetNewCookieJar
		$this->client->setCookieJar();
		$this->client->setCookie('cookie', 'value');
		$this->client->setCookie('chocolate', 'chips');
		$jar = $this->client->getCookieJar();

		// Try unsetting the cookiejar
		$this->client->setCookieJar(null);

		$this->assertNull($this->client->getCookieJar(), 'Cookie jar is expected to be null but it is not');
	}

	/**
	 * Make sure using an invalid cookie jar object throws an exception
	 *
	 */
	public function testSetInvalidCookieJar()
	{
		try {
			$this->client->setCookieJar('cookiejar');
			$this->fail('Invalid cookiejar exception was not thrown');
		} catch (Exception $e) {
			// We're good
		}
	}

	/**
	 * Other Tests
	 */

	/**
	 * Check we get an exception when trying to send a POST request with an
	 * invalid content-type header
	 */
	public function testInvalidPostContentType()
	{
		$this->client->setEncType('x-foo/something-fake');
		$this->client->setParameterPost('parameter', 'value');

		try {
			$this->client->request('POST');
			$this->fail('Building the body with an unknown content-type for POST values should have failed, it didn\'t');
		} catch (Zend_Http_Client_Exception $e) {
			// We are ok!
		}
	}

	/**
	 * Check we get an exception if there's an error in the socket
	 *
	 */
	public function testSocketErrorException() {
		// Try to connect to an invalid host
		$this->client->setUri('http://255.255.255.255');
		// Reduce timeout to 3 seconds to avoid waiting
		$this->client->setConfig(array('timeout' => 3));

		try {
			$this->client->request();
			$this->fail('Expected connection error exception was not thrown');
		} catch (Zend_Http_Client_Adapter_Exception $e) {
			// We're good!
		}
	}

	/**
	 * Check that we can set methods which are not documented in the RFC.
	 * Also, check that an exception is thrown if non-word characters are
	 * used in the request method.
	 *
	 */
	public function testSettingExtendedMethod()
	{
		$goodMethods = array(
			'OPTIONS',
			'POST',
			'DOSOMETHING',
			'PROPFIND',
			'Some_Characters',
			'X-MS-ENUMATTS'
		);

		foreach ($goodMethods as $method) {
			try {
				$this->client->setMethod($method);
			} catch (Exception $e) {
				$this->fail("An unexpected exception was thrown when setting request method to '{$method}'");
			}
		}

		$badMethods = array(
			'N@5TYM3T#0D',
			'TWO WORDS',
			'GET http://foo.com/?',
			"Injected\nnewline"
		);

		foreach ($badMethods as $method) {
			try {
				$this->client->setMethod($method);
				$this->fail("A Zend_Http_Client_Exception was expected but was not thrown when setting request method to '{$method}'");
			} catch (Zend_Http_Client_Exception $e) {
				// We're ok!
			}
		}
	}

	/**
	 * Test that configuration options are passed to the adapter after the
	 * adapter is instantiated
	 *
	 * @link http://framework.zend.com/issues/browse/ZF-4557
	 */
	public function testConfigPassToAdapterZF4557()
	{
	    require_once 'Zend/Http/Client/Adapter/Test.php';
	    $adapter = new Zend_Http_Client_Adapter_Test();

	    // test that config passes when we set the adapter
	    $this->client->setConfig(array('param' => 'value1'));
	    $this->client->setAdapter($adapter);
	    $adapterCfg = $this->getObjectAttribute($adapter, 'config');
	    $this->assertEquals('value1', $adapterCfg['param']);

	    // test that adapter config value changes when we set client config
	    $this->client->setConfig(array('param' => 'value2'));
	    $adapterCfg = $this->getObjectAttribute($adapter, 'config');
	    $this->assertEquals('value2', $adapterCfg['param']);
	}
}
