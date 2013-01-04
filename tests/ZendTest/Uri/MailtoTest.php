<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Uri
 */

namespace ZendTest\Uri;

use Zend\Uri\Mailto as MailtoUri;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @category   Zend
 * @package    Zend_Uri
 * @subpackage UnitTests
 * @group      Zend_Uri
 * @group      Zend_Uri_Http
 * @group      Zend_Http
 */
class MailtoTest extends TestCase
{
    /**
     * Data Providers
     */

    /**
     * Valid schemes
     *
     * @return array
     */
    public function validSchemeProvider()
    {
        return array(
            array('mailto'),
            array('MAILTO'),
            array('Mailto'),
        );
    }

    /**
     * Invalid schemes
     *
     * @return array
     */
    public function invalidSchemeProvider()
    {
        return array(
            array('file'),
            array('http'),
            array('g'),
            array('mailto:')
        );
    }

    public function invalidUris()
    {
        return array(
            array('mailto:/foo@example.com'),
            array('mailto://foo@example.com'),
            array('mailto:foo@example.com/bar/baz'),
            array('mailto:foo:bar@example.com/bar/baz'),
            array('mailto:foo:bar'),
        );
    }

    /**
     * Tests
     */

    /**
     * Test that specific schemes are valid for this class
     *
     * @param string $scheme
     * @dataProvider validSchemeProvider
     */
    public function testValidScheme($scheme)
    {
        $uri = new MailtoUri;
        $uri->setScheme($scheme);
        $this->assertEquals($scheme, $uri->getScheme());
    }

    /**
     * Test that specific schemes are invalid for this class
     *
     * @param string $scheme
     * @dataProvider invalidSchemeProvider
     */
    public function testInvalidScheme($scheme)
    {
        $uri = new MailtoUri;
        $this->setExpectedException('Zend\Uri\Exception\InvalidUriPartException');
        $uri->setScheme($scheme);
    }

    /**
     * Test that validateScheme returns false for schemes not valid for use
     * with the Mailto class
     *
     * @param string $scheme
     * @dataProvider invalidSchemeProvider
     */
    public function testValidateSchemeInvalid($scheme)
    {
        $this->assertFalse(MailtoUri::validateScheme($scheme));
    }

    public function testCapturesQueryString()
    {
        $uri = new MailtoUri('mailto:foo@example.com?Subject=Testing%20Subjects');
        $this->assertEquals('Subject=Testing%20Subjects', $uri->getQuery());
        $this->assertEquals(array('Subject' => 'Testing Subjects'), $uri->getQueryAsArray());
    }

    public function testUserInfoIsNull()
    {
        $uri = new MailtoUri('mailto:foo@example.com?Subject=Testing%20Subjects');
        $this->assertNull($uri->getUserInfo());
    }

    public function testHostIsNull()
    {
        $uri = new MailtoUri('mailto:foo@example.com?Subject=Testing%20Subjects');
        $this->assertNull($uri->getHost());
    }

    public function testPortIsNull()
    {
        $uri = new MailtoUri('mailto:foo@example.com?Subject=Testing%20Subjects');
        $this->assertNull($uri->getPort());
    }

    public function testPathEquatesToEmail()
    {
        $uri = new MailtoUri('mailto:foo@example.com?Subject=Testing%20Subjects');
        $this->assertEquals('foo@example.com', $uri->getPath());
        $this->assertEquals('foo@example.com', $uri->getEmail());
        $this->assertEquals($uri->getEmail(), $uri->getPath());
    }

    /**
     * @dataProvider invalidUris
     */
    public function testInvalidMailtoUris($uri)
    {
        $uri = new MailtoUri($uri);
        $parts = array(
            'scheme'    => $uri->getScheme(),
            'user_info' => $uri->getUserInfo(),
            'host'      => $uri->getHost(),
            'port'      => $uri->getPort(),
            'path'      => $uri->getPath(),
            'query'     => $uri->getQueryAsArray(),
            'fragment'  => $uri->getFragment(),
        );
        $this->assertFalse($uri->isValid(), var_export($parts, 1));
    }
}
