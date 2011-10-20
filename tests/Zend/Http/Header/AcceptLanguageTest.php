<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\AcceptLanguage;

class AcceptLanguageTest extends \PHPUnit_Framework_TestCase
{

    public function testAcceptLanguageFromStringCreatesValidAcceptLanguageHeader()
    {
        $acceptLanguageHeader = AcceptLanguage::fromString('Accept-Language: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $acceptLanguageHeader);
        $this->assertInstanceOf('Zend\Http\Header\AcceptLanguage', $acceptLanguageHeader);
    }

    public function testAcceptLanguageGetFieldNameReturnsHeaderName()
    {
        $acceptLanguageHeader = new AcceptLanguage();
        $this->assertEquals('Accept-Language', $acceptLanguageHeader->getFieldName());
    }

    public function testAcceptLanguageGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('AcceptLanguage needs to be completed');

        $acceptLanguageHeader = new AcceptLanguage();
        $this->assertEquals('xxx', $acceptLanguageHeader->getFieldValue());
    }

    public function testAcceptLanguageToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('AcceptLanguage needs to be completed');

        $acceptLanguageHeader = new AcceptLanguage();

        // @todo set some values, then test output
        $this->assertEmpty('Accept-Language: xxx', $acceptLanguageHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

