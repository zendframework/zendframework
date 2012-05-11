<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\Via;

class ViaTest extends \PHPUnit_Framework_TestCase
{

    public function testViaFromStringCreatesValidViaHeader()
    {
        $viaHeader = Via::fromString('Via: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $viaHeader);
        $this->assertInstanceOf('Zend\Http\Header\Via', $viaHeader);
    }

    public function testViaGetFieldNameReturnsHeaderName()
    {
        $viaHeader = new Via();
        $this->assertEquals('Via', $viaHeader->getFieldName());
    }

    public function testViaGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Via needs to be completed');

        $viaHeader = new Via();
        $this->assertEquals('xxx', $viaHeader->getFieldValue());
    }

    public function testViaToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Via needs to be completed');

        $viaHeader = new Via();

        // @todo set some values, then test output
        $this->assertEmpty('Via: xxx', $viaHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

