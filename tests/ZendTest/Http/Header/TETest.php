<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Http\Header;

use Zend\Http\Header\TE;

class TETest extends \PHPUnit_Framework_TestCase
{
    public function testTEFromStringCreatesValidTEHeader()
    {
        $tEHeader = TE::fromString('TE: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $tEHeader);
        $this->assertInstanceOf('Zend\Http\Header\TE', $tEHeader);
    }

    public function testTEGetFieldNameReturnsHeaderName()
    {
        $tEHeader = new TE();
        $this->assertEquals('TE', $tEHeader->getFieldName());
    }

    public function testTEGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('TE needs to be completed');

        $tEHeader = new TE();
        $this->assertEquals('xxx', $tEHeader->getFieldValue());
    }

    public function testTEToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('TE needs to be completed');

        $tEHeader = new TE();

        // @todo set some values, then test output
        $this->assertEmpty('TE: xxx', $tEHeader->toString());
    }

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = TE::fromString("TE: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = new TE("xxx\r\n\r\nevilContent");
    }
}
