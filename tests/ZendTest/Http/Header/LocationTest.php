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

use Zend\Http\Header\Location;

class LocationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @paramstring $uri The URL to redirect to
     * @dataProvider locationFromStringCreatesValidLocationHeaderProvider
     */
    public function testLocationFromStringCreatesValidLocationHeader($uri)
    {
        $locationHeader = Location::fromString('Location: ' . $uri);
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $locationHeader);
        $this->assertInstanceOf('Zend\Http\Header\Location', $locationHeader);
    }

    public function locationFromStringCreatesValidLocationHeaderProvider()
    {
        return array(
            array('http://www.example.com'),
            array('https://www.example.com'),
            array('mailto://www.example.com'),
            array('file://www.example.com'),
        );
    }

    /**
     * Test that we can set a redirect to different URI-Schemes
     *
     * @param string $uri
     * @param string $expectedClass
     *
     * @dataProvider locationCanSetDifferentSchemeUrisProvider
     */
    public function testLocationCanSetDifferentSchemeUris($uri, $expectedClass)
    {
        $locationHeader = new Location;
        $locationHeader->setUri($uri);
        $this->assertAttributeInstanceof($expectedClass, 'uri', $locationHeader);
    }

    /**
     * Test that we can set a redirect to different URI-schemes via a class
     *
     * @param string $uri
     * @param string $expectedClass
     *
     * @dataProvider locationCanSetDifferentSchemeUrisProvider
     */
    public function testLocationCanSetDifferentSchemeUriObjects($uri, $expectedClass)
    {
            $uri = \Zend\Uri\UriFactory::factory($uri);
        $locationHeader = new Location;
        $locationHeader->setUri($uri);
        $this->assertAttributeInstanceof($expectedClass, 'uri', $locationHeader);

    }

    /**
     * Provide data to the locationCanSetDifferentSchemeUris-test
     *
     * @return array
     */
    public function locationCanSetDifferentSchemeUrisProvider()
    {
        return array(
            array('http://www.example.com', '\Zend\Uri\Http'),
            array('https://www.example.com', '\Zend\Uri\Http'),
            array('mailto://www.example.com', '\Zend\Uri\Mailto'),
            array('file://www.example.com', '\Zend\Uri\File'),
        );
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
        $this->assertInstanceOf('Zend\Uri\Uri', $uri);
        $this->assertFalse($uri->isAbsolute());
        $this->assertEquals('/path/to', $locationHeader->getUri());
    }

}
