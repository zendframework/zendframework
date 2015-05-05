<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Http\Header;

use Zend\Http\Header\KeepAlive;

class KeepAliveTest extends \PHPUnit_Framework_TestCase
{
    public function testKeepAliveFromStringCreatesValidKeepAliveHeader()
    {
        $keepAliveHeader = KeepAlive::fromString('Keep-Alive: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $keepAliveHeader);
        $this->assertInstanceOf('Zend\Http\Header\KeepAlive', $keepAliveHeader);
    }

    public function testKeepAliveGetFieldNameReturnsHeaderName()
    {
        $keepAliveHeader = new KeepAlive();
        $this->assertEquals('Keep-Alive', $keepAliveHeader->getFieldName());
    }

    public function testKeepAliveGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('KeepAlive needs to be completed');

        $keepAliveHeader = new KeepAlive();
        $this->assertEquals('xxx', $keepAliveHeader->getFieldValue());
    }

    public function testKeepAliveToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('KeepAlive needs to be completed');

        $keepAliveHeader = new KeepAlive();

        // @todo set some values, then test output
        $this->assertEmpty('Keep-Alive: xxx', $keepAliveHeader->toString());
    }

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = KeepAlive::fromString("Keep-Alive: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = new KeepAlive("xxx\r\n\r\nevilContent");
    }
}
