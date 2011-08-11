<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\Pragma;

class PragmaTest extends \PHPUnit_Framework_TestCase
{

    public function testPragmaFromStringCreatesValidPragmaHeader()
    {
        $pragmaHeader = Pragma::fromString('Pragma: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $pragmaHeader);
        $this->assertInstanceOf('Zend\Http\Header\Pragma', $pragmaHeader);
    }

    public function testPragmaGetFieldNameReturnsHeaderName()
    {
        $pragmaHeader = new Pragma();
        $this->assertEquals('Pragma', $pragmaHeader->getFieldName());
    }

    public function testPragmaGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Pragma needs to be completed');

        $pragmaHeader = new Pragma();
        $this->assertEquals('xxx', $pragmaHeader->getFieldValue());
    }

    public function testPragmaToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Pragma needs to be completed');

        $pragmaHeader = new Pragma();

        // @todo set some values, then test output
        $this->assertEmpty('Pragma: xxx', $pragmaHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

