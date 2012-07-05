<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\From;

class FromTest extends \PHPUnit_Framework_TestCase
{

    public function testFromFromStringCreatesValidFromHeader()
    {
        $fromHeader = From::fromString('From: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $fromHeader);
        $this->assertInstanceOf('Zend\Http\Header\From', $fromHeader);
    }

    public function testFromGetFieldNameReturnsHeaderName()
    {
        $fromHeader = new From();
        $this->assertEquals('From', $fromHeader->getFieldName());
    }

    public function testFromGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('From needs to be completed');

        $fromHeader = new From();
        $this->assertEquals('xxx', $fromHeader->getFieldValue());
    }

    public function testFromToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('From needs to be completed');

        $fromHeader = new From();

        // @todo set some values, then test output
        $this->assertEmpty('From: xxx', $fromHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

