<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Http\Header;

use Zend\Http\Header\ContentSecurityPolicy;

class ContentSecurityPolicyTest extends \PHPUnit_Framework_TestCase
{
    public function testContentSecurityPolicyFromStringThrowsExceptionIfImproperHeaderNameUsed()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        ContentSecurityPolicy::fromString('X-Content-Security-Policy: default-src *;');
    }

    public function testContentSecurityPolicyFromStringParsesDirectivesCorrectly()
    {
        $csp = ContentSecurityPolicy::fromString(
            "Content-Security-Policy: default-src 'none'; script-src 'self'; img-src 'self'; style-src 'self';"
        );
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $csp);
        $this->assertInstanceOf('Zend\Http\Header\ContentSecurityPolicy', $csp);
        $directives = array('default-src' => "'none'",
                            'script-src'  => "'self'",
                            'img-src'     => "'self'",
                            'style-src'   => "'self'");
        $this->assertEquals($directives, $csp->getDirectives());
    }

    public function testContentSecurityPolicyGetFieldNameReturnsHeaderName()
    {
        $csp = new ContentSecurityPolicy();
        $this->assertEquals('Content-Security-Policy', $csp->getFieldName());
    }

    public function testContentSecurityPolicyToStringReturnsHeaderFormattedString()
    {
        $csp = ContentSecurityPolicy::fromString(
            "Content-Security-Policy: default-src 'none'; img-src 'self' https://*.gravatar.com;"
        );
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $csp);
        $this->assertInstanceOf('Zend\Http\Header\ContentSecurityPolicy', $csp);
        $this->assertEquals(
            "Content-Security-Policy: default-src 'none'; img-src 'self' https://*.gravatar.com;",
            $csp->toString()
        );
    }

    public function testContentSecurityPolicySetDirective()
    {
        $csp = new ContentSecurityPolicy();
        $csp->setDirective('default-src', array('https://*.google.com', 'http://foo.com'))
            ->setDirective('img-src', array("'self'"))
            ->setDirective('script-src', array('https://*.googleapis.com', 'https://*.bar.com'));
        $header = "Content-Security-Policy: default-src https://*.google.com http://foo.com; "
                . "img-src 'self'; script-src https://*.googleapis.com https://*.bar.com;";
        $this->assertEquals($header, $csp->toString());
    }

    public function testContentSecurityPolicySetDirectiveWithEmptySourcesDefaultsToNone()
    {
        $csp = new ContentSecurityPolicy();
        $csp->setDirective('default-src', array("'self'"))
            ->setDirective('img-src', array('*'))
            ->setDirective('script-src', array());
        $this->assertEquals(
            "Content-Security-Policy: default-src 'self'; img-src *; script-src 'none';",
            $csp->toString()
        );
    }

    public function testContentSecurityPolicySetDirectiveThrowsExceptionIfInvalidDirectiveNameGiven()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $csp = new ContentSecurityPolicy();
        $csp->setDirective('foo', array());
    }

    public function testContentSecurityPolicyGetFieldValueReturnsProperValue()
    {
        $csp = new ContentSecurityPolicy();
        $csp->setDirective('default-src', array("'self'"))
            ->setDirective('img-src', array('https://*.github.com'));
        $this->assertEquals("default-src 'self'; img-src https://*.github.com;", $csp->getFieldValue());
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = ContentSecurityPolicy::fromString("Content-Security-Policy: default-src 'none'\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaDirective()
    {
        $header = new ContentSecurityPolicy();
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header->setDirective('default-src', array("\rsome\r\nCRLF\ninjection"));
    }
}
