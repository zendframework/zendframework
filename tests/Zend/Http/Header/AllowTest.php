<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\Allow;

class AllowTest extends \PHPUnit_Framework_TestCase
{

    public function testAllowFromStringCreatesValidAllowHeader()
    {
        $allowHeader = Allow::fromString('Allow: GET, POST');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $allowHeader);
        $this->assertInstanceOf('Zend\Http\Header\Allow', $allowHeader);
        $this->assertEquals(array('GET', 'POST'), $allowHeader->getAllowedMethods());
    }

    public function testAllowGetFieldNameReturnsHeaderName()
    {
        $allowHeader = new Allow();
        $this->assertEquals('Allow', $allowHeader->getFieldName());
    }

    public function testAllowGetFieldValueReturnsProperValue()
    {
        $allowHeader = new Allow();
        $allowHeader->setAllowedMethods(array('GET', 'POST'));
        $this->assertEquals('GET, POST', $allowHeader->getFieldValue());
    }

    public function testAllowToStringReturnsHeaderFormattedString()
    {
        $allowHeader = new Allow();
        $allowHeader->setAllowedMethods(array('GET', 'POST'));

        // @todo set some values, then test output
        $this->assertEquals('Allow: GET, POST', $allowHeader->toString());
    }

}

