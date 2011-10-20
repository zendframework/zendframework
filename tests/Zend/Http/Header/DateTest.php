<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\Date;

class DateTest extends \PHPUnit_Framework_TestCase
{

    public function testDateFromStringCreatesValidDateHeader()
    {
        $dateHeader = Date::fromString('Date: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $dateHeader);
        $this->assertInstanceOf('Zend\Http\Header\Date', $dateHeader);
    }

    public function testDateGetFieldNameReturnsHeaderName()
    {
        $dateHeader = new Date();
        $this->assertEquals('Date', $dateHeader->getFieldName());
    }

    public function testDateGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Date needs to be completed');

        $dateHeader = new Date();
        $this->assertEquals('xxx', $dateHeader->getFieldValue());
    }

    public function testDateToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Date needs to be completed');

        $dateHeader = new Date();

        // @todo set some values, then test output
        $this->assertEmpty('Date: xxx', $dateHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

