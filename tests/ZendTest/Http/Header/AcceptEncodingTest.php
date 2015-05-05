<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Http\Header;

use Zend\Http\Header\AcceptEncoding;

class AcceptEncodingTest extends \PHPUnit_Framework_TestCase
{
    public function testAcceptEncodingFromStringCreatesValidAcceptEncodingHeader()
    {
        $acceptEncodingHeader = AcceptEncoding::fromString('Accept-Encoding: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $acceptEncodingHeader);
        $this->assertInstanceOf('Zend\Http\Header\AcceptEncoding', $acceptEncodingHeader);
    }

    public function testAcceptEncodingGetFieldNameReturnsHeaderName()
    {
        $acceptEncodingHeader = new AcceptEncoding();
        $this->assertEquals('Accept-Encoding', $acceptEncodingHeader->getFieldName());
    }

    public function testAcceptEncodingGetFieldValueReturnsProperValue()
    {
        $acceptEncodingHeader = AcceptEncoding::fromString('Accept-Encoding: xxx');
        $this->assertEquals('xxx', $acceptEncodingHeader->getFieldValue());
    }

    public function testAcceptEncodingToStringReturnsHeaderFormattedString()
    {
        $acceptEncodingHeader = new AcceptEncoding();
        $acceptEncodingHeader->addEncoding('compress', 0.5)
                             ->addEncoding('gzip', 1);

        $this->assertEquals('Accept-Encoding: compress;q=0.5, gzip', $acceptEncodingHeader->toString());
    }

    /** Implementation specific tests here */

    public function testCanParseCommaSeparatedValues()
    {
        $header = AcceptEncoding::fromString('Accept-Encoding: compress;q=0.5,gzip');
        $this->assertTrue($header->hasEncoding('compress'));
        $this->assertTrue($header->hasEncoding('gzip'));
    }

    public function testPrioritizesValuesBasedOnQParameter()
    {
        $header   = AcceptEncoding::fromString('Accept-Encoding: compress;q=0.8,gzip,*;q=0.4');
        $expected = array(
            'gzip',
            'compress',
            '*'
        );

        $test = array();
        foreach ($header->getPrioritized() as $type) {
            $this->assertEquals(array_shift($expected), $type->getEncoding());
        }
    }

    public function testWildcharEncoder()
    {
        $acceptHeader = new AcceptEncoding();
        $acceptHeader->addEncoding('compress', 0.8)
                     ->addEncoding('*', 0.4);

        $this->assertTrue($acceptHeader->hasEncoding('compress'));
        $this->assertTrue($acceptHeader->hasEncoding('gzip'));
        $this->assertEquals('Accept-Encoding: compress;q=0.8, *;q=0.4', $acceptHeader->toString());
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = AcceptEncoding::fromString("Accept-Encoding: compress\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaSetters()
    {
        $header = new AcceptEncoding();
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException', 'valid type');
        $header->addEncoding("\nc\rom\r\npress");
    }
}
