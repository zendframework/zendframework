<?php
/**
 * @package Zend_Rest
 * @subpackage UnitTests
 */

/**
 * Zend_Rest_Server
 */
require_once 'Zend/Rest/Client/Result.php';

/**
 * PHPUnit Test Case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Test cases for Zend_Rest_Server
 *
 * @package Zend_Rest
 * @subpackage UnitTests
 */
class Zend_Rest_ResultTest extends PHPUnit_Framework_TestCase 
{
    static $path;

    public function __construct()
    {
        self::$path = dirname(__FILE__).'/responses/';
    }
    
    public function testResponseSuccess()
    {
        $xml = file_get_contents(self::$path ."returnString.xml");
        $client = new Zend_Rest_Client_Result($xml);
        $this->assertTrue($client->isSuccess());
    }
    
    public function testResponseIsError()
    {
        $xml = file_get_contents(self::$path ."returnError.xml");
        $client = new Zend_Rest_Client_Result($xml);
        $this->assertTrue($client->isError());
    }
    
    public function testResponseString()
    {
        $xml = file_get_contents(self::$path ."returnString.xml");
        $client = new Zend_Rest_Client_Result($xml);
        $this->assertEquals("string", $client->__toString());
    }
    
    public function testResponseInt()
    {
        $xml = file_get_contents(self::$path ."returnInt.xml");
        $client = new Zend_Rest_Client_Result($xml);
        $this->assertEquals("123", $client->__toString());
    }
    
    public function testResponseArray()
    {
        $xml = file_get_contents(self::$path ."returnArray.xml");
        // <foo>bar</foo><baz>1</baz><key_1>0</key_1><bat>123</bat>
        $client = new Zend_Rest_Client_Result($xml);
        foreach ($client as $key => $value) {
            $result_array[$key] = (string) $value;
        }
        $this->assertEquals(array("foo" => "bar", "baz" => "1", "key_1" => "0", "bat" => "123", "status" => "success"), $result_array);
    }
    
    public function testResponseObject()
    {
        $xml = file_get_contents(self::$path ."returnObject.xml");
        // <foo>bar</foo><baz>1</baz><bat>123</bat><qux>0</qux><status>success</status>
        $client = new Zend_Rest_Client_Result($xml);
        $this->assertEquals("bar", $client->foo());
        $this->assertEquals(1, $client->baz());
        $this->assertEquals(123, $client->bat());
        $this->assertEquals(0, $client->qux());
        $this->assertEquals("success", $client->status());
    }
    
    public function testResponseTrue()
    {
        $xml = file_get_contents(self::$path ."returnTrue.xml");
        $client = new Zend_Rest_Client_Result($xml);
        $this->assertTrue((bool)$client->response);
    }
    
    public function testResponseFalse()
    {
        $xml = file_get_contents(self::$path ."returnFalse.xml");
        $client = new Zend_Rest_Client_Result($xml);
        $this->assertFalse((bool) $client->response());
    }
    
    public function testResponseVoid()
    {
        $xml = file_get_contents(self::$path . "returnVoid.xml");
        $client = new Zend_Rest_Client_Result($xml);
        $this->assertEquals(null, $client->response());
    }
    
    public function testResponseException()
    {
        $xml = file_get_contents(self::$path . "returnError.xml");
        $client = new Zend_Rest_Client_Result($xml);
        $this->assertTrue($client->isError());
    }

    public function testGetXpathValue()
    {
        $xml = file_get_contents(self::$path . DIRECTORY_SEPARATOR . 'returnNestedArray.xml');
        $result = new Zend_Rest_Client_Result($xml);
        $key_1 = $result->key_1();
        $this->assertEquals(0, $key_1);
    }

    public function testGetXpathValueMissing()
    {
        $xml = file_get_contents(self::$path . DIRECTORY_SEPARATOR . 'returnNestedArray.xml');
        $result = new Zend_Rest_Client_Result($xml);
        $lola = $result->lola;
        $this->assertNull($lola);
    }

    public function testGetXpathValueArray()
    {
        $xml = file_get_contents(self::$path . DIRECTORY_SEPARATOR . 'returnNestedArray.xml');
        $result = new Zend_Rest_Client_Result($xml);
        $baz = $result->baz;
        $this->assertTrue(is_array($baz), var_export($baz, 1));
        $this->assertEquals('1', (string) $baz[0]);
        $this->assertEquals('farama', (string) $baz[1]);
    }

    public function testIsset()
    {
        $xml = file_get_contents(self::$path . DIRECTORY_SEPARATOR . 'returnNestedArray.xml');
        $result = new Zend_Rest_Client_Result($xml);
        $this->assertTrue(isset($result->bar));
    }

    public function testIssetXpathValue()
    {
        $xml = file_get_contents(self::$path . DIRECTORY_SEPARATOR . 'returnNestedArray.xml');
        $result = new Zend_Rest_Client_Result($xml);
        $this->assertTrue(isset($result->baz));
    }

    public function testIssetInvalidValue()
    {
        $xml = file_get_contents(self::$path . DIRECTORY_SEPARATOR . 'returnNestedArray.xml');
        $result = new Zend_Rest_Client_Result($xml);
        $this->assertFalse(isset($result->lola));
    }

    public function testCall()
    {
        $xml = file_get_contents(self::$path . DIRECTORY_SEPARATOR . 'returnNestedArray.xml');
        $result = new Zend_Rest_Client_Result($xml);
        $returned = $result->key_1();
        $this->assertEquals(0, $returned);
    }
}
