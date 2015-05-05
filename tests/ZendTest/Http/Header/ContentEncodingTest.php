<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Http\Header;

use Zend\Http\Header\ContentEncoding;

class ContentEncodingTest extends \PHPUnit_Framework_TestCase
{
    public function testContentEncodingFromStringCreatesValidContentEncodingHeader()
    {
        $contentEncodingHeader = ContentEncoding::fromString('Content-Encoding: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $contentEncodingHeader);
        $this->assertInstanceOf('Zend\Http\Header\ContentEncoding', $contentEncodingHeader);
    }

    public function testContentEncodingGetFieldNameReturnsHeaderName()
    {
        $contentEncodingHeader = new ContentEncoding();
        $this->assertEquals('Content-Encoding', $contentEncodingHeader->getFieldName());
    }

    public function testContentEncodingGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('ContentEncoding needs to be completed');

        $contentEncodingHeader = new ContentEncoding();
        $this->assertEquals('xxx', $contentEncodingHeader->getFieldValue());
    }

    public function testContentEncodingToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('ContentEncoding needs to be completed');

        $contentEncodingHeader = new ContentEncoding();

        // @todo set some values, then test output
        $this->assertEmpty('Content-Encoding: xxx', $contentEncodingHeader->toString());
    }

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = ContentEncoding::fromString("Content-Encoding: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = new ContentEncoding("xxx\r\n\r\nevilContent");
    }
}
