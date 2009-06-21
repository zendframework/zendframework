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
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @see Zend_Auth_Adapter_Http
 */
require_once 'Zend/Auth/Adapter/Http.php';


/**
 * @see Zend_Auth_Adapter_Http_Resolver_File
 */
require_once 'Zend/Auth/Adapter/Http/Resolver/File.php';


/**
 * @see Zend_Controller_Request_Http
 */
require_once 'Zend/Controller/Request/Http.php';


/**
 * @see Zend_Controller_Response_Http
 */
require_once 'Zend/Controller/Response/Http.php';


/**
 * @category   Zend
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Auth_Adapter_Http_AuthTest extends PHPUnit_Framework_TestCase
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
     * Set up test configuration
     *
     * @return void
     */
    public function __construct()
    {
        $this->_filesPath      = dirname(__FILE__) . '/_files';
        $this->_basicResolver  = new Zend_Auth_Adapter_Http_Resolver_File("{$this->_filesPath}/htbasic.1");
        $this->_digestResolver = new Zend_Auth_Adapter_Http_Resolver_File("{$this->_filesPath}/htdigest.3");
        $this->_basicConfig    = array(
            'accept_schemes' => 'basic',
            'realm'          => 'Test Realm'
        );
        $this->_digestConfig   = array(
            'accept_schemes' => 'digest',
            'realm'          => 'Test Realm',
            'digest_domains' => '/ http://localhost/',
            'nonce_timeout'  => 300
        );
        $this->_bothConfig     = array(
            'accept_schemes' => 'basic digest',
            'realm'          => 'Test Realm',
            'digest_domains' => '/ http://localhost/',
            'nonce_timeout'  => 300
        );
    }

    public function testBasicChallenge()
    {
        // Trying to authenticate without sending an Authorization header
        // should result in a 401 reply with a Www-Authenticate header, and a
        // false result.

        // The expected Basic Www-Authenticate header value
        $basic = 'Basic realm="' . $this->_bothConfig['realm'] . '"';

        $data = $this->_doAuth('', 'basic');
        $this->_checkUnauthorized($data, $basic);
    }

    public function testDigestChallenge()
    {
        // Trying to authenticate without sending an Authorization header
        // should result in a 401 reply with a Www-Authenticate header, and a
        // false result.

        // The expected Digest Www-Authenticate header value
        $digest = $this->_digestChallenge();

        $data = $this->_doAuth('', 'digest');
        $this->_checkUnauthorized($data, $digest);
    }

    public function testBothChallenges()
    {
        // Trying to authenticate without sending an Authorization header
        // should result in a 401 reply with at least one Www-Authenticate
        // header, and a false result.

        $data = $this->_doAuth('', 'both');
        extract($data); // $result, $status, $headers

        // The expected Www-Authenticate header values
        $basic  = 'Basic realm="' . $this->_bothConfig['realm'] . '"';
        $digest = $this->_digestChallenge();

        // Make sure the result is false
        $this->assertType('Zend_Auth_Result', $result);
        $this->assertFalse($result->isValid());

        // Verify the status code and the presence of both challenges
        $this->assertEquals(401, $status);
        $this->assertEquals('Www-Authenticate', $headers[0]['name']);
        $this->assertEquals('Www-Authenticate', $headers[1]['name']);

        // Check to see if the expected challenges match the actual
        $this->assertEquals($basic,  $headers[0]['value']);
        $this->assertEquals($digest, $headers[1]['value']);
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

        // The expected Basic Www-Authenticate header value
        $basic = 'Basic realm="' . $this->_basicConfig['realm'] . '"';

        $data = $this->_doAuth('Basic ' . base64_encode("Bad\tChars:In:Creds"), 'basic');
        $this->_checkUnauthorized($data, $basic);
    }

    public function testBasicAuthBadUser()
    {
        // Attempt Basic Authentication with a nonexistant username and 
        // password

        // The expected Basic Www-Authenticate header value
        $basic = 'Basic realm="' . $this->_basicConfig['realm'] . '"';

        $data = $this->_doAuth('Basic ' . base64_encode('Nobody:NotValid'), 'basic');
        $this->_checkUnauthorized($data, $basic);
    }

    public function testBasicAuthBadPassword()
    {
        // Attempt Basic Authentication with a valid username, but invalid 
        // password

        // The expected Basic Www-Authenticate header value
        $basic = 'Basic realm="' . $this->_basicConfig['realm'] . '"';

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

        // The expected Digest Www-Authenticate header value
        $digest = $this->_digestChallenge();

        $data = $this->_doAuth($this->_digestReply('Nobody', 'NotValid'), 'digest');
        $this->_checkUnauthorized($data, $digest);
    }

    public function testDigestAuthBadCreds2()
    {
        // Formerly, a username with invalid characters would result in a 400 
        // response, but now should result in 401 response.

        // The expected Digest Www-Authenticate header value
        $digest = $this->_digestChallenge();

        $data = $this->_doAuth($this->_digestReply('Bad:chars', 'NotValid'), 'digest');
        $this->_checkUnauthorized($data, $digest);
    }

    public function testDigestTampered()
    {
        // Create the tampered header value
        $tampered = $this->_digestReply('Bryce', 'ThisIsNotMyPassword');
        $tampered = preg_replace(
            '/ nonce="[a-fA-F0-9]{32}", /',
            ' nonce="'.str_repeat('0', 32).'", ',
            $tampered
        );

        // The expected Digest Www-Authenticate header value
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
        // If any of the individual parts of the Digest Authorization header
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
     * @return array Containing the result, response headers, and the status
     */
    protected function _doAuth($clientHeader, $scheme)
    {
        // Set up stub request and response objects
        $request  = $this->getMock('Zend_Controller_Request_Http');
        $response = new Zend_Controller_Response_Http;
        $response->setHttpResponseCode(200);
        $response->headersSentThrowsException = false;

        // Set stub method return values
        $request->expects($this->any())
                ->method('getRequestUri')
                ->will($this->returnValue('/'));
        $request->expects($this->any())
                ->method('getMethod')
                ->will($this->returnValue('GET'));
        $request->expects($this->any())
                ->method('getServer')
                ->will($this->returnValue('PHPUnit'));
        $request->expects($this->any())
                ->method('getHeader')
                ->will($this->returnValue($clientHeader));

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
        $a = new Zend_Auth_Adapter_Http($use);
        $a->setBasicResolver($this->_basicResolver);
        $a->setDigestResolver($this->_digestResolver);

        // Send the authentication request
        $a->setRequest($request);
        $a->setResponse($response);
        $result = $a->authenticate();

        $return = array(
            'result'  => $result,
            'status'  => $response->getHttpResponseCode(),
            'headers' => $response->getHeaders()
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
        $timeout = ceil(time() / 300) * 300;
        $nonce   = md5($timeout . ':PHPUnit:Zend_Auth_Adapter_Http');
        $opaque  = md5('Opaque Data:Zend_Auth_Adapter_Http');
        $wwwauth = 'Digest '
                 . 'realm="' . $this->_digestConfig['realm'] . '", '
                 . 'domain="' . $this->_digestConfig['digest_domains'] . '", '
                 . 'nonce="' . $nonce . '", '
                 . 'opaque="' . $opaque . '", '
                 . 'algorithm="MD5", '
                 . 'qop="auth"';

        return $wwwauth;
    }

    /**
     * Constructs a client digest Authorization header
     *
     * @return string
     */
    protected function _digestReply($user, $pass)
    {
        $nc       = '00000001';
        $timeout  = ceil(time() / 300) * 300;
        $nonce    = md5($timeout . ':PHPUnit:Zend_Auth_Adapter_Http');
        $opaque   = md5('Opaque Data:Zend_Auth_Adapter_Http');
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
     * Checks for an expected 401 Unauthorized response
     *
     * @param  array  $data     Authentication results
     * @param  string $expected Expected Www-Authenticate header value
     * @return void
     */
    protected function _checkUnauthorized($data, $expected)
    {
        extract($data); // $result, $status, $headers

        // Make sure the result is false
        $this->assertType('Zend_Auth_Result', $result);
        $this->assertFalse($result->isValid());

        // Verify the status code and the presence of the challenge
        $this->assertEquals(401, $status);
        $this->assertEquals('Www-Authenticate', $headers[0]['name']);

        // Check to see if the expected challenge matches the actual
        $this->assertEquals($expected, $headers[0]['value']);
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
        $this->assertType('Zend_Auth_Result', $result);
        $this->assertTrue($result->isValid());

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
        $this->assertType('Zend_Auth_Result', $result);
        $this->assertFalse($result->isValid());

        // Make sure it set the right HTTP code
        $this->assertEquals(400, $status);
    }
}
