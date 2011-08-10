<?php

namespace ZendTest\Http;

use ZendTest\Http\TestAsset\HeadersStub,
    ZendTest\Http\TestAsset\FakeHeader;

class HeadersTest extends \PHPUnit_Framework_TestCase
{
    public function testHeadersImplementsProperClasses()
    {
        $headers = new HeadersStub();
        $this->assertInstanceOf('Iterator', $headers);
        $this->assertInstanceOf('Countable', $headers);
    }

    public function testHeadersFromStringFactoryCreatesSingleObject()
    {
        $headers = HeadersStub::fromString("Fake: foo-bar");
        $this->assertEquals(1, $headers->count());

        $header = $headers->get('fake');
        $this->assertEquals('Fake', $header->getFieldName());
        $this->assertEquals('foo-bar', $header->getFieldValue());
    }

    public function testHeadersFromStringFactoryCreatesMultipleObjects()
    {
        $headers = HeadersStub::fromString("Multi-Fake: foo-bar, boo-baz");
        $this->assertEquals(1, $headers->count());

        $headers = $headers->get('multifake');
        $header1 = $headers[0];
        $this->assertEquals('Multi-Fake', $header1->getFieldName());
        $this->assertEquals('foo-bar', $header1->getFieldValue());
        $header2 = $headers[1];
        $this->assertEquals('Multi-Fake', $header2->getFieldName());
        $this->assertEquals('boo-baz', $header2->getFieldValue());
    }

    public function testHeadersAggregatesHeaderObjects()
    {
        $fakeHeader = new FakeHeader();
        $headers = new HeadersStub();
        $headers->addHeader($fakeHeader);
        $this->assertEquals(1, $headers->count());
        $this->assertSame($fakeHeader, $headers->get('Fake'));
    }

}
