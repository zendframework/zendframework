<?php

/**
 * @namespace
 */
namespace ZendTest\Http;

use Zend\Http\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testClientRetrievesUppercaseHttpMethodFromRequestObject()
    {
        $client = new Client;
        $client->setMethod('post');
        $this->assertEquals(Client::ENC_URLENCODED, $client->getEncType());
    }
    
    public function testIfZeroValueCookiesCanBeSet()
    {
        try {
            $client = new Client();
            $client->addCookie("test", 0);
            $client->addCookie("test2", "0");
            $client->addCookie("test3", false);
        } catch (Exception\InvalidArgumentException $e) {
            $this->fail('Zero Values should be valid');
        }
        $this->assertTrue(true);
    }
    
    /**
    * @expectedException Zend\Http\Exception\InvalidArgumentException
    */
    public function testIfNullValueCookiesThrowsException()
    {
        $client = new Client();
        $client->addCookie("test", null);
    } 
}
