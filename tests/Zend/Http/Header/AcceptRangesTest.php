<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\AcceptRanges;

class AcceptRangesTest extends \PHPUnit_Framework_TestCase
{

    public function testAcceptRangesFromStringCreatesValidAcceptRangesHeader()
    {
        $acceptRangesHeader = AcceptRanges::fromString('Accept-Ranges: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $acceptRangesHeader);
        $this->assertInstanceOf('Zend\Http\Header\AcceptRanges', $acceptRangesHeader);
    }

    public function testAcceptRangesGetFieldNameReturnsHeaderName()
    {
        $acceptRangesHeader = new AcceptRanges();
        $this->assertEquals('Accept-Ranges', $acceptRangesHeader->getFieldName());
    }

    public function testAcceptRangesGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('AcceptRanges needs to be completed');

        $acceptRangesHeader = new AcceptRanges();
        $this->assertEquals('xxx', $acceptRangesHeader->getFieldValue());
    }

    public function testAcceptRangesToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('AcceptRanges needs to be completed');

        $acceptRangesHeader = new AcceptRanges();

        // @todo set some values, then test output
        $this->assertEmpty('Accept-Ranges: xxx', $acceptRangesHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

