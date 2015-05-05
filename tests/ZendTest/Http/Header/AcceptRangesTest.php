<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Http\Header;

use Zend\Http\Header\AcceptRanges;

class AcceptRangesTest extends \PHPUnit_Framework_TestCase
{
    public function testAcceptRangesFromStringCreatesValidAcceptRangesHeader()
    {
        $acceptRangesHeader = AcceptRanges::fromString('Accept-Ranges: bytes');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $acceptRangesHeader);
        $this->assertInstanceOf('Zend\Http\Header\AcceptRanges', $acceptRangesHeader);
    }

    public function testAcceptRangesGetFieldNameReturnsHeaderName()
    {
        $acceptRangesHeader = new AcceptRanges();
        $this->assertEquals('Accept-Ranges', $acceptRangesHeader->getFieldName());
    }

    public function testAcceptRangesGetFieldValueReturnsProperValue()
    {
        $acceptRangesHeader = AcceptRanges::fromString('Accept-Ranges: bytes');
        $this->assertEquals('bytes', $acceptRangesHeader->getFieldValue());
        $this->assertEquals('bytes', $acceptRangesHeader->getRangeUnit());
    }

    public function testAcceptRangesToStringReturnsHeaderFormattedString()
    {
        $acceptRangesHeader = new AcceptRanges();
        $acceptRangesHeader->setRangeUnit('bytes');

        // @todo set some values, then test output
        $this->assertEquals('Accept-Ranges: bytes', $acceptRangesHeader->toString());
    }

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = AcceptRanges::fromString("Accept-Ranges: bytes;\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = new AcceptRanges("bytes;\r\n\r\nevilContent");
    }
}
