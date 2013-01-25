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

use Zend\Http\Header\IfRange;

class IfRangeTest extends \PHPUnit_Framework_TestCase
{

    public function testIfRangeFromStringCreatesValidIfRangeHeader()
    {
        $ifRangeHeader = IfRange::fromString('If-Range: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $ifRangeHeader);
        $this->assertInstanceOf('Zend\Http\Header\IfRange', $ifRangeHeader);
    }

    public function testIfRangeGetFieldNameReturnsHeaderName()
    {
        $ifRangeHeader = new IfRange();
        $this->assertEquals('If-Range', $ifRangeHeader->getFieldName());
    }

    public function testIfRangeGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('IfRange needs to be completed');

        $ifRangeHeader = new IfRange();
        $this->assertEquals('xxx', $ifRangeHeader->getFieldValue());
    }

    public function testIfRangeToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('IfRange needs to be completed');

        $ifRangeHeader = new IfRange();

        // @todo set some values, then test output
        $this->assertEmpty('If-Range: xxx', $ifRangeHeader->toString());
    }

    /** Implmentation specific tests here */

}
