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

use Zend\Http\Header\IfMatch;

class IfMatchTest extends \PHPUnit_Framework_TestCase
{

    public function testIfMatchFromStringCreatesValidIfMatchHeader()
    {
        $ifMatchHeader = IfMatch::fromString('If-Match: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $ifMatchHeader);
        $this->assertInstanceOf('Zend\Http\Header\IfMatch', $ifMatchHeader);
    }

    public function testIfMatchGetFieldNameReturnsHeaderName()
    {
        $ifMatchHeader = new IfMatch();
        $this->assertEquals('If-Match', $ifMatchHeader->getFieldName());
    }

    public function testIfMatchGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('IfMatch needs to be completed');

        $ifMatchHeader = new IfMatch();
        $this->assertEquals('xxx', $ifMatchHeader->getFieldValue());
    }

    public function testIfMatchToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('IfMatch needs to be completed');

        $ifMatchHeader = new IfMatch();

        // @todo set some values, then test output
        $this->assertEmpty('If-Match: xxx', $ifMatchHeader->toString());
    }

    /** Implmentation specific tests here */

}
