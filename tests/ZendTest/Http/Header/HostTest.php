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

use Zend\Http\Header\Host;

class HostTest extends \PHPUnit_Framework_TestCase
{

    public function testHostFromStringCreatesValidHostHeader()
    {
        $hostHeader = Host::fromString('Host: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $hostHeader);
        $this->assertInstanceOf('Zend\Http\Header\Host', $hostHeader);
    }

    public function testHostGetFieldNameReturnsHeaderName()
    {
        $hostHeader = new Host();
        $this->assertEquals('Host', $hostHeader->getFieldName());
    }

    public function testHostGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Host needs to be completed');

        $hostHeader = new Host();
        $this->assertEquals('xxx', $hostHeader->getFieldValue());
    }

    public function testHostToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Host needs to be completed');

        $hostHeader = new Host();

        // @todo set some values, then test output
        $this->assertEmpty('Host: xxx', $hostHeader->toString());
    }

    /** Implmentation specific tests here */

}
