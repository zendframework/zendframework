<?php

namespace ZendTest\Http;

use Zend\Http\RequestHeaders;

class RequestHeadersTest extends \PHPUnit_Framework_TestCase
{
    public function testRequestHeadersStructureIsCorrect()
    {
        $rHeaders = new RequestHeaders();
        $this->assertInstanceOf('Zend\Http\Headers', $rHeaders);
        $this->assertInstanceOf('SplQueue', $rHeaders);
        $this->assertInstanceOf('Iterator', $rHeaders);
        $this->assertInstanceOf('ArrayAccess', $rHeaders);
        $this->assertInstanceOf('Countable', $rHeaders);
    }

    

}
