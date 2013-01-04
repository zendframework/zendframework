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

use Zend\Uri\Uri;

/**
 * @category   Zend
 * @package    Zend_Uri
 * @subpackage UnitTests
 * @group      Zend_Uri
 */
class UriTest extends \PHPUnit_Framework_TestCase
{
    /**
     * General composing / parsing tests
     */

    /**
     * Test that parsing and composing a valid URI returns the same URI
     *
     * @param        string $uriString
     * @dataProvider validUriStringProvider
     */
    public function testParseComposeUri($uriString)
    {
        $uri = new Uri($uriString);
        $this->assertEquals($uriString, $uri->toString());
    }

    /**
     * Test composing a new URI by setting the different URI parts programatically.
     *
     * Also tests casting a URI object to string.
     *
     * @param string $exp
     * @param array  $parts
     * @dataProvider uriWithPartsProvider
     */
    public function testComposeNewUriAndCastToString($exp, $parts)
    {
        $uri = new Uri;
        foreach ($parts as $k => $v) {
            $setMethod = 'set' . ucfirst($k);
            $uri->$setMethod($v);
        }

        $this->assertEquals($exp, (string) $uri);
    }

    /**
     * Test the parseScheme static method to extract the scheme part
     *
     * @param string $uriString
     * @param array  $parts
     * @dataProvider uriWithPartsProvider
     */
    public function testParseScheme($uriString, $parts)
    {
        $scheme = Uri::parseScheme($uriString);
        if (! isset($parts['scheme'])) {
            $parts['scheme'] = null;
        }

        $this->assertEquals($parts['scheme'], $scheme);
    }

    /**
     * Test that parseScheme throws an exception in case of invalid input

     * @param  mixed $input
     * @dataProvider notStringInputProvider
     */
    public function testParseSchemeInvalidInput($input)
    {
        $this->setExpectedException('Zend\Uri\Exception\InvalidArgumentException');
        $scheme = Uri::parseScheme($input);
    }

    /**
     * Test that __toString() (magic) returns an empty string if URI is invalid
     *
     * @dataProvider invalidUriObjectProvider
     */
    public function testMagicToStringEmptyIfInvalid(Uri $uri)
    {
        $this->assertEquals('', (string) $uri);
    }

    /**
     * Test that toString() (not magic) throws an exception if URI is invalid
     *
     * @dataProvider invalidUriObjectProvider
     */
    public function testToStringThrowsExceptionIfInvalid(Uri $uri)
    {
        $this->setExpectedException('Zend\Uri\Exception\InvalidUriException');
        $string = $uri->toString();
    }

    /**
     * Test that we can parse a malformed URI
     *
     * @link http://framework.zend.com/issues/browse/ZF-11286
     */
    public function testCanParseMalformedUrlZF11286()
    {
        $urlString = 'http://example.org/SitePages/file has spaces.html?foo=bar';
        $uri = new Uri($urlString);
        $fixedUri = new Uri($uri->toString());

        $this->assertEquals('/SitePages/file%20has%20spaces.html', $fixedUri->getPath());
    }

    /**
     * Accessor Tests
     */

    /**
     * Test that we can get the scheme out of a parsed URI
     *
     * @param string $uriString
     * @param array  $parts
     * @dataProvider uriWithPartsProvider
     */
    public function testGetScheme($uriString, $parts)
    {
        $uri = new Uri($uriString);
        if (isset($parts['scheme'])) {
            $this->assertEquals($parts['scheme'], $uri->getScheme());
        } else {
            $this->assertNull($uri->getScheme());
        }
    }

    /**
     * Test that we get the correct userInfo
     *
     * @param string $uriString
     * @param array  $parts
     * @dataProvider uriWithPartsProvider
     */
    public function testGetUserInfo($uriString, $parts)
    {
        $uri = new Uri($uriString);
        if (isset($parts['userInfo'])) {
            $this->assertEquals($parts['userInfo'], $uri->getUserInfo());
        } else {
            $this->assertNull($uri->getUserInfo());
        }
    }

    /**
     * Test that we can get the host out of a parsed URI
     *
     * @param string $uriString
     * @param array  $parts
     * @dataProvider uriWithPartsProvider
     */
    public function testGetHost($uriString, $parts)
    {
        $uri = new Uri($uriString);
        if (isset($parts['host'])) {
            $this->assertEquals($parts['host'], $uri->getHost());
        } else {
            $this->assertNull($uri->getHost());
        }
    }

