<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\Location;

class LocationTest extends \PHPUnit_Framework_TestCase
{

    public function testLocationFromStringCreatesValidLocationHeader()
    {
        $locationHeader = Location::fromString('Location: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $locationHeader);
        $this->assertInstanceOf('Zend\Http\Header\Location', $locationHeader);
    }

    public function testLocationGetFieldNameReturnsHeaderName()
    {
        $locationHeader = new Location();
        $this->assertEquals('Location', $locationHeader->getFieldName());
    }

    public function testLocationGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Location needs to be completed');

        $locationHeader = new Location();
        $this->assertEquals('xxx', $locationHeader->getFieldValue());
    }

    public function testLocationToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Location needs to be completed');

        $locationHeader = new Location();

        // @todo set some values, then test output
        $this->assertEmpty('Location: xxx', $locationHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

