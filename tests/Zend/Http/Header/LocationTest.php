<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\Location;
use Zend\Uri\Http as HttpUri;

class LocationTest extends \PHPUnit_Framework_TestCase
{

    public function testLocationFromStringCreatesValidLocationHeader()
    {
        $locationHeader = Location::fromString('Location: http://www.example.com/');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $locationHeader);
        $this->assertInstanceOf('Zend\Http\Header\Location', $locationHeader);
    }

    public function testLocationGetFieldValueReturnsProperValue()
    {
        $locationHeader = new Location();
        $locationHeader->setUri('http://www.example.com/');
        $this->assertEquals('http://www.example.com/', $locationHeader->getFieldValue());

        $locationHeader->setUri('/path');
        $this->assertEquals('/path', $locationHeader->getFieldValue());
    }

    public function testLocationToStringReturnsHeaderFormattedString()
    {
        $locationHeader = new Location();
        $locationHeader->setUri('http://www.example.com/path?query');

        $this->assertEquals('Location: http://www.example.com/path?query', $locationHeader->toString());
    }

    /** Implementation specific tests  */

    public function testLocationCanSetAndAccessAbsoluteUri()
    {
        $locationHeader = Location::fromString('Location: http://www.example.com/path');
        $uri = $locationHeader->uri();
        $this->assertInstanceOf('Zend\Uri\Http', $uri);
        $this->assertTrue($uri->isAbsolute());
        $this->assertEquals('http://www.example.com/path', $locationHeader->getUri());
    }

    public function testLocationCanSetAndAccessRelativeUri()
    {
        $locationHeader = Location::fromString('Location: /path/to');
        $uri = $locationHeader->uri();
        $this->assertInstanceOf('Zend\Uri\Http', $uri);
        $this->assertFalse($uri->isAbsolute());
        $this->assertEquals('/path/to', $locationHeader->getUri());
    }

}