    /**
     * Test that we can get the port out of a parsed Uri
     *
     * @param string $uriString
     * @param array  $parts
     * @dataProvider uriWithPartsProvider
     */
    public function testGetPort($uriString, $parts)
    {
        $uri = new Uri($uriString);
        if (isset($parts['port'])) {
            $this->assertEquals($parts['port'], $uri->getPort());
        } else {
            $this->assertNull($uri->getPort());
        }
    }

    /**
     * Test that we can get the path out of a parsed Uri
     *
     * @param string $uriString
     * @param array  $parts
     * @dataProvider uriWithPartsProvider
     */
    public function testGetPath($uriString, $parts)
    {
        $uri = new Uri($uriString);
        if (isset($parts['path'])) {
            $this->assertEquals($parts['path'], $uri->getPath());
        } else {
            $this->assertNull($uri->getPath());
        }
    }

    /**
     * Test that we can get the query out of a parsed Uri
     *
     * @param string $uriString
     * @param array  $parts
     * @dataProvider uriWithPartsProvider
     */
    public function testGetQuery($uriString, $parts)
    {
        $uri = new Uri($uriString);
        if (isset($parts['query'])) {
            $this->assertEquals($parts['query'], $uri->getQuery());
        } else {
            $this->assertNull($uri->getQuery());
        }
    }

    /**
     * @group ZF-1480
     */
    public function testGetQueryAsArrayReturnsCorrectArray()
    {
        $url = new Uri('http://example.com/foo/?test=a&var[]=1&var[]=2&some[thing]=3');
        $this->assertEquals('test=a&var[]=1&var[]=2&some[thing]=3', $url->getQuery());

        $exp = array(
            'test' => 'a',
            'var'  => array(1, 2),
            'some' => array('thing' => 3)
        );

        $this->assertEquals($exp, $url->getQueryAsArray());
    }

    /**
     * Test that we can get the fragment out of a parsed URI
     *
     * @param string $uriString
     * @param array  $parts
     * @dataProvider uriWithPartsProvider
     */
    public function testGetFragment($uriString, $parts)
    {
        $uri = new Uri($uriString);
        if (isset($parts['fragment'])) {
            $this->assertEquals($parts['fragment'], $uri->getFragment());
        } else {
            $this->assertNull($uri->getFragment());
        }
    }

    /**
     * Mutator Tests
     */

    /**
     * Test we can set the scheme to NULL
     *
     */
    public function testSetSchemeNull()
    {
        $uri = new Uri('http://example.com');
        $this->assertEquals('http', $uri->getScheme());

        $uri->setScheme(null);
        $this->assertNull($uri->getScheme());
    }

    /**
     * Test we can set different valid schemes
     *
     * @param string $scheme
     * @dataProvider validSchemeProvider
     */
    public function testSetSchemeValid($scheme)
    {
        $uri = new Uri;
        $uri->setScheme($scheme);
        $this->assertEquals($scheme, $uri->getScheme());
    }

    /**
     * Test that setting an invalid scheme causes an exception
     *
     * @param string $scheme
     * @dataProvider invalidSchemeProvider
     */
    public function testSetInvalidScheme($scheme)
    {
        $uri = new Uri;
        $this->setExpectedException('Zend\Uri\Exception\InvalidUriPartException');
        $uri->setScheme($scheme);
    }

    /**
     * Test that we can set a valid hostname
     *
     * @param string $host
     * @dataProvider validHostProvider
     */
    public function testSetGetValidHost($host)
    {
        $uri = new Uri;
        $uri->setHost($host);
        $this->assertEquals($host, $uri->getHost());
    }

    /**
     * Test that when setting an invalid host an exception is thrown
     *
     * @param string $host
     * @dataProvider invalidHostProvider
     */
    public function testSetInvalidHost($host)
    {
        $uri = new Uri;
        $this->setExpectedException('Zend\Uri\Exception\InvalidUriPartException');
        $uri->setHost($host);
    }

    /**
     * Test that we can set the host part to 'null'
     *
     */
    public function testSetNullHost()
    {
        $uri = new Uri('http://example.com/bar');
        $uri->setHost(null);
        $this->assertNull($uri->getHost());
    }

    /**
     * Test that we can use an array to set the query parameters
     *
     * @param array  $data
     * @param string $expqs
     * @dataProvider queryParamsArrayProvider
     */
    public function testSetQueryFromArray(array $data, $expqs)
    {
        $uri = new Uri();
        $uri->setQuery($data);

        $this->assertEquals('?' . $expqs, $uri->toString());
    }

    /**
     * Validation and encoding tests
     */

    /**
     * Test that valid URIs pass validation
     *
     * @param string $uriString
     * @dataProvider validUriStringProvider
     */
    public function testValidUriIsValid($uriString)
    {
        $uri = new Uri($uriString);
        $this->assertTrue($uri->isValid());
    }

