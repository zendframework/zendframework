<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\GenericHeader,
    Zend\Http\Header\Accept;

class AcceptTest extends \PHPUnit_Framework_TestCase
{

    public function testAcceptFromStringCreatesValidAcceptHeader()
    {
        $acceptHeader = Accept::fromString('Accept: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $acceptHeader);
        $this->assertInstanceOf('Zend\Http\Header\Accept', $acceptHeader);
    }

    public function testAcceptGetFieldNameReturnsHeaderName()
    {
        $acceptHeader = new Accept();
        $this->assertEquals('Accept', $acceptHeader->getFieldName());
    }

    public function testAcceptGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Accept needs to be completed');

        $acceptHeader = new Accept();
        $this->assertEquals('xxx', $acceptHeader->getFieldValue());
    }

    public function testAcceptToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Accept needs to be completed');

        $acceptHeader = new Accept();

        // @todo set some values, then test output
        $this->assertEmpty('Accept: xxx', $acceptHeader->toString());
    }

    /** Implmentation specific tests here */

    public function testCanParseCommaSeparatedValues()
    {
        $header = Accept::fromString('Accept: text/plain; q=0.5, text/html, text/x-dvi; q=0.8, text/x-c');
        $this->assertTrue($header->hasMediaType('text/plain'));
        $this->assertTrue($header->hasMediaType('text/html'));
        $this->assertTrue($header->hasMediaType('text/x-dvi'));
        $this->assertTrue($header->hasMediaType('text/x-c'));
    }

    public function testPrioritizesValuesBasedOnQParameter()
    {
        $header   = Accept::fromString('Accept: text/plain; q=0.5, text/html, text/xml; q=0, text/x-dvi; q=0.8, text/x-c');
        $expected = array(
            'text/html',
            'text/x-c',
            'text/x-dvi',
            'text/plain',
        );

        $test = array();
        foreach($header->getPrioritized() as $type) {
            $test[] = $type;
        }
        $this->assertEquals($expected, $test);
    }
}
