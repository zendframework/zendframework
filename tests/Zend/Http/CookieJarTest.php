<?php

/**
 * @category   Zend
 * @package    Zend_Http
 * @subpackage UnitTests
 * @version    $Id$
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com/)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Test helper
 */
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Zend/Http/CookieJar.php';

/**
 * Zend_Http_CookieJar unit tests
 *
 * @category   Zend
 * @package    Zend_Http
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com/)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Http_CookieJarTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test we can add cookies to the jar
     *
     */
    public function testAddCookie()
    {
        $jar = new Zend_Http_CookieJar();
        $this->assertEquals(0, count($jar->getAllCookies()), 'Cookie jar is expected to contain 0 cookies');

        $jar->addCookie('foo=bar; domain=example.com');
        $cookie = $jar->getCookie('http://example.com/', 'foo');
        $this->assertTrue($cookie instanceof Zend_Http_Cookie, '$cookie is expected to be a Cookie object');
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
        $jar = new Zend_Http_CookieJar();

        try {
            $jar->addCookie('garbage');
            $this->fail('Expected exception was not thrown');
        } catch (Zend_Http_Exception $e) {
            // We are ok
        }

        try {
            $jar->addCookie(new Zend_Http_Cookiejar());
            $this->fail('Expected exception was not thrown');
        } catch (Zend_Http_Exception $e) {
            // We are ok
        }
    }

    /**
     * Test we can read cookies from a Response object
     *
     */
    public function testAddCookiesFromResponse()
    {
        $jar = new Zend_Http_Cookiejar();
        $res_str = file_get_contents(dirname(realpath(__FILE__)) .
            DIRECTORY_SEPARATOR . '_files'  . DIRECTORY_SEPARATOR . 'response_with_cookies');
        $response = Zend_Http_Response::fromString($res_str);

        $jar->addCookiesFromResponse($response, 'http://www.example.com');

        $this->assertEquals(3, count($jar->getAllCookies()));

        $cookie_str = 'foo=bar;BOFH=Feature+was+not+beta+tested;time=1164234700;';
        $this->assertEquals($cookie_str, $jar->getAllCookies(Zend_Http_CookieJar::COOKIE_STRING_CONCAT));
    }

    public function testExceptAddCookiesInvalidResponse()
    {
        $jar = new Zend_Http_Cookiejar();

        try {
            $jar->addCookiesFromResponse('somestring', 'http://www.example.com');
            $this->fail('Excepted exception was not thrown');
        } catch (Zend_Http_Exception $e) {
            // We are ok
        }

        try {
            $jar->addCookiesFromResponse(new stdClass(), 'http://www.example.com');
            $this->fail('Excepted exception was not thrown');
        } catch (Zend_Http_Exception $e) {
            // We are ok
        }
    }

    /**
     * Test we can get all cookies as an array of Cookie objects
     *
     */
    public function testGetAllCookies()
    {
        $jar = new Zend_Http_CookieJar();

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
            $this->assertContains($cookie->__toString(), $cookies[$id]);
        }
    }

    /**
     * Test we can get all cookies as a concatenated string
     *
     */
    public function testGetAllCookiesAsConcat()
    {
        $jar = new Zend_Http_CookieJar();

        $cookies = array(
            'name=Arthur; domain=camelot.gov.uk',
            'quest=holy+grail; domain=forest.euwing.com',
            'swallow=african; domain=bridge-of-death.net'
        );

        foreach ($cookies as $cookie) {
            $jar->addCookie($cookie);
        }

        $expected = 'name=Arthur;quest=holy+grail;swallow=african;';
        $real = $jar->getAllCookies(Zend_Http_CookieJar::COOKIE_STRING_CONCAT );

        $this->assertEquals($expected, $real, 'Concatenated string is not as expected');
    }

    /**
     * Test we can get a single cookie as an object
     *
     */
    public function testGetCookieAsObject()
    {
        $cookie = Zend_Http_Cookie::fromString('foo=bar; domain=www.example.com; path=/tests');
        $jar = new Zend_Http_CookieJar();
        $jar->addCookie($cookie->__toString(), 'http://www.example.com/tests/');

        $cobj = $jar->getCookie('http://www.example.com/tests/', 'foo');

        $this->assertTrue($cobj instanceof Zend_Http_Cookie, '$cobj is not a Cookie object');
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
        $cookie = Zend_Http_Cookie::fromString('foo=bar; domain=www.example.com; path=/tests');
        $jar = new Zend_Http_CookieJar();
        $jar->addCookie($cookie);

        $cstr = $jar->getCookie('http://www.example.com/tests/', 'foo', Zend_Http_CookieJar::COOKIE_STRING_ARRAY);
        $this->assertEquals($cookie->__toString(), $cstr, 'Cookie string is not the expected string');

        $cstr = $jar->getCookie('http://www.example.com/tests/', 'foo', Zend_Http_CookieJar::COOKIE_STRING_CONCAT);
        $this->assertEquals($cookie->__toString(), $cstr, 'Cookie string is not the expected string');
    }

    /**
     * Check we can get false when trying to get a non-existant cookie
     */
    public function testGetCookieReturnFalse()
    {
        $cookie = Zend_Http_Cookie::fromString('foo=bar; domain=www.example.com; path=/tests');
        $jar = new Zend_Http_CookieJar();
        $jar->addCookie($cookie);

        $cstr = $jar->getCookie('http://www.example.com/tests/', 'otherfoo', Zend_Http_CookieJar::COOKIE_STRING_ARRAY);
        $this->assertFalse($cstr, 'getCookie was expected to return false, no such cookie');

        $cstr = $jar->getCookie('http://www.otherexample.com/tests/', 'foo', Zend_Http_CookieJar::COOKIE_STRING_CONCAT);
        $this->assertFalse($cstr, 'getCookie was expected to return false, no such domain');

        $cstr = $jar->getCookie('http://www.example.com/othertests/', 'foo', Zend_Http_CookieJar::COOKIE_STRING_CONCAT);
        $this->assertFalse($cstr, 'getCookie was expected to return false, no such path');
    }

    /**
     * Test we get a proper exception when an invalid URI is passed
     */
    public function testExceptGetCookieInvalidUri()
    {
        $cookie = Zend_Http_Cookie::fromString('foo=bar; domain=www.example.com; path=/tests');
        $jar = new Zend_Http_CookieJar();
        $jar->addCookie($cookie);

        try {
            $jar->getCookie('foo.com', 'foo');
            $this->fail('Expected getCookie to throw exception, invalid URI string passed');
        } catch (Zend_Exception $e) {
            // We are ok!
        }

        try {
            $jar->getCookie(Zend_Uri::factory('mailto:nobody@dev.null.com'), 'foo');
            $this->fail('Expected getCookie to throw exception, invalid URI object passed');
        } catch (Zend_Exception $e) {
            // We are ok!
        }
    }

    /**
     * Test we get a proper exception when an invalid return constant is passed
     *
     */
    public function testExceptGetCookieInvalidReturnType()
    {
        $cookie = Zend_Http_Cookie::fromString('foo=bar; domain=example.com;');
        $jar = new Zend_Http_CookieJar();
        $jar->addCookie($cookie);

        try {
            $jar->getCookie('http://example.com/', 'foo', 5);
            $this->fail('Expected getCookie to throw exception, invalid return type');
        } catch (Zend_Http_Exception $e) {
            // We are ok!
        }
    }

    /**
     * Test we can get all matching cookies for a request, with session cookies
     */
    public function testGetMatchingCookies()
    {
        $jar = new Zend_Http_CookieJar();
        $cookies = array(
            Zend_Http_Cookie::fromString('foo1=bar1; domain=.foo.com; path=/path; expires=' . date(DATE_COOKIE, time() + 3600)),
            Zend_Http_Cookie::fromString('foo2=bar2; domain=.foo.com; path=/; expires=' . date(DATE_COOKIE, time() + 3600)),
            Zend_Http_Cookie::fromString('foo3=bar3; domain=.foo.com; path=/; expires=' . date(DATE_COOKIE, time() - 3600)),
            Zend_Http_Cookie::fromString('foo4=bar4; domain=.foo.com; path=/;'),
            Zend_Http_Cookie::fromString('foo5=bar5; domain=.foo.com; path=/; secure; expires=' . date(DATE_COOKIE, time() + 3600)),
            Zend_Http_Cookie::fromString('foo6=bar6; domain=.foo.com; path=/otherpath; expires=' . date(DATE_COOKIE, time() + 3600)),
            Zend_Http_Cookie::fromString('foo7=bar7; domain=www.foo.com; path=/path; expires=' . date(DATE_COOKIE, time() + 3600)),
            Zend_Http_Cookie::fromString('foo8=bar8; domain=subdomain.foo.com; path=/path; expires=' . date(DATE_COOKIE, time() + 3600)),
        );

        foreach ($cookies as $cookie) $jar->addCookie($cookie);

        $this->assertEquals(8, count($jar->getAllCookies()), 'Cookie count is expected to be 8');

        $cookies = $jar->getMatchingCookies('http://www.foo.com/path/file.txt');
        $this->assertEquals(4, count($cookies), 'Cookie count is expected to be 4');

        $cookies = $jar->getMatchingCookies('https://www.foo.com/path/file.txt');
        $this->assertEquals(5, count($cookies), 'Cookie count is expected to be 5');
    }

    /**
     * Test we can get all matching cookies for a request, without session cookies
     */
    public function testGetMatchingCookiesNoSession()
    {
        $jar = new Zend_Http_CookieJar();
        $cookies = array(
            Zend_Http_Cookie::fromString('foo1=bar1; domain=.foo.com; path=/path; expires=' . date(DATE_COOKIE, time() + 3600)),
            Zend_Http_Cookie::fromString('foo2=bar2; domain=.foo.com; path=/; expires=' . date(DATE_COOKIE, time() + 3600)),
            Zend_Http_Cookie::fromString('foo3=bar3; domain=.foo.com; path=/; expires=' . date(DATE_COOKIE, time() - 3600)),
            Zend_Http_Cookie::fromString('foo4=bar4; domain=.foo.com; path=/;'),
            Zend_Http_Cookie::fromString('foo5=bar5; domain=.foo.com; path=/; secure; expires=' . date(DATE_COOKIE, time() + 3600)),
            Zend_Http_Cookie::fromString('foo6=bar6; domain=.foo.com; path=/otherpath; expires=' . date(DATE_COOKIE, time() + 3600)),
            Zend_Http_Cookie::fromString('foo7=bar7; domain=www.foo.com; path=/path; expires=' . date(DATE_COOKIE, time() + 3600)),
            Zend_Http_Cookie::fromString('foo8=bar8; domain=subdomain.foo.com; path=/path; expires=' . date(DATE_COOKIE, time() + 3600)),
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
        $jar = new Zend_Http_CookieJar();
        $cookies = array(
            Zend_Http_Cookie::fromString('foo1=bar1; domain=.foo.com; path=/path; expires=' . date(DATE_COOKIE, time() + 3600)),
            Zend_Http_Cookie::fromString('foo2=bar2; domain=.foo.com; path=/; expires=' . date(DATE_COOKIE, time() + 7200)),
            Zend_Http_Cookie::fromString('foo3=bar3; domain=.foo.com; path=/; expires=' . date(DATE_COOKIE, time() - 3600)),
            Zend_Http_Cookie::fromString('foo4=bar4; domain=.foo.com; path=/;'),
            Zend_Http_Cookie::fromString('foo5=bar5; domain=.foo.com; path=/; secure; expires=' . date(DATE_COOKIE, time() - 7200)),
            Zend_Http_Cookie::fromString('foo6=bar6; domain=.foo.com; path=/otherpath; expires=' . date(DATE_COOKIE, time() + 3600)),
            Zend_Http_Cookie::fromString('foo7=bar7; domain=www.foo.com; path=/path; expires=' . date(DATE_COOKIE, time() + 3600)),
            Zend_Http_Cookie::fromString('foo8=bar8; domain=subdomain.foo.com; path=/path; expires=' . date(DATE_COOKIE, time() + 3600)),
        );

        foreach ($cookies as $cookie) $jar->addCookie($cookie);

        $this->assertEquals(8, count($jar->getAllCookies()), 'Cookie count is expected to be 8');

        $cookies = $jar->getMatchingCookies('http://www.foo.com/path/file.txt', true, Zend_Http_CookieJar::COOKIE_OBJECT, time() + 3700);
        $this->assertEquals(2, count($cookies), 'Cookie count is expected to be 2');

        $cookies = $jar->getMatchingCookies('http://www.foo.com/path/file.txt', true, Zend_Http_CookieJar::COOKIE_OBJECT, time() - 3700);
        $this->assertEquals(5, count($cookies), 'Cookie count is expected to be 5');
    }

    /**
     * Test we can get all matching cookies for a request, and return as strings array / concat
     */
    public function testGetMatchingCookiesAsStrings()
    {
        $jar = new Zend_Http_CookieJar();
        $cookies = array(
            Zend_Http_Cookie::fromString('foo1=bar1; domain=.foo.com; path=/path; expires=' . date(DATE_COOKIE, time() + 3600)),
            Zend_Http_Cookie::fromString('foo2=bar2; domain=.foo.com; path=/; expires=' . date(DATE_COOKIE, time() + 3600)),
            Zend_Http_Cookie::fromString('foo3=bar3; domain=.foo.com; path=/; expires=' . date(DATE_COOKIE, time() - 3600)),
            Zend_Http_Cookie::fromString('foo4=bar4; domain=.foo.com; path=/;'),
            Zend_Http_Cookie::fromString('foo5=bar5; domain=.foo.com; path=/; secure; expires=' . date(DATE_COOKIE, time() + 3600)),
            Zend_Http_Cookie::fromString('foo6=bar6; domain=.foo.com; path=/otherpath; expires=' . date(DATE_COOKIE, time() + 3600)),
            Zend_Http_Cookie::fromString('foo7=bar7; domain=www.foo.com; path=/path; expires=' . date(DATE_COOKIE, time() + 3600)),
            Zend_Http_Cookie::fromString('foo8=bar8; domain=subdomain.foo.com; path=/path; expires=' . date(DATE_COOKIE, time() + 3600)),
        );

        foreach ($cookies as $cookie) $jar->addCookie($cookie);

        $this->assertEquals(8, count($jar->getAllCookies()), 'Cookie count is expected to be 8');

        $cookies = $jar->getMatchingCookies('http://www.foo.com/path/file.txt', true, Zend_Http_CookieJar::COOKIE_STRING_ARRAY);
        $this->assertType('array', $cookies, '$cookies is expected to be an array, but it is not');
        $this->assertType('string', $cookies[0], '$cookies[0] is expected to be a string');;

        $cookies = $jar->getMatchingCookies('http://www.foo.com/path/file.txt', true, Zend_Http_CookieJar::COOKIE_STRING_CONCAT);
        $this->assertType('string', $cookies, '$cookies is expected to be a string');
    }

    /**
     * Test we get a proper exception when an invalid URI is passed
     */
    public function testExceptGetMatchingCookiesInvalidUri()
    {
        $jar = new Zend_Http_CookieJar();

        try {
            $cookies = $jar->getMatchingCookies('invalid.com', true, Zend_Http_CookieJar::COOKIE_STRING_ARRAY);
            $this->fail('Expected getMatchingCookies to throw exception, invalid URI string passed');
        } catch (Zend_Exception $e) {
            // We are ok!
        }

        try {
            $cookies = $jar->getMatchingCookies(new stdClass(), true, Zend_Http_CookieJar::COOKIE_STRING_ARRAY);
            $this->fail('Expected getCookie to throw exception, invalid URI object passed');
        } catch (Zend_Exception $e) {
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
        $response = Zend_Http_Response::fromString($res_str);

        $jar = Zend_Http_CookieJar::fromResponse($response, 'http://www.example.com');

        $this->assertTrue($jar instanceof Zend_Http_CookieJar, '$jar is not an instance of CookieJar as expected');
        $this->assertEquals(1, count($jar->getAllCookies()), 'CookieJar expected to contain 1 cookie');
    }

    /**
     * Test we can build a new object from a response object (multiple cookie headers)
     */
    public function testFromResponseMultiHeader()
    {
        $res_str = file_get_contents(dirname(realpath(__FILE__)) .
            DIRECTORY_SEPARATOR . '_files'  . DIRECTORY_SEPARATOR . 'response_with_cookies');
        $response = Zend_Http_Response::fromString($res_str);

        $jar = Zend_Http_CookieJar::fromResponse($response, 'http://www.example.com');

        $this->assertTrue($jar instanceof Zend_Http_CookieJar, '$jar is not an instance of CookieJar as expected');
        $this->assertEquals(3, count($jar->getAllCookies()), 'CookieJar expected to contain 3 cookies');
    }

    /**
     * Make sure that paths with trailing slashes are matched as well as paths with no trailing slashes
     */
    public function testMatchPathWithTrailingSlash()
    {
        $jar = new Zend_Http_CookieJar();
        $cookies = array(
            Zend_Http_Cookie::fromString('foo1=bar1; domain=.example.com; path=/a/b'),
            Zend_Http_Cookie::fromString('foo2=bar2; domain=.example.com; path=/a/b/')
        );

        foreach ($cookies as $cookie) $jar->addCookie($cookie);
        $cookies = $jar->getMatchingCookies('http://www.example.com/a/b/file.txt');

        $this->assertType('array', $cookies);
        $this->assertEquals(2, count($cookies));
    }

    public function testIteratorAndCountable()
    {
        $jar = new Zend_Http_CookieJar();
        $cookies = array(
            Zend_Http_Cookie::fromString('foo1=bar1; domain=.example.com; path=/a/b'),
            Zend_Http_Cookie::fromString('foo2=bar2; domain=.example.com; path=/a/b/')
        );
        foreach ($cookies as $cookie) $jar->addCookie($cookie);
        foreach ($jar as $cookie) {
            $this->assertType('Zend_Http_Cookie', $cookie);
        }
        $this->assertEquals(2, count($jar));
        $this->assertFalse($jar->isEmpty());
        $jar->reset();
        $this->assertTrue($jar->isEmpty());
    }
}
