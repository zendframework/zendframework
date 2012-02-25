<?php

/**
 * @namespace
 */
namespace ZendTest\Http;

use Zend\Http\Response;

class ResponseTest extends \PHPUnit_Framework_TestCase
{

    public function testResponseFactoryFromStringCreatesValidResponse()
    {
        $string = 'HTTP/1.0 200 OK' . "\r\n\r\n" . 'Foo Bar';
        $response = Response::fromString($string);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Foo Bar', $response->getContent());
    }

    public function testResponseCanRenderStatusLine()
    {
        $response = new Response;
        $response->setVersion(1.1);
        $response->setStatusCode(Response::STATUS_CODE_404);
        $this->assertEquals('HTTP/1.1 404 Not Found', $response->renderStatusLine());

        $response->setReasonPhrase('Foo Bar');
        $this->assertEquals('HTTP/1.1 404 Foo Bar', $response->renderStatusLine());
    }

    public function testResponseUsesHeadersContainerByDefault()
    {
        $response = new Response();
        $this->assertInstanceOf('Zend\Http\Headers', $response->headers());
    }

    public function testRequestCanSetHeaders()
    {
        $response = new Response();
        $headers = new \Zend\Http\Headers();

        $ret = $response->setHeaders($headers);
        $this->assertInstanceOf('Zend\Http\Response', $ret);
        $this->assertSame($headers, $response->headers());
    }

    public function testResponseCanSetStatusCode()
    {
        $response = new Response;
        $this->assertEquals(200, $response->getStatusCode());
        $response->setStatusCode('303');
        $this->assertEquals(303, $response->getStatusCode());
    }

