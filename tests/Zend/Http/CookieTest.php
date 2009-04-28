<?php

/**
 * @category   Zend
 * @package    Zend_Http
 * @subpackage UnitTests
 * @version    $Id$
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com/)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once dirname(__FILE__) . '/../../TestHelper.php';
require_once 'Zend/Http/Cookie.php';

/**
 * Zend_Http_Cookie unit tests
 * 
 * @category   Zend
 * @package    Zend_Http
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com/)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Http_CookieTest extends PHPUnit_Framework_TestCase 
{
	/**
	 * Make sure we can't set invalid names
	 */
    public function testSetInvalidName()
    {
    	$invalidcharacters = "=,; \t\r\n\013\014";
    	$l = strlen($invalidcharacters) - 1;
    	for ($i = 0; $i < $l; $i++) {
    		$name = 'cookie_' . $invalidcharacters[$i];
    		try {
    			$cookie = new Zend_Http_Cookie($name, 'foo', 'example.com');
    			$this->fail('Expected invalid cookie name exception was not thrown for "' . $name . '"');
    		} catch (Zend_Http_Exception $e) {
    			// We're good!
    		}
    	}
    }
    
	/**
     * Test we get the cookie name properly
     */
    public function testGetName() 
    {
    	// Array of cookies and their names. We need to test each 'keyword' in
    	// a cookie string
    	$cookies = array(
    		'justacookie' => 'justacookie=foo; domain=example.com',
    		'expires'     => 'expires=tomorrow; expires=Tue, 21-Nov-2006 08:33:44 GMT; domain=example.com',
    		'domain'      => 'domain=unittests; expires=Tue, 21-Nov-2006 08:33:44 GMT; domain=example.com', 
    		'path'        => 'path=indexAction; path=/; domain=.foo.com',
    		'secure'      => 'secure=sha1; secure; domain=.foo.com',
    		'PHPSESSID'   => 'PHPSESSID=1234567890abcdef; secure; domain=.foo.com; path=/; expires=Tue, 21-Nov-2006 08:33:44 GMT;'
    	);
    	
    	foreach ($cookies as $name => $cstr) {
    		$cookie = Zend_Http_Cookie::fromString($cstr);
    		if (! $cookie instanceof Zend_Http_Cookie) $this->fail('Cookie ' . $name . ' is not a proper Cookie object');
    		$this->assertEquals($name, $cookie->getName(), 'Cookie name is not as expected');
    	}
    }

    /**
     * Make sure we get the correct value if it was set through the constructor
     * 
     */
    public function testGetValueConstructor() 
    {
    	$values = array(
    		'simpleCookie', 'space cookie', '!@#$%^*&()* ][{}?;', "line\n\rbreaks"
    	);
    	
    	foreach ($values as $val) {
    		$cookie = new Zend_Http_Cookie('cookie', $val, 'example.com', time(), '/', true);
    		$this->assertEquals($val, $cookie->getValue());
    	}
    }
    
    /**
     * Make sure we get the correct value if it was set through fromString()
     *
     */
    public function testGetValueFromString()
    {
    	$values = array(
    		'simpleCookie', 'space cookie', '!@#$%^*&()* ][{}?;', "line\n\rbreaks"
    	);
    	
    	foreach ($values as $val) {
    		$cookie = Zend_Http_Cookie::fromString('cookie=' . urlencode($val) . '; domain=example.com');
    		$this->assertEquals($val, $cookie->getValue());
    	}
    }

    /**
     * Make sure we get the correct domain when it's set in the cookie string
     * 
     */
    public function testGetDomainInStr() 
    {
        $domains = array(
            'cookie=foo; domain=example.com' => 'example.com',
            'cookie=foo; path=/; expires=Tue, 21-Nov-2006 08:33:44 GMT; domain=.example.com;' => '.example.com',
            'cookie=foo; domain=some.really.deep.domain.com; path=/; expires=Tue, 21-Nov-2006 08:33:44 GMT;' => 'some.really.deep.domain.com'
        );
    	
        foreach ($domains as $cstr => $domain) {
        	$cookie = Zend_Http_Cookie::fromString($cstr);
        	if (! $cookie instanceof Zend_Http_Cookie) $this->fail('We didn\'t get a valid Cookie object');
        	$this->assertEquals($domain, $cookie->getDomain());
        }
    }

    /**
     * Make sure we get the correct domain when it's set in a reference URL
     * 
     */
    public function testGetDomainInRefUrl() 
    {
        $domains = array(
            'example.com', 'www.example.com', 'some.really.deep.domain.com'
        );
    	
        foreach ($domains as $domain) {
        	$cookie = Zend_Http_Cookie::fromString('foo=baz; path=/', 'http://' . $domain);
        	if (! $cookie instanceof Zend_Http_Cookie) $this->fail('We didn\'t get a valid Cookie object');
        	$this->assertEquals($domain, $cookie->getDomain());
        }
    }

    /**
     * Make sure we get the correct path when it's set in the cookie string
     */
    public function testGetPathInStr() 
    {
    	$cookies = array(
    	    'cookie=foo; domain=example.com' => '/',
            'cookie=foo; path=/foo/baz; expires=Tue, 21-Nov-2006 08:33:44 GMT; domain=.example.com;' => '/foo/baz',
            'cookie=foo; domain=some.really.deep.domain.com; path=/Space Out/; expires=Tue, 21-Nov-2006 08:33:44 GMT;' => '/Space Out/'
        );
        
        foreach ($cookies as $cstr => $path) {
        	$cookie = Zend_Http_Cookie::fromString($cstr);
        	if (! $cookie instanceof Zend_Http_Cookie) $this->fail('Failed generatic a valid cookie object');
        	$this->assertEquals($path, $cookie->getPath(), 'Cookie path is not as expected');
        }
    }

    /**
     * Make sure we get the correct path when it's set a reference URL
     */
    public function testGetPathInRefUrl() 
    {
    	$refUrls = array(
    	    'http://www.example.com/foo/bar/' => '/foo/bar',
    	    'http://foo.com'                 => '/',
    	    'http://qua.qua.co.uk/path/to/very/deep/file.php' => '/path/to/very/deep'
    	);
    	
    	foreach ($refUrls as $url => $path) {
    		$cookie = Zend_Http_Cookie::fromString('foo=bar', $url);
    		if (! $cookie instanceof Zend_Http_Cookie) $this->fail('Failed generating a valid cookie object');
    		$this->assertEquals($path, $cookie->getPath(), 'Cookie path is not as expected');
    	}
    }

    /**
     * Test we get the correct expiry time
     * 
     */
    public function testGetExpiryTime() 
    {
    	$now = time();
    	$yesterday = $now - (3600 * 24);
        $cookies = array(
            'cookie=bar; domain=example.com; expires=' . date(DATE_COOKIE, $now) . ';' => $now,
            'cookie=foo; expires=' . date(DATE_COOKIE, $yesterday) . '; domain=some.really.deep.domain.com; path=/;' => $yesterday,
            'cookie=baz; domain=foo.com; path=/some/path; secure' => null
        );
        
        foreach ($cookies as $cstr => $exp) {
        	$cookie = Zend_Http_Cookie::fromString($cstr);
        	if (! $cookie) $this->fail('Got no cookie object from a valid cookie string');
        	$this->assertEquals($exp, $cookie->getExpiryTime(), 'Expiry time is not as expected');
        }
    }

    /**
     * Make sure the "is secure" flag is correctly set
     */
    public function testIsSecure() 
    {
        $cookies = array(
            'cookie=foo; path=/foo/baz; secure; expires=Tue, 21-Nov-2006 08:33:44 GMT; domain=.example.com;' => true,
            'cookie=foo; path=/; expires=Tue, 21-Nov-2006 08:33:44 GMT; domain=.example.com;' => false,
            'cookie=foo; path=/; SECURE; domain=.example.com;' => true,
            'cookie=foo; path=/; domain=.example.com; SECURE' => true
        );
        
        foreach ($cookies as $cstr => $secure) {
        	$cookie = Zend_Http_Cookie::fromString($cstr);
        	if (! $cookie) $this->fail('Got no cookie object from a valid cookie string');
        	$this->assertEquals($secure, $cookie->isSecure(), 'isSecure is not as expected');
        }
        
    }

    /**
     * Make sure we get the correct value for 'isExpired'
     */
    public function testIsExpired() 
    {
		$notexpired = time() + 3600;
		$expired = time() - 3600;
		
		$cookies = array(
			'cookie=foo; domain=example.com; expires=' . date(DATE_COOKIE, $notexpired) => false,
			'cookie=foo; domain=example.com; expires=' . date(DATE_COOKIE, $expired) => true,
			'cookie=foo; domain=example.com;' => false
		);
		
		foreach ($cookies as $cstr => $isexp) {
			$cookie = Zend_Http_Cookie::fromString($cstr);
			if (! $cookie) $this->fail('Got no cookie object from a valid cookie string');
			$this->assertEquals($isexp, $cookie->isExpired(), 'Got the wrong value for isExpired()');
		}
    }

    /**
     * Make sure we get the correct value for 'isExpired', when time is manually set
     */
    public function testIsExpiredDifferentTime() 
    {
		$notexpired = time() + 3600;
		$expired = time() - 3600;
		$now = time() + 7200;
		
		$cookies = array(
			'cookie=foo; domain=example.com; expires=' . date(DATE_COOKIE, $notexpired),
			'cookie=foo; domain=example.com; expires=' . date(DATE_COOKIE, $expired)
		);
		
		// Make sure all cookies are expired
		foreach ($cookies as $cstr) {
			$cookie = Zend_Http_Cookie::fromString($cstr);
			if (! $cookie) $this->fail('Got no cookie object from a valid cookie string');
			$this->assertTrue($cookie->isExpired($now), 'Cookie is expected to be expired');
		}
		
		// Make sure all cookies are not expired
		$now = time() - 7200;
		foreach ($cookies as $cstr) {
			$cookie = Zend_Http_Cookie::fromString($cstr);
			if (! $cookie) $this->fail('Got no cookie object from a valid cookie string');
			$this->assertFalse($cookie->isExpired($now), 'Cookie is expected not to be expired');
		}
    }

    /**
     * Test we can properly check if a cookie is a session cookie (has no expiry time)
     */
    public function testIsSessionCookie() 
    {
        $cookies = array(
            'cookie=foo; path=/foo/baz; secure; expires=Tue, 21-Nov-2006 08:33:44 GMT; domain=.example.com;' => false,
            'cookie=foo; path=/; domain=.example.com;' => true,
            'cookie=foo; path=/; secure; domain=.example.com;' => true,
            'cookie=foo; path=/; domain=.example.com; secure; expires=' . date(DATE_COOKIE) => false
        );
        
        foreach ($cookies as $cstr => $issession) {
        	$cookie = Zend_Http_Cookie::fromString($cstr);
        	if (! $cookie) $this->fail('Got no cookie object from a valid cookie string');
        	$this->assertEquals($issession, $cookie->isSessionCookie(), 'isSessionCookie is not as expected');
        }
    }

    /**
     * Make sure cookies are properly converted back to strings
     */
    public function testToString() 
    {
    	$cookies = array(
    	    'name=value;',
    	    'blank=;',
    	    'urlencodedstuff=' . urlencode('!@#$)(@$%_+{} !@#?^&') . ';',    	
    	);
    	
    	foreach ($cookies as $cstr) {
    		$cookie = Zend_Http_Cookie::fromString($cstr, 'http://example.com');
    		if (! $cookie) $this->fail('Got no cookie object from a valid cookie string');
    		$this->assertEquals($cstr, $cookie->__toString(), 'Cookie is not converted back to the expected string');
    	}
    
    }
    
    public function testGarbageInStrIsIgnored()
    {
    	$cookies = array(
    	    'name=value; domain=foo.com; silly=place; secure',
    	    'foo=value; someCrap; secure; domain=foo.com; ',
    	    'anothercookie=value; secure; has some crap; ignore=me; domain=foo.com; '
    	);
    	
    	foreach ($cookies as $cstr) {
    		$cookie = Zend_Http_Cookie::fromString($cstr);
    		if (! $cookie) $this->fail('Got no cookie object from a valid cookie string');
    		$this->assertEquals('value', $cookie->getValue(), 'Value is not as expected');
    		$this->assertEquals('foo.com', $cookie->getDomain(), 'Domain is not as expected');
    		$this->assertTrue($cookie->isSecure(), 'Cookie is expected to be secure');
    	}
    }

    /**
     * Test the match() method against a domain
     * 
     */
    public function testMatchDomain() 
    {
    	$cookie = Zend_Http_Cookie::fromString('foo=bar; domain=.example.com;');
    	$this->assertTrue($cookie->match('http://www.example.com/foo/bar.php'), 'Cookie expected to match, but didn\'t');
    	$this->assertFalse($cookie->match('http://www.somexample.com/foo/bar.php'), 'Cookie expected not to match, but did');
    	
    	$uri = Zend_Uri::factory('http://www.foo.com/some/file.txt');
    	$cookie = Zend_Http_Cookie::fromString('cookie=value; domain=www.foo.com');
    	$this->assertTrue($cookie->match($uri), 'Cookie expected to match, but didn\'t');
    	$this->assertTrue($cookie->match('http://il.www.foo.com'), 'Cookie expected to match, but didn\'t');
    	$this->assertFalse($cookie->match('http://bar.foo.com'), 'Cookie expected not to match, but did');
    }
    
    /**
     * Test the match() method against a domain
     * 
     */
    public function testMatchPath() 
    {
    	$cookie = Zend_Http_Cookie::fromString('foo=bar; domain=.example.com; path=/foo');
    	$this->assertTrue($cookie->match('http://www.example.com/foo/bar.php'), 'Cookie expected to match, but didn\'t');
    	$this->assertFalse($cookie->match('http://www.example.com/bar.php'), 'Cookie expected not to match, but did');
    	
    	$cookie = Zend_Http_Cookie::fromString('cookie=value; domain=www.foo.com; path=/some/long/path');
    	$this->assertTrue($cookie->match('http://www.foo.com/some/long/path/file.txt'), 'Cookie expected to match, but didn\'t');
    	$this->assertTrue($cookie->match('http://www.foo.com/some/long/path/and/even/more'), 'Cookie expected to match, but didn\'t');
    	$this->assertFalse($cookie->match('http://www.foo.com/some/long/file.txt'), 'Cookie expected not to match, but did');
    	$this->assertFalse($cookie->match('http://www.foo.com/some/different/path/file.txt'), 'Cookie expected not to match, but did');
    }
    
    /**
     * Test the match() method against secure / non secure connections
     *
     */
    public function testMatchSecure()
    {
    	// A non secure cookie, should match both
    	$cookie = Zend_Http_Cookie::fromString('foo=bar; domain=.example.com;');
    	$this->assertTrue($cookie->match('http://www.example.com/foo/bar.php'), 'Cookie expected to match, but didn\'t');
    	$this->assertTrue($cookie->match('https://www.example.com/bar.php'), 'Cookie expected to match, but didn\'t');
    	
    	// A secure cookie, should match secure connections only
    	$cookie = Zend_Http_Cookie::fromString('foo=bar; domain=.example.com; secure');
    	$this->assertFalse($cookie->match('http://www.example.com/foo/bar.php'), 'Cookie expected not to match, but it did');
    	$this->assertTrue($cookie->match('https://www.example.com/bar.php'), 'Cookie expected to match, but didn\'t');
    }

    /**
     * Test the match() method against different expiry times
     *
     */
    public function testMatchExpire()
    {
    	// A session cookie - should always be valid
    	$cookie = Zend_Http_Cookie::fromString('foo=bar; domain=.example.com;');
    	$this->assertTrue($cookie->match('http://www.example.com/'), 'Cookie expected to match, but didn\'t');
    	$this->assertTrue($cookie->match('http://www.example.com/', true, time() + 3600), 'Cookie expected to match, but didn\'t');
    	
    	// A session cookie, should not match
    	$this->assertFalse($cookie->match('https://www.example.com/', false), 'Cookie expected not to match, but it did');
    	$this->assertFalse($cookie->match('https://www.example.com/', false, time() - 3600), 'Cookie expected not to match, but it did');
    	
    	// A cookie with expiry time in the future
    	$cookie = Zend_Http_Cookie::fromString('foo=bar; domain=.example.com; expires=' . date(DATE_COOKIE, time() + 3600));
    	$this->assertTrue($cookie->match('http://www.example.com/'), 'Cookie expected to match, but didn\'t');
    	$this->assertFalse($cookie->match('https://www.example.com/', true, time() + 7200), 'Cookie expected not to match, but it did');
    	
    	// A cookie with expiry time in the past
    	$cookie = Zend_Http_Cookie::fromString('foo=bar; domain=.example.com; expires=' . date(DATE_COOKIE, time() - 3600));
    	$this->assertFalse($cookie->match('http://www.example.com/'), 'Cookie expected not to match, but it did');
    	$this->assertTrue($cookie->match('https://www.example.com/', true, time() - 7200), 'Cookie expected to match, but didn\'t');
    }

    public function testFromStringFalse()
    {
        $cookie = Zend_Http_Cookie::fromString('foo; domain=www.exmaple.com');
        $this->assertEquals(false, $cookie, 'fromString was expected to fail and return false');
        
        $cookie = Zend_Http_Cookie::fromString('=bar; secure; domain=foo.nl');
        $this->assertEquals(false, $cookie, 'fromString was expected to fail and return false');
        
        $cookie = Zend_Http_Cookie::fromString('fo;o=bar; secure; domain=foo.nl');
        $this->assertEquals(false, $cookie, 'fromString was expected to fail and return false');
    }
    
    /**
     * Test that cookies with far future expiry date (beyond the 32 bit unsigned int range) are
     * not mistakenly marked as 'expired' 
     *
     * @link http://framework.zend.com/issues/browse/ZF-5690
     */
    public function testZF5690OverflowingExpiryDate()
    {
        $expTime = "Sat, 29-Jan-2039 00:54:42 GMT";
        $cookie = Zend_Http_Cookie::fromString("foo=bar; domain=.example.com; expires=$expTime");
        $this->assertFalse($cookie->isExpired(), 'Expiry: ' . $cookie->getExpiryTime()); 
    }
}
