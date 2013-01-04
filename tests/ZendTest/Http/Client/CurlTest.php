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

use Zend\Config\Config;
use Zend\Http\Client\Adapter;

/**
 * This Testsuite includes all Zend_Http_Client that require a working web
 * server to perform. It was designed to be extendable, so that several
 * test suites could be run against several servers, with different client
 * adapters and configurations.
 *
 * Note that $this->baseuri must point to a directory on a web server
 * containing all the files under the files directory. You should symlink
 * or copy these files and set 'baseuri' properly.
 *
 * You can also set the proper constand in your test configuration file to
 * point to the right place.
 *
 * @category   Zend
 * @package    Zend_Http_Client
 * @subpackage UnitTests
 * @group      Zend_Http
 * @group      Zend_Http_Client
 */
class CurlTest extends CommonHttpTests
{
    /**
     * Configuration array
     *
     * @var array
     */
    protected $config = array(
        'adapter'     => 'Zend\Http\Client\Adapter\Curl',
        'curloptions' => array(
            CURLOPT_INFILESIZE => 102400000,
        ),
    );

    protected function setUp()
    {
        if (!extension_loaded('curl')) {
            $this->markTestSkipped('cURL is not installed, marking all Http Client Curl Adapter tests skipped.');
        }
        parent::setUp();
    }

    /**
     * Off-line common adapter tests
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

        $this->_adapter->setOptions($config);

        $hasConfig = $this->_adapter->getConfig();
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

        $config = new Config(array(
            'timeout'  => 400,
            'nested'   => array(
                'item' => 'value',
            )
        ));

        $this->_adapter->setOptions($config);

        $hasConfig = $this->_adapter->getConfig();
        $this->assertEquals($config->timeout, $hasConfig['timeout']);
        $this->assertEquals($config->nested->item, $hasConfig['nested']['item']);
    }

    /**
     * Check that an exception is thrown when trying to set invalid config
     *
     * @dataProvider invalidConfigProvider
     */
    public function testSetConfigInvalidConfig($config)
    {
        $this->setExpectedException(
            'Zend\Http\Client\Adapter\Exception\InvalidArgumentException',
            'Array or Traversable object expected');

        $this->_adapter->setOptions($config);
    }

    /**
     * CURLOPT_CLOSEPOLICY never worked and returns false on setopt always:
     * @link http://de2.php.net/manual/en/function.curl-setopt.php#84277
     *
     * This should throw an exception.
     */
    public function testSettingInvalidCurlOption()
    {
        $config = array(
            'adapter'     => 'Zend\Http\Client\Adapter\Curl',
            'curloptions' => array(CURLOPT_CLOSEPOLICY => true),
        );
        $this->client = new \Zend\Http\Client($this->client->getUri(true), $config);

        $this->setExpectedException(
            'Zend\Http\Client\Adapter\Exception\RuntimeException',
            'Unknown or erroreous cURL option'
            );
        $this->client->send();
    }

    public function testRedirectWithGetOnly()
    {
        $this->client->setUri($this->baseuri . 'testRedirections.php');

        // Set some parameters
        $this->client->setParameterGet(array('swallow', 'african'));

        // Request
        $res = $this->client->send();

        $this->assertEquals(3, $this->client->getRedirectionsCount(), 'Redirection counter is not as expected');

        // Make sure the body does *not* contain the set parameters
        $this->assertNotContains('swallow', $res->getBody());
        $this->assertNotContains('Camelot', $res->getBody());
    }

    /**
     * This is a specific problem of the request type: If you let cURL handle redirects internally
     * but start with a POST request that sends data then the location ping-pong will lead to an
     * Content-Length: x\r\n GET request of the client that the server won't answer because no content is sent.
     *
     * Set CURLOPT_FOLLOWLOCATION = false for this type of request and let the Zend_Http_Client handle redirects
     * in his own loop.
     *
     */
    public function testRedirectPostToGetWithCurlFollowLocationOptionLeadsToTimeout()
    {
        $adapter = new Adapter\Curl();
        $this->client->setAdapter($adapter);
        $adapter->setOptions(array(
            'curloptions' => array(
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 1,
            ))
        );

        $this->client->setUri($this->baseuri . 'testRedirections.php');

        //  Set some parameters
        $this->client->setParameterGet(array ('swallow' => 'african'));
        $this->client->setParameterPost(array ('Camelot' => 'A silly place'));
        $this->client->setMethod('POST');
        $this->setExpectedException(
            'Zend\Http\Client\Adapter\Exception\RuntimeException',
            'Error in cURL request: Operation timed out after 1000 milliseconds with 0 bytes received');
        $this->client->send();
    }

