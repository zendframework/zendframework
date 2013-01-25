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

use Zend\Http\ClientStatic as HTTPClient;
use Zend\Http\Client;


/**
 * This are the test for the prototype of Zend\Http\Client
 *
 * @category   Zend
 * @package    Zend\Http\Client
 * @subpackage UnitTests
 * @group      Zend\Http
 * @group      Zend\Http\Client
 */
class StaticClientTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Uri for test
     *
     * @var string
     */
    protected $baseuri;

    /**
     * Set up the test case
     */
    protected function setUp()
    {
        if (defined('TESTS_ZEND_HTTP_CLIENT_BASEURI')
            && (TESTS_ZEND_HTTP_CLIENT_BASEURI != false)) {

            $this->baseuri = TESTS_ZEND_HTTP_CLIENT_BASEURI;
            if (substr($this->baseuri, -1) != '/') $this->baseuri .= '/';

        } else {
            // Skip tests
            $this->markTestSkipped("Zend_Http_Client dynamic tests are not enabled in TestConfiguration.php");
        }
    }

    /**
     * Test simple GET
     */
    public function testHttpSimpleGet()
    {
        $response= HTTPClient::get($this->baseuri . 'testSimpleRequests.php');
        $this->assertTrue($response->isSuccess());
    }

    /**
     * Test GET with query string in URI
     */
    public function testHttpGetWithParamsInUri()
    {
        $response= HTTPClient::get($this->baseuri . 'testGetData.php?foo');
        $this->assertTrue($response->isSuccess());
        $this->assertContains('foo', $response->getBody());
    }

    /**
     * Test GET with query as params
     */
    public function testHttpMultiGetWithParam()
    {
        $response= HTTPClient::get($this->baseuri . 'testGetData.php',array('foo' => 'bar'));
        $this->assertTrue($response->isSuccess());
        $this->assertContains('foo', $response->getBody());
        $this->assertContains('bar', $response->getBody());
    }

    /**
     * Test simple POST
     */
    public function testHttpSimplePost()
    {
        $response= HTTPClient::post($this->baseuri . 'testPostData.php',array('foo' => 'bar'));
        $this->assertTrue($response->isSuccess());
        $this->assertContains('foo', $response->getBody());
        $this->assertContains('bar', $response->getBody());
    }

    /**
     * Test POST with header Content-Type
     */
    public function testHttpPostContentType()
    {
        $response= HTTPClient::post($this->baseuri . 'testPostData.php',
                                    array('foo' => 'bar'),
                                    array('Content-Type' => Client::ENC_URLENCODED));
        $this->assertTrue($response->isSuccess());
        $this->assertContains('foo', $response->getBody());
        $this->assertContains('bar', $response->getBody());
    }

    /**
     * Test POST with body
     */
    public function testHttpPostWithBody()
    {
        $postBody = 'foo';

        $response= HTTPClient::post($this->baseuri . 'testRawPostData.php',
                                    array('foo' => 'bar'),
                                    array('Content-Type' => Client::ENC_URLENCODED),
                                    $postBody);

        $this->assertTrue($response->isSuccess());
        $this->assertContains($postBody, $response->getBody());
    }
}
