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
        $this->markTestIncomplete('Authorization needs to be completed');

        $authorizationHeader = new Authorization();
        $this->assertEquals('xxx', $authorizationHeader->getFieldValue());
    }

    public function testAuthorizationToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Authorization needs to be completed');

        $authorizationHeader = new Authorization();

        // @todo set some values, then test output
        $this->assertEmpty('Authorization: xxx', $authorizationHeader->toString());
    }

    /** Implmentation specific tests here */

}
