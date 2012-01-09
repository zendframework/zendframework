<?php
/**
 * @namespace
 */
namespace ZendTest\Http\Client;
use Zend\Http\Client as HTTPClient,
    Zend\Http\Client\Adapter,
    Zend\Http\Client\Adapter\Exception as AdapterException,
    Zend\Http\Response,
    Zend\Http\Request;


/**
 * This are the test for the prototype of Zend\Http\Client
 *
 * @category   Zend
 * @package    Zend\Http\Client
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Http
 * @group      Zend_Http_Client
 */
class UseCaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The bast URI for this test, containing all files in the _files directory
     * Should be set in TestConfiguration.php or TestConfiguration.php.dist
     *
     * @var string
     */
    protected $baseuri = 'http://www.google.com/';

    /**
     * Common HTTP client
     *
     * @var Zend_Http_Client
     */
    protected $client = null;

    /**
     * Common HTTP client adapter
     *
     * @var Zend_Http_Client_Adapter_Interface
     */
    protected $adapter = null;

    /**
     * Configuration array
     *
     * @var array
     */
    protected $config = array(
        'adapter'     => 'Zend\Http\Client\Adapter\Socket'
    );

    /**
     * Set up the test case
     */
    protected function setUp()
    {
        $this->client= new HTTPClient($this->baseuri);
    }

    /**
     * Clean up the test environment
     *
     */
    protected function tearDown()
    {
        $this->client = null;
    }
    
    public function testHttpGet()
    {
        $this->client->setMethod(Request::METHOD_GET);
        $response= $this->client->send();
        $this->assertTrue($response->isSuccess());
    }
    
    public function testStaticHttpGet()
    {
//        $response= HTTPClient::get($this->baseuri);
//        $this->assertTrue($response->isSuccess());
    }
    
    public function testRequestHttpGet()
    {
        $client= new HTTPClient();
        $request= new Request();
        $request->setUri($this->baseuri);
        $request->setMethod(Request::METHOD_GET);
        $response= $client->send($request);
        $this->assertTrue($response->isSuccess());
    }
    
}
