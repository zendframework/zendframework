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
 * @package    Zend_GData
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\GData;

use Zend\GData;
use Zend\Http\Client;
use Zend\Http\Client\Adapter\Test as AdapterTest;

/**
 * @category   Zend
 * @package    Zend_GData
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_Gdata_AuthSub
 */
class AuthSubTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Dummy token used during testing
     * @var string
     */
    protected $token = 'DQAAFPHOW7DCTN';

    public function testNormalGetAuthSubTokenUri()
    {
        $uri = GData\AuthSub::getAuthSubTokenUri(
                'http://www.example.com/foo.php', //next
                'http://www.google.com/calendar/feeds', //scope
                0, //secure
                1); //session

        // Note: the scope here is not encoded.  It should be encoded,
        // but the method getAuthSubTokenUri calls urldecode($scope).
        // This currently works (no reported bugs) as web browsers will
        // handle the encoding in most cases.
       $this->assertEquals('https://www.google.com/accounts/AuthSubRequest?next=http%3A%2F%2Fwww.example.com%2Ffoo.php&scope=http://www.google.com/calendar/feeds&secure=0&session=1', $uri);
    }

    public function testGetAuthSubTokenUriModifiedBase()
    {
        $uri = GData\AuthSub::getAuthSubTokenUri(
                'http://www.example.com/foo.php', //next
                'http://www.google.com/calendar/feeds', //scope
                0, //secure
                1, //session
                'http://www.otherauthservice.com/accounts/AuthSubRequest');

        // Note: the scope here is not encoded.  It should be encoded,
        // but the method getAuthSubTokenUri calls urldecode($scope).
        // This currently works (no reported bugs) as web browsers will
        // handle the encoding in most cases.
       $this->assertEquals('http://www.otherauthservice.com/accounts/AuthSubRequest?next=http%3A%2F%2Fwww.example.com%2Ffoo.php&scope=http://www.google.com/calendar/feeds&secure=0&session=1', $uri);
    }

    public function testSecureAuthSubSigning()
    {
        if (!extension_loaded('openssl')) {
            $this->markTestSkipped('The openssl extension is not available');
        } else {
            $c = new GData\HttpClient();
            $c->setAuthSubPrivateKeyFile("Zend/GData/_files/RsaKey.pem",
                                         null, true);
            $c->setAuthSubToken('abcdefg');
            $requestData = $c->filterHttpRequest('POST',
                                                 'http://www.example.com/feed',
                                                  array(),
                                                  'foo bar',
                                                  'text/plain');

            $authHeaderCheckPassed = false;
            $headers = $requestData['headers'];
            foreach ($headers as $headerName => $headerValue) {
                if (strtolower($headerName) == 'authorization') {
                    preg_match('/data="([^"]*)"/', $headerValue, $matches);
                    $dataToSign = $matches[1];
                    preg_match('/sig="([^"]*)"/', $headerValue, $matches);
                    $sig = $matches[1];
                    if (function_exists('openssl_verify')) {
                        $fp = fopen('Zend/GData/_files/RsaCert.pem', 'r', true);
                        $cert = '';
                        while (!feof($fp)) {
                            $cert .= fread($fp, 8192);
                        }
                        fclose($fp);
                        $pubkeyid = openssl_get_publickey($cert);
                        $verified = openssl_verify($dataToSign,
                                               base64_decode($sig), $pubkeyid);
                        $this->assertEquals(
                            1, $verified,
                            'The generated signature was unable ' .
                            'to be verified.');
                        $authHeaderCheckPassed = true;
                    }
                }
            }
            $this->assertEquals(true, $authHeaderCheckPassed,
                                'Auth header not found for sig verification.');
        }
    }

    public function testPrivateKeyNotFound()
    {
        $this->setExpectedException('Zend\GData\App\InvalidArgumentException');

        if (!extension_loaded('openssl')) {
            $this->markTestSkipped('The openssl extension is not available');
        } else {
            $c = new GData\HttpClient();
            $c->setAuthSubPrivateKeyFile("zendauthsubfilenotfound",  null, true);
        }
    }
    public function testAuthSubSessionTokenReceivesSuccessfulResult()
    {
        $adapter = new AdapterTest();
        $adapter->setResponse("HTTP/1.1 200 OK\r\n\r\nToken={$this->token}\r\nExpiration=20201004T123456Z");

        $client = new GData\HttpClient();
        $client->setUri('http://example.com/AuthSub');
        $client->setAdapter($adapter);

        $respToken = GData\AuthSub::getAuthSubSessionToken($this->token, $client);
        $this->assertEquals($this->token, $respToken);
    }

    /**
     * @expectedException Zend\GData\App\AuthException
     */
    public function testAuthSubSessionTokenCatchesFailedResult()
    {
        $adapter = new AdapterTest();
        $adapter->setResponse("HTTP/1.1 500 Internal Server Error\r\n\r\nInternal Server Error");

        $client = new GData\HttpClient();
        $client->setUri('http://example.com/AuthSub');
        $client->setAdapter($adapter);

        $newtok = GData\AuthSub::getAuthSubSessionToken($this->token, $client);
    }

    /**
     * @expectedException Zend\GData\App\HttpException
     */
    public function testAuthSubSessionTokenCatchesHttpClientException()
    {
        $adapter = new AdapterTest();
        $adapter->setNextRequestWillFail(true);

        $client = new GData\HttpClient();
        $client->setUri('http://example.com/AuthSub');
        $client->setAdapter($adapter);

        $newtok = GData\AuthSub::getAuthSubSessionToken($this->token, $client);
    }

    public function testAuthSubRevokeTokenReceivesSuccessfulResult()
    {
        $adapter = new AdapterTest();
        $adapter->setResponse("HTTP/1.1 200 OK");

        $client = new GData\HttpClient();
        $client->setUri('http://example.com/AuthSub');
        $client->setAdapter($adapter);

        $revoked = GData\AuthSub::AuthSubRevokeToken($this->token, $client);
        $this->assertTrue($revoked);
    }

    public function testAuthSubRevokeTokenCatchesFailedResult()
    {
        $adapter = new AdapterTest();
        $adapter->setResponse("HTTP/1.1 500 Not Successful");

        $client = new GData\HttpClient();
        $client->setUri('http://example.com/AuthSub');
        $client->setAdapter($adapter);

        $revoked = GData\AuthSub::AuthSubRevokeToken($this->token, $client);
        $this->assertFalse($revoked);
    }

    /**
     * @expectedException Zend\Gdata\App\HttpException
     */
    public function testAuthSubRevokeTokenCatchesHttpClientException()
    {
        $adapter = new AdapterTest();
        $adapter->setNextRequestWillFail(true);

        $client = new GData\HttpClient();
        $client->setUri('http://example.com/AuthSub');
        $client->setAdapter($adapter);

        $revoked = GData\AuthSub::AuthSubRevokeToken($this->token, $client);
    }

    public function testGetAuthSubTokenInfoReceivesSuccessfulResult()
    {
        $adapter = new AdapterTest();
        $adapter->setResponse("HTTP/1.1 200 OK

Target=http://example.com
Scope=http://example.com
Secure=false");

        $client = new GData\HttpClient();
        $client->setUri('http://example.com/AuthSub');
        $client->setAdapter($adapter);

        $respBody = GData\AuthSub::getAuthSubTokenInfo($this->token, $client);

        $this->assertContains("Target=http://example.com", $respBody);
        $this->assertContains("Scope=http://example.com", $respBody);
        $this->assertContains("Secure=false", $respBody);
    }

    /**
     * @expectedException Zend\Gdata\App\HttpException
     */
    public function testGetAuthSubTokenInfoCatchesHttpClientException()
    {
        $adapter = new AdapterTest();
        $adapter->setNextRequestWillFail(true);

        $client = new GData\HttpClient();
        $client->setUri('http://example.com/AuthSub');
        $client->setAdapter($adapter);

        $revoked = GData\AuthSub::getAuthSubTokenInfo($this->token, $client);
    }

    public function testGetHttpClientProvidesNewClientWhenNullPassed()
    {
        $client = GData\AuthSub::getHttpClient($this->token);
        $this->assertTrue($client instanceof GData\HttpClient );
        $this->assertEquals($this->token, $client->getAuthSubToken());
    }
}
