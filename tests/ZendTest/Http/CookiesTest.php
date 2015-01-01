<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Http;

use Zend\Http\Header\SetCookie;
use Zend\Http\Response;
use Zend\Http\Headers;
use Zend\Http\Cookies;

class CookiesTest extends \PHPUnit_Framework_TestCase
{
    public function testFromResponseInSetCookie()
    {
        $response = new Response();
        $headers = new Headers();
        $header = new SetCookie("foo", "bar");
        $header->setDomain("www.zend.com");
        $header->setPath("/");
        $headers->addHeader($header);
        $response->setHeaders($headers);

        $response = Cookies::fromResponse($response, "http://www.zend.com");
        $this->assertSame($header, $response->getCookie('http://www.zend.com', 'foo'));
    }

    public function testFromResponseInCookie()
    {
        $response = new Response();
        $headers = new Headers();
        $header = new SetCookie("foo", "bar");
        $header->setDomain("www.zend.com");
        $header->setPath("/");
        $headers->addHeader($header);
        $response->setHeaders($headers);

        $response = Cookies::fromResponse($response, "http://www.zend.com");
        $this->assertSame($header, $response->getCookie('http://www.zend.com', 'foo'));
    }
}
