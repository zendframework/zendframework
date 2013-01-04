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

use Zend\Http\Header\IfNoneMatch;

class IfNoneMatchTest extends \PHPUnit_Framework_TestCase
{

    public function testIfNoneMatchFromStringCreatesValidIfNoneMatchHeader()
    {
        $ifNoneMatchHeader = IfNoneMatch::fromString('If-None-Match: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $ifNoneMatchHeader);
        $this->assertInstanceOf('Zend\Http\Header\IfNoneMatch', $ifNoneMatchHeader);
    }

    public function testIfNoneMatchGetFieldNameReturnsHeaderName()
    {
        $ifNoneMatchHeader = new IfNoneMatch();
        $this->assertEquals('If-None-Match', $ifNoneMatchHeader->getFieldName());
    }

    public function testIfNoneMatchGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('IfNoneMatch needs to be completed');

        $ifNoneMatchHeader = new IfNoneMatch();
        $this->assertEquals('xxx', $ifNoneMatchHeader->getFieldValue());
    }

    public function testIfNoneMatchToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('IfNoneMatch needs to be completed');

        $ifNoneMatchHeader = new IfNoneMatch();

        // @todo set some values, then test output
        $this->assertEmpty('If-None-Match: xxx', $ifNoneMatchHeader->toString());
    }

    /** Implmentation specific tests here */

}