    /**
     * Test that valid relative URIs pass validation
     *
     * @param string $uriString
     * @dataProvider validRelativeUriStringProvider
     */
    public function testValidRelativeUriIsValid($uriString)
    {
        $uri = new Uri($uriString);
        $this->assertTrue($uri->isValidRelative());
    }

    /**
     * Test that invalid URIs fail validation
     *
     * @param \Zend\Uri\Uri $uri
     * @dataProvider invalidUriObjectProvider
     */
    public function testInvalidUriIsInvalid(Uri $uri)
    {
        $this->assertFalse($uri->isValid());
    }

    /**
     * Test that invalid relative URIs fail validation
     *
     * @param \Zend\Uri\Uri $uri
     * @dataProvider invalidRelativeUriObjectProvider
     */
    public function testInvalidRelativeUriIsInvalid(Uri $uri)
    {
        $this->assertFalse($uri->isValidRelative());
    }

    /**
     * Check that valid schemes are valid according to validateScheme()
     *
     * @param string $scheme
     * @dataProvider validSchemeProvider
     */
    public function testValidateSchemeValid($scheme)
    {
        $this->assertTrue(Uri::validateScheme($scheme));
    }

    /**
     * Check that invalid schemes are invalid according to validateScheme()
     *
     * @param string $scheme
     * @dataProvider invalidSchemeProvider
     */
    public function testValidateSchemeInvalid($scheme)
    {
        $this->assertFalse(Uri::validateScheme($scheme));
    }

    /**
     * Check that valid hosts are valid according to validateHost()
     *
     * @param string $host
     * @dataProvider validHostProvider
     */
    public function testValidateHostValid($host)
    {
        $this->assertTrue(Uri::validateHost($host));
    }

    /**
     * Check that invalid hosts are invalid according to validateHost()
     *
     * @param string $host
     * @dataProvider invalidHostProvider
     */
    public function testValidateHostInvalid($host)
    {
        $this->assertFalse(Uri::validateHost($host));
    }

    /**
     * Check that valid paths are valid according to validatePath()
     *
     * @param string $path
     * @dataProvider validPathProvider
     */
    public function testValidatePathValid($path)
    {
        $this->assertTrue(Uri::validatePath($path));
    }

    /**
     * Check that invalid paths are invalid according to validatePath()
     *
     * @param string $path
     * @dataProvider invalidPathProvider
     */
    public function testValidatePathInvalid($path)
    {
        $this->assertFalse(Uri::validatePath($path));
    }

    /**
     * Test that valid path parts are unchanged by the 'encode' function
     *
     * @param string $path
     * @dataProvider validPathProvider
     */
    public function testEncodePathValid($path)
    {
        $this->assertEquals($path, Uri::encodePath($path));
    }

    /**
     * Test that invalid path parts are properly encoded by the 'encode' function
     *
     * @param string $path
     * @param string $exp
     * @dataProvider invalidPathProvider
     */
    public function testEncodePathInvalid($path, $exp)
    {
        $this->assertEquals($exp, Uri::encodePath($path));
    }

    /**
     * Test that valid query or fragment parts are validated properly
     *
     * @param $input
     * @dataProvider validQueryFragmentProvider
     */
    public function testValidQueryFragment($input)
    {
        $this->assertTrue(Uri::validateQueryFragment($input));
    }

    /**
     * Test that invalid query or fragment parts are validated properly
     *
     * @param $input
     * @dataProvider invalidQueryFragmentProvider
     */
    public function testInvalidQueryFragment($input, $exp)
    {
        $this->assertFalse(Uri::validateQueryFragment($input));
    }

    /**
     * Test that valid query or fragment parts properly encoded
     *
     * @param $input
     * @param $exp
     * @dataProvider invalidQueryFragmentProvider
     */
    public function testEncodeInvalidQueryFragment($input, $exp)
    {
        $actual = Uri::encodeQueryFragment($input);
        $this->assertEquals($exp, $actual);
    }

    /**
     * Test that valid query or fragment parts are not modified when paseed
     * through encodeQueryFragment()
     *
     * @param $input
     * @param $exp
     * @dataProvider validQueryFragmentProvider
     */
    public function testEncodeValidQueryFragment($input)
    {
        $actual = Uri::encodeQueryFragment($input);
        $this->assertEquals($input, $actual);
    }

    /**
     * Test that valid userInfo input is validated by validateUserInfo
     *
     * @param string $userInfo
     * @dataProvider validUserInfoProvider
     */
    public function testValidateUserInfoValid($userInfo)
    {
        $this->assertTrue(Uri::validateUserInfo($userInfo));
    }

