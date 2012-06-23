<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\TE;

class TETest extends \PHPUnit_Framework_TestCase
{

    public function testTEFromStringCreatesValidTEHeader()
    {
        $tEHeader = TE::fromString('TE: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $tEHeader);
        $this->assertInstanceOf('Zend\Http\Header\TE', $tEHeader);
    }

    public function testTEGetFieldNameReturnsHeaderName()
    {
        $tEHeader = new TE();
        $this->assertEquals('TE', $tEHeader->getFieldName());
    }

    public function testTEGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('TE needs to be completed');

        $tEHeader = new TE();
        $this->assertEquals('xxx', $tEHeader->getFieldValue());
    }

    public function testTEToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('TE needs to be completed');

        $tEHeader = new TE();

        // @todo set some values, then test output
        $this->assertEmpty('TE: xxx', $tEHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

