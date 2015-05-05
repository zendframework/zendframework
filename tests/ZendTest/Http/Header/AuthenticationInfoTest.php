<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Http\Header;

use Zend\Http\Header\AuthenticationInfo;

class AuthenticationInfoTest extends \PHPUnit_Framework_TestCase
{
    public function testAuthenticationInfoFromStringCreatesValidAuthenticationInfoHeader()
    {
        $authenticationInfoHeader = AuthenticationInfo::fromString('Authentication-Info: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $authenticationInfoHeader);
        $this->assertInstanceOf('Zend\Http\Header\AuthenticationInfo', $authenticationInfoHeader);
    }

    public function testAuthenticationInfoGetFieldNameReturnsHeaderName()
    {
        $authenticationInfoHeader = new AuthenticationInfo();
        $this->assertEquals('Authentication-Info', $authenticationInfoHeader->getFieldName());
    }

    public function testAuthenticationInfoGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('AuthenticationInfo needs to be completed');

        $authenticationInfoHeader = new AuthenticationInfo();
        $this->assertEquals('xxx', $authenticationInfoHeader->getFieldValue());
    }

    public function testAuthenticationInfoToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('AuthenticationInfo needs to be completed');

        $authenticationInfoHeader = new AuthenticationInfo();

        // @todo set some values, then test output
        $this->assertEmpty('Authentication-Info: xxx', $authenticationInfoHeader->toString());
    }

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = AuthenticationInfo::fromString("Authentication-Info: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = new AuthenticationInfo("xxx\r\n\r\nevilContent");
    }
}
