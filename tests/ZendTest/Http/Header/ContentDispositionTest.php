<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Http\Header;

use Zend\Http\Header\ContentDisposition;

class ContentDispositionTest extends \PHPUnit_Framework_TestCase
{
    public function testContentDispositionFromStringCreatesValidContentDispositionHeader()
    {
        $contentDispositionHeader = ContentDisposition::fromString('Content-Disposition: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $contentDispositionHeader);
        $this->assertInstanceOf('Zend\Http\Header\ContentDisposition', $contentDispositionHeader);
    }

    public function testContentDispositionGetFieldNameReturnsHeaderName()
    {
        $contentDispositionHeader = new ContentDisposition();
        $this->assertEquals('Content-Disposition', $contentDispositionHeader->getFieldName());
    }

    public function testContentDispositionGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('ContentDisposition needs to be completed');

        $contentDispositionHeader = new ContentDisposition();
        $this->assertEquals('xxx', $contentDispositionHeader->getFieldValue());
    }

    public function testContentDispositionToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('ContentDisposition needs to be completed');

        $contentDispositionHeader = new ContentDisposition();

        // @todo set some values, then test output
        $this->assertEmpty('Content-Disposition: xxx', $contentDispositionHeader->toString());
    }

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = ContentDisposition::fromString("Content-Disposition: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = new ContentDisposition("xxx\r\n\r\nevilContent");
    }
}
