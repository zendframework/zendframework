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

use Zend\Http\Header\Referer;

class RefererTest extends \PHPUnit_Framework_TestCase
{

    public function testRefererFromStringCreatesValidLocationHeader()
    {
        $refererHeader = Referer::fromString('Referer: http://www.example.com/');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $refererHeader);
        $this->assertInstanceOf('Zend\Http\Header\Referer', $refererHeader);
    }

    public function testRefererGetFieldValueReturnsProperValue()
    {
        $refererHeader = new Referer();
        $refererHeader->setUri('http://www.example.com/');
        $this->assertEquals('http://www.example.com/', $refererHeader->getFieldValue());

        $refererHeader->setUri('/path');
        $this->assertEquals('/path', $refererHeader->getFieldValue());
    }

    public function testRefererToStringReturnsHeaderFormattedString()
    {
        $refererHeader = new Referer();
        $refererHeader->setUri('http://www.example.com/path?query');

        $this->assertEquals('Referer: http://www.example.com/path?query', $refererHeader->toString());
    }

    /** Implementation specific tests  */

    public function testRefererCanSetAndAccessAbsoluteUri()
    {
        $refererHeader = Referer::fromString('Referer: http://www.example.com/path');
        $uri = $refererHeader->uri();
        $this->assertInstanceOf('Zend\Uri\Http', $uri);
        $this->assertTrue($uri->isAbsolute());
        $this->assertEquals('http://www.example.com/path', $refererHeader->getUri());
    }

    public function testRefererCanSetAndAccessRelativeUri()
    {
        $refererHeader = Referer::fromString('Referer: /path/to');
        $uri = $refererHeader->uri();
        $this->assertInstanceOf('Zend\Uri\Uri', $uri);
        $this->assertFalse($uri->isAbsolute());
        $this->assertEquals('/path/to', $refererHeader->getUri());
    }

    public function testRefererDoesNotHaveUriFragment()
    {
        $refererHeader = new Referer();
        $refererHeader->setUri('http://www.example.com/path?query#fragment');
        $this->assertEquals('Referer: http://www.example.com/path?query', $refererHeader->toString());
    }
}
