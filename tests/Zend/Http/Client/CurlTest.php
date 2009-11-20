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
 * @package    Zend_Http_Client
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Http_Client_CurlTest::main');
}

require_once dirname(__FILE__) . '/CommonHttpTests.php';

require_once 'Zend/Http/Client/Adapter/Curl.php';

/**
 * This Testsuite includes all Zend_Http_Client that require a working web
 * server to perform. It was designed to be extendable, so that several
 * test suites could be run against several servers, with different client
 * adapters and configurations.
 *
 * Note that $this->baseuri must point to a directory on a web server
 * containing all the files under the _files directory. You should symlink
 * or copy these files and set 'baseuri' properly.
 *
 * You can also set the proper constand in your test configuration file to
 * point to the right place.
 *
 * @category   Zend
 * @package    Zend_Http_Client
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Http
 * @group      Zend_Http_Client
 */
class Zend_Http_Client_CurlTest extends Zend_Http_Client_CommonHttpTests
{
    /**
     * Configuration array
     *
     * @var array
     */
    protected $config = array(
        'adapter'     => 'Zend_Http_Client_Adapter_Curl'
    );

    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

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

        $this->_adapter->setConfig($config);

        $hasConfig = $this->_adapter->getConfig();
        foreach($config as $k => $v) {
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
        require_once 'Zend/Config.php';

        $config = new Zend_Config(array(
            'timeout'  => 400,
            'nested'   => array(
                'item' => 'value',
            )
        ));

        $this->_adapter->setConfig($config);

        $hasConfig = $this->_adapter->getConfig();
        $this->assertEquals($config->timeout, $hasConfig['timeout']);
        $this->assertEquals($config->nested->item, $hasConfig['nested']['item']);
    }

    /**
     * Check that an exception is thrown when trying to set invalid config
     *
     * @expectedException Zend_Http_Client_Adapter_Exception
     * @dataProvider invalidConfigProvider
     */
    public function testSetConfigInvalidConfig($config)
    {
        $this->_adapter->setConfig($config);
    }

    /**
     * CURLOPT_CLOSEPOLICY never worked and returns false on setopt always:
     * @link http://de2.php.net/manual/en/function.curl-setopt.php#84277
     *
     * This should throw an exception.
     *
     * @expectedException Zend_Http_Exception
     */
    public function testSettingInvalidCurlOption()
    {
        $config = array(
            'adapter'     => 'Zend_Http_Client_Adapter_Curl',
            'curloptions' => array(CURLOPT_CLOSEPOLICY => true),
        );
        $this->client = new Zend_Http_Client($this->client->getUri(true), $config);

        $this->client->request('GET');
        $this->fail();
    }

    public function testRedirectWithGetOnly()
    {
        $this->client->setUri($this->baseuri . 'testRedirections.php');

        // Set some parameters
        $this->client->setParameterGet('swallow', 'african');

        // Request
        $res = $this->client->request('GET');

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
     * @expectedException Zend_Http_Client_Exception
     */
    public function testRedirectPostToGetWithCurlFollowLocationOptionLeadsToTimeout()
    {
        $adapter = new Zend_Http_Client_Adapter_Curl();
        $this->client->setAdapter($adapter);
        $adapter->setConfig(array('timeout' => 1, 'curloptions' => array(CURLOPT_FOLLOWLOCATION => true)));

        $this->client->setUri($this->baseuri . 'testRedirections.php');

        //  Set some parameters
        $this->client->setParameterGet('swallow', 'african');
        $this->client->setParameterPost('Camelot', 'A silly place');
        $this->client->request("POST");
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

        $this->client->setRawData($putFileContents);
        $this->client->request('PUT');
        $this->assertEquals($putFileContents, $this->client->getLastResponse()->getBody());
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

        $adapter = new Zend_Http_Client_Adapter_Curl();
        $this->client->setAdapter($adapter);
        $adapter->setConfig(array(
            'curloptions' => array(CURLOPT_INFILE => $putFileHandle, CURLOPT_INFILESIZE => $putFileSize)
        ));
        $this->client->request('PUT');
        $this->assertEquals(gzcompress($putFileContents), gzcompress($this->client->getLastResponse()->getBody()));
    }

    public function testWritingAndNotConnectedWithCurlHandleThrowsException()
    {
        $this->setExpectedException("Zend_Http_Client_Adapter_Exception", "Trying to write but we are not connected");

        $adapter = new Zend_Http_Client_Adapter_Curl();
        $adapter->write("GET", "someUri");
    }

    public function testSetConfigIsNotArray()
    {
        $this->setExpectedException("Zend_Http_Client_Adapter_Exception");

        $adapter = new Zend_Http_Client_Adapter_Curl();
        $adapter->setConfig("foo");
    }

    public function testSetCurlOptions()
    {
        $adapter = new Zend_Http_Client_Adapter_Curl();

        $adapter->setCurlOption('foo', 'bar')
                ->setCurlOption('bar', 'baz');

        $this->assertEquals(
            array('curloptions' => array('foo' => 'bar', 'bar' => 'baz')),
            $this->readAttribute($adapter, '_config')
        );
    }

    public function testWorkWithProxyConfiguration()
    {
        $adapter = new Zend_Http_Client_Adapter_Curl();
        $adapter->setConfig(array(
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
            $expected, $this->readAttribute($adapter, '_config')
        );
    }

    /**
     * @group ZF-7040
     */
    public function testGetCurlHandle()
    {
        $adapter = new Zend_Http_Client_Adapter_Curl();
        $adapter->setConfig(array('timeout' => 2, 'maxredirects' => 1));
        $adapter->connect("http://framework.zend.com");

        $this->assertTrue(is_resource($adapter->getHandle()));
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Http_Client_CurlTest::main') {
    Zend_Http_Client_CurlTest::main();
}
