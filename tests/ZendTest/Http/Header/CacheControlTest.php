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

use Zend\Http\Header\CacheControl;

class CacheControlTest extends \PHPUnit_Framework_TestCase
{

    public function testCacheControlFromStringCreatesValidCacheControlHeader()
    {
        $cacheControlHeader = CacheControl::fromString('Cache-Control: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $cacheControlHeader);
        $this->assertInstanceOf('Zend\Http\Header\CacheControl', $cacheControlHeader);
    }

    public function testCacheControlGetFieldNameReturnsHeaderName()
    {
        $cacheControlHeader = new CacheControl();
        $this->assertEquals('Cache-Control', $cacheControlHeader->getFieldName());
    }

    public function testCacheControlGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('CacheControl needs to be completed');

        $cacheControlHeader = new CacheControl();
        $this->assertEquals('xxx', $cacheControlHeader->getFieldValue());
    }

    public function testCacheControlToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('CacheControl needs to be completed');

        $cacheControlHeader = new CacheControl();

        // @todo set some values, then test output
        $this->assertEmpty('Cache-Control: xxx', $cacheControlHeader->toString());
    }

    /** Implmentation specific tests here */

    public function testCacheControlIsEmpty()
    {
        $cacheControlHeader = new CacheControl();
        $this->assertTrue($cacheControlHeader->isEmpty());
        $cacheControlHeader->addDirective('xxx');
        $this->assertFalse($cacheControlHeader->isEmpty());
        $cacheControlHeader->removeDirective('xxx');
        $this->assertTrue($cacheControlHeader->isEmpty());
    }

    public function testCacheControlAddHasGetRemove()
    {
        $cacheControlHeader = new CacheControl();
        $cacheControlHeader->addDirective('xxx');
        $this->assertTrue($cacheControlHeader->hasDirective('xxx'));
        $this->assertTrue($cacheControlHeader->getDirective('xxx'));
        $cacheControlHeader->removeDirective('xxx');
        $this->assertFalse($cacheControlHeader->hasDirective('xxx'));
        $this->assertNull($cacheControlHeader->getDirective('xxx'));

        $cacheControlHeader->addDirective('xxx', 'foo');
        $this->assertTrue($cacheControlHeader->hasDirective('xxx'));
        $this->assertEquals('foo', $cacheControlHeader->getDirective('xxx'));
        $cacheControlHeader->removeDirective('xxx');
        $this->assertFalse($cacheControlHeader->hasDirective('xxx'));
        $this->assertNull($cacheControlHeader->getDirective('xxx'));
    }

    public function testCacheControlGetFieldValue()
    {
        $cacheControlHeader = new CacheControl();
        $this->assertEmpty($cacheControlHeader->getFieldValue());
        $cacheControlHeader->addDirective('xxx');
        $this->assertEquals('xxx', $cacheControlHeader->getFieldValue());
        $cacheControlHeader->addDirective('aaa');
        $this->assertEquals('aaa, xxx', $cacheControlHeader->getFieldValue());
        $cacheControlHeader->addDirective('yyy', 'foo');
        $this->assertEquals('aaa, xxx, yyy=foo', $cacheControlHeader->getFieldValue());
        $cacheControlHeader->addDirective('zzz', 'bar, baz');
        $this->assertEquals('aaa, xxx, yyy=foo, zzz="bar, baz"', $cacheControlHeader->getFieldValue());
    }

    public function testCacheControlParse()
    {
        $cacheControlHeader = CacheControl::fromString('Cache-Control: a, b=foo, c="bar, baz"');
        $this->assertTrue($cacheControlHeader->hasDirective('a'));
        $this->assertTrue($cacheControlHeader->getDirective('a'));
        $this->assertTrue($cacheControlHeader->hasDirective('b'));
        $this->assertEquals('foo', $cacheControlHeader->getDirective('b'));
        $this->assertTrue($cacheControlHeader->hasDirective('c'));
        $this->assertEquals('bar, baz', $cacheControlHeader->getDirective('c'));
    }
}
