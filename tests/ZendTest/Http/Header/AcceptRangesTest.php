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

    /** Implmentation specific tests here */

}
