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
        $acceptHeader = Accept::fromString('Accept: xxx');
        $this->assertEquals('xxx', $acceptHeader->getFieldValue());
    }

    public function testAcceptToStringReturnsHeaderFormattedString()
    {
        $acceptHeader = new Accept();
        $acceptHeader->addMediaType('text/html', 0.8)
                     ->addMediaType('application/json', 1)
                     ->addMediaType('application/atom+xml', 0.9);

        // @todo set some values, then test output
        $this->assertEquals('Accept: text/html;q=0.8,application/json,application/atom+xml;q=0.9', $acceptHeader->toString());
    }

    /** Implementation specific tests here */

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
    
    public function testLevel()
    {
        $acceptHeader = new Accept();
        $acceptHeader->addMediaType('text/html', 0.8, 1)
                     ->addMediaType('text/html', 0.4, 2)
                     ->addMediaType('application/atom+xml', 0.9);

        $this->assertEquals('Accept: text/html;level=1;q=0.8,text/html;level=2;q=0.4,application/atom+xml;q=0.9', $acceptHeader->toString());
    }
    
    public function testPrioritizedLevel()
    {
        $header = Accept::fromString('Accept: text/*;q=0.3, text/html;q=0.7, text/html;level=1,text/html;level=2;q=0.4, */*;q=0.5');
        
        $expected = array (
            'text/html;level=1',
            'text/html',
            '*/*',
            'text/html;level=2',
            'text/*'
        );
        
        $test = array();
        foreach($header->getPrioritized() as $type) {
            $test[] = $type;
        }
        $this->assertEquals($expected, $test);
    }
    
    public function testWildcharMediaType()
    {
        $acceptHeader = new Accept();
        $acceptHeader->addMediaType('text/*', 0.8)
                     ->addMediaType('application/*', 1)
                     ->addMediaType('*/*', 0.4);
        
        $this->assertTrue($acceptHeader->hasMediaType('text/html'));
        $this->assertTrue($acceptHeader->hasMediaType('application/atom+xml'));
        $this->assertTrue($acceptHeader->hasMediaType('audio/basic'));
        $this->assertEquals('Accept: text/*;q=0.8,application/*,*/*;q=0.4', $acceptHeader->toString());
    }
}
