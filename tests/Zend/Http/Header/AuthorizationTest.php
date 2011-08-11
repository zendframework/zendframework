<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\Authorization;

class AuthorizationTest extends \PHPUnit_Framework_TestCase
{

    public function testAuthorizationFromStringCreatesValidAuthorizationHeader()
    {
        $authorizationHeader = Authorization::fromString('Authorization: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $authorizationHeader);
        $this->assertInstanceOf('Zend\Http\Header\Authorization', $authorizationHeader);
    }

    public function testAuthorizationGetFieldNameReturnsHeaderName()
    {
        $authorizationHeader = new Authorization();
        $this->assertEquals('Authorization', $authorizationHeader->getFieldName());
    }

    public function testAuthorizationGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Authorization needs to be completed');

        $authorizationHeader = new Authorization();
        $this->assertEquals('xxx', $authorizationHeader->getFieldValue());
    }

    public function testAuthorizationToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Authorization needs to be completed');

        $authorizationHeader = new Authorization();

        // @todo set some values, then test output
        $this->assertEmpty('Authorization: xxx', $authorizationHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

