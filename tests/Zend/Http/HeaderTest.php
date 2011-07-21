<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Http_Cookie
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Http;

/**
 * Zend_Http_Header unit tests
 *
 * @category   Zend
 * @package    Zend_Http_Header
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Http
 * @group      Zend_Http_Cookie
 */
class HeaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test construct with type
     */
    public function testConstructWithType()
    {
        $header= new \Zend\Http\Header('Accept');
        $this->assertEquals($header->getType(),'Accept');
    }
    /**
     * Test construct with type and value
     */
    public function testConstructWithTypeValue()
    {
        $header= new \Zend\Http\Header('Accept','text/html');
        $this->assertEquals($header->getType(),'Accept');
        $this->assertEquals($header->getValue(),'text/html');
    }
    /**
     * Test construct with a header encoded in a raw string
     */
    public function testConstructWithRawString()
    {
        $header= new \Zend\Http\Header('Accept: text/html');
        $this->assertEquals($header->getType(),'Accept');
        $this->assertEquals($header->getValue(),'text/html');
    }
    /**
     * Test construct with Accept-Charset type and multiple values
     */
    public function testConstructAcceptMultipleValue()
    {
        $header= new \Zend\Http\Header('Accept-Charset: iso-8859-1, utf-8');
        $this->assertEquals($header->getValue(),'iso-8859-1, utf-8');
    }
    /**
     * Test normalize header type
     */
    public function testNormalizeHeaderType()
    {
        $header= new \Zend\Http\Header('accept');
        $this->assertEquals($header->getType(),'Accept');
        $header->setType('Accept charset');
        $this->assertEquals($header->getType(),'Accept-Charset');
    }
    /**
     * Test load header from a raw string
     */
    public function testLoadFromString()
    {
        $header= new \Zend\Http\Header('Accept');
        $this->assertTrue($header->fromString('Accept: text/html'));
        $this->assertEquals($header->getType(),'Accept');
        $this->assertEquals($header->getValue(),'text/html');
    }
    /**
     * Test to string
     */
    public function testToString()
    {
        $header= new \Zend\Http\Header('Accept','text/html');
        $this->assertEquals((string) $header,"Accept: text/html\r\n");
    }
    /**
     * Test load header from an invalid raw string
     */
    public function testLoadFromInvalidString()
    {
        $header= new \Zend\Http\Header('Accept');
        $this->setExpectedException(
            'Zend\Http\Exception\InvalidArgumentException',
            'The header specified is not valid'
        );
        $header->fromString('text/html');
    }
    /**
     * Test set type
     */
    public function testSetType()
    {
        $header= new \Zend\Http\Header('Accept');
        $header->setType('Accept-Encoding');
        $this->assertEquals($header->getType(),'Accept-Encoding');
    }
    /**
     * Test set value
     */
    public function testSetValue()
    {
        $header= new \Zend\Http\Header('Accept');
        $header->setValue('text/html');
        $this->assertEquals($header->getValue(),'text/html');
    }
    /**
     * Test has value
     */
    public function testHasValue()
    {
        $header= new \Zend\Http\Header('Accept: text/html');
        $this->assertTrue($header->hasValue('text/html'));
        $this->assertEquals($header->getValue(),'text/html');
    }
    /**
     * Test has value with multiple values
     */
    public function testHasValueWithMultiple()
    {
        $header= new \Zend\Http\Header('Accept: text/html, text/plain');
        $this->assertTrue($header->hasValue('text/html'));
        $this->assertTrue($header->hasValue('text/plain'));
        $this->assertEquals($header->getValue(),'text/html, text/plain');
    }
    /**
     * Test quality factor value
     */
    public function testQualityFactor()
    {
        $header= new \Zend\Http\Header('Accept-Charset: iso-8859-1, utf-8;q=0.5, *;q=0.5');
        $this->assertTrue($header->hasValue('utf-8'));
        $this->assertEquals($header->getQualityFactor('utf-8'),'0.5');
        $this->assertTrue($header->hasValue('iso-8859-1'));
        $this->assertEquals($header->getQualityFactor('iso-8859-1'),'1'); // by default
        $this->assertTrue($header->hasValue('*'));
        $this->assertEquals($header->getQualityFactor('*'),'0.5');
    }
    /**
     * Test level value
     */
    public function testLevel()
    {
        $header= new \Zend\Http\Header('Accept-Charset: iso-8859-1;level=1, utf-8;q=0.5;level=2, *;q=0.5');
        $this->assertTrue($header->hasValue('utf-8'));
        $this->assertEquals($header->getLevel('utf-8'),'2');
        $this->assertTrue($header->hasValue('iso-8859-1'));
        $this->assertEquals($header->getLevel('iso-8859-1'),'1'); // by default
        $this->assertTrue($header->hasValue('*'));
        $this->assertEquals($header->getLevel('*'),false);
    }
}

