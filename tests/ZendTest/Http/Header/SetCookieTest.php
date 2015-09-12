<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
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
            'myname',
            'myvalue',
            'Wed, 13-Jan-2021 22:23:01 GMT',
            '/accounts',
            'docs.foo.com',
            true,
            true,
            99,
            9
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

    public function testSetCookieFromStringWithQuotedValue()
    {
        $setCookieHeader = SetCookie::fromString('Set-Cookie: myname="quotedValue"');
        $this->assertEquals('quotedValue', $setCookieHeader->getValue());
        $this->assertEquals('myname=quotedValue', $setCookieHeader->getFieldValue());
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

    /**
     * @group 6673
     * @group 6923
     */
    public function testSetCookieWithDateTimeFieldValueReturnsProperValue()
    {
        $setCookieHeader = new SetCookie();
        $setCookieHeader->setName('myname');
        $setCookieHeader->setValue('myvalue');
        $setCookieHeader->setExpires(new \DateTime('Wed, 13-Jan-2021 22:23:01 GMT'));
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
        $this->assertNotEquals($target, $headerLine);

        $target = 'Set-Cookie: myname=myvalue; Expires=Wed, 13-Jan-2021 22:23:01 GMT;'
            . ' Domain=docs.foo.com; Path=/accounts;'
            . ' Secure; HttpOnly';
        $target.= "\n";
        $target.= 'Set-Cookie: othername=othervalue';
        $this->assertEquals($target, $headerLine);
    }

    public function testSetCookieAttributesAreUnsettable()
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
        $this->assertSame($target, $setCookieHeader->getFieldValue()); // attributes set

        $setCookieHeader->setExpires(null);
        $setCookieHeader->setDomain(null);
        $setCookieHeader->setPath(null);
        $setCookieHeader->setSecure(null);
        $setCookieHeader->setHttponly(null);
        $this->assertSame('myname=myvalue', $setCookieHeader->getFieldValue()); // attributes unset

        $setCookieHeader->setValue(null);
        $this->assertSame('myname=', $setCookieHeader->getFieldValue());
        $this->assertNull($setCookieHeader->getValue());
        $this->assertNull($setCookieHeader->getExpires());
        $this->assertNull($setCookieHeader->getDomain());
        $this->assertNull($setCookieHeader->getPath());
        $this->assertNull($setCookieHeader->isSecure());
        $this->assertNull($setCookieHeader->isHttponly());
    }

    public function testSetCookieFieldValueIsEmptyStringWhenNameIsUnset()
    {
        $setCookieHeader = new SetCookie();
        $this->assertSame('', $setCookieHeader->getFieldValue()); // empty

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
        $this->assertSame($target, $setCookieHeader->getFieldValue()); // not empty

        $setCookieHeader->setName(null);
        $this->assertSame('', $setCookieHeader->getFieldValue()); // empty again
        $this->assertNull($setCookieHeader->getName());
    }

    public function testSetCookieSetExpiresWithZeroTimeStamp()
    {
        $setCookieHeader = new SetCookie('myname', 'myvalue', 0);
        $this->assertSame('Thu, 01-Jan-1970 00:00:00 GMT', $setCookieHeader->getExpires());

        $setCookieHeader = new SetCookie('myname', 'myvalue', 1);
        $this->assertSame('Thu, 01-Jan-1970 00:00:01 GMT', $setCookieHeader->getExpires());

        $setCookieHeader->setExpires(0);
        $this->assertSame('Thu, 01-Jan-1970 00:00:00 GMT', $setCookieHeader->getExpires());

        $target = 'myname=myvalue; Expires=Thu, 01-Jan-1970 00:00:00 GMT';
        $this->assertSame($target, $setCookieHeader->getFieldValue());
    }

    public function testSetCookieSetExpiresWithUnixEpochString()
    {
        $setCookieHeader = new SetCookie('myname', 'myvalue', 'Thu, 01-Jan-1970 00:00:00 GMT');
        $this->assertSame('Thu, 01-Jan-1970 00:00:00 GMT', $setCookieHeader->getExpires());
        $this->assertSame(0, $setCookieHeader->getExpires(true));

        $setCookieHeader = new SetCookie('myname', 'myvalue', 1);
        $this->assertSame('Thu, 01-Jan-1970 00:00:01 GMT', $setCookieHeader->getExpires());

        $setCookieHeader->setExpires('Thu, 01-Jan-1970 00:00:00 GMT');
        $this->assertSame('Thu, 01-Jan-1970 00:00:00 GMT', $setCookieHeader->getExpires());
        $this->assertSame(0, $setCookieHeader->getExpires(true));

        $target = 'myname=myvalue; Expires=Thu, 01-Jan-1970 00:00:00 GMT';
        $this->assertSame($target, $setCookieHeader->getFieldValue());
    }

    /**
     * Check that setCookie does not fail when an expiry date which is bigger
     * then 2038 is supplied (effect only 32bit systems)
     */
    public function testSetCookieSetExpiresWithStringDateBiggerThen2038()
    {
        if (PHP_INT_SIZE !== 4) {
            $this->markTestSkipped('Testing set cookie expiry which is over 2038 is only relevant on 32bit systems');
            return;
        }
        $setCookieHeader = new SetCookie('myname', 'myvalue', 'Thu, 01-Jan-2040 00:00:00 GMT');
        $this->assertSame(2147483647, $setCookieHeader->getExpires(true));
    }

    public function testIsValidForRequestSubdomainMatch()
    {
        $setCookieHeader = new SetCookie(
            'myname',
            'myvalue',
            'Wed,
            13-Jan-2021 22:23:01 GMT',
            '/accounts',
            '.foo.com',
            true,
            true,
            99,
            9
        );
        $this->assertTrue($setCookieHeader->isValidForRequest('bar.foo.com', '/accounts', true));
        $this->assertFalse(
            $setCookieHeader->isValidForRequest('bar.foooo.com', '/accounts', true)
        ); // false because of domain
        $this->assertFalse(
            $setCookieHeader->isValidForRequest('bar.foo.com', '/accounts', false)
        ); // false because of isSecure
        $this->assertFalse(
            $setCookieHeader->isValidForRequest('bar.foo.com', '/somethingelse', true)
        ); // false because of path
    }

    /** Implementation specific tests here */

    /**
     * @group ZF2-169
     */
    public function test169()
    {
        $cookie = 'Set-Cookie: leo_auth_token=example; Version=1; Max-Age=1799; Expires=Mon, 20-Feb-2012 02:49:57 GMT; Path=/';
        $setCookieHeader = SetCookie::fromString($cookie);
        $this->assertEquals($cookie, $setCookieHeader->toString());
    }

    /**
     * @group ZF2-169
     */
    public function testDoesNotAcceptCookieNameFromArbitraryLocationInHeaderValue()
    {
        $cookie = 'Set-Cookie: Version=1; Max-Age=1799; Expires=Mon, 20-Feb-2012 02:49:57 GMT; Path=/; leo_auth_token=example';
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

    public function testRfcCompatibility()
    {
        $name = 'myname';
        $value = 'myvalue';
        $formatUnquoted = '%s: %s=%s';
        $formatQuoted = '%s: %s="%s"';

        $cookie = new SetCookie($name, $value);

        // default
        $this->assertEquals($cookie->toString(), sprintf($formatUnquoted, $cookie->getFieldName(), $name, $value));

        // rfc with quote
        $cookie->setQuoteFieldValue(true);
        $this->assertEquals($cookie->toString(), sprintf($formatQuoted, $cookie->getFieldName(), $name, $value));

        // rfc without quote
        $cookie->setQuoteFieldValue(false);
        $this->assertEquals($cookie->toString(), sprintf($formatUnquoted, $cookie->getFieldName(), $name, $value));
    }

    public function testSetJsonValue()
    {
        $cookieName ="fooCookie";
        $jsonData = json_encode(array('foo'=>'bar'));

        $cookie= new SetCookie($cookieName, $jsonData);

        $regExp = sprintf('#^%s=%s#', $cookieName, urlencode($jsonData));
        $this->assertRegExp($regExp, $cookie->getFieldValue());

        $cookieName ="fooCookie";
        $jsonData = json_encode(array('foo'=>'bar'));

        $cookie= new SetCookie($cookieName, $jsonData);
        $cookie->setDomain('example.org');

        $regExp = sprintf('#^%s=%s; Domain=#', $cookieName, urlencode($jsonData));
        $this->assertRegExp($regExp, $cookie->getFieldValue());
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = SetCookie::fromString("Set-Cookie: leo_auth_token=example;\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $header = new SetCookie("leo_auth_token", "example\r\n\r\nevilContent");
        $this->assertEquals('Set-Cookie: leo_auth_token=example%0D%0A%0D%0AevilContent', $header->toString());
    }

    public function testPreventsCRLFAttackViaSetValue()
    {
        $header = new SetCookie("leo_auth_token");
        $header->setValue("example\r\n\r\nevilContent");
        $this->assertEquals('Set-Cookie: leo_auth_token=example%0D%0A%0D%0AevilContent', $header->toString());
    }

    public function setterInjections()
    {
        return array(
            'name'   => array('setName', "\r\nThis\rIs\nThe\r\nName"),
            'domain' => array('setDomain', "\r\nexample\r.\nco\r\n.uk"),
            'path'   => array('setPath', "\r\n/\rbar\n/foo\r\n/baz"),
        );
    }

    /**
     * @dataProvider setterInjections
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaSetters($method, $value)
    {
        $header = new SetCookie();
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header->{$method}($value);
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
            array(
                'Set-Cookie: emptykey=    ; Domain=docs.foo.com;',
                array(
                    'name'    => 'myname',
                    'value'   => '',
                    'domain'  => 'docs.foo.com',
                ),
                'emptykey=; Domain=docs.foo.com'
            ),
            array(
                'Set-Cookie: emptykey= ; Domain=docs.foo.com;',
                array(
                    'name'    => 'myname',
                    'value'   => '',
                    'domain'  => 'docs.foo.com',
                ),
                'emptykey=; Domain=docs.foo.com'
            ),
            array(
                'Set-Cookie: emptykey=; Domain=docs.foo.com;',
                array(
                    'name'    => 'myname',
                    'value'   => '',
                    'domain'  => 'docs.foo.com',
                ),
                'emptykey=; Domain=docs.foo.com'
            ),
            array(
                'Set-Cookie: emptykey; Domain=docs.foo.com;',
                array(
                    'name'    => 'myname',
                    'value'   => '',
                    'domain'  => 'docs.foo.com',
                ),
                'emptykey=; Domain=docs.foo.com'
            ),
        );
    }
}
