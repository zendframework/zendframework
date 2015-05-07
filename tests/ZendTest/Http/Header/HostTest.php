<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
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

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = Host::fromString("Host: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = new Host("xxx\r\n\r\nevilContent");
    }
}
