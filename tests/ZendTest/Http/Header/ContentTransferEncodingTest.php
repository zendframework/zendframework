<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Http
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

    /** Implmentation specific tests here */

}
