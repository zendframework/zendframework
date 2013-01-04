<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Authentication
 */

namespace ZendTest\Authentication\Adapter\Http;

use Zend\Authentication\Adapter\Http;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Http\Response;

/**
 * @category   Zend
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @group      Zend_Auth
 */
class ProxyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Path to test files
     *
     * @var string
     */
    protected $_filesPath;

    /**
     * HTTP Basic configuration
     *
     * @var array
     */
    protected $_basicConfig;

    /**
     * HTTP Digest configuration
     *
     * @var array
     */
    protected $_digestConfig;

    /**
     * HTTP Basic Digest configuration
     *
     * @var array
     */
    protected $_bothConfig;

    /**
     * File resolver setup against with HTTP Basic auth file
     *
     * @var Zend_Auth_Adapter_Http_Resolver_File
     */
    protected $_basicResolver;

    /**
     * File resolver setup against with HTTP Digest auth file
     *
     * @var Zend_Auth_Adapter_Http_Resolver_File
     */
    protected $_digestResolver;

    /**
     * Sets up test configuration
     *
     * @return void
     */
    public function setUp()
    {
        $this->_filesPath      = __DIR__ . '/TestAsset';
        $this->_basicResolver  = new Http\FileResolver("{$this->_filesPath}/htbasic.1");
        $this->_digestResolver = new Http\FileResolver("{$this->_filesPath}/htdigest.3");
        $this->_basicConfig    = array(
            'accept_schemes' => 'basic',
            'realm'          => 'Test Realm',
            'proxy_auth'     => true
        );
        $this->_digestConfig   = array(
            'accept_schemes' => 'digest',
            'realm'          => 'Test Realm',
            'digest_domains' => '/ http://localhost/',
            'nonce_timeout'  => 300,
            'proxy_auth'     => true
        );
        $this->_bothConfig     = array(
            'accept_schemes' => 'basic digest',
            'realm'          => 'Test Realm',
            'digest_domains' => '/ http://localhost/',
            'nonce_timeout'  => 300,
            'proxy_auth'     => true
        );
    }

    public function testBasicChallenge()
    {
        // Trying to authenticate without sending an Proxy-Authorization header
        // should result in a 407 reply with a Proxy-Authenticate header, and a
        // false result.

        // The expected Basic Proxy-Authenticate header value
        $basic = array(
            'type'   => 'Basic ',
            'realm'  => 'realm="' . $this->_bothConfig['realm'] . '"',
        );

        $data = $this->_doAuth('', 'basic');
        $this->_checkUnauthorized($data, $basic);
    }

    public function testDigestChallenge()
    {
        // Trying to authenticate without sending an Proxy-Authorization header
        // should result in a 407 reply with a Proxy-Authenticate header, and a
        // false result.

        // The expected Digest Proxy-Authenticate header value
        $digest = $this->_digestChallenge();

        $data = $this->_doAuth('', 'digest');
        $this->_checkUnauthorized($data, $digest);
    }

    public function testBothChallenges()
    {
        // Trying to authenticate without sending an Proxy-Authorization header
        // should result in a 407 reply with at least one Proxy-Authenticate
        // header, and a false result.

        $data = $this->_doAuth('', 'both');
        extract($data); // $result, $status, $headers

        // The expected Proxy-Authenticate header values
        $basic  = 'Basic realm="' . $this->_bothConfig['realm'] . '"';
        $digest = $this->_digestChallenge();

        // Make sure the result is false
        $this->assertInstanceOf('Zend\\Authentication\\Result', $result);
        $this->assertFalse($result->isValid());

        // Verify the status code and the presence of both challenges
        $this->assertEquals(407, $status);
        $this->assertTrue($headers->has('Proxy-Authenticate'));
        $authHeader = $headers->get('Proxy-Authenticate');
        $this->assertEquals(2, count($authHeader), var_export($authHeader, 1));

        // Check to see if the expected challenges match the actual
        $basicFound = $digestFound = false;
        foreach ($authHeader as $header) {
            $value = $header->getFieldValue();
            if (preg_match('/^Basic/', $value)) {
                $basicFound = true;
            }
            if (preg_match('/^Digest/', $value)) {
                $digestFound = true;
            }
        }
        $this->assertTrue($basicFound);
        $this->assertTrue($digestFound);
    }

    public function testBasicAuthValidCreds()
    {
        // Attempt Basic Authentication with a valid username and password

        $data = $this->_doAuth('Basic ' . base64_encode('Bryce:ThisIsNotMyPassword'), 'basic');
        $this->_checkOK($data);
    }

    public function testBasicAuthBadCreds()
    {
        // Ensure that credentials containing invalid characters are treated as
        // a bad username or password.

        // The expected Basic WWW-Authenticate header value
        $basic = array(
            'type'   => 'Basic ',
            'realm'  => 'realm="' . $this->_basicConfig['realm'] . '"',
        );

        $data = $this->_doAuth('Basic ' . base64_encode("Bad\tChars:In:Creds"), 'basic');
        $this->_checkUnauthorized($data, $basic);
    }

    public function testBasicAuthBadUser()
    {
        // Attempt Basic Authentication with a bad username and password

        // The expected Basic Proxy-Authenticate header value
        $basic = array(
            'type'   => 'Basic ',
            'realm'  => 'realm="' . $this->_basicConfig['realm'] . '"',
        );

        $data = $this->_doAuth('Basic ' . base64_encode('Nobody:NotValid'), 'basic');
        $this->_checkUnauthorized($data, $basic);
    }

    public function testBasicAuthBadPassword()
    {
        // Attempt Basic Authentication with a valid username, but invalid
        // password

        // The expected Basic WWW-Authenticate header value
        $basic = array(
            'type'   => 'Basic ',
            'realm'  => 'realm="' . $this->_basicConfig['realm'] . '"',
        );

        $data = $this->_doAuth('Basic ' . base64_encode('Bryce:Invalid'), 'basic');
        $this->_checkUnauthorized($data, $basic);
    }

    public function testDigestAuthValidCreds()
    {
        // Attempt Digest Authentication with a valid username and password

        $data = $this->_doAuth($this->_digestReply('Bryce', 'ThisIsNotMyPassword'), 'digest');
        $this->_checkOK($data);
    }

    public function testDigestAuthDefaultAlgo()
    {
        // If the client omits the aglorithm argument, it should default to MD5,
        // and work just as above

        $cauth = $this->_digestReply('Bryce', 'ThisIsNotMyPassword');
        $cauth = preg_replace('/algorithm="MD5", /', '', $cauth);

        $data = $this->_doAuth($cauth, 'digest');
        $this->_checkOK($data);
    }

    public function testDigestAuthQuotedNC()
    {
        // The nonce count isn't supposed to be quoted, but apparently some
        // clients do anyway.

        $cauth = $this->_digestReply('Bryce', 'ThisIsNotMyPassword');
        $cauth = preg_replace('/nc=00000001/', 'nc="00000001"', $cauth);

        $data = $this->_doAuth($cauth, 'digest');
        $this->_checkOK($data);
    }

    public function testDigestAuthBadCreds()
    {
        // Attempt Digest Authentication with a bad username and password

        // The expected Digest Proxy-Authenticate header value
        $digest = $this->_digestChallenge();

        $data = $this->_doAuth($this->_digestReply('Nobody', 'NotValid'), 'digest');
        $this->_checkUnauthorized($data, $digest);
    }

    public function testDigestTampered()
    {
        // Create the tampered header value
        $tampered = $this->_digestReply('Bryce', 'ThisIsNotMyPassword');
        $tampered = preg_replace(
            '/ nonce="[a-fA-F0-9]{32}", /',
            ' nonce="' . str_repeat('0', 32).'", ',
            $tampered
        );

        // The expected Digest Proxy-Authenticate header value
        $digest = $this->_digestChallenge();

        $data = $this->_doAuth($tampered, 'digest');
        $this->_checkUnauthorized($data, $digest);
    }

    public function testBadSchemeRequest()
    {
        // Sending a request for an invalid authentication scheme should result
        // in a 400 Bad Request response.

        $data = $this->_doAuth('Invalid ' . base64_encode('Nobody:NotValid'), 'basic');
        $this->_checkBadRequest($data);
    }

    public function testBadDigestRequest()
    {
        // If any of the individual parts of the Digest Proxy-Authorization header
        // are bad, it results in a 400 Bad Request. But that's a lot of
        // possibilities, so we're just going to pick one for now.
        $bad = $this->_digestReply('Bryce', 'ThisIsNotMyPassword');
        $bad = preg_replace(
            '/realm="([^"]+)"/',  // cut out the realm
            '', $bad
        );

        $data = $this->_doAuth($bad, 'digest');
        $this->_checkBadRequest($data);
    }

    /**
     * Acts like a client sending the given Authenticate header value.
     *
     * @param  string $clientHeader Authenticate header value
     * @param  string $scheme       Which authentication scheme to use
     * @return array Containing the result, the response headers, and the status
     */
    public function _doAuth($clientHeader, $scheme)
    {
        // Set up stub request and response objects
        $response = new Response;
        $response->setStatusCode(200);

        $headers  = new Headers();
        $headers->addHeaderLine('Proxy-Authorization', $clientHeader);
        $headers->addHeaderLine('User-Agent', 'PHPUnit');

        $request  = new Request();
        $request->setUri('http://localhost/');
        $request->setMethod('GET');
        $request->setHeaders($headers);

        // Select an Authentication scheme
        switch ($scheme) {
            case 'basic':
                $use = $this->_basicConfig;
                break;
            case 'digest':
                $use = $this->_digestConfig;
                break;
            case 'both':
            default:
                $use = $this->_bothConfig;
        }

        // Create the HTTP Auth adapter
        $a = new \Zend\Authentication\Adapter\Http($use);
        $a->setBasicResolver($this->_basicResolver);
        $a->setDigestResolver($this->_digestResolver);

        // Send the authentication request
        $a->setRequest($request);
        $a->setResponse($response);
        $result = $a->authenticate();

        $return = array(
            'result'  => $result,
            'status'  => $response->getStatusCode(),
            'headers' => $response->getHeaders(),
        );
        return $return;
    }

    /**
     * Constructs a local version of the digest challenge we expect to receive
     *
     * @return string
     */
    protected function _digestChallenge()
    {
        return array(
            'type'   => 'Digest ',
            'realm'  => 'realm="' . $this->_digestConfig['realm'] . '"',
            'domain' => 'domain="' . $this->_bothConfig['digest_domains'] . '"',
        );
    }

    /**
     * Constructs a client digest Proxy-Authorization header
     *
     * @param  string $user
     * @param  string $pass
     * @return string
     */
    protected function _digestReply($user, $pass)
    {
        $nc       = '00000001';
        $timeout  = ceil(time() / 300) * 300;
        $nonce    = md5($timeout . ':PHPUnit:Zend\\Authentication\\Adapter\\Http');
        $opaque   = md5('Opaque Data:Zend\\Authentication\\Adapter\\Http');
        $cnonce   = md5('cnonce');
        $response = md5(md5($user . ':' . $this->_digestConfig['realm'] . ':' . $pass) . ":$nonce:$nc:$cnonce:auth:"
                  . md5('GET:/'));
        $cauth = 'Digest '
               . 'username="Bryce", '
               . 'realm="' . $this->_digestConfig['realm'] . '", '
               . 'nonce="' . $nonce . '", '
               . 'uri="/", '
               . 'response="' . $response . '", '
               . 'algorithm="MD5", '
               . 'cnonce="' . $cnonce . '", '
               . 'opaque="' . $opaque . '", '
               . 'qop="auth", '
               . 'nc=' . $nc;

        return $cauth;
    }

    /**
     * Checks for an expected 407 Proxy-Unauthorized response
     *
     * @param  array  $data     Authentication results
     * @param  string $expected Expected Proxy-Authenticate header value
     * @return void
     */
    protected function _checkUnauthorized($data, $expected)
    {
        extract($data); // $result, $status, $headers

        // Make sure the result is false
        $this->assertInstanceOf('Zend\\Authentication\\Result', $result);
        $this->assertFalse($result->isValid());

        // Verify the status code and the presence of the challenge
        $this->assertEquals(407, $status);
        $this->assertTrue($headers->has('Proxy-Authenticate'));

        // Check to see if the expected challenge matches the actual
        $headers = $headers->get('Proxy-Authenticate');
        $this->assertTrue($headers instanceof \ArrayIterator);
        $this->assertEquals(1, count($headers));
        $header = $headers[0]->getFieldValue();
        $this->assertContains($expected['type'], $header, $header);
        $this->assertContains($expected['realm'], $header, $header);
        if (isset($expected['domain'])) {
            $this->assertContains($expected['domain'], $header, $header);
            $this->assertContains('algorithm="MD5"', $header, $header);
            $this->assertContains('qop="auth"', $header, $header);
            $this->assertRegExp('/nonce="[a-fA-F0-9]{32}"/', $header, $header);
            $this->assertRegExp('/opaque="[a-fA-F0-9]{32}"/', $header, $header);
        }
    }

    /**
     * Checks for an expected 200 OK response
     *
     * @param  array $data Authentication results
     * @return void
     */
    protected function _checkOK($data)
    {
        extract($data); // $result, $status, $headers

        // Make sure the result is true
        $this->assertInstanceOf('Zend\\Authentication\\Result', $result);
        $this->assertTrue($result->isValid(), var_export($result->getMessages(), 1));

        // Verify we got a 200 response
        $this->assertEquals(200, $status);
    }

    /**
     * Checks for an expected 400 Bad Request response
     *
     * @param  array $data Authentication results
     * @return void
     */
    protected function _checkBadRequest($data)
    {
        extract($data); // $result, $status, $headers

        // Make sure the result is false
        $this->assertInstanceOf('Zend\\Authentication\\Result', $result);
        $this->assertFalse($result->isValid());

        // Make sure it set the right HTTP code
        $this->assertEquals(400, $status);
    }
}
