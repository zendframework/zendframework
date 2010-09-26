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
 * @package    Zend_Http_CookieJar
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Http;
use Zend\Http;
use Zend\Http\Response;

/**
 * Test helper
 */


/**
 * Zend_Http_CookieJar unit tests
 *
 * @category   Zend
 * @package    Zend_Http_CookieJar
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Http
 * @group      Zend_Http_CookieJar
 */
class CookieJarTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test we can add cookies to the jar
     *
     */
    public function testAddCookie()
    {
        $jar = new Http\CookieJar();
        $this->assertEquals(0, count($jar->getAllCookies()), 'Cookie jar is expected to contain 0 cookies');

        $jar->addCookie('foo=bar; domain=example.com');
        $cookie = $jar->getCookie('http://example.com/', 'foo');
        $this->assertTrue($cookie instanceof Http\Cookie, '$cookie is expected to be a Cookie object');
        $this->assertEquals('bar', $cookie->getValue(), 'Cookie value is expected to be "bar"');

        $jar->addCookie('cookie=brownie; domain=geekz.co.uk;');
        $this->assertEquals(2, count($jar->getAllCookies()), 'Cookie jar is expected to contain 2 cookies');
    }

    /**
     * Check we get an expection if a non-valid cookie is passed to addCookie
     *
     */
    public function testExceptAddInvalidCookie()
    {
        $jar = new Http\CookieJar();

        try {
            $jar->addCookie('garbage');
            $this->fail('Expected exception was not thrown');
        } catch (Http\Exception $e) {
            // We are ok
        }

        try {
            $jar->addCookie(new Http\CookieJar());
            $this->fail('Expected exception was not thrown');
        } catch (Http\Exception $e) {
            // We are ok
        }
    }

    /**
     * Test we can read cookies from a Response object
     *
     */
    public function testAddCookiesFromResponse()
    {
        $jar = new Http\CookieJar();
        $res_str = file_get_contents(dirname(realpath(__FILE__)) .
            DIRECTORY_SEPARATOR . '_files'  . DIRECTORY_SEPARATOR . 'response_with_cookies');
        $response = Response::fromString($res_str);

        $jar->addCookiesFromResponse($response, 'http://www.example.com');

        $this->assertEquals(3, count($jar->getAllCookies()));

        $cookie_str = 'foo=bar;BOFH=Feature+was+not+beta+tested;time=1164234700;';
        $this->assertEquals($cookie_str, $jar->getAllCookies(Http\CookieJar::COOKIE_STRING_CONCAT));
    }

    /**
     * Test we get an exception in case of invalid response objects
     *
     * @dataProvider invalidResponseProvider
     * @expectedException Zend\Http\Exception
     */
    public function testExceptAddCookiesInvalidResponse($resp)
    {
        $jar = new Http\CookieJar();
        $jar->addCookiesFromResponse($resp, 'http://www.example.com');
    }

    static public function invalidResponseProvider()
    {
        return array(
            array(new \stdClass),
            array(null),
            array(12),
            array('hi')
        );
    }

    /**
     * Test we can get all cookies as an array of Cookie objects
     *
     */
    public function testGetAllCookies()
    {
        $jar = new Http\CookieJar();

        $cookies = array(
            'name=Arthur; domain=camelot.gov.uk',
            'quest=holy+grail; domain=forest.euwing.com',
            'swallow=african; domain=bridge-of-death.net'
        );

        foreach ($cookies as $cookie) {
            $jar->addCookie($cookie);
        }

        $cobjects = $jar->getAllCookies();

        foreach ($cobjects as $id => $cookie) {
            $this->assertContains((string) $cookie, $cookies[$id]);
        }
    }

    /**
     * Test we can get all cookies as a concatenated string
     *
     */
    public function testGetAllCookiesAsConcat()
    {
        $jar = new Http\CookieJar();

        $cookies = array(
            'name=Arthur; domain=camelot.gov.uk',
            'quest=holy+grail; domain=forest.euwing.com',
            'swallow=african; domain=bridge-of-death.net'
        );

        foreach ($cookies as $cookie) {
            $jar->addCookie($cookie);
        }

        $expected = 'name=Arthur;quest=holy+grail;swallow=african;';
        $real = $jar->getAllCookies(Http\CookieJar::COOKIE_STRING_CONCAT );

        $this->assertEquals($expected, $real, 'Concatenated string is not as expected');
    }

    /**
     * Test we can get a single cookie as an object
     *
     */
    public function testGetCookieAsObject()
    {
        $cookie = Http\Cookie::fromString('foo=bar; domain=www.example.com; path=/tests');
        $jar = new Http\CookieJar();
        $jar->addCookie($cookie->__toString(), 'http://www.example.com/tests/');

        $cobj = $jar->getCookie('http://www.example.com/tests/', 'foo');

        $this->assertTrue($cobj instanceof Http\Cookie, '$cobj is not a Cookie object');
        $this->assertEquals($cookie->getName(), $cobj->getName(), 'Cookie name is not as expected');
        $this->assertEquals($cookie->getValue(), $cobj->getValue(), 'Cookie value is not as expected');
        $this->assertEquals($cookie->getDomain(), $cobj->getDomain(), 'Cookie domain is not as expected');
        $this->assertEquals($cookie->getPath(), $cobj->getPath(), 'Cookie path is not as expected');
    }

    /**
     * Check we can get a cookie as a string
     */
    public function testGetCookieAsString()
    {
        $cookie = Http\Cookie::fromString('foo=bar; domain=www.example.com; path=/tests');
        $jar = new Http\CookieJar();
        $jar->addCookie($cookie);

        $cstr = $jar->getCookie('http://www.example.com/tests/', 'foo', Http\CookieJar::COOKIE_STRING_ARRAY);
        $this->assertEquals($cookie->__toString(), $cstr, 'Cookie string is not the expected string');

        $cstr = $jar->getCookie('http://www.example.com/tests/', 'foo', Http\CookieJar::COOKIE_STRING_CONCAT);
        $this->assertEquals($cookie->__toString(), $cstr, 'Cookie string is not the expected string');
    }

    /**
     * Check we can get false when trying to get a non-existant cookie
     */
    public function testGetCookieReturnFalse()
    {
        $cookie = Http\Cookie::fromString('foo=bar; domain=www.example.com; path=/tests');
        $jar = new Http\CookieJar();
        $jar->addCookie($cookie);

        $cstr = $jar->getCookie('http://www.example.com/tests/', 'otherfoo', Http\CookieJar::COOKIE_STRING_ARRAY);
        $this->assertFalse($cstr, 'getCookie was expected to return false, no such cookie');

        $cstr = $jar->getCookie('http://www.otherexample.com/tests/', 'foo', Http\CookieJar::COOKIE_STRING_CONCAT);
        $this->assertFalse($cstr, 'getCookie was expected to return false, no such domain');

        $cstr = $jar->getCookie('http://www.example.com/othertests/', 'foo', Http\CookieJar::COOKIE_STRING_CONCAT);
        $this->assertFalse($cstr, 'getCookie was expected to return false, no such path');
    }

    /**
     * Test we get a proper exception when an invalid URI is passed
     */
    public function testExceptGetCookieInvalidUri()
    {
        $cookie = Http\Cookie::fromString('foo=bar; domain=www.example.com; path=/tests');
        $jar = new Http\CookieJar();
        $jar->addCookie($cookie);

        try {
            $jar->getCookie('foo.com', 'foo');
            $this->fail('Expected getCookie to throw exception, invalid URI string passed');
        } catch (\Zend\Exception $e) {
            // We are ok!
        }

        try {
            $jar->getCookie(new \Zend\Uri\Url('mailto:nobody@dev.null.com'), 'foo');
            $this->fail('Expected getCookie to throw exception, invalid URI object passed');
        } catch (\Zend\Exception $e) {
            // We are ok!
        }
    }

    /**
     * Test we get a proper exception when an invalid return constant is passed
     *
     */
    public function testExceptGetCookieInvalidReturnType()
    {
        $cookie = Http\Cookie::fromString('foo=bar; domain=example.com;');
        $jar = new Http\CookieJar();
        $jar->addCookie($cookie);

        try {
            $jar->getCookie('http://example.com/', 'foo', 5);
            $this->fail('Expected getCookie to throw exception, invalid return type');
        } catch (Http\Exception $e) {
            // We are ok!
        }
    }

    /**
     * Test we can get all matching cookies for a request, with session cookies
     *
     * @dataProvider cookieMatchTestProvider
     */
    public function testGetMatchingCookies($url, $expected)
    {
        $jar = new Http\CookieJar();
        $cookies = array(
            Http\Cookie::fromString('foo1=bar1; domain=.foo.com; path=/path; expires=' . date(DATE_COOKIE, time() + 3600)),
            Http\Cookie::fromString('foo2=bar2; domain=foo.com; path=/; expires=' . date(DATE_COOKIE, time() + 3600)),
            Http\Cookie::fromString('foo3=bar3; domain=.foo.com; path=/; expires=' . date(DATE_COOKIE, time() - 3600)),
            Http\Cookie::fromString('foo4=bar4; domain=.foo.com; path=/;'),
            Http\Cookie::fromString('foo5=bar5; domain=.foo.com; path=/; secure; expires=' . date(DATE_COOKIE, time() + 3600)),
            Http\Cookie::fromString('foo6=bar6; domain=.foo.com; path=/otherpath; expires=' . date(DATE_COOKIE, time() + 3600)),
            Http\Cookie::fromString('foo7=bar7; domain=www.foo.com; path=/path; expires=' . date(DATE_COOKIE, time() + 3600)),
            Http\Cookie::fromString('foo7=bar7; domain=newwww.foo.com; path=/;'),
            Http\Cookie::fromString('foo8=bar8; domain=subdomain.foo.com; path=/path; expires=' . date(DATE_COOKIE, time() + 3600)),
        );

        foreach ($cookies as $cookie) $jar->addCookie($cookie);
        $cookies = $jar->getMatchingCookies($url);
        $this->assertEquals($expected, count($cookies), $jar->getMatchingCookies($url, true, Http\CookieJar::COOKIE_STRING_CONCAT));
    }

    static public function cookieMatchTestProvider()
    {
        return array(
            array('http://www.foo.com/path/file.txt', 4),
            array('http://foo.com/path/file.txt', 3),
            array('https://www.foo.com/path/file.txt', 5),
            array('http://subdomain.foo.com/path', 4),
            array('http://subdomain.foo.com/otherpath', 3),
            array('http://blog.foo.com/news', 2)
        );
    }

    /**
     * Test we can get all matching cookies for a request, without session cookies
     */
    public function testGetMatchingCookiesNoSession()
    {
        $jar = new Http\CookieJar();
        $cookies = array(
            Http\Cookie::fromString('foo1=bar1; domain=.foo.com; path=/path; expires=' . date(DATE_COOKIE, time() + 3600)),
            Http\Cookie::fromString('foo2=bar2; domain=.foo.com; path=/; expires=' . date(DATE_COOKIE, time() + 3600)),
            Http\Cookie::fromString('foo3=bar3; domain=.foo.com; path=/; expires=' . date(DATE_COOKIE, time() - 3600)),
            Http\Cookie::fromString('foo4=bar4; domain=.foo.com; path=/;'),
            Http\Cookie::fromString('foo5=bar5; domain=.foo.com; path=/; secure; expires=' . date(DATE_COOKIE, time() + 3600)),
            Http\Cookie::fromString('foo6=bar6; domain=.foo.com; path=/otherpath; expires=' . date(DATE_COOKIE, time() + 3600)),
            Http\Cookie::fromString('foo7=bar7; domain=www.foo.com; path=/path; expires=' . date(DATE_COOKIE, time() + 3600)),
            Http\Cookie::fromString('foo8=bar8; domain=subdomain.foo.com; path=/path; expires=' . date(DATE_COOKIE, time() + 3600)),
        );

        foreach ($cookies as $cookie) $jar->addCookie($cookie);

        $this->assertEquals(8, count($jar->getAllCookies()), 'Cookie count is expected to be 8');

        $cookies = $jar->getMatchingCookies('http://www.foo.com/path/file.txt', false);
        $this->assertEquals(3, count($cookies), 'Cookie count is expected to be 3');

        $cookies = $jar->getMatchingCookies('https://www.foo.com/path/file.txt', false);
        $this->assertEquals(4, count($cookies), 'Cookie count is expected to be 4');
    }

    /**
     * Test we can get all matching cookies for a request, when we set a different time for now
     */
    public function testGetMatchingCookiesWithTime()
    {
        $jar = new Http\CookieJar();
        $cookies = array(
            Http\Cookie::fromString('foo1=bar1; domain=.foo.com; path=/path; expires=' . date(DATE_COOKIE, time() + 3600)),
            Http\Cookie::fromString('foo2=bar2; domain=.foo.com; path=/; expires=' . date(DATE_COOKIE, time() + 7200)),
            Http\Cookie::fromString('foo3=bar3; domain=.foo.com; path=/; expires=' . date(DATE_COOKIE, time() - 3600)),
            Http\Cookie::fromString('foo4=bar4; domain=.foo.com; path=/;'),
            Http\Cookie::fromString('foo5=bar5; domain=.foo.com; path=/; secure; expires=' . date(DATE_COOKIE, time() - 7200)),
            Http\Cookie::fromString('foo6=bar6; domain=.foo.com; path=/otherpath; expires=' . date(DATE_COOKIE, time() + 3600)),
            Http\Cookie::fromString('foo7=bar7; domain=www.foo.com; path=/path; expires=' . date(DATE_COOKIE, time() + 3600)),
            Http\Cookie::fromString('foo8=bar8; domain=subdomain.foo.com; path=/path; expires=' . date(DATE_COOKIE, time() + 3600)),
        );

        foreach ($cookies as $cookie) $jar->addCookie($cookie);

        $this->assertEquals(8, count($jar->getAllCookies()), 'Cookie count is expected to be 8');

        $cookies = $jar->getMatchingCookies('http://www.foo.com/path/file.txt', true, Http\CookieJar::COOKIE_OBJECT, time() + 3700);
        $this->assertEquals(2, count($cookies), 'Cookie count is expected to be 2');

        $cookies = $jar->getMatchingCookies('http://www.foo.com/path/file.txt', true, Http\CookieJar::COOKIE_OBJECT, time() - 3700);
        $this->assertEquals(5, count($cookies), 'Cookie count is expected to be 5');
    }

    /**
     * Test we can get all matching cookies for a request, and return as strings array / concat
     */
    public function testGetMatchingCookiesAsStrings()
    {
        $jar = new Http\CookieJar();
        $cookies = array(
            Http\Cookie::fromString('foo1=bar1; domain=.foo.com; path=/path; expires=' . date(DATE_COOKIE, time() + 3600)),
            Http\Cookie::fromString('foo2=bar2; domain=.foo.com; path=/; expires=' . date(DATE_COOKIE, time() + 3600)),
            Http\Cookie::fromString('foo3=bar3; domain=.foo.com; path=/; expires=' . date(DATE_COOKIE, time() - 3600)),
            Http\Cookie::fromString('foo4=bar4; domain=.foo.com; path=/;'),
            Http\Cookie::fromString('foo5=bar5; domain=.foo.com; path=/; secure; expires=' . date(DATE_COOKIE, time() + 3600)),
            Http\Cookie::fromString('foo6=bar6; domain=.foo.com; path=/otherpath; expires=' . date(DATE_COOKIE, time() + 3600)),
            Http\Cookie::fromString('foo7=bar7; domain=www.foo.com; path=/path; expires=' . date(DATE_COOKIE, time() + 3600)),
            Http\Cookie::fromString('foo8=bar8; domain=subdomain.foo.com; path=/path; expires=' . date(DATE_COOKIE, time() + 3600)),
        );

        foreach ($cookies as $cookie) $jar->addCookie($cookie);

        $this->assertEquals(8, count($jar->getAllCookies()), 'Cookie count is expected to be 8');

        $cookies = $jar->getMatchingCookies('http://www.foo.com/path/file.txt', true, Http\CookieJar::COOKIE_STRING_ARRAY);
        $this->assertType('array', $cookies, '$cookies is expected to be an array, but it is not');
        $this->assertType('string', $cookies[0], '$cookies[0] is expected to be a string');

        $cookies = $jar->getMatchingCookies('http://www.foo.com/path/file.txt', true, Http\CookieJar::COOKIE_STRING_CONCAT);
        $this->assertType('string', $cookies, '$cookies is expected to be a string');
    }

    /**
     * Test we get a proper exception when an invalid URI is passed
     */
    public function testExceptGetMatchingCookiesInvalidUri()
    {
        $jar = new Http\CookieJar();

        try {
            $cookies = $jar->getMatchingCookies('invalid.com', true, Http\CookieJar::COOKIE_STRING_ARRAY);
            $this->fail('Expected getMatchingCookies to throw exception, invalid URI string passed');
        } catch (\Zend\Exception $e) {
            // We are ok!
        }

        try {
            $cookies = $jar->getMatchingCookies(new \stdClass(), true, Http\CookieJar::COOKIE_STRING_ARRAY);
            $this->fail('Expected getCookie to throw exception, invalid URI object passed');
        } catch (\Zend\Exception $e) {
            // We are ok!
        }
    }

    /**
     * Test we can build a new object from a response object (single cookie header)
     */
    public function testFromResponse()
    {
        $res_str = file_get_contents(dirname(realpath(__FILE__)) .
            DIRECTORY_SEPARATOR . '_files'  . DIRECTORY_SEPARATOR . 'response_with_single_cookie');
        $response = Response::fromString($res_str);

        $jar = Http\CookieJar::fromResponse($response, 'http://www.example.com');

        $this->assertTrue($jar instanceof Http\CookieJar, '$jar is not an instance of CookieJar as expected');
        $this->assertEquals(1, count($jar->getAllCookies()), 'CookieJar expected to contain 1 cookie');
    }

    /**
     * Test we can build a new object from a response object (multiple cookie headers)
     */
    public function testFromResponseMultiHeader()
    {
        $res_str = file_get_contents(dirname(realpath(__FILE__)) .
            DIRECTORY_SEPARATOR . '_files'  . DIRECTORY_SEPARATOR . 'response_with_cookies');
        $response = Response::fromString($res_str);

        $jar = Http\CookieJar::fromResponse($response, 'http://www.example.com');

        $this->assertTrue($jar instanceof Http\CookieJar, '$jar is not an instance of CookieJar as expected');
        $this->assertEquals(3, count($jar->getAllCookies()), 'CookieJar expected to contain 3 cookies');
    }

    /**
     * Make sure that paths with trailing slashes are matched as well as paths with no trailing slashes
     */
    public function testMatchPathWithTrailingSlash()
    {
        $jar = new Http\CookieJar();
        $cookies = array(
            Http\Cookie::fromString('foo1=bar1; domain=.example.com; path=/a/b'),
            Http\Cookie::fromString('foo2=bar2; domain=.example.com; path=/a/b/')
        );

        foreach ($cookies as $cookie) $jar->addCookie($cookie);
        $cookies = $jar->getMatchingCookies('http://www.example.com/a/b/file.txt');

        $this->assertType('array', $cookies);
        $this->assertEquals(2, count($cookies));
    }

    public function testIteratorAndCountable()
    {
        $jar = new Http\CookieJar();
        $cookies = array(
            Http\Cookie::fromString('foo1=bar1; domain=.example.com; path=/a/b'),
            Http\Cookie::fromString('foo2=bar2; domain=.example.com; path=/a/b/')
        );
        foreach ($cookies as $cookie) $jar->addCookie($cookie);
        foreach ($jar as $cookie) {
            $this->assertType('Zend\Http\Cookie', $cookie);
        }
        $this->assertEquals(2, count($jar));
        $this->assertFalse($jar->isEmpty());
        $jar->reset();
        $this->assertTrue($jar->isEmpty());
    }
}
