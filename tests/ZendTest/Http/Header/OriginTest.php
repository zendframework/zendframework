<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Http\Header;

use Zend\Http\Header\Origin;

class OriginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group 6484
     */
    public function testOriginFieldValueIsAlwaysAString()
    {
        $origin = new Origin();

        $this->assertInternalType('string', $origin->getFieldValue());
    }

    public function testOriginFromStringCreatesValidOriginHeader()
    {
        $OriginHeader = Origin::fromString('Origin: http://zend.org');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $OriginHeader);
        $this->assertInstanceOf('Zend\Http\Header\Origin', $OriginHeader);
    }

    public function testOriginGetFieldNameReturnsHeaderName()
    {
        $OriginHeader = new Origin();
        $this->assertEquals('Origin', $OriginHeader->getFieldName());
    }

    public function testOriginGetFieldValueReturnsProperValue()
    {
        $OriginHeader = Origin::fromString('Origin: http://zend.org');
        $this->assertEquals('http://zend.org', $OriginHeader->getFieldValue());
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     * @expectedException Zend\Uri\Exception\InvalidUriPartException
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $header = Origin::fromString("Origin: http://zend.org\r\n\r\nevilContent");
    }

    /**
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = new Origin("http://zend.org\r\n\r\nevilContent");
    }
}
