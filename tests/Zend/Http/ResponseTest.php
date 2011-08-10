<?php

/**
 * @namespace
 */
namespace ZendTest\Http;

use Zend\Http\Response;

class ResponseTest extends \PHPUnit_Framework_TestCase
{

    public function testResponseFactoryFromStringCreatesValidResponse()
    {
        $string = 'HTTP/1.0 200 OK';
        $response = Response::fromString($string);
        //var_dump($response);
    }
}
