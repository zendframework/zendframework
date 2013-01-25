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

use Zend\Http\Header\Trailer;

class TrailerTest extends \PHPUnit_Framework_TestCase
{

    public function testTrailerFromStringCreatesValidTrailerHeader()
    {
        $trailerHeader = Trailer::fromString('Trailer: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $trailerHeader);
        $this->assertInstanceOf('Zend\Http\Header\Trailer', $trailerHeader);
    }

    public function testTrailerGetFieldNameReturnsHeaderName()
    {
        $trailerHeader = new Trailer();
        $this->assertEquals('Trailer', $trailerHeader->getFieldName());
    }

    public function testTrailerGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Trailer needs to be completed');

        $trailerHeader = new Trailer();
        $this->assertEquals('xxx', $trailerHeader->getFieldValue());
    }

    public function testTrailerToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Trailer needs to be completed');

        $trailerHeader = new Trailer();

        // @todo set some values, then test output
        $this->assertEmpty('Trailer: xxx', $trailerHeader->toString());
    }

    /** Implmentation specific tests here */

}
