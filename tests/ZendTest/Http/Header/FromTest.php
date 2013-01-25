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

use Zend\Http\Header\From;

class FromTest extends \PHPUnit_Framework_TestCase
{

    public function testFromFromStringCreatesValidFromHeader()
    {
        $fromHeader = From::fromString('From: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $fromHeader);
        $this->assertInstanceOf('Zend\Http\Header\From', $fromHeader);
    }

    public function testFromGetFieldNameReturnsHeaderName()
    {
        $fromHeader = new From();
        $this->assertEquals('From', $fromHeader->getFieldName());
    }

    public function testFromGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('From needs to be completed');

        $fromHeader = new From();
        $this->assertEquals('xxx', $fromHeader->getFieldValue());
    }

    public function testFromToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('From needs to be completed');

        $fromHeader = new From();

        // @todo set some values, then test output
        $this->assertEmpty('From: xxx', $fromHeader->toString());
    }

    /** Implmentation specific tests here */

}
