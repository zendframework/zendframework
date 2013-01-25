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

use Zend\Http\Header\Warning;

class WarningTest extends \PHPUnit_Framework_TestCase
{

    public function testWarningFromStringCreatesValidWarningHeader()
    {
        $warningHeader = Warning::fromString('Warning: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $warningHeader);
        $this->assertInstanceOf('Zend\Http\Header\Warning', $warningHeader);
    }

    public function testWarningGetFieldNameReturnsHeaderName()
    {
        $warningHeader = new Warning();
        $this->assertEquals('Warning', $warningHeader->getFieldName());
    }

    public function testWarningGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Warning needs to be completed');

        $warningHeader = new Warning();
        $this->assertEquals('xxx', $warningHeader->getFieldValue());
    }

    public function testWarningToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Warning needs to be completed');

        $warningHeader = new Warning();

        // @todo set some values, then test output
        $this->assertEmpty('Warning: xxx', $warningHeader->toString());
    }

    /** Implmentation specific tests here */

}
