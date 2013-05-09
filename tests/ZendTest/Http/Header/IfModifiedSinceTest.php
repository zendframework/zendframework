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

use Zend\Http\Header\IfModifiedSince;

class IfModifiedSinceTest extends \PHPUnit_Framework_TestCase
{

    public function testIfModifiedSinceFromStringCreatesValidIfModifiedSinceHeader()
    {
        $ifModifiedSinceHeader = IfModifiedSince::fromString('If-Modified-Since: Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $ifModifiedSinceHeader);
        $this->assertInstanceOf('Zend\Http\Header\IfModifiedSince', $ifModifiedSinceHeader);
    }

    public function testIfModifiedSinceGetFieldNameReturnsHeaderName()
    {
        $ifModifiedSinceHeader = new IfModifiedSince();
        $this->assertEquals('If-Modified-Since', $ifModifiedSinceHeader->getFieldName());
    }

    public function testIfModifiedSinceGetFieldValueReturnsProperValue()
    {
        $ifModifiedSinceHeader = new IfModifiedSince();
        $ifModifiedSinceHeader->setDate('Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertEquals('Sun, 06 Nov 1994 08:49:37 GMT', $ifModifiedSinceHeader->getFieldValue());
    }

    public function testIfModifiedSinceToStringReturnsHeaderFormattedString()
    {
        $ifModifiedSinceHeader = new IfModifiedSince();
        $ifModifiedSinceHeader->setDate('Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertEquals('If-Modified-Since: Sun, 06 Nov 1994 08:49:37 GMT', $ifModifiedSinceHeader->toString());
    }

    /**
     * Implementation specific tests are covered by DateTest
     * @see ZendTest\Http\Header\DateTest
     */

}
