<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Http\Header;

use Zend\Http\Header\ContentTransferEncoding;

class ContentTransferEncodingTest extends \PHPUnit_Framework_TestCase
{
    public function testContentTransferEncodingFromStringCreatesValidContentTransferEncodingHeader()
    {
        $contentTransferEncodingHeader = ContentTransferEncoding::fromString('Content-Transfer-Encoding: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $contentTransferEncodingHeader);
        $this->assertInstanceOf('Zend\Http\Header\ContentTransferEncoding', $contentTransferEncodingHeader);
    }

    public function testContentTransferEncodingGetFieldNameReturnsHeaderName()
    {
        $contentTransferEncodingHeader = new ContentTransferEncoding();
        $this->assertEquals('Content-Transfer-Encoding', $contentTransferEncodingHeader->getFieldName());
    }

    public function testContentTransferEncodingGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('ContentTransferEncoding needs to be completed');

        $contentTransferEncodingHeader = new ContentTransferEncoding();
        $this->assertEquals('xxx', $contentTransferEncodingHeader->getFieldValue());
    }

    public function testContentTransferEncodingToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('ContentTransferEncoding needs to be completed');

        $contentTransferEncodingHeader = new ContentTransferEncoding();

        // @todo set some values, then test output
        $this->assertEmpty('Content-Transfer-Encoding: xxx', $contentTransferEncodingHeader->toString());
    }

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = ContentTransferEncoding::fromString("Content-Transfer-Encoding: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = new ContentTransferEncoding("xxx\r\n\r\nevilContent");
    }
}
