<?php

// Read local configuration
if (! defined('TESTS_ZEND_HTTP_CLIENT_BASEURI') &&
    is_readable('TestConfiguration.php')) {

    require_once 'TestConfiguration.php';
}

require_once realpath(dirname(__FILE__) . '/../../../') . '/TestHelper.php';

require_once 'Zend/Http/Client.php';

require_once 'Zend/Uri/Http.php';


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
 * You can also set the proper constant in your test configuration file to
 * point to the right place.
 *
 * @category   Zend
 * @package    Zend_Http_Client
 * @subpackage UnitTests
 * @version    $Id$
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Http_Client_CommonHttpTests extends PHPUnit_Framework_TestCase
{
    /**
     * The bast URI for this test, containing all files in the _files directory
     * Should be set in TestConfiguration.php or TestConfiguration.php.dist
     *
     * @var string
     */
    protected $baseuri;

    /**
     * Common HTTP client
     *
     * @var Zend_Http_Client
     */
    protected $client = null;

    /**
     * Configuration array
     *
     * @var array
     */
    protected $config = array(
        'adapter'     => 'Zend_Http_Client_Adapter_Socket'
    );

    /**
     * Set up the test case
     *
     */
    protected function setUp()
    {
        if (defined('TESTS_ZEND_HTTP_CLIENT_BASEURI') &&
            Zend_Uri_Http::check(TESTS_ZEND_HTTP_CLIENT_BASEURI)) {

            $this->baseuri = TESTS_ZEND_HTTP_CLIENT_BASEURI;
            if (substr($this->baseuri, -1) != '/') $this->baseuri .= '/';
            
            $name = $this->getName();
            if (($pos = strpos($name, ' ')) !== false) {
                $name = substr($name, 0, $pos);
            }
            
            $uri = $this->baseuri . $name . '.php'; 
            $this->client = new Zend_Http_Client($uri, $this->config);

        } else {
            // Skip tests
            $this->markTestSkipped("Zend_Http_Client dynamic tests are not enabled in TestConfiguration.php");
        }
    }
    
    /**
     * Clean up the test environment
     * 
     */
    protected function tearDown()
    {
        $this->client = null;
    }

    /**
     * Simple request tests
     */

    /**
     * Test simple requests
     *
     */
    public function testSimpleRequests()
    {
        $methods = array('GET', 'POST', 'OPTIONS', 'PUT', 'DELETE');

        foreach ($methods as $method) {
            $res = $this->client->request($method);
            $this->assertEquals('Success', $res->getBody(), "HTTP {$method} request failed.");
        }
    }

    /**
     * Test we can get the last request as string
     *
     */
    public function testGetLastRequest()
    {
        $this->client->setUri($this->baseuri . 'testHeaders.php');
        $this->client->setParameterGet('someinput', 'somevalue');
        $this->client->setHeaders(array(
            'X-Powered-By' => 'My Glorious Golden Ass',
        ));

        $res = $this->client->request(Zend_Http_Client::TRACE);
        if ($res->getStatus() == 405) {
            $this->markTestSkipped("Server does not allow the TRACE method");
        }

        $this->assertEquals($this->client->getLastRequest(), $res->getBody(), 'Response body should be exactly like the last request');
    }

    /**
     * GET and POST parameters tests
     */

    /**
     * Test we can properly send GET parameters
     *
     * @dataProvider parameterArrayProvider
     */
    public function testGetData($params)
    {
        $this->client->setUri($this->client->getUri(true) . '?name=Arthur');

        $this->client->setParameterGet($params);
        $res = $this->client->request('GET');
        $this->assertEquals(serialize(array_merge(array('name' => 'Arthur'), $params)), $res->getBody());
    }

    /**
     * Test we can properly send POST parameters with
     * application/x-www-form-urlencoded content type
     *
     * @dataProvider parameterArrayProvider
     */
    public function testPostDataUrlEncoded($params)
    {
        $this->client->setUri($this->baseuri . 'testPostData.php');
        $this->client->setEncType(Zend_Http_Client::ENC_URLENCODED);
        $this->client->setParameterPost($params);
        $res = $this->client->request('POST');
        $this->assertEquals(serialize($params), $res->getBody(), "POST data integrity test failed");
    }

    /**
     * Test we can properly send POST parameters with
     * multipart/form-data content type
     *
     * @dataProvider parameterArrayProvider
     */
    public function testPostDataMultipart($params)
    {
        $this->client->setUri($this->baseuri . 'testPostData.php');
        $this->client->setEncType(Zend_Http_Client::ENC_FORMDATA);
        $this->client->setParameterPost($params);
        $res = $this->client->request('POST');
        $this->assertEquals(serialize($params), $res->getBody(), "POST data integrity test failed");
    }

    /**
     * Test using raw HTTP POST data
     *
     */
    public function testRawPostData()
    {
        $data = "Chuck Norris never wet his bed as a child. The bed wet itself out of fear.";

        $res = $this->client->setRawData($data, 'text/html')->request('POST');
        $this->assertEquals($data, $res->getBody(), 'Response body does not contain the expected data');
    }

    /**
     * Make sure we can reset the parameters between consecutive requests
     *
     */
    public function testResetParameters()
    {
        $params = array(
            'quest' => 'To seek the holy grail',
            'YourMother' => 'Was a hamster',
            'specialChars' => '<>$+ &?=[]^%',
            'array' => array('firstItem', 'secondItem', '3rdItem')
        );

        $this->client->setParameterPost($params);
        $this->client->setParameterGet($params);

        $res = $this->client->request('POST');

        $this->assertContains(serialize($params) . "\n" . serialize($params),
            $res->getBody(), "returned body does not contain all GET and POST parameters (it should!)");

        $this->client->resetParameters();
        $res = $this->client->request('POST');

        $this->assertNotContains(serialize($params), $res->getBody(),
            "returned body contains GET or POST parameters (it shouldn't!)");
    }

    /**
     * Test parameters get reset when we unset them
     *
     */
    public function testParameterUnset()
    {
        $this->client->setUri($this->baseuri . 'testResetParameters.php');

        $gparams = array (
            'cheese' => 'camambert',
            'beer'   => 'jever pilnsen',
        );

        $pparams = array (
            'from' => 'bob',
            'to'   => 'alice'
        );

        $this->client->setParameterGet($gparams)->setParameterPost($pparams);

        // Remove some parameters
        $this->client->setParameterGet('cheese', null)->setParameterPost('to', null);
        $res = $this->client->request('POST');

        $this->assertNotContains('cheese', $res->getBody(), 'The "cheese" GET parameter was expected to be unset');
        $this->assertNotContains('alice', $res->getBody(), 'The "to" POST parameter was expected to be unset');
    }

    /**
     * Header Tests
     */

    /**
     * Make sure we can set a single header
     *
     */
    public function testHeadersSingle()
    {
        $this->client->setUri($this->baseuri . 'testHeaders.php');

        $headers = array(
            'Accept-encoding' => 'gzip,deflate',
            'X-baz' => 'Foo',
            'X-powered-by' => 'A large wooden badger'
        );

        foreach ($headers as $key => $val) {
            $this->client->setHeaders($key, $val);
        }

        $acceptHeader = "Accept: text/xml,text/html,*/*";
        $this->client->setHeaders($acceptHeader);

        $res = $this->client->request('TRACE');
        if ($res->getStatus() == 405) {
            $this->markTestSkipped("Server does not allow the TRACE method");
        }
        
        $body = strtolower($res->getBody());

        foreach ($headers as $key => $val)
            $this->assertContains(strtolower("$key: $val"), $body);

        $this->assertContains(strtolower($acceptHeader), $body);
    }

    /**
     * Test we can set an array of headers
     *
     */
    public function testHeadersArray()
    {
        $this->client->setUri($this->baseuri . 'testHeaders.php');

        $headers = array(
            'Accept-encoding' => 'gzip,deflate',
            'X-baz' => 'Foo',
            'X-powered-by' => 'A large wooden badger',
            'Accept: text/xml,text/html,*/*'
        );

        $this->client->setHeaders($headers);

        $res = $this->client->request('TRACE');
        if ($res->getStatus() == 405) {
            $this->markTestSkipped("Server does not allow the TRACE method");
        }
        
        $body = strtolower($res->getBody());

        foreach ($headers as $key => $val) {
            if (is_string($key)) {
                $this->assertContains(strtolower("$key: $val"), $body);
            } else {
                $this->assertContains(strtolower($val), $body);
            }
        }
     }

     /**
      * Test we can set a set of values for one header
      *
      */
     public function testMultipleHeader()
     {
         $this->client->setUri($this->baseuri . 'testHeaders.php');
        $headers = array(
            'Accept-encoding' => 'gzip,deflate',
            'X-baz' => 'Foo',
            'X-powered-by' => array(
                'A large wooden badger',
                'My Shiny Metal Ass',
                'Dark Matter'
            ),
            'Cookie' => array(
                'foo=bar',
                'baz=waka'
            )
        );

        $this->client->setHeaders($headers);
        $res = $this->client->request('TRACE');
        if ($res->getStatus() == 405) {
            $this->markTestSkipped("Server does not allow the TRACE method");
        }
        $body = strtolower($res->getBody());

        foreach ($headers as $key => $val) {
            if (is_array($val))
                $val = implode(', ', $val);

            $this->assertContains(strtolower("$key: $val"), $body);
        }
     }

     /**
      * Redirection tests
      */

     /**
      * Test the client properly redirects in default mode
      *
      */
    public function testRedirectDefault()
    {
        $this->client->setUri($this->baseuri . 'testRedirections.php');

        // Set some parameters
        $this->client->setParameterGet('swallow', 'african');
        $this->client->setParameterPost('Camelot', 'A silly place');

        // Request
        $res = $this->client->request('POST');

        $this->assertEquals(3, $this->client->getRedirectionsCount(), 'Redirection counter is not as expected');

        // Make sure the body does *not* contain the set parameters
        $this->assertNotContains('swallow', $res->getBody());
        $this->assertNotContains('Camelot', $res->getBody());
    }

    /**
     * Make sure the client properly redirects in strict mode
     *
     */
    public function testRedirectStrict()
    {
        $this->client->setUri($this->baseuri . 'testRedirections.php');

        // Set some parameters
        $this->client->setParameterGet('swallow', 'african');
        $this->client->setParameterPost('Camelot', 'A silly place');

        // Set strict redirections
        $this->client->setConfig(array('strictredirects' => true));

        // Request
        $res = $this->client->request('POST');

        $this->assertEquals(3, $this->client->getRedirectionsCount(), 'Redirection counter is not as expected');

        // Make sure the body *does* contain the set parameters
        $this->assertContains('swallow', $res->getBody());
        $this->assertContains('Camelot', $res->getBody());
    }

    /**
     * Make sure redirections stop when limit is exceeded
     *
     */
    public function testMaxRedirectsExceeded()
    {
        $this->client->setUri($this->baseuri . 'testRedirections.php');

        // Set some parameters
        $this->client->setParameterGet('swallow', 'african');
        $this->client->setParameterPost('Camelot', 'A silly place');

        // Set lower max redirections
        // Try with strict redirections first
        $this->client->setConfig(array('strictredirects' => true, 'maxredirects' => 2));

        $res = $this->client->request('POST');
        $this->assertTrue($res->isRedirect(),
            "Last response was not a redirection as expected. Response code: {$res->getStatus()}. Redirections counter: {$this->client->getRedirectionsCount()} (when strict redirects are on)");

        // Then try with normal redirections
        $this->client->setParameterGet('redirection', '0');
        $this->client->setConfig(array('strictredirects' => false));
        $res = $this->client->request('POST');
        $this->assertTrue($res->isRedirect(),
            "Last response was not a redirection as expected. Response code: {$res->getStatus()}. Redirections counter: {$this->client->getRedirectionsCount()} (when strict redirects are off)");
    }

    /**
     * Test we can properly redirect to an absolute path (not full URI)
     *
     */
    public function testAbsolutePathRedirect()
    {
        $this->client->setUri($this->baseuri . 'testRelativeRedirections.php');
        $this->client->setParameterGet('redirect', 'abpath');
        $this->client->setConfig(array('maxredirects' => 1));

        // Get the host and port part of our baseuri
        $uri = $this->client->getUri()->getScheme() . '://' . $this->client->getUri()->getHost() . ':' .
            $this->client->getUri()->getPort();

        $res = $this->client->request('GET');

        $this->assertEquals("{$uri}/path/to/fake/file.ext?redirect=abpath", $this->client->getUri(true),
            "The new location is not as expected: {$this->client->getUri(true)}");
    }

    /**
     * Test we can properly redirect to a relative path
     *
     */
    public function testRelativePathRedirect()
    {
        $this->client->setUri($this->baseuri . 'testRelativeRedirections.php');
        $this->client->setParameterGet('redirect', 'relpath');
        $this->client->setConfig(array('maxredirects' => 1));

        // Set the new expected URI
        $uri = clone $this->client->getUri();
        $uri->setPath(dirname($uri->getPath()) . '/path/to/fake/file.ext');
        $uri = $uri->__toString();

        $res = $this->client->request('GET');

        $this->assertEquals("{$uri}?redirect=relpath", $this->client->getUri(true),
            "The new location is not as expected: {$this->client->getUri(true)}");
    }

    /**
     * HTTP Authentication Tests
     *
     */

    /**
     * Test we can properly use Basic HTTP authentication
     *
     */
    public function testHttpAuthBasic()
    {
        $this->client->setUri($this->baseuri. 'testHttpAuth.php');
        $this->client->setParameterGet(array(
            'user'   => 'alice',
            'pass'   => 'secret',
            'method' => 'Basic'
        ));

        // First - fail password
        $this->client->setAuth('alice', 'wrong');
        $res = $this->client->request();
        $this->assertEquals(401, $res->getStatus(), 'Expected HTTP 401 response was not recieved');

        // Now use good password
        $this->client->setAuth('alice', 'secret');
        $res = $this->client->request();
        $this->assertEquals(200, $res->getStatus(), 'Expected HTTP 200 response was not recieved');
    }

    /**
     * Test we can unset HTTP authentication
     *
     */
    public function testCancelAuth()
    {
        $this->client->setUri($this->baseuri. 'testHttpAuth.php');

        // Set auth and cancel it
        $this->client->setAuth('alice', 'secret');
        $this->client->setAuth(false);
        $res = $this->client->request();

        $this->assertEquals(401, $res->getStatus(), 'Expected HTTP 401 response was not recieved');
        $this->assertNotContains('alice', $res->getBody(), "Body contains the user name, but it shouldn't");
        $this->assertNotContains('secret', $res->getBody(), "Body contains the password, but it shouldn't");
    }

    /**
     * Cookie and CookieJar Tests
     *
     */

    /**
     * Test we can set string cookies with no jar
     *
     */
    public function testCookiesStringNoJar()
    {
        $this->client->setUri($this->baseuri. 'testCookies.php');

        $cookies = array(
            'name'   => 'value',
            'cookie' => 'crumble'
        );

        foreach ($cookies as $k => $v) {
            $this->client->setCookie($k, $v);
        }

        $res = $this->client->request();

        $this->assertEquals($res->getBody(), serialize($cookies), 'Response body does not contain the expected cookies');
    }

    /**
     * Make sure we can set object cookies with no jar
     *
     */
    public function testSetCookieObjectNoJar()
    {
        $this->client->setUri($this->baseuri. 'testCookies.php');
        $refuri = $this->client->getUri();

        $cookies = array(
            Zend_Http_Cookie::fromString('chocolate=chips', $refuri),
            Zend_Http_Cookie::fromString('crumble=apple', $refuri)
        );

        $strcookies = array();
        foreach ($cookies as $c) {
            $this->client->setCookie($c);
            $strcookies[$c->getName()] = $c->getValue();
        }

        $res = $this->client->request();
        $this->assertEquals($res->getBody(), serialize($strcookies), 'Response body does not contain the expected cookies');
    }

    /**
     * Make sure we can set an array of object cookies
     *
     */
    public function testSetCookieObjectArray()
    {
        $this->client->setUri($this->baseuri. 'testCookies.php');
        $refuri = $this->client->getUri();

        $cookies = array(
            Zend_Http_Cookie::fromString('chocolate=chips', $refuri),
            Zend_Http_Cookie::fromString('crumble=apple', $refuri),
            Zend_Http_Cookie::fromString('another=cookie', $refuri)
        );

        $this->client->setCookie($cookies);

        $strcookies = array();
        foreach ($cookies as $c) {
            $strcookies[$c->getName()] = $c->getValue();
        }

        $res = $this->client->request();
        $this->assertEquals($res->getBody(), serialize($strcookies), 'Response body does not contain the expected cookies');
    }

    /**
     * Make sure we can set an array of string cookies
     *
     */
    public function testSetCookieStringArray()
    {
        $this->client->setUri($this->baseuri. 'testCookies.php');

        $cookies = array(
            'chocolate' => 'chips',
            'crumble'   => 'apple',
            'another'   => 'cookie'
        );

        $this->client->setCookie($cookies);

        $res = $this->client->request();
        $this->assertEquals($res->getBody(), serialize($cookies), 'Response body does not contain the expected cookies');
    }

    /**
     * Make sure we can set cookie objects with a jar
     *
     */
    public function testSetCookieObjectJar()
    {
        $this->client->setUri($this->baseuri. 'testCookies.php');
        $this->client->setCookieJar();
        $refuri = $this->client->getUri();

        $cookies = array(
            Zend_Http_Cookie::fromString('chocolate=chips', $refuri),
            Zend_Http_Cookie::fromString('crumble=apple', $refuri)
        );

        $strcookies = array();
        foreach ($cookies as $c) {
            $this->client->setCookie($c);
            $strcookies[$c->getName()] = $c->getValue();
        }

        $res = $this->client->request();
        $this->assertEquals($res->getBody(), serialize($strcookies), 'Response body does not contain the expected cookies');
    }

    /**
     * File Upload Tests
     *
     */

    /**
     * Test we can upload raw data as a file
     *
     */
    public function testUploadRawData()
    {
        $this->client->setUri($this->baseuri. 'testUploads.php');

        $rawdata = file_get_contents(__FILE__);
        $this->client->setFileUpload('myfile.txt', 'uploadfile', $rawdata, 'text/plain');
        $res = $this->client->request('POST');

        $body = 'uploadfile myfile.txt text/plain ' . strlen($rawdata) . "\n";
        $this->assertEquals($body, $res->getBody(), 'Response body does not include expected upload parameters');
    }

    /**
     * Test we can upload an existing file
     *
     */
    public function testUploadLocalFile()
    {
        $this->client->setUri($this->baseuri. 'testUploads.php');
        $this->client->setFileUpload(__FILE__, 'uploadfile', null, 'text/x-foo-bar');
        $res = $this->client->request('POST');

        $size = filesize(__FILE__);

        $body = "uploadfile " . basename(__FILE__) . " text/x-foo-bar $size\n";
        $this->assertEquals($body, $res->getBody(), 'Response body does not include expected upload parameters');
    }

    public function testUploadLocalDetectMime()
    {
        $detect = null;
        if (function_exists('finfo_file')) {
            $f = @finfo_open(FILEINFO_MIME);
            if ($f) $detect = 'finfo';

        } elseif (function_exists('mime_content_type')) {
            if (mime_content_type(__FILE__)) {
                $detect = 'mime_magic';
            }
        }

        if (! $detect) {
            $this->markTestSkipped('No MIME type detection capability (fileinfo or mime_magic extensions) is available');
        }

        $file = dirname(realpath(__FILE__)) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'staticFile.jpg';

        $this->client->setUri($this->baseuri. 'testUploads.php');
        $this->client->setFileUpload($file, 'uploadfile');
        $res = $this->client->request('POST');

        $size = filesize($file);
        $body = "uploadfile " . basename($file) . " image/jpeg $size\n";
        $this->assertEquals($body, $res->getBody(), 'Response body does not include expected upload parameters (detect: ' . $detect . ')');
    }

    public function testUploadNameWithSpecialChars()
    {
        $this->client->setUri($this->baseuri. 'testUploads.php');

        $rawdata = file_get_contents(__FILE__);
        $this->client->setFileUpload('/some strage/path%/with[!@#$&]/myfile.txt', 'uploadfile', $rawdata, 'text/plain');
        $res = $this->client->request('POST');

        $body = 'uploadfile myfile.txt text/plain ' . strlen($rawdata) . "\n";
        $this->assertEquals($body, $res->getBody(), 'Response body does not include expected upload parameters');
    }

    public function testStaticLargeFileDownload()
    {
        $this->client->setUri($this->baseuri . 'staticFile.jpg');

        $got = $this->client->request()->getBody();
        $expected = $this->_getTestFileContents('staticFile.jpg');

        $this->assertEquals($expected, $got, 'Downloaded file does not seem to match!');
    }

    /**
     * Test that one can upload multiple files with the same form name, as an 
     * array
     *
     * @link http://framework.zend.com/issues/browse/ZF-5744
     */
    public function testMutipleFilesWithSameFormNameZF5744()
    {
        $rawData = 'Some test raw data here...';
        
        $this->client->setUri($this->baseuri . 'testUploads.php');
        
        $files = array('file1.txt', 'file2.txt', 'someotherfile.foo');
        
        $expectedBody = '';
        foreach($files as $filename) {
            $this->client->setFileUpload($filename, 'uploadfile[]', $rawData, 'text/plain');
            $expectedBody .= "uploadfile $filename text/plain " . strlen($rawData) . "\n";
        }
        
        $res = $this->client->request('POST');

        $this->assertEquals($expectedBody, $res->getBody(), 'Response body does not include expected upload parameters');
    }
    
    /**
     * Test that lines that might be evaluated as boolean false do not break
     * the reading prematurely.
     *
     * @see http://framework.zend.com/issues/browse/ZF-4238
     */
    public function testZF4238FalseLinesInResponse()
    {
        $this->client->setUri($this->baseuri . 'ZF4238-zerolineresponse.txt');

        $got = $this->client->request()->getBody();
        $expected = $this->_getTestFileContents('ZF4238-zerolineresponse.txt');
        $this->assertEquals($expected, $got);
    }

    /**
     * Internal helpder function to get the contents of test files
     *
     * @param  string $file
     * @return string
     */
    protected function _getTestFileContents($file)
    {
        return file_get_contents(dirname(realpath(__FILE__)) . DIRECTORY_SEPARATOR .
           '_files' . DIRECTORY_SEPARATOR . $file);
    }

    /**
     * Data provider for complex, nesting parameter arrays
     * 
     * @return array
     */
    static public function parameterArrayProvider()
    {
        return array(
            array(
                array(
                    'quest' => 'To seek the holy grail',
                    'YourMother' => 'Was a hamster',
                    'specialChars' => '<>$+ &?=[]^%',
                    'array' => array('firstItem', 'secondItem', '3rdItem')
                )
            ),
            
            array(
                array(
                    'someData' => array(
                        "1", 
                        "2", 
                        'key' => 'value',
                        'nesting' => array(
                            'a' => 'AAA',
                            'b' => 'BBB'
                        )
                    ),
                    'someOtherData' => array('foo', 'bar')
                )
            ),
            
            array(
                array(
                    'foo1' => 'bar',
                    'foo2' => array('baz', 'w00t')
                ) 
            )
        );
    }
}