    /**
     * Test that invalid userInfo input is not accepted by validateUserInfo
     *
     * @param string $userInfo
     * @param string $exp
     * @dataProvider invalidUserInfoProvider
     */
    public function testValidateUserInfoInvalid($userInfo, $exp)
    {
        $this->assertFalse(Uri::validateUserInfo($userInfo));
    }

    /**
     * Test that valid userInfo is returned unchanged by encodeUserInfo
     *
     * @param $userInfo
     * @dataProvider validUserInfoProvider
     */
    public function testEncodeUserInfoValid($userInfo)
    {
        $this->assertEquals($userInfo, Uri::encodeUserInfo($userInfo));
    }

    /**
     * Test that invalid userInfo input properly encoded by encodeUserInfo
     *
     * @param string $userInfo
     * @param string $exp
     * @dataProvider invalidUserInfoProvider
     */
    public function testEncodeUserInfoInvalid($userInfo, $exp)
    {
        $this->assertEquals($exp, Uri::encodeUserInfo($userInfo));
    }

    /**
     * Test that validatePort works for valid ports
     *
     * @param mixed $port
     * @dataProvider validPortProvider
     */
    public function testValidatePortValid($port)
    {
        $this->assertTrue(Uri::validatePort($port));
    }

    /**
     * Test that validatePort works for invalid ports
     *
     * @param mixed $port
     * @dataProvider invalidPortProvider
     */
    public function testValidatePortInvalid($port)
    {
        $this->assertFalse(Uri::validatePort($port));
    }

    /**
     * @group ZF-1480
     */
    /*
    public function testAddReplaceQueryParametersModifiesQueryAndReturnsOldQuery()
    {
        $url = new Uri('http://example.com/foo/?a=1&b=2&c=3');
        $url->addReplaceQueryParameters(array('b' => 4, 'd' => -1));
        $this->assertEquals(array(
            'a' => 1,
            'b' => 4,
            'c' => 3,
            'd' => -1
        ), $url->getQueryAsArray());
        $this->assertEquals('a=1&b=4&c=3&d=-1', $url->getQuery());
    }
    */

    /**
     * @group ZF-1480
     */
    /*
    public function testRemoveQueryParametersModifiesQueryAndReturnsOldQuery()
    {
        $url = new Uri('http://example.com/foo/?a=1&b=2&c=3&d=4');
        $url->removeQueryParameters(array('b', 'd', 'e'));
        $this->assertEquals(array('a' => 1, 'c' => 3), $url->getQueryAsArray());
        $this->assertEquals('a=1&c=3', $url->getQuery());
    }
    */

    /**
     * Resolving, Normalization and Reference creation tests
     */

    /**
     * Test that resolving relative URIs works using the examples specified in
     * the RFC
     *
     * @param string $relative
     * @param string $expected
     * @dataProvider resolvedAbsoluteUriProvider
     */
    public function testRelativeUriResolvingRfcSamples($baseUrl, $relative, $expected)
    {
        $uri = new Uri($relative);
        $uri->resolve($baseUrl);

        $this->assertEquals($expected, $uri->toString());
    }

    /**
     * Test the removal of extra dot segments from paths
     *
     * @param        $orig
     * @param        $expected
     * @dataProvider pathWithDotSegmentProvider
     */
    public function testRemovePathDotSegments($orig, $expected)
    {
        $this->assertEquals($expected, Uri::removePathDotSegments($orig));
    }

    /**
     * Test normalizing URLs
     *
     * @param string $orig
     * @param string $expected
     * @dataProvider normalizedUrlsProvider
     */
    public function testNormalizeUrl($orig, $expected)
    {
        $url = new Uri($orig);
        $this->assertEquals($expected, $url->normalize()->toString());
    }

