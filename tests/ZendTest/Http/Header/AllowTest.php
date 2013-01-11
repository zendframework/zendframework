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

use Zend\Http\Header\Allow;

class AllowTest extends \PHPUnit_Framework_TestCase
{

    public function testAllowFromStringCreatesValidAllowHeader()
    {
        $allowHeader = Allow::fromString('Allow: GET, POST, PUT');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $allowHeader);
        $this->assertInstanceOf('Zend\Http\Header\Allow', $allowHeader);
        $this->assertEquals(array('GET', 'POST', 'PUT'), $allowHeader->getAllowedMethods());
    }

    public function testAllowFromStringSupportsExtensionMethods()
    {
        $allowHeader = Allow::fromString('Allow: GET, POST, PROCREATE');
        $this->assertTrue($allowHeader->isAllowedMethod('PROCREATE'));
    }

    public function testAllowGetFieldNameReturnsHeaderName()
    {
        $allowHeader = new Allow();
        $this->assertEquals('Allow', $allowHeader->getFieldName());
    }

    public function testAllowListAllDefinedMethods()
    {
        $methods = array(
            'OPTIONS' => false,
            'GET'     => true,
            'HEAD'    => false,
            'POST'    => true,
            'PUT'     => false,
            'DELETE'  => false,
            'TRACE'   => false,
            'CONNECT' => false,
            'PATCH'   => false,
        );
        $allowHeader = new Allow();
        $this->assertEquals($methods, $allowHeader->getAllMethods());
    }

    public function testAllowGetDefaultAllowedMethods()
    {
        $allowHeader = new Allow();
        $this->assertEquals(array('GET', 'POST'), $allowHeader->getAllowedMethods());
    }

    public function testAllowGetFieldValueReturnsProperValue()
    {
        $allowHeader = new Allow();
        $allowHeader->allowMethods(array('GET', 'POST', 'TRACE'));
        $this->assertEquals('GET, POST, TRACE', $allowHeader->getFieldValue());
    }

    public function testAllowToStringReturnsHeaderFormattedString()
    {
        $allowHeader = new Allow();
        $allowHeader->allowMethods(array('GET', 'POST', 'TRACE'));
        $this->assertEquals('Allow: GET, POST, TRACE', $allowHeader->toString());
    }

    public function testAllowChecksAllowedMethod()
    {
        $allowHeader = new Allow();
        $allowHeader->allowMethods(array('GET', 'POST', 'TRACE'));
        $this->assertTrue($allowHeader->isAllowedMethod('TRACE'));
    }
}
