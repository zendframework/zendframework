<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Http\Header;

use Zend\Http\Header\ContentType;

class ContentTypeTest extends \PHPUnit_Framework_TestCase
{

    public function testContentTypeFromStringCreatesValidContentTypeHeader()
    {
        $contentTypeHeader = ContentType::fromString('Content-Type: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $contentTypeHeader);
        $this->assertInstanceOf('Zend\Http\Header\ContentType', $contentTypeHeader);
    }

    public function testContentTypeGetFieldNameReturnsHeaderName()
    {
        $contentTypeHeader = new ContentType();
        $this->assertEquals('Content-Type', $contentTypeHeader->getFieldName());
    }

    public function testContentTypeGetFieldValueReturnsProperValue()
    {
        $header = ContentType::fromString('Content-Type: application/json');
        $this->assertEquals('application/json', $header->getFieldValue());
    }

    public function testContentTypeToStringReturnsHeaderFormattedString()
    {
        $header = new ContentType();
        $header->setMediaType('application/atom+xml')
               ->setCharset('ISO-8859-1');

        $this->assertEquals('Content-Type: application/atom+xml; charset=ISO-8859-1', $header->toString());
    }

    /** Implementation specific tests here */

    public function wildcardMatches()
    {
        return array(
            'wildcard' => array('*/*'),
            'wildcard-type-subtype-fixed-format' => array('*/*+json'),
            'wildcard-type-format-subtype' => array('*/json'),
            'fixed-type-wildcard-subtype' => array('application/*'),
            'fixed-type-fixed-subtype-wildcard-format' => array('application/vnd.foobar+*'),
            'fixed' => array('application/vnd.foobar+json'),
        );
    }

    /**
     * @dataProvider wildcardMatches
     */
    public function testMatchWildCard($matchAgainst)
    {
        $header = ContentType::fromString('Content-Type: application/vnd.foobar+json');
        $result = $header->match($matchAgainst);
        $this->assertTrue($header->match($matchAgainst));
    }
}
