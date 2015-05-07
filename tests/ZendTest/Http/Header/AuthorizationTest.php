<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Http\Header;

use Zend\Http\Header\Authorization;

class AuthorizationTest extends \PHPUnit_Framework_TestCase
{
    public function testAuthorizationFromStringCreatesValidAuthorizationHeader()
    {
        $authorizationHeader = Authorization::fromString('Authorization: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $authorizationHeader);
        $this->assertInstanceOf('Zend\Http\Header\Authorization', $authorizationHeader);
    }

    public function testAuthorizationGetFieldNameReturnsHeaderName()
    {
        $authorizationHeader = new Authorization();
        $this->assertEquals('Authorization', $authorizationHeader->getFieldName());
    }

    public function testAuthorizationGetFieldValueReturnsProperValue()
    {
        $authorizationHeader = new Authorization('xxx');
        $this->assertEquals('xxx', $authorizationHeader->getFieldValue());
    }

    public function testAuthorizationToStringReturnsHeaderFormattedString()
    {
        $authorizationHeader = new Authorization('xxx');
        $this->assertEquals('Authorization: xxx', $authorizationHeader->toString());

        $authorizationHeader = Authorization::fromString('Authorization: xxx2');
        $this->assertEquals('Authorization: xxx2', $authorizationHeader->toString());
    }

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = Authorization::fromString("Authorization: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = new Authorization("xxx\r\n\r\nevilContent");
    }
}
