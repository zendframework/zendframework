<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Http\Header;

use Zend\Http\Header\ContentRange;

class ContentRangeTest extends \PHPUnit_Framework_TestCase
{
    public function testContentRangeFromStringCreatesValidContentRangeHeader()
    {
        $contentRangeHeader = ContentRange::fromString('Content-Range: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $contentRangeHeader);
        $this->assertInstanceOf('Zend\Http\Header\ContentRange', $contentRangeHeader);
    }

    public function testContentRangeGetFieldNameReturnsHeaderName()
    {
        $contentRangeHeader = new ContentRange();
        $this->assertEquals('Content-Range', $contentRangeHeader->getFieldName());
    }

    public function testContentRangeGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('ContentRange needs to be completed');

        $contentRangeHeader = new ContentRange();
        $this->assertEquals('xxx', $contentRangeHeader->getFieldValue());
    }

    public function testContentRangeToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('ContentRange needs to be completed');

        $contentRangeHeader = new ContentRange();

        // @todo set some values, then test output
        $this->assertEmpty('Content-Range: xxx', $contentRangeHeader->toString());
    }

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = ContentRange::fromString("Content-Range: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = new ContentRange("xxx\r\n\r\nevilContent");
    }
}