    public function testResponseSetStatusCodeThrowsExceptionOnInvalidCode()
    {
        $response = new Response;
        $this->setExpectedException('Zend\Http\Exception\InvalidArgumentException', 'Invalid status code');
        $response->setStatusCode(606);
    }

// @todo OLD TESTS BELOW, determine if we can use them

//    public function testGzipResponse ()
//    {
//        $response_text = file_get_contents(__DIR__ . '/_files/response_gzip');
//
//        $res = Response::fromString($response_text);
//
//        $this->assertEquals('gzip', $res->getHeader('Content-encoding'));
//        $this->assertEquals('0b13cb193de9450aa70a6403e2c9902f', md5($res->getBody()));
//        $this->assertEquals('f24dd075ba2ebfb3bf21270e3fdc5303', md5($res->getRawBody()));
//    }
//
//    public function testDeflateResponse ()
//    {
//        $response_text = file_get_contents(__DIR__ . '/_files/response_deflate');
//
//        $res = Response::fromString($response_text);
//
//        $this->assertEquals('deflate', $res->getHeader('Content-encoding'));
//        $this->assertEquals('0b13cb193de9450aa70a6403e2c9902f', md5($res->getBody()));
//        $this->assertEquals('ad62c21c3aa77b6a6f39600f6dd553b8', md5($res->getRawBody()));
//    }
//
//    /**
//     * Make sure wer can handle non-RFC complient "deflate" responses.
//     *
//     * Unlike stanrdard 'deflate' response, those do not contain the zlib header
//     * and trailer. Unfortunately some buggy servers (read: IIS) send those and
//     * we need to support them.
//     *
//     * @link http://framework.zend.com/issues/browse/ZF-6040
//     */
//    public function testNonStandardDeflateResponseZF6040()
//    {
//        $response_text = file_get_contents(__DIR__ . '/_files/response_deflate_iis');
//
//        $res = Response::fromString($response_text);
//
//        $this->assertEquals('deflate', $res->getHeader('Content-encoding'));
//        $this->assertEquals('d82c87e3d5888db0193a3fb12396e616', md5($res->getBody()));
//        $this->assertEquals('c830dd74bb502443cf12514c185ff174', md5($res->getRawBody()));
//    }
//
//    public function testChunkedResponse ()
//    {
//        $response_text = file_get_contents(__DIR__ . '/_files/response_chunked');
//
//        $res = Response::fromString($response_text);
//
//        $this->assertEquals('chunked', $res->getHeader('Transfer-encoding'));
//        $this->assertEquals('0b13cb193de9450aa70a6403e2c9902f', md5($res->getBody()));
//        $this->assertEquals('c0cc9d44790fa2a58078059bab1902a9', md5($res->getRawBody()));
//    }
//
//    public function testChunkedResponseCaseInsensitiveZF5438()
//    {
//        $response_text = file_get_contents(__DIR__ . '/_files/response_chunked_case');
//
//        $res = Response::fromString($response_text);
//
//        $this->assertEquals('chunked', strtolower($res->getHeader('Transfer-encoding')));
//        $this->assertEquals('0b13cb193de9450aa70a6403e2c9902f', md5($res->getBody()));
//        $this->assertEquals('c0cc9d44790fa2a58078059bab1902a9', md5($res->getRawBody()));
//    }
//
//
//    public function testLineBreaksCompatibility()
//    {
//        $response_text_lf = $this->readResponse('response_lfonly');
//        $res_lf = Response::fromString($response_text_lf);
//
//        $response_text_crlf = $this->readResponse('response_crlf');
//        $res_crlf = Response::fromString($response_text_crlf);
//
//        $this->assertEquals($res_lf->getHeadersAsString(true), $res_crlf->getHeadersAsString(true), 'Responses headers do not match');
//        $this->assertEquals($res_lf->getBody(), $res_crlf->getBody(), 'Response bodies do not match');
//    }
//
//    public function testExtractMessageCrlf()
//    {
//        $response_text = file_get_contents(__DIR__ . '/_files/response_crlf');
//        $this->assertEquals("OK", Response::extractMessage($response_text), "Response message is not 'OK' as expected");
//    }
//
//    public function testExtractMessageLfonly()
//    {
//        $response_text = file_get_contents(__DIR__ . '/_files/response_lfonly');
//        $this->assertEquals("OK", Response::extractMessage($response_text), "Response message is not 'OK' as expected");
//    }
//
//    public function test404IsError()
//    {
//        $response_text = $this->readResponse('response_404');
//        $response = Response::fromString($response_text);
//
//        $this->assertEquals(404, $response->getStatus(), 'Response code is expected to be 404, but it\'s not.');
//        $this->assertTrue($response->isError(), 'Response is an error, but isError() returned false');
//        $this->assertFalse($response->isSuccess(), 'Response is an error, but isSuccess() returned true');
//        $this->assertFalse($response->isRedirect(), 'Response is an error, but isRedirect() returned true');
//    }
//
//    public function test500isError()
//    {
//        $response_text = $this->readResponse('response_500');
//        $response = Response::fromString($response_text);
//
//        $this->assertEquals(500, $response->getStatus(), 'Response code is expected to be 500, but it\'s not.');
//        $this->assertTrue($response->isError(), 'Response is an error, but isError() returned false');
//        $this->assertFalse($response->isSuccess(), 'Response is an error, but isSuccess() returned true');
//        $this->assertFalse($response->isRedirect(), 'Response is an error, but isRedirect() returned true');
//    }
//
//    /**
//     * @group ZF-5520
//     */
//    public function test302LocationHeaderMatches()
//    {
//        $headerName  = 'Location';
//        $headerValue = 'http://www.google.com/ig?hl=en';
//        $response    = Response::fromString($this->readResponse('response_302'));
//        $responseIis = Response::fromString($this->readResponse('response_302_iis'));
//
//        $this->assertEquals($headerValue, $response->getHeader($headerName));
//        $this->assertEquals($headerValue, $responseIis->getHeader($headerName));
//    }
//
//    public function test300isRedirect()
//    {
//        $response = Response::fromString($this->readResponse('response_302'));
//
//        $this->assertEquals(302, $response->getStatus(), 'Response code is expected to be 302, but it\'s not.');
//        $this->assertTrue($response->isRedirect(), 'Response is a redirection, but isRedirect() returned false');
//        $this->assertFalse($response->isError(), 'Response is a redirection, but isError() returned true');
//        $this->assertFalse($response->isSuccess(), 'Response is a redirection, but isSuccess() returned true');
//    }
//
//    public function test200Ok()
//    {
//        $response = Response::fromString($this->readResponse('response_deflate'));
//
//        $this->assertEquals(200, $response->getStatus(), 'Response code is expected to be 200, but it\'s not.');
//        $this->assertFalse($response->isError(), 'Response is OK, but isError() returned true');
//        $this->assertTrue($response->isSuccess(), 'Response is OK, but isSuccess() returned false');
//        $this->assertFalse($response->isRedirect(), 'Response is OK, but isRedirect() returned true');
//    }
//
//    public function test100Continue()
//    {
//        $this->markTestIncomplete();
//    }
//
//    public function testAutoMessageSet()
//    {
//        $response = Response::fromString($this->readResponse('response_403_nomessage'));
//
//        $this->assertEquals(403, $response->getStatus(), 'Response status is expected to be 403, but it isn\'t');
//        $this->assertEquals('Forbidden', $response->getMessage(), 'Response is 403, but message is not "Forbidden" as expected');
//
//        // While we're here, make sure it's classified as error...
//        $this->assertTrue($response->isError(), 'Response is an error, but isError() returned false');
//        $this->assertFalse($response->isSuccess(), 'Response is an error, but isSuccess() returned true');
//        $this->assertFalse($response->isRedirect(), 'Response is an error, but isRedirect() returned true');
//    }
//
//    public function testAsString()
//    {
//        $response_str = $this->readResponse('response_404');
//        $response = Response::fromString($response_str);
//
//        $this->assertEquals(strtolower($response_str), strtolower($response->asString()), 'Response convertion to string does not match original string');
//        $this->assertEquals(strtolower($response_str), strtolower((string)$response), 'Response convertion to string does not match original string');
//    }
//
//    public function testGetHeaders()
//    {
//        $response = Response::fromString($this->readResponse('response_deflate'));
//        $headers = $response->getHeaders();
//
//        $this->assertEquals(8, count($headers), 'Header count is not as expected');
//        $this->assertEquals('Apache', $headers['Server'], 'Server header is not as expected');
//        $this->assertEquals('deflate', $headers['Content-encoding'], 'Content-type header is not as expected');
//    }
//
//    public function testGetVersion()
//    {
//        $response = Response::fromString($this->readResponse('response_chunked'));
//        $this->assertEquals(1.1, $response->getVersion(), 'Version is expected to be 1.1');
//    }
//
//    public function testResponseCodeAsText()
//    {
//        // This is an entirely static test
//
//        // Test some response codes
//        $this->assertEquals('Continue', Response::responseCodeAsText(100));
//        $this->assertEquals('OK', Response::responseCodeAsText(200));
//        $this->assertEquals('Multiple Choices', Response::responseCodeAsText(300));
//        $this->assertEquals('Bad Request', Response::responseCodeAsText(400));
//        $this->assertEquals('Internal Server Error', Response::responseCodeAsText(500));
//
//        // Make sure that invalid codes return 'Unkown'
//        $this->assertEquals('Unknown', Response::responseCodeAsText(600));
//
//        // Check HTTP/1.0 value for 302
//        $this->assertEquals('Found', Response::responseCodeAsText(302));
//        $this->assertEquals('Moved Temporarily', Response::responseCodeAsText(302, false));
//
//        // Check we get an array if no code is passed
//        $codes = Response::responseCodeAsText();
//        $this->assertInternalType('array', $codes);
//        $this->assertEquals('OK', $codes[200]);
//    }
//
//    public function testUnknownCode()
//    {
//        $response_str = $this->readResponse('response_unknown');
//        $response = Response::fromString($response_str);
//
//        // Check that dynamically the message is parsed
//        $this->assertEquals(550, $response->getStatus(), 'Status is expected to be a non-standard 550');
//        $this->assertEquals('Printer On Fire', $response->getMessage(), 'Message is expected to be extracted');
//
//        // Check that statically, an Unknown string is returned for the 550 code
//        $this->assertEquals('Unknown', Response::responseCodeAsText($response_str));
//    }
//
//    public function testMultilineHeader()
//    {
//        $response = Response::fromString($this->readResponse('response_multiline_header'));
//
//        // Make sure we got the corrent no. of headers
//        $this->assertEquals(6, count($response->getHeaders()), 'Header count is expected to be 6');
//
//        // Check header integrity
//        $this->assertEquals('timeout=15, max=100', $response->getHeader('keep-alive'));
//        $this->assertEquals('text/html; charset=iso-8859-1', $response->getHeader('content-type'));
//    }
//
//    public function testExceptInvalidChunkedBody()
//    {
//        try {
//            Response::decodeChunkedBody($this->readResponse('response_deflate'));
//            $this->fail('An expected exception was not thrown');
//        } catch (\Zend\Http\Exception $e) {
//            // We are ok!
//        }
//    }
//
//    public function testExtractorsOnInvalidString()
//    {
//        // Try with an empty string
//        $response_str = '';
//
//        $this->assertTrue(Response::extractCode($response_str) === false);
//        $this->assertTrue(Response::extractMessage($response_str) === false);
//        $this->assertTrue(Response::extractVersion($response_str) === false);
//        $this->assertTrue(Response::extractBody($response_str) === '');
//        $this->assertTrue(Response::extractHeaders($response_str) === array());
//    }
//
//    /**
//     * Make sure a response with some leading whitespace in the response body
//     * does not get modified (see ZF-1924)
//     *
//     */
//    public function testLeadingWhitespaceBody()
//    {
//        $body = Response::extractBody($this->readResponse('response_leadingws'));
//        $this->assertEquals($body, "\r\n\t  \n\r\tx", 'Extracted body is not identical to expected body');
//    }
//
//    /**
//     * Test that parsing a multibyte-encoded chunked response works.
//     *
//     * This can potentially fail on different PHP environments - for example
//     * when mbstring.func_overload is set to overload strlen().
//     *
//     */
//    public function testMultibyteChunkedResponse()
//    {
//        $md5 = 'ab952f1617d0e28724932401f2d3c6ae';
//
//        $response = Response::fromString($this->readResponse('response_multibyte_body'));
//        $this->assertEquals($md5, md5($response->getBody()));
//    }
//
//    /**
//     * Helper function: read test response from file
//     *
//     * @param string $response
//     * @return string
//     */
//    protected function readResponse($response)
//    {
//        return file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . $response);
//    }
}
