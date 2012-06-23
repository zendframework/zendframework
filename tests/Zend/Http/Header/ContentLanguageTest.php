<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\ContentLanguage;

class ContentLanguageTest extends \PHPUnit_Framework_TestCase
{

    public function testContentLanguageFromStringCreatesValidContentLanguageHeader()
    {
        $contentLanguageHeader = ContentLanguage::fromString('Content-Language: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $contentLanguageHeader);
        $this->assertInstanceOf('Zend\Http\Header\ContentLanguage', $contentLanguageHeader);
    }

    public function testContentLanguageGetFieldNameReturnsHeaderName()
    {
        $contentLanguageHeader = new ContentLanguage();
        $this->assertEquals('Content-Language', $contentLanguageHeader->getFieldName());
    }

    public function testContentLanguageGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('ContentLanguage needs to be completed');

        $contentLanguageHeader = new ContentLanguage();
        $this->assertEquals('xxx', $contentLanguageHeader->getFieldValue());
    }

    public function testContentLanguageToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('ContentLanguage needs to be completed');

        $contentLanguageHeader = new ContentLanguage();

        // @todo set some values, then test output
        $this->assertEmpty('Content-Language: xxx', $contentLanguageHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