    /**
     * Test the merge() static method for merging new URIs
     *
     * @param string $base
     * @param string $relative
     * @param string $expected
     * @dataProvider resolvedAbsoluteUriProvider
     */
    public function testMergeToNewUri($base, $relative, $expected)
    {
        $actual = Uri::merge($base, $relative)->toString();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Make sure that the ::merge() method does not modify any input objects
     *
     * This performs two checks:
     *  1. That the result object is *not* the same object as any of the input ones
     *  2. That the method doesn't modify the input objects
     *
     */
    public function testMergeTwoObjectsNotModifying()
    {
        $base = new Uri('http://example.com/bar');
        $ref  = new Uri('baz?qwe=1');

        $baseSig = serialize($base);
        $refSig  = serialize($ref);

        $actual = Uri::merge($base, $ref);

        $this->assertNotSame($base, $actual);
        $this->assertNotSame($ref, $actual);

        $this->assertEquals($baseSig, serialize($base));
        $this->assertEquals($refSig, serialize($ref));
    }

    /**
     * Test that makeRelative() works as expected
     *
     * @dataProvider commonBaseUriProvider
     */
    public function testMakeRelative($base, $url, $expected)
    {
        $url = new Uri($url);
        $url->makeRelative($base);
        $this->assertEquals($expected, $url->toString());
    }

    /**
     * Other tests
     */

    /**
     * Test that the copy constructor works
     *
     * @dataProvider validUriStringProvider
     */
    public function testConstructorCopyExistingObject($uriString)
    {
        $uri = new Uri($uriString);
        $uri2 = new Uri($uri);

        $this->assertEquals($uri, $uri2);
    }

    /**
     * Test that the constructor throws an exception on invalid input
     *
     * @param mixed $input
     * @dataProvider invalidConstructorInputProvider
     */
    public function testConstructorInvalidInput($input)
    {
        $this->setExpectedException('Zend\Uri\Exception\InvalidArgumentException');
        $uri = new Uri($input);
    }

    /**
     * Test the fluent interface
     *
     * @param string $method
     * @param string $params
     * @dataProvider fluentInterfaceMethodProvider
     */
    public function testFluentInterface($method, $params)
    {
        $uri = new Uri;
        $ret = call_user_func_array(array($uri, $method), $params);
        $this->assertSame($uri, $ret);
    }

    /**
     * Data Providers
     */

    public function validUserInfoProvider()
    {
        return array(
            array('user:'),
            array(':password'),
            array('user:password'),
            array(':'),
            array('my-user'),
            array('one:two:three:four'),
            array('my-user-has-%3A-colon:pass'),
            array('a_.!~*\'(-)n0123Di%25%26:pass;:&=+$,word')
        );
    }

    public function invalidUserInfoProvider()
    {
        return array(
            array('an`di:password',    'an%60di:password'),
            array('user name',         'user%20name'),
            array('shahar.e@zend.com', 'shahar.e%40zend.com')
        );
    }

    /**
     * Data provider for valid URIs, not necessarily complete
     *
     * @return array
     */
    public function validUriStringProvider()
    {
        return array(
            array('a:b'),
            array('http://www.zend.com'),
            array('https://example.com:10082/foo/bar?query'),
            array('../relative/path'),
            array('?queryOnly'),
            array('#fragmentOnly'),
            array('mailto:bob@example.com'),
            array('bob@example.com'),
            array('http://a_.!~*\'(-)n0123Di%25%26:pass;:&=+$,word@www.zend.com'),
            array('http://[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]:80/index.html'),
            array('http://[1080::8:800:200C:417A]/foo'),
            array('http://[::192.9.5.5]/ipng'),
            array('http://[::FFFF:129.144.52.38]:80/index.html'),
            array('http://[2620:0:1cfe:face:b00c::3]/'),
            array('http://[2010:836B:4179::836B:4179]'),
            array('http'),
            array('www.example.org:80'),
            array('www.example.org'),
            array('http://foo'),
            array('ftp://user:pass@example.org/'),
            array('www.fi/'),
            array('http://1.1.1.1/'),
            array('1.1.1.1'),
            array('1.256.1.1'), // Hostnames can be only numbers
            array('http://[::1]/'),
            array('file:/'), // schema without host
            array('http:///'), // host empty
            array('http:::/foo'), // schema + path
            array('2620:0:1cfe:face:b00c::3'), // Not IPv6, is Path
        );
    }

    /**
     * Data provider for valid relative URIs, not necessarily complete
     *
     * @return array
     */
    public function validRelativeUriStringProvider()
    {
        return array(
            array('foo/bar?query'),
            array('../relative/path'),
            array('?queryOnly'),
            array('#fragmentOnly'),
        );
    }

    /**
     * Valid schemes
     *
     * @return array
     */
    public function validSchemeProvider()
    {
        return array(
            // Valid schemes
            array('http'),
            array('HTTP'),
            array('File'),
            array('h'),
            array('h2'),
            array('a+b'),
            array('k-'),
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
            array('ht tp'),
            array('htp_p'),
            array('-tp'),
            array('22c'),
            array('h%acp'),
        );
    }

    /**
     * Valid query or fragment parts
     *
     * Each valid query or fragment part should require no encoding and if
     * passed throuh an encoding method shoudl return unchanged.
     *
     * @return array
     */
    public function validQueryFragmentProvider()
    {
        return array(
            array('a=1&b=2&c=3&d=4'),
            array('with?questionmark/andslash'),
            array('id=123&url=http://example.com/?bar=foo+baz'),
            array('with%20%0Aline%20break'),
        );
    }

    /**
     * Invalid query or fragment parts.
     *
     * Additionally, this method supplies a valid, URL-encoded representation
     * of each invalid part, which can be used to test encoding.
     *
     * @return array
     */
    public function invalidQueryFragmentProvider()
    {
        return array(
            array('with#pound', 'with%23pound'),
            array('with space', 'with%20space'),
            array('test=a&var[]=1&var[]=2&some[thing]=3', 'test=a&var%5B%5D=1&var%5B%5D=2&some%5Bthing%5D=3'),
            array("with \nline break", "with%20%0Aline%20break"),
            array("with%percent", "with%25percent"),
        );
    }

    /**
     * Data provider for invalid URI objects
     *
     * @return array
     */
    public function invalidUriObjectProvider()
    {
        // Empty URI is not valid
        $obj1 = new Uri;

        // Path cannot begin with '//' if there is no authority part
        $obj2 = new Uri;
        $obj2->setPath('//path');

        // A port-only URI with no host
        $obj3 = new Uri;
        $obj3->setPort(123);

        // A userinfo-only URI with no host
        $obj4 = new Uri;
        $obj4->setUserInfo('shahar:password');

        return array(
            array($obj1),
            array($obj2),
            array($obj3),
            array($obj4)
        );
    }

    /**
     * Data provider for invalid relative URI objects
     *
     * @return array
     */
    public function invalidRelativeUriObjectProvider()
    {
        // Empty URI is not valid
        $obj1 = new Uri;

        // Path cannot begin with '//'
        $obj2 = new Uri;
        $obj2->setPath('//path');

        // A object with port
        $obj3 = new Uri;
        $obj3->setPort(123);

        // A object with userInfo
        $obj4 = new Uri;
        $obj4->setUserInfo('shahar:password');

        // A object with scheme
        $obj5 = new Uri;
        $obj5->setScheme('https');

        // A object with host
        $obj6 = new Uri;
        $obj6->setHost('example.com');

        return array(
            array($obj1),
            array($obj2),
            array($obj3),
            array($obj4),
            array($obj5),
            array($obj6)
        );
    }


    /**
     * Data provider for valid URIs with their different parts
     *
     * @return array
     */
    public function uriWithPartsProvider()
    {
        return array(
            array('ht-tp://server/path?query', array(
                'scheme'   => 'ht-tp',
                'host'     => 'server',
                'path'     => '/path',
                'query'    => 'query',
            )),
            array('file:///foo/bar', array(
                'scheme'   => 'file',
                'host'     => '',
                'path'     => '/foo/bar',
            )),
            array('http://dude:lebowski@example.com/#fr/ag?me.nt', array(
                'scheme'   => 'http',
                'userInfo' => 'dude:lebowski',
                'host'     => 'example.com',
                'path'     => '/',
                'fragment' => 'fr/ag?me.nt'
            )),
            array('/relative/path', array(
                'path' => '/relative/path'
            )),
            array('ftp://example.com:5555', array(
                'scheme' => 'ftp',
                'host'   => 'example.com',
                'port'   => 5555,
                'path'   => ''
            )),
            array('http://example.com/foo//bar/baz//fob/', array(
                'scheme' => 'http',
                'host'   => 'example.com',
                'path'   => '/foo//bar/baz//fob/'
            ))
        );
    }

    /**
     * Provider for valid ports
     *
     * @return array
     */
    public function validPortProvider()
    {
        return array(
            array(null),
            array(1),
            array(0xffff),
            array(80),
            array('443')
        );
    }

    /**
     * Provider for invalid ports
     *
     * @return array
     */
    public function invalidPortProvider()
    {
        return array(
            array(0),
            array(-1),
            array(0x10000),
            array('foo'),
            array('0xf'),
            array('-'),
            array(':'),
            array('/')
        );
    }

    public function validHostProvider()
    {
        return array(
            // IPv4 addresses
            array('10.1.2.3'),
            array('127.0.0.1'),
            array('0.0.0.0'),
            array('255.255.255.255'),

            // IPv6 addresses
            // Examples from http://en.wikipedia.org/wiki/IPv6_address
            array('[2001:0db8:85a3:0000:0000:8a2e:0370:7334]'),
            array('[2001:db8:85a3:0:0:8a2e:370:7334]'),
            array('[2001:db8:85a3::8a2e:370:7334]'),
            array('[0:0:0:0:0:0:0:1]'),
            array('[::1]'),
            array('[2001:0db8:85a3:08d3:1319:8a2e:0370:7348]'),

            // Internet and local DNS names
            array('www.example.com'),
            array('zend.com'),
            array('php-israel.org'),
            array('arr.gr'),
            array('localhost'),
            array('loca.host'),
            array('zend-framework.test'),
            array('a.b.c.d'),
            array('a1.b2.c3.d4'),
            array('some-domain-with-dashes'),

            // Registered name (other than DNS names)
            array('some~unre_served.ch4r5'),
            array('pct.%D7%A9%D7%97%D7%A8%20%D7%94%D7%92%D7%93%D7%95%D7%9C.co.il'),
            array('sub-delims-!$&\'()*+,;=.are.ok'),
            array('%2F%3A')
        );
    }

    public function invalidHostProvider()
    {
        return array(
            array('with space'),
            array('[]'),
            array('[12:34'),
        );
    }

    public function validPathProvider()
    {
        return array(
            array(''),
            array('/'),
            array(':'),
            array('/foo/bar'),
            array('foo/bar'),
            array('/foo;arg2=1&arg2=2/bar;baz/bla'),
            array('foo'),
            array('example.com'),
            array('some-path'),
            array('foo:bar'),
            array('C:/Program%20Files/Zend'),
        );
    }

    public function invalidPathProvider()
    {
        return array(
            array('?', '%3F'),
            array('/#', '/%23'),

            // See http://framework.zend.com/issues/browse/ZF-11286
            array('Giri%C5%9F Sayfas%C4%B1.aspx', 'Giri%C5%9F%20Sayfas%C4%B1.aspx')
        );
    }

    /**
     * Return all methods that are expected to return the same object they
     * are called on, to test that the fluent interface is not broken
     *
     * @return array
     */
    public function fluentInterfaceMethodProvider()
    {
        return array(
            array('setScheme',    array('file')),
            array('setUserInfo',  array('userInfo')),
            array('setHost',      array('example.com')),
            array('setPort',      array(80)),
            array('setPath',      array('/baz/baz')),
            array('setQuery',     array('foo=bar')),
            array('setFragment',  array('part2')),
            array('makeRelative', array('http://foo.bar/')),
            array('resolve',      array('http://foo.bar/')),
            array('normalize',    array())
        );
    }

    /**
     * Test cases for absolute URI resolving
     *
     * These examples are taken from RFC-3986 section about relative URI
     * resolving (@link http://tools.ietf.org/html/rfc3986#section-5.4).
     *
     * @return array
     */
    public function resolvedAbsoluteUriProvider()
    {
        return array(
            // Normal examples
            array('http://a/b/c/d;p?q', 'g:h',     'g:h'),
            array('http://a/b/c/d;p?q', 'g',       'http://a/b/c/g'),
            array('http://a/b/c/d;p?q', './g',     'http://a/b/c/g'),
            array('http://a/b/c/d;p?q', 'g/',      'http://a/b/c/g/'),
            array('http://a/b/c/d;p?q', '/g',      'http://a/g'),
            array('http://a/b/c/d;p?q', '//g',     'http://g'),
            array('http://a/b/c/d;p?q', '?y',      'http://a/b/c/d;p?y'),
            array('http://a/b/c/d;p?q', 'g?y',     'http://a/b/c/g?y'),
            array('http://a/b/c/d;p?q', '#s',      'http://a/b/c/d;p?q#s'),
            array('http://a/b/c/d;p?q', 'g#s',     'http://a/b/c/g#s'),
            array('http://a/b/c/d;p?q', 'g?y#s',   'http://a/b/c/g?y#s'),
            array('http://a/b/c/d;p?q', ';x',      'http://a/b/c/;x'),
            array('http://a/b/c/d;p?q', 'g;x',     'http://a/b/c/g;x'),
            array('http://a/b/c/d;p?q', 'g;x?y#s', 'http://a/b/c/g;x?y#s'),
            array('http://a/b/c/d;p?q', '',        'http://a/b/c/d;p?q'),
            array('http://a/b/c/d;p?q', '.',       'http://a/b/c/'),
            array('http://a/b/c/d;p?q', './',      'http://a/b/c/'),
            array('http://a/b/c/d;p?q', '..',      'http://a/b/'),
            array('http://a/b/c/d;p?q', '../',     'http://a/b/'),
            array('http://a/b/c/d;p?q', '../g',    'http://a/b/g'),
            array('http://a/b/c/d;p?q', '../..',   'http://a/'),
            array('http://a/b/c/d;p?q', '../../',  'http://a/'),
            array('http://a/b/c/d;p?q', '../../g', 'http://a/g'),

            // Abnormal examples
            array('http://a/b/c/d;p?q', '../../../g',    'http://a/g'),
            array('http://a/b/c/d;p?q', '../../../../g', 'http://a/g'),
            array('http://a/b/c/d;p?q', '/./g',          'http://a/g'),
            array('http://a/b/c/d;p?q', '/../g',         'http://a/g'),
            array('http://a/b/c/d;p?q', 'g.',            'http://a/b/c/g.'),
            array('http://a/b/c/d;p?q', '.g',            'http://a/b/c/.g'),
            array('http://a/b/c/d;p?q', 'g..',           'http://a/b/c/g..'),
            array('http://a/b/c/d;p?q', '..g',           'http://a/b/c/..g'),
            array('http://a/b/c/d;p?q', './../g',        'http://a/b/g'),
            array('http://a/b/c/d;p?q', './g/.',         'http://a/b/c/g/'),
            array('http://a/b/c/d;p?q', 'g/./h',         'http://a/b/c/g/h'),
            array('http://a/b/c/d;p?q', 'g/../h',        'http://a/b/c/h'),
            array('http://a/b/c/d;p?q', 'g;x=1/./y',     'http://a/b/c/g;x=1/y'),
            array('http://a/b/c/d;p?q', 'g;x=1/../y',    'http://a/b/c/y'),
            array('http://a/b/c/d;p?q', 'http:g',        'http:g'),
        );
    }

    /**
     * Data provider for arrays of query string parameters, with the expected
     * query string
     *
     * @return array
     */
    public function queryParamsArrayProvider()
    {
        return array(
            array(array(
                'foo' => 'bar',
                'baz' => 'waka'
            ), 'foo=bar&baz=waka'),
            array(array(
                'some key' => 'some crazy value?!#[]',
                '1'        => ''
            ), 'some%20key=some%20crazy%20value%3F%21%23%5B%5D&1='),
            array(array(
                'array'        => array('foo', 'bar', 'baz'),
                'otherstuff[]' => 1234
            ), 'array%5B0%5D=foo&array%5B1%5D=bar&array%5B2%5D=baz&otherstuff%5B%5D=1234')
        );
    }

    /**
     * Provider for testing removal of extra dot segments in paths
     *
     * @return array
     */
    public function pathWithDotSegmentProvider()
    {
        return array(
            array('/a/b/c/./../../g',   '/a/g'),
            array('mid/content=5/../6', 'mid/6')
        );
    }

    public function normalizedUrlsProvider()
    {
        return array(
            array('hTtp://example.com', 'http://example.com/'),
            array('https://EXAMPLE.COM/FOO/BAR', 'https://example.com/FOO/BAR'),
            array('FOO:/bar/with space?que%3fry#frag%ment#', 'foo:/bar/with%20space?que?ry#frag%25ment%23'),
            array('/path/%68%65%6c%6c%6f/world', '/path/hello/world'),
            array('/foo/bar?url=http%3A%2F%2Fwww.example.com%2Fbaz', '/foo/bar?url=http://www.example.com/baz'),
            array('File:///SitePages/fi%6ce%20has%20spaces', 'file:///SitePages/file%20has%20spaces'),
            array('/foo/bar/../baz?do=action#showFragment', '/foo/baz?do=action#showFragment'),

            //  RFC 3986 Capitalizing letters in escape sequences.
            array('http://www.example.com/a%c2%b1b', 'http://www.example.com/a%C2%B1b'),

            // This should be left unchanged, at least for the generic Uri class
            array('http://example.com:80/file?query=bar', 'http://example.com:80/file?query=bar'),
        );
    }

    public function commonBaseUriProvider()
    {
        return array(
             array('http://example.com/dir/subdir/', 'http://example.com/dir/subdir/more/file1.txt', 'more/file1.txt'),
             array('http://example.com/dir/subdir/', 'http://example.com/dir/otherdir/file2.txt',    '../otherdir/file2.txt'),
             array('http://example.com/dir/subdir/', 'http://otherhost.com/dir/subdir/file3.txt',    'http://otherhost.com/dir/subdir/file3.txt'),
        );
    }


    /**
     * Provider for testing the constructor's behavior on invalid input
     *
     * @return array
     */
    public function invalidConstructorInputProvider()
    {
        return array(
            array(new \stdClass()),
            array(false),
            array(true),
            array(array('scheme' => 'http')),
            array(12)
        );
    }

    /**
     * Provider for testing the behaviors of functions that expect only strings
     *
     * Most of these methods are expected to throw an exception for the
     * provided values
     *
     * @return array
     */
    public function notStringInputProvider()
    {
        return array(
            array(new Uri('http://foo.bar')),
            array(null),
            array(12),
            array(array('scheme' => 'http', 'host' => 'example.com'))
        );
    }
}
