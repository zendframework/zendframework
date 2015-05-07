<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Http\Header;

use Zend\Http\Header\Via;

class ViaTest extends \PHPUnit_Framework_TestCase
{
    public function testViaFromStringCreatesValidViaHeader()
    {
        $viaHeader = Via::fromString('Via: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $viaHeader);
        $this->assertInstanceOf('Zend\Http\Header\Via', $viaHeader);
    }

    public function testViaGetFieldNameReturnsHeaderName()
    {
        $viaHeader = new Via();
        $this->assertEquals('Via', $viaHeader->getFieldName());
    }

    public function testViaGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Via needs to be completed');

        $viaHeader = new Via();
        $this->assertEquals('xxx', $viaHeader->getFieldValue());
    }

    public function testViaToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Via needs to be completed');

        $viaHeader = new Via();

        // @todo set some values, then test output
        $this->assertEmpty('Via: xxx', $viaHeader->toString());
    }

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = Via::fromString("Via: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = new Via("xxx\r\n\r\nevilContent");
    }
}
