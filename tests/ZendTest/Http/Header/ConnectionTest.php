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

use Zend\Http\Header\Connection;

class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    public function testConnectionFromStringCreatesValidConnectionHeader()
    {
        $connectionHeader = Connection::fromString('Connection: close');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $connectionHeader);
        $this->assertInstanceOf('Zend\Http\Header\Connection', $connectionHeader);
        $this->assertEquals('close', $connectionHeader->getFieldValue());
        $this->assertFalse($connectionHeader->isPersistent());
    }

    public function testConnectionGetFieldNameReturnsHeaderName()
    {
        $connectionHeader = new Connection();
        $this->assertEquals('Connection', $connectionHeader->getFieldName());
    }

    public function testConnectionGetFieldValueReturnsProperValue()
    {
        $connectionHeader = new Connection();
        $connectionHeader->setValue('Keep-Alive');
        $this->assertEquals('keep-alive', $connectionHeader->getFieldValue());
    }

    public function testConnectionToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Connection needs to be completed');

        $connectionHeader = new Connection();
        $connectionHeader->setValue('close');
        $this->assertEmpty('Connection: close', $connectionHeader->toString());
    }

    public function testConnectionSetPersistentReturnsProperValue()
    {
        $connectionHeader = new Connection();
        $connectionHeader->setPersistent(true);
        $this->assertEquals('keep-alive', $connectionHeader->getFieldValue());
        $connectionHeader->setPersistent(false);
        $this->assertEquals('close', $connectionHeader->getFieldValue());

    }

}
