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

use Zend\Http\Header\SetCookie;

class SetCookieTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group ZF2-254
     */
    public function testSetCookieConstructor()
    {
        $setCookieHeader = new SetCookie(
            'myname', 'myvalue', 'Wed, 13-Jan-2021 22:23:01 GMT',
            '/accounts', 'docs.foo.com', true, true, 99, 9
        );
        $this->assertEquals('myname', $setCookieHeader->getName());
        $this->assertEquals('myvalue', $setCookieHeader->getValue());
        $this->assertEquals('Wed, 13-Jan-2021 22:23:01 GMT', $setCookieHeader->getExpires());
        $this->assertEquals('/accounts', $setCookieHeader->getPath());
        $this->assertEquals('docs.foo.com', $setCookieHeader->getDomain());
        $this->assertTrue($setCookieHeader->isSecure());
        $this->assertTrue($setCookieHeader->isHttpOnly());
        $this->assertEquals(99, $setCookieHeader->getMaxAge());
        $this->assertEquals(9, $setCookieHeader->getVersion());
    }

    public function testSetCookieFromStringCreatesValidSetCookieHeader()
    {
        $setCookieHeader = SetCookie::fromString('Set-Cookie: xxx');
        $this->assertInstanceOf('Zend\Http\Header\MultipleHeaderInterface', $setCookieHeader);
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $setCookieHeader);
        $this->assertInstanceOf('Zend\Http\Header\SetCookie', $setCookieHeader);
    }

    public function testSetCookieFromStringCanCreateSingleHeader()
    {
        $setCookieHeader = SetCookie::fromString('Set-Cookie: myname=myvalue');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $setCookieHeader);
        $this->assertEquals('myname', $setCookieHeader->getName());
        $this->assertEquals('myvalue', $setCookieHeader->getValue());

        $setCookieHeader = SetCookie::fromString(
            'set-cookie: myname=myvalue; Domain=docs.foo.com; Path=/accounts;'
            . 'Expires=Wed, 13-Jan-2021 22:23:01 GMT; Secure; HttpOnly'
        );
        $this->assertInstanceOf('Zend\Http\Header\MultipleHeaderInterface', $setCookieHeader);
        $this->assertEquals('myname', $setCookieHeader->getName());
        $this->assertEquals('myvalue', $setCookieHeader->getValue());
        $this->assertEquals('docs.foo.com', $setCookieHeader->getDomain());
        $this->assertEquals('/accounts', $setCookieHeader->getPath());
        $this->assertEquals('Wed, 13-Jan-2021 22:23:01 GMT', $setCookieHeader->getExpires());
        $this->assertTrue($setCookieHeader->isSecure());
        $this->assertTrue($setCookieHeader->isHttponly());
    }

    public function testSetCookieFromStringCanCreateMultipleHeaders()
    {
        $setCookieHeaders = SetCookie::fromString(
            'Set-Cookie: myname=myvalue, '
            . 'someothername=someothervalue; Domain=docs.foo.com; Path=/accounts;'
            . 'Expires=Wed, 13-Jan-2021 22:23:01 GMT; Secure; HttpOnly'
        );

        $this->assertInternalType('array', $setCookieHeaders);

        $setCookieHeader = $setCookieHeaders[0];
        $this->assertInstanceOf('Zend\Http\Header\MultipleHeaderInterface', $setCookieHeader);
        $this->assertEquals('myname', $setCookieHeader->getName());
        $this->assertEquals('myvalue', $setCookieHeader->getValue());

        $setCookieHeader = $setCookieHeaders[1];
        $this->assertInstanceOf('Zend\Http\Header\MultipleHeaderInterface', $setCookieHeader);
        $this->assertEquals('someothername', $setCookieHeader->getName());
        $this->assertEquals('someothervalue', $setCookieHeader->getValue());
        $this->assertEquals('Wed, 13-Jan-2021 22:23:01 GMT', $setCookieHeader->getExpires());
        $this->assertEquals('docs.foo.com', $setCookieHeader->getDomain());
        $this->assertEquals('/accounts', $setCookieHeader->getPath());
        $this->assertTrue($setCookieHeader->isSecure());
        $this->assertTrue($setCookieHeader->isHttponly());

    }

    public function testSetCookieGetFieldNameReturnsHeaderName()
    {
        $setCookieHeader = new SetCookie();
        $this->assertEquals('Set-Cookie', $setCookieHeader->getFieldName());

    }

    public function testSetCookieGetFieldValueReturnsProperValue()
    {
        $setCookieHeader = new SetCookie();
        $setCookieHeader->setName('myname');
        $setCookieHeader->setValue('myvalue');
        $setCookieHeader->setExpires('Wed, 13-Jan-2021 22:23:01 GMT');
        $setCookieHeader->setDomain('docs.foo.com');
        $setCookieHeader->setPath('/accounts');
        $setCookieHeader->setSecure(true);
        $setCookieHeader->setHttponly(true);

        $target = 'myname=myvalue; Expires=Wed, 13-Jan-2021 22:23:01 GMT;'
            . ' Domain=docs.foo.com; Path=/accounts;'
            . ' Secure; HttpOnly';

        $this->assertEquals($target, $setCookieHeader->getFieldValue());
    }

    public function testSetCookieToStringReturnsHeaderFormattedString()
    {
        $setCookieHeader = new SetCookie();
        $setCookieHeader->setName('myname');
        $setCookieHeader->setValue('myvalue');
        $setCookieHeader->setExpires('Wed, 13-Jan-2021 22:23:01 GMT');
        $setCookieHeader->setDomain('docs.foo.com');
        $setCookieHeader->setPath('/accounts');
        $setCookieHeader->setSecure(true);
        $setCookieHeader->setHttponly(true);

        $target = 'Set-Cookie: myname=myvalue; Expires=Wed, 13-Jan-2021 22:23:01 GMT;'
            . ' Domain=docs.foo.com; Path=/accounts;'
            . ' Secure; HttpOnly';

        $this->assertEquals($target, $setCookieHeader->toString());
    }

    public function testSetCookieCanAppendOtherHeadersInWhenCreatingString()
    {
        $setCookieHeader = new SetCookie();
        $setCookieHeader->setName('myname');
        $setCookieHeader->setValue('myvalue');
        $setCookieHeader->setExpires('Wed, 13-Jan-2021 22:23:01 GMT');
        $setCookieHeader->setDomain('docs.foo.com');
        $setCookieHeader->setPath('/accounts');
        $setCookieHeader->setSecure(true);
        $setCookieHeader->setHttponly(true);

        $appendCookie = new SetCookie('othername', 'othervalue');
        $headerLine = $setCookieHeader->toStringMultipleHeaders(array($appendCookie));

        $target = 'Set-Cookie: myname=myvalue; Expires=Wed, 13-Jan-2021 22:23:01 GMT;'
            . ' Domain=docs.foo.com; Path=/accounts;'
            . ' Secure; HttpOnly, othername=othervalue';
        $this->assertEquals($target, $headerLine);
    }

    public function testIsValidForRequestSubdomainMatch()
    {
        $setCookieHeader = new SetCookie(
            'myname', 'myvalue', 'Wed, 13-Jan-2021 22:23:01 GMT',
            '/accounts', '.foo.com', true, true, 99, 9
        );
        $this->assertTrue($setCookieHeader->isValidForRequest('bar.foo.com', '/accounts', true));
        $this->assertFalse($setCookieHeader->isValidForRequest('bar.foooo.com', '/accounts', true)); // false because of domain
        $this->assertFalse($setCookieHeader->isValidForRequest('bar.foo.com', '/accounts', false)); // false because of isSecure
        $this->assertFalse($setCookieHeader->isValidForRequest('bar.foo.com', '/somethingelse', true)); // false because of path
    }

    /** Implementation specific tests here */

    /**
     * @group ZF2-169
     */
    public function testZF2_169()
    {
        $cookie = 'Set-Cookie: leo_auth_token="example"; Version=1; Max-Age=1799; Expires=Mon, 20-Feb-2012 02:49:57 GMT; Path=/';
        $setCookieHeader = SetCookie::fromString($cookie);
        $this->assertEquals($cookie, $setCookieHeader->toString());
    }

    /**
     * @group ZF2-169
     */
    public function testDoesNotAcceptCookieNameFromArbitraryLocationInHeaderValue()
    {
        $cookie = 'Set-Cookie: Version=1; Max-Age=1799; Expires=Mon, 20-Feb-2012 02:49:57 GMT; Path=/; leo_auth_token="example"';
        $setCookieHeader = SetCookie::fromString($cookie);
        $this->assertNotEquals('leo_auth_token', $setCookieHeader->getName());
    }

    public function testGetFieldName()
    {
        $c = new SetCookie();
        $this->assertEquals('Set-Cookie', $c->getFieldName());
    }

    /**
     * @dataProvider validCookieWithInfoProvider
     */
    public function testGetFieldValue($cStr, $info, $expected)
    {
        $cookie = SetCookie::fromString($cStr);
        if (! $cookie instanceof SetCookie) {
            $this->fail("Failed creating a cookie object from '$cStr'");
        }
        $this->assertEquals($expected, $cookie->getFieldValue());
        $this->assertEquals($cookie->getFieldName() . ': ' . $expected, $cookie->toString());
    }

    /**
     * @dataProvider validCookieWithInfoProvider
     */
    public function testToString($cStr, $info, $expected)
    {
        $cookie = SetCookie::fromString($cStr);
        if (! $cookie instanceof SetCookie) {
            $this->fail("Failed creating a cookie object from '$cStr'");
        }
        $this->assertEquals($cookie->getFieldName() . ': ' . $expected, $cookie->toString());
    }

    /**
     * Provide valid cookie strings with information about them
     *
     * @return array
     */
    public static function validCookieWithInfoProvider()
    {
        $now = time();
        $yesterday = $now - (3600 * 24);

        return array(
            array(
                'Set-Cookie: justacookie=foo; domain=example.com',
                array(
                    'name'    => 'justacookie',
                    'value'   => 'foo',
                    'domain'  => 'example.com',
                    'path'    => '/',
                    'expires' => null,
                    'secure'  => false,
                    'httponly'=> false
                ),
                'justacookie=foo; Domain=example.com'
            ),
            array(
                'Set-Cookie: expires=tomorrow; secure; path=/Space Out/; expires=Tue, 21-Nov-2006 08:33:44 GMT; domain=.example.com',
                array(
                    'name'    => 'expires',
                    'value'   => 'tomorrow',
                    'domain'  => '.example.com',
                    'path'    => '/Space Out/',
                    'expires' => strtotime('Tue, 21-Nov-2006 08:33:44 GMT'),
                    'secure'  => true,
                    'httponly'=> false
                ),
                'expires=tomorrow; Expires=Tue, 21-Nov-2006 08:33:44 GMT; Domain=.example.com; Path=/Space Out/; Secure'
            ),
            array(
                'Set-Cookie: domain=unittests; expires=' . gmdate('D, d-M-Y H:i:s', $now) . ' GMT; domain=example.com; path=/some%20value/',
                array(
                    'name'    => 'domain',
                    'value'   => 'unittests',
                    'domain'  => 'example.com',
                    'path'    => '/some%20value/',
                    'expires' => $now,
                    'secure'  => false,
                    'httponly'=> false
                ),
                'domain=unittests; Expires=' . gmdate('D, d-M-Y H:i:s', $now) . ' GMT; Domain=example.com; Path=/some%20value/'
            ),
            array(
                'Set-Cookie: path=indexAction; path=/; domain=.foo.com; expires=' . gmdate('D, d-M-Y H:i:s', $yesterday) . ' GMT',
                array(
                    'name'    => 'path',
                    'value'   => 'indexAction',
                    'domain'  => '.foo.com',
                    'path'    => '/',
                    'expires' => $yesterday,
                    'secure'  => false,
                    'httponly'=> false
                ),
                'path=indexAction; Expires=' . gmdate('D, d-M-Y H:i:s', $yesterday) . ' GMT; Domain=.foo.com; Path=/'
            ),

            array(
                'Set-Cookie: secure=sha1; secure; SECURE; domain=some.really.deep.domain.com',
                array(
                    'name'    => 'secure',
                    'value'   => 'sha1',
                    'domain'  => 'some.really.deep.domain.com',
                    'path'    => '/',
                    'expires' => null,
                    'secure'  => true,
                    'httponly'=> false
                ),
                'secure=sha1; Domain=some.really.deep.domain.com; Secure'
            ),
            array(
                'Set-Cookie: justacookie=foo; domain=example.com; httpOnly',
                array(
                    'name'    => 'justacookie',
                    'value'   => 'foo',
                    'domain'  => 'example.com',
                    'path'    => '/',
                    'expires' => null,
                    'secure'  => false,
                    'httponly'=> true
                ),
                'justacookie=foo; Domain=example.com; HttpOnly'
            ),
            array(
                'Set-Cookie: PHPSESSID=123456789+abcd%2Cef; secure; domain=.localdomain; path=/foo/baz; expires=Tue, 21-Nov-2006 08:33:44 GMT;',
                array(
                    'name'    => 'PHPSESSID',
                    'value'   => '123456789+abcd%2Cef',
                    'domain'  => '.localdomain',
                    'path'    => '/foo/baz',
                    'expires' => 'Tue, 21-Nov-2006 08:33:44 GMT',
                    'secure'  => true,
                    'httponly'=> false
                ),
                'PHPSESSID=123456789+abcd%2Cef; Expires=Tue, 21-Nov-2006 08:33:44 GMT; Domain=.localdomain; Path=/foo/baz; Secure'
            ),
            array(
                'Set-Cookie: myname=myvalue; Domain=docs.foo.com; Path=/accounts; Expires=Wed, 13-Jan-2021 22:23:01 GMT; Secure; HttpOnly',
                array(
                    'name'    => 'myname',
                    'value'   => 'myvalue',
                    'domain'  => 'docs.foo.com',
                    'path'    => '/accounts',
                    'expires' => 'Wed, 13-Jan-2021 22:23:01 GMT',
                    'secure'  => true,
                    'httponly'=> true
                ),
                'myname=myvalue; Expires=Wed, 13-Jan-2021 22:23:01 GMT; Domain=docs.foo.com; Path=/accounts; Secure; HttpOnly'
            ),
            array(
                'Set-Cookie:',
                array(),
                ''
            ),
            array(
                'Set-Cookie: ',
                array(),
                ''
            ),
        );
    }
}