    /**
     * @group ZF-3758
     * @link http://framework.zend.com/issues/browse/ZF-3758
     */
    public function testPutFileContentWithHttpClient()
    {
        // Method 1: Using the binary string of a file to PUT
        $this->client->setUri($this->baseuri . 'testRawPostData.php');
        $putFileContents = file_get_contents(dirname(realpath(__FILE__)) . DIRECTORY_SEPARATOR .
            '_files' . DIRECTORY_SEPARATOR . 'staticFile.jpg');

        $this->client->setRawBody($putFileContents);
        $this->client->setMethod('PUT');
        $this->client->send();
        $this->assertEquals($putFileContents, $this->client->getResponse()->getBody());
    }

    /**
     * @group ZF-3758
     * @link http://framework.zend.com/issues/browse/ZF-3758
     */
    public function testPutFileHandleWithHttpClient()
    {
        $this->client->setUri($this->baseuri . 'testRawPostData.php');
        $putFileContents = file_get_contents(dirname(realpath(__FILE__)) . DIRECTORY_SEPARATOR .
            '_files' . DIRECTORY_SEPARATOR . 'staticFile.jpg');

        // Method 2: Using a File-Handle to the file to PUT the data
        $putFilePath = dirname(realpath(__FILE__)) . DIRECTORY_SEPARATOR .
            '_files' . DIRECTORY_SEPARATOR . 'staticFile.jpg';
        $putFileHandle = fopen($putFilePath, "r");
        $putFileSize = filesize($putFilePath);

        $adapter = new Adapter\Curl();
        $this->client->setAdapter($adapter);
        $adapter->setOptions(array(
            'curloptions' => array(CURLOPT_INFILE => $putFileHandle, CURLOPT_INFILESIZE => $putFileSize)
        ));
        $this->client->setMethod('PUT');
        $this->client->send();
        $this->assertEquals(gzcompress($putFileContents), gzcompress($this->client->getResponse()->getBody()));
    }

    public function testWritingAndNotConnectedWithCurlHandleThrowsException()
    {
        $adapter = new Adapter\Curl();
        $this->setExpectedException('Zend\Http\Client\Adapter\Exception\RuntimeException',
                                    'Trying to write but we are not connected');
        $adapter->write("GET", "someUri");
    }

    public function testSetConfigIsNotArray()
    {
        $adapter = new Adapter\Curl();
        $this->setExpectedException('Zend\Http\Client\Adapter\Exception\InvalidArgumentException');
        $adapter->setOptions("foo");
    }

    public function testSetCurlOptions()
    {
        $adapter = new Adapter\Curl();

        $adapter->setCurlOption('foo', 'bar')
                ->setCurlOption('bar', 'baz');

        $this->assertEquals(
            array('curloptions' => array('foo' => 'bar', 'bar' => 'baz')),
            $this->readAttribute($adapter, 'config')
        );
    }

    public function testWorkWithProxyConfiguration()
    {
        $adapter = new Adapter\Curl();
        $adapter->setOptions(array(
            'proxy_host' => 'localhost',
            'proxy_port' => 80,
            'proxy_user' => 'foo',
            'proxy_pass' => 'baz',
        ));

        $expected = array(
            'curloptions' => array(
                CURLOPT_PROXYUSERPWD => 'foo:baz',
                CURLOPT_PROXY => 'localhost',
                CURLOPT_PROXYPORT => 80,
            ),
        );

        $this->assertEquals(
            $expected, $this->readAttribute($adapter, 'config')
        );
    }

    /**
     * @group ZF-7040
     */
    public function testGetCurlHandle()
    {
        $adapter = new Adapter\Curl();
        $adapter->setOptions(array('timeout' => 2, 'maxredirects' => 1));
        $adapter->connect("http://framework.zend.com");

        $this->assertTrue(is_resource($adapter->getHandle()));
    }

    /**
     * @group ZF-9857
     */
    public function testHeadRequest()
    {
        $this->client->setUri($this->baseuri . 'testRawPostData.php');
        $adapter = new Adapter\Curl();
        $this->client->setAdapter($adapter);
        $this->client->setMethod('HEAD');
        $this->client->send();
        $this->assertEquals('', $this->client->getResponse()->getBody());
    }

    public function testAuthorizeHeader()
    {
        // We just need someone to talk to
        $this->client->setUri($this->baseuri. 'testHttpAuth.php');
        $adapter = new Adapter\Curl();
        $this->client->setAdapter($adapter);

        $uid = 'alice';
        $pwd = 'secret';

        $hash   = base64_encode($uid . ':' . $pwd);
        $header = 'Authorization: Basic ' . $hash;

        $this->client->setAuth($uid, $pwd);
        $res = $this->client->send();

        $curlInfo = curl_getinfo($adapter->getHandle());
        $this->assertArrayHasKey('request_header', $curlInfo, 'Expecting request_header in curl_getinfo() return value');

        $this->assertContains($header, $curlInfo['request_header'], 'Expecting valid basic authorization header');
    }
}
