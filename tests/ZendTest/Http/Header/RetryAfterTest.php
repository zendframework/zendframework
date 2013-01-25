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

use Zend\Http\Header\RetryAfter;

class RetryAfterTest extends \PHPUnit_Framework_TestCase
{

    public function testRetryAfterFromStringCreatesValidRetryAfterHeader()
    {
        $retryAfterHeader = RetryAfter::fromString('Retry-After: 10');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $retryAfterHeader);
        $this->assertInstanceOf('Zend\Http\Header\RetryAfter', $retryAfterHeader);
        $this->assertEquals('10', $retryAfterHeader->getDeltaSeconds());
    }

    public function testRetryAfterFromStringCreatesValidRetryAfterHeaderFromDate()
    {
        $retryAfterHeader = RetryAfter::fromString('Retry-After: Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertEquals('Sun, 06 Nov 1994 08:49:37 GMT', $retryAfterHeader->getDate());
    }

    public function testRetryAfterGetFieldNameReturnsHeaderName()
    {
        $retryAfterHeader = new RetryAfter();
        $this->assertEquals('Retry-After', $retryAfterHeader->getFieldName());
    }

    public function testRetryAfterGetFieldValueReturnsProperValue()
    {
        $retryAfterHeader = new RetryAfter();
        $retryAfterHeader->setDeltaSeconds(3600);
        $this->assertEquals('3600', $retryAfterHeader->getFieldValue());
        $retryAfterHeader->setDate('Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertEquals('Sun, 06 Nov 1994 08:49:37 GMT', $retryAfterHeader->getFieldValue());
    }

    public function testRetryAfterToStringReturnsHeaderFormattedString()
    {
        $retryAfterHeader = new RetryAfter();

        $retryAfterHeader->setDeltaSeconds(3600);
        $this->assertEquals('Retry-After: 3600', $retryAfterHeader->toString());

        $retryAfterHeader->setDate('Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertEquals('Retry-After: Sun, 06 Nov 1994 08:49:37 GMT', $retryAfterHeader->toString());

    }
}
