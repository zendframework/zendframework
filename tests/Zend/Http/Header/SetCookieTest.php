<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\SetCookie;

class SetCookieTest extends \PHPUnit_Framework_TestCase
{

    public function testSetCookieFromStringCreatesValidSetCookieHeader()
    {
        $setCookieHeader = SetCookie::fromString('Set-Cookie: xxx');
        $this->assertInstanceOf('Zend\Http\Header\MultipleHeaderDescription', $setCookieHeader);
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $setCookieHeader);
        $this->assertInstanceOf('Zend\Http\Header\SetCookie', $setCookieHeader);
    }

    public function testSetCookieFromStringCanCreateSingleHeader()
    {
        $setCookieHeader = SetCookie::fromString('Set-Cookie: myname=myvalue');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $setCookieHeader);
        $this->assertEquals('myname', $setCookieHeader->getName());
        $this->assertEquals('myvalue', $setCookieHeader->getValue());

        $setCookieHeader = SetCookie::fromString(
            'set-cookie: myname=myvalue; Domain=docs.foo.com; Path=/accounts;'
            . 'Expires=Wed, 13-Jan-2021 22:23:01 GMT; Secure; HttpOnly'
        );
        $this->assertInstanceOf('Zend\Http\Header\MultipleHeaderDescription', $setCookieHeader);
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
        $this->assertInstanceOf('Zend\Http\Header\MultipleHeaderDescription', $setCookieHeader);
        $this->assertEquals('myname', $setCookieHeader->getName());
        $this->assertEquals('myvalue', $setCookieHeader->getValue());

        $setCookieHeader = $setCookieHeaders[1];
        $this->assertInstanceOf('Zend\Http\Header\MultipleHeaderDescription', $setCookieHeader);
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

    /** Implmentation specific tests here */
    
    /**
     * ZF2-169
     * 
     * @see http://framework.zend.com/issues/browse/ZF2-169
     */
    public function testZF2_169()
    {
        $cookie = 'Set-Cookie: leo_auth_token="example"; Version=1; Max-Age=1799; Expires=Mon, 20-Feb-2012 02:49:57 GMT; Path=/';
        $setCookieHeader = SetCookie::fromString($cookie);
        $this->assertEquals($cookie, $setCookieHeader->toString());
    }
}

