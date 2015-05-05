<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Http\Header;

use Zend\Http\Header\Etag;

class EtagTest extends \PHPUnit_Framework_TestCase
{
    public function testEtagFromStringCreatesValidEtagHeader()
    {
        $etagHeader = Etag::fromString('Etag: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $etagHeader);
        $this->assertInstanceOf('Zend\Http\Header\Etag', $etagHeader);
    }

    public function testEtagGetFieldNameReturnsHeaderName()
    {
        $etagHeader = new Etag();
        $this->assertEquals('Etag', $etagHeader->getFieldName());
    }

    public function testEtagGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Etag needs to be completed');

        $etagHeader = new Etag();
        $this->assertEquals('xxx', $etagHeader->getFieldValue());
    }

    public function testEtagToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Etag needs to be completed');

        $etagHeader = new Etag();

        // @todo set some values, then test output
        $this->assertEmpty('Etag: xxx', $etagHeader->toString());
    }

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = Etag::fromString("Etag: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = new Etag("xxx\r\n\r\nevilContent");
    }
}
