<?php
// Call Zend_Amf_AuthTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Amf_ResourceTest::main");
}

require_once 'PHPUnit/Framework/TestCase.php';
require_once dirname(__FILE__) . '/../../TestHelper.php';
require_once 'Zend/Amf/Server.php';
require_once 'Zend/Amf/Request.php';
require_once 'Zend/Amf/Parse/TypeLoader.php';
require_once 'Zend/Amf/Value/Messaging/RemotingMessage.php';

/**
 *  test case.
 */
class Zend_Amf_ResourceTest extends PHPUnit_Framework_TestCase
{

    /**
     * Enter description here...
     *
     * @var Zend_Amf_Server
     */
    protected $_server;

    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Amf_ResourceTest");
        PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        $this->_server = new Zend_Amf_Server();
        $this->_server->setProduction(false);
        Zend_Amf_Parse_TypeLoader::resetMap();
    }
	
	protected function tearDown()
	{
        unset($this->_server);
	}
	
	protected function _callService($method, $class = 'Zend_Amf_Resource_testclass')
	{
        $request = new Zend_Amf_Request();
        $request->setObjectEncoding(0x03);
        $this->_server->setClass($class);
        $newBody = new Zend_Amf_Value_MessageBody("$class.$method","/1",array("test"));
		$request->addAmfBody($newBody);
        $this->_server->handle($request);
        $response = $this->_server->getResponse();
        return $response;
	}
	
	public function testFile()
	{
		$resp = $this->_callService("returnFile");
		$this->assertContains("test data", $resp->getResponse());
	}
	
	/**
	 * Defining new unknown resource type
	 * 
	 * @expectException Zend_Amf_Server_Exception
	 *
	 */
	public function testCtxNoResource()
	{
		try {
			$this->_callService("returnCtx");
		} catch(Zend_Amf_Server_Exception $e) {
			$this->assertContains("serialize resource type", $e->getMessage());
			return;
		}
		$this->fail("Failed to throw exception on unknown resource");
	}
	
	/**
	 * Defining new unknown resource type via plugin loader and handling it
	 *
	 */ 
	public function testCtxLoader()
	{
		Zend_Amf_Parse_TypeLoader::addResourceDirectory("Test_Resource", dirname(__FILE__)."/Resources");
		$resp = $this->_callService("returnCtx");
		$this->assertContains("Accept-language:", $resp->getResponse());
		$this->assertContains("foo=bar", $resp->getResponse());
	}
	
	/**
	 * Defining new unknown resource type and handling it
	 *
	 */ 
	public function testCtx()
	{
		Zend_Amf_Parse_TypeLoader::setResourceLoader(new Zend_Amf_TestResourceLoader("2"));
		$resp = $this->_callService("returnCtx");
		$this->assertContains("Accept-language:", $resp->getResponse());
		$this->assertContains("foo=bar", $resp->getResponse());
	}
	
	/**
	 * Defining new unknown resource type, handler has no parse()
	 *
	 */ 
	public function testCtxNoParse()
	{
		Zend_Amf_Parse_TypeLoader::setResourceLoader(new Zend_Amf_TestResourceLoader("3"));
		try {
			$resp = $this->_callService("returnCtx");
		} catch(Zend_Amf_Server_Exception $e) {
			$this->assertContains("Could not call parse()", $e->getMessage());
			return;
		}
		$this->fail("Failed to throw exception on unknown resource");
	}
	
}

class Zend_Amf_Resource_testclass {
	function returnFile() 
	{
		return fopen(dirname(__FILE__)."/_files/testdata", "r");
	}
	function returnCtx() 
	{
		$opts = array(
  			'http'=>array(
		    'method'=>"GET",
    		'header'=>"Accept-language: en\r\n" .
            	  "Cookie: foo=bar\r\n"
  			)
		);
		$context = stream_context_create($opts);
		return $context;
	}
}

class StreamContext2
{
    public function parse($resource) {
       		return stream_context_get_options($resource);
	}
}	
class StreamContext3
{
    protected function parse($resource) {
       		return stream_context_get_options($resource);
	}
}	
class Zend_Amf_TestResourceLoader implements Zend_Loader_PluginLoader_Interface {
	public $suffix;
	public function __construct($suffix) {
		$this->suffix = $suffix;
	}
    public function addPrefixPath($prefix, $path) {}
    public function removePrefixPath($prefix, $path = null) {}
    public function isLoaded($name) {}
    public function getClassName($name) {}
    public function load($name) {
    	return $name.$this->suffix;
    }
}

if (PHPUnit_MAIN_METHOD == "Zend_Amf_ResourceTest::main") {
    Zend_Amf_ResourceTest::main();
}
