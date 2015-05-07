<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Http\Header;

use Zend\Http\Header\UserAgent;

class UserAgentTest extends \PHPUnit_Framework_TestCase
{
    public function testUserAgentFromStringCreatesValidUserAgentHeader()
    {
        $userAgentHeader = UserAgent::fromString('User-Agent: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $userAgentHeader);
        $this->assertInstanceOf('Zend\Http\Header\UserAgent', $userAgentHeader);
    }

    public function testUserAgentGetFieldNameReturnsHeaderName()
    {
        $userAgentHeader = new UserAgent();
        $this->assertEquals('User-Agent', $userAgentHeader->getFieldName());
    }

    public function testUserAgentGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('UserAgent needs to be completed');

        $userAgentHeader = new UserAgent();
        $this->assertEquals('xxx', $userAgentHeader->getFieldValue());
    }

    public function testUserAgentToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('UserAgent needs to be completed');

        $userAgentHeader = new UserAgent();

        // @todo set some values, then test output
        $this->assertEmpty('User-Agent: xxx', $userAgentHeader->toString());
    }

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = UserAgent::fromString("User-Agent: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = new UserAgent("xxx\r\n\r\nevilContent");
    }
}
