<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Http\Header;

use Zend\Http\Header\ContentLength;

class ContentLengthTest extends \PHPUnit_Framework_TestCase
{
    public function testContentLengthFromStringCreatesValidContentLengthHeader()
    {
        $contentLengthHeader = ContentLength::fromString('Content-Length: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $contentLengthHeader);
        $this->assertInstanceOf('Zend\Http\Header\ContentLength', $contentLengthHeader);
    }

    public function testContentLengthGetFieldNameReturnsHeaderName()
    {
        $contentLengthHeader = new ContentLength();
        $this->assertEquals('Content-Length', $contentLengthHeader->getFieldName());
    }

    public function testContentLengthGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('ContentLength needs to be completed');

        $contentLengthHeader = new ContentLength();
        $this->assertEquals('xxx', $contentLengthHeader->getFieldValue());
    }

    public function testContentLengthToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('ContentLength needs to be completed');

        $contentLengthHeader = new ContentLength();

        // @todo set some values, then test output
        $this->assertEmpty('Content-Length: xxx', $contentLengthHeader->toString());
    }

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = ContentLength::fromString("Content-Length: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = new ContentLength("Content-Length: xxx\r\n\r\nevilContent");
    }
}
