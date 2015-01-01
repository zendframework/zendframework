<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Soap\Client;

use PHPUnit_Framework_TestCase;
use Zend\Soap\Client\Common;
use Zend\Soap\Client\DotNet as DotNetClient;

require_once __DIR__ . '/../TestAsset/call_user_func.php';

/**
 * .NET SOAP client tester.
 *
 * @group      Zend_Soap
 */
class DotNetTest extends PHPUnit_Framework_TestCase
{
    /**
     * .NET SOAP client.
     *
     * @var \Zend\Soap\Client\DotNet
     */
    private $client = null;

    /**
     * cURL client.
     *
     * @var \Zend\Http\Client\Adapter\Curl
     */
    private $curlClient = null;

    /**
     * Sets up the fixture.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->client = new DotNetClient(null, array('location' => 'http://unithost/test',
                                                    'uri'      => 'http://unithost/test'));
    }

    /**
     * Tests that a default cURL client is used if none is injected.
     *
     * @return void
     * @covers Zend\Soap\Client\DotNet::getCurlClient
     */
    public function testADefaultCurlClientIsUsedIfNoneIsInjected()
    {
        $this->assertInstanceOf('Zend\Http\Client\Adapter\Curl', $this->client->getCurlClient());
    }

    /**
     * Tests that the cURL client can be injected.
     *
     * @return void
     * @covers Zend\Soap\Client\DotNet::getCurlClient
     * @covers Zend\Soap\Client\DotNet::setCurlClient
     */
    public function testCurlClientCanBeInjected()
    {
        $this->mockCurlClient();
        $this->assertSame($this->curlClient, $this->client->getCurlClient());
    }

    /**
     * Tests that a cURL client request is done when using NTLM
     * authentication.
     *
     * @return void
     * @covers Zend\Soap\Client\DotNet::_doRequest
     */
    public function testCurlClientRequestIsDoneWhenUsingNtlmAuthentication()
    {
        $this->mockNtlmRequest();
        $this->assertInstanceOf('stdClass', $this->client->TestMethod());
    }

    /**
     * Tests that the default SOAP client request is done when not using NTLM authentication.
     *
     * @return void
     * @covers Zend\Soap\Client\DotNet::_doRequest
     */
    public function testDefaultSoapClientRequestIsDoneWhenNotUsingNtlmAuthentication()
    {
        $unitTest = $this;
        $soapClient = new Common(
            function (Common $client, $request, $location, $action, $version, $oneWay = null) use ($unitTest) {
                $unitTest->assertEquals('http://unit/test#TestMethod', $action);
                $result = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">'
                    . '<s:Body>';

                $result .= '<TestMethodResponse xmlns="http://unit/test">'
                    . '<TestMethodResult>'
                    . '<TestMethodResult><dummy></dummy></TestMethodResult>'
                    . '</TestMethodResult>'
                    . '</TestMethodResponse>';

                $result .= '</s:Body>'
                    . '</s:Envelope>';

                return $result;
            },
            null,
            array(
                'location' => 'http://unit/test',
                'uri'      => 'http://unit/test'
            )
        );
        $this->assertAttributeEquals(false, 'useNtlm', $this->client);
        $this->client->setOptions(array('authentication' => 'ntlm',
            'login'          => 'username',
            'password'       => 'testpass'));
        $this->client->setSoapClient($soapClient);
        $this->assertInstanceOf('stdClass', $this->client->TestMethod());
    }

    /**
     * Tests that the last request headers can be fetched correctly.
     *
     * @return void
     * @covers Zend\Soap\Client\DotNet::getLastRequestHeaders
     */
    public function testLastRequestHeadersCanBeFetchedCorrectly()
    {
        $expectedHeaders = "Content-Type: text/xml; charset=utf-8\r\n"
                         . "Method: POST\r\n"
                         . "SOAPAction: \"http://unithost/test#TestMethod\"\r\n"
                         . "User-Agent: PHP-SOAP-CURL\r\n";

        $this->mockNtlmRequest();
        $this->client->TestMethod();

        $this->assertSame($expectedHeaders, $this->client->getLastRequestHeaders());
    }

    /**
     * Tests that the last response headers can be fetched correctly.
     *
     * @return void
     * @covers Zend\Soap\Client\DotNet::getLastResponseHeaders
     */
    public function testLastResponseHeadersCanBeFetchedCorrectly()
    {
        $expectedHeaders = "Cache-Control: private\r\n"
                         . "Content-Type: text/xml; charset=utf-8\r\n";

        $this->mockNtlmRequest();
        $this->client->TestMethod();

        $this->assertSame($expectedHeaders, $this->client->getLastResponseHeaders());
    }

    /**
     * Mocks the cURL client.
     *
     * @return void
     */
    private function mockCurlClient()
    {
        $this->curlClient = $this->getMock('Zend\Http\Client\Adapter\Curl',
                                           array('close', 'connect', 'read', 'write'));
        $this->client->setCurlClient($this->curlClient);
    }

    /**
     * Mocks an NTLM SOAP request.
     *
     * @return void
     */
    private function mockNtlmRequest()
    {
        $headers  = array('Content-Type' => 'text/xml; charset=utf-8',
                          'Method'       => 'POST',
                          'SOAPAction'   => '"http://unithost/test#TestMethod"',
                          'User-Agent'   => 'PHP-SOAP-CURL');
        $response = "HTTP/1.1 200 OK\n"
                  . "Cache-Control: private\n"
                  . "Content-Type: text/xml; charset=utf-8\n"
                  . "\n\n"
                  . '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">'
                  . '<s:Body>'
                  . '<TestMethodResponse xmlns="http://unit/test">'
                  . '<TestMethodResult>'
                  . '<TestMethodResult><dummy></dummy></TestMethodResult>'
                  . '</TestMethodResult>'
                  . '</TestMethodResponse>'
                  . '</s:Body>'
                  . '</s:Envelope>';

        $this->mockCurlClient();

        $this->curlClient->expects($this->once())
                         ->method('connect')
                         ->with('unithost', 80);
        $this->curlClient->expects($this->once())
                         ->method('read')
                         ->will($this->returnValue($response));
        $this->curlClient->expects($this->any())
                         ->method('write')
                         ->with('POST', $this->isInstanceOf('Zend\Uri\Http'), 1.1, $headers, $this->stringContains('<SOAP-ENV'));

        $this->client->setOptions(array('authentication' => 'ntlm',
                                        'login'          => 'username',
                                        'password'       => 'testpass'));
    }
}
