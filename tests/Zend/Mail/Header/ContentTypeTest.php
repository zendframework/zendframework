<?php

namespace ZendTest\Mail\Header;

use Zend\Mail\Header\ContentType;

class ContentTypeTest extends \PHPUnit_Framework_TestCase
{

    public function testContentTypeFromStringCreatesValidContentTypeHeader()
    {
        $contentTypeHeader = ContentType::fromString('Content-Type: xxx/yyy');
        $this->assertInstanceOf('Zend\Mail\Header\HeaderDescription', $contentTypeHeader);
        $this->assertInstanceOf('Zend\Mail\Header\ContentType', $contentTypeHeader);
    }

    public function testContentTypeGetFieldNameReturnsHeaderName()
    {
        $contentTypeHeader = new ContentType();
        $this->assertEquals('Content-Type', $contentTypeHeader->getFieldName());
    }

    public function testContentTypeGetFieldValueReturnsProperValue()
    {
        $contentTypeHeader = new ContentType();
        $contentTypeHeader->setType('foo/bar');
        $this->assertEquals('foo/bar', $contentTypeHeader->getFieldValue());
    }

    public function testContentTypeToStringReturnsHeaderFormattedString()
    {
        $contentTypeHeader = new ContentType();
        $contentTypeHeader->setType('foo/bar');
        $this->assertEquals("Content-Type: foo/bar\r\n", $contentTypeHeader->toString());
    }

    public function testProvidingParametersIntroducesHeaderFolding()
    {
        $header = new ContentType();
        $header->setType('application/x-unit-test');
        $header->addParameter('charset', 'us-ascii');
        $string = $header->toString();

        $this->assertContains("Content-Type: application/x-unit-test;\r\n", $string);
        $this->assertContains(";\r\n charset=\"us-ascii\"", $string);
    }
    
}

