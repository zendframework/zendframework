<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Http\Header;

use Zend\Http\Header\Pragma;

class PragmaTest extends \PHPUnit_Framework_TestCase
{
    public function testPragmaFromStringCreatesValidPragmaHeader()
    {
        $pragmaHeader = Pragma::fromString('Pragma: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $pragmaHeader);
        $this->assertInstanceOf('Zend\Http\Header\Pragma', $pragmaHeader);
    }

    public function testPragmaGetFieldNameReturnsHeaderName()
    {
        $pragmaHeader = new Pragma();
        $this->assertEquals('Pragma', $pragmaHeader->getFieldName());
    }

    public function testPragmaGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Pragma needs to be completed');

        $pragmaHeader = new Pragma();
        $this->assertEquals('xxx', $pragmaHeader->getFieldValue());
    }

    public function testPragmaToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Pragma needs to be completed');

        $pragmaHeader = new Pragma();

        // @todo set some values, then test output
        $this->assertEmpty('Pragma: xxx', $pragmaHeader->toString());
    }

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = Pragma::fromString("Pragma: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = new Pragma("xxx\r\n\r\nevilContent");
    }
}
