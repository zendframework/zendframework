<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Http
 */

namespace ZendTest\Http\Header;

use Zend\Http\Header\ContentLocation;

class ContentLocationTest extends \PHPUnit_Framework_TestCase
{

    public function testContentLocationFromStringCreatesValidLocationHeader()
    {
        $contentLocationHeader = ContentLocation::fromString('Content-Location: http://www.example.com/');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $contentLocationHeader);
        $this->assertInstanceOf('Zend\Http\Header\ContentLocation', $contentLocationHeader);
    }

    public function testContentLocationGetFieldValueReturnsProperValue()
    {
        $contentLocationHeader = new ContentLocation();
        $contentLocationHeader->setUri('http://www.example.com/');
        $this->assertEquals('http://www.example.com/', $contentLocationHeader->getFieldValue());

        $contentLocationHeader->setUri('/path');
        $this->assertEquals('/path', $contentLocationHeader->getFieldValue());
    }

    public function testContentLocationToStringReturnsHeaderFormattedString()
    {
        $contentLocationHeader = new ContentLocation();
        $contentLocationHeader->setUri('http://www.example.com/path?query');

        $this->assertEquals('Content-Location: http://www.example.com/path?query', $contentLocationHeader->toString());
    }

    /** Implementation specific tests  */

    public function testContentLocationCanSetAndAccessAbsoluteUri()
    {
        $contentLocationHeader = ContentLocation::fromString('Content-Location: http://www.example.com/path');
        $uri = $contentLocationHeader->uri();
        $this->assertInstanceOf('Zend\Uri\UriInterface', $uri);
        $this->assertTrue($uri->isAbsolute());
        $this->assertEquals('http://www.example.com/path', $contentLocationHeader->getUri());
    }

    public function testContentLocationCanSetAndAccessRelativeUri()
    {
        $contentLocationHeader = ContentLocation::fromString('Content-Location: /path/to');
        $uri = $contentLocationHeader->uri();
        $this->assertInstanceOf('Zend\Uri\UriInterface', $uri);
        $this->assertFalse($uri->isAbsolute());
        $this->assertEquals('/path/to', $contentLocationHeader->getUri());
    }

}
