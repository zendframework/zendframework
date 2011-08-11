<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\Age;

class AgeTest extends \PHPUnit_Framework_TestCase
{

    public function testAgeFromStringCreatesValidAgeHeader()
    {
        $ageHeader = Age::fromString('Age: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $ageHeader);
        $this->assertInstanceOf('Zend\Http\Header\Age', $ageHeader);
    }

    public function testAgeGetFieldNameReturnsHeaderName()
    {
        $ageHeader = new Age();
        $this->assertEquals('Age', $ageHeader->getFieldName());
    }

    public function testAgeGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Age needs to be completed');

        $ageHeader = new Age();
        $this->assertEquals('xxx', $ageHeader->getFieldValue());
    }

    public function testAgeToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Age needs to be completed');

        $ageHeader = new Age();

        // @todo set some values, then test output
        $this->assertEmpty('Age: xxx', $ageHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

