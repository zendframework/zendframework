<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Http
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
        $this->assertInstanceOf('Zend\Http\Headers', $response->getHeaders());
    }

    public function testRequestCanSetHeaders()
    {
        $response = new Response();
        $headers = new \Zend\Http\Headers();

        $ret = $response->setHeaders($headers);
        $this->assertInstanceOf('Zend\Http\Response', $ret);
        $this->assertSame($headers, $response->getHeaders());
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

    public function testResponseEndsAtStatusCode()
    {
        $string = 'HTTP/1.0 200' . "\r\n\r\n" . 'Foo Bar';
        $response = Response::fromString($string);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Foo Bar', $response->getContent());
    }

    public function testResponseHasZeroLengthReasonPhrase()
    {
        // Space after status code is mandatory,
        // though, reason phrase can be empty.
        // @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec6.html#sec6.1
        $string = 'HTTP/1.0 200 ' . "\r\n\r\n" . 'Foo Bar';

        $response = Response::fromString($string);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Foo Bar', $response->getContent());

        // Reason phrase would fallback to default reason phrase.
        $this->assertEquals('OK', $response->getReasonPhrase());
    }

    public function testGzipResponse ()
    {
        $response_text = file_get_contents(__DIR__ . '/_files/response_gzip');

        $res = Response::fromString($response_text);

        $this->assertEquals('gzip', $res->getHeaders()->get('Content-encoding')->getFieldValue());
        $this->assertEquals('0b13cb193de9450aa70a6403e2c9902f', md5($res->getBody()));
        $this->assertEquals('f24dd075ba2ebfb3bf21270e3fdc5303', md5($res->getContent()));
    }

    public function testDeflateResponse ()
    {
        $response_text = file_get_contents(__DIR__ . '/_files/response_deflate');

        $res = Response::fromString($response_text);

        $this->assertEquals('deflate', $res->getHeaders()->get('Content-encoding')->getFieldValue());
        $this->assertEquals('0b13cb193de9450aa70a6403e2c9902f', md5($res->getBody()));
        $this->assertEquals('ad62c21c3aa77b6a6f39600f6dd553b8', md5($res->getContent()));
    }

    /**
     * Make sure wer can handle non-RFC complient "deflate" responses.
     *
     * Unlike stanrdard 'deflate' response, those do not contain the zlib header
     * and trailer. Unfortunately some buggy servers (read: IIS) send those and
     * we need to support them.
     *
     * @link http://framework.zend.com/issues/browse/ZF-6040
     */
    public function testNonStandardDeflateResponseZF6040()
    {
        $this->markTestSkipped('Not correctly handling non-RFC complient "deflate" responses');
        $response_text = file_get_contents(__DIR__ . '/_files/response_deflate_iis');

        $res = Response::fromString($response_text);

        $this->assertEquals('deflate', $res->getHeaders()->get('Content-encoding')->getFieldValue());
        $this->assertEquals('d82c87e3d5888db0193a3fb12396e616', md5($res->getBody()));
        $this->assertEquals('c830dd74bb502443cf12514c185ff174', md5($res->getContent()));
    }

    public function testChunkedResponse ()
    {
        $response_text = file_get_contents(__DIR__ . '/_files/response_chunked');

        $res = Response::fromString($response_text);

        $this->assertEquals('chunked', $res->getHeaders()->get('Transfer-encoding')->getFieldValue());
        $this->assertEquals('0b13cb193de9450aa70a6403e2c9902f', md5($res->getBody()));
        $this->assertEquals('c0cc9d44790fa2a58078059bab1902a9', md5($res->getContent()));
    }

    public function testChunkedResponseCaseInsensitiveZF5438()
    {
        $response_text = file_get_contents(__DIR__ . '/_files/response_chunked_case');

        $res = Response::fromString($response_text);

        $this->assertEquals('chunked', strtolower($res->getHeaders()->get('Transfer-encoding')->getFieldValue()));
        $this->assertEquals('0b13cb193de9450aa70a6403e2c9902f', md5($res->getBody()));
        $this->assertEquals('c0cc9d44790fa2a58078059bab1902a9', md5($res->getContent()));
    }

    public function testLineBreaksCompatibility()
    {
        $response_text_lf = $this->readResponse('response_lfonly');
        $res_lf = Response::fromString($response_text_lf);

        $response_text_crlf = $this->readResponse('response_crlf');
        $res_crlf = Response::fromString($response_text_crlf);

        $this->assertEquals($res_lf->getHeaders()->toString(), $res_crlf->getHeaders()->toString(), 'Responses headers do not match');

        $this->markTestIncomplete('Something is fishy with the response bodies in the test responses');
        $this->assertEquals($res_lf->getBody(), $res_crlf->getBody(), 'Response bodies do not match');
    }

    public function test404IsClientErrorAndNotFound()
    {
        $response_text = $this->readResponse('response_404');
        $response = Response::fromString($response_text);

        $this->assertEquals(404, $response->getStatusCode(), 'Response code is expected to be 404, but it\'s not.');
        $this->assertTrue($response->isClientError(), 'Response is an error, but isClientError() returned false');
        $this->assertFalse($response->isForbidden(), 'Response is an error, but isForbidden() returned true');
        $this->assertFalse($response->isInformational(), 'Response is an error, but isInformational() returned true');
        $this->assertTrue($response->isNotFound(), 'Response is an error, but isNotFound() returned false');
        $this->assertFalse($response->isOk(), 'Response is an error, but isOk() returned true');
        $this->assertFalse($response->isServerError(), 'Response is an error, but isServerError() returned true');
        $this->assertFalse($response->isRedirect(), 'Response is an error, but isRedirect() returned true');
        $this->assertFalse($response->isSuccess(), 'Response is an error, but isSuccess() returned true');
    }

    public function test500isError()
    {
        $response_text = $this->readResponse('response_500');
        $response = Response::fromString($response_text);

        $this->assertEquals(500, $response->getStatusCode(), 'Response code is expected to be 500, but it\'s not.');
        $this->assertFalse($response->isClientError(), 'Response is an error, but isClientError() returned true');
        $this->assertFalse($response->isForbidden(), 'Response is an error, but isForbidden() returned true');
        $this->assertFalse($response->isInformational(), 'Response is an error, but isInformational() returned true');
        $this->assertFalse($response->isNotFound(), 'Response is an error, but isNotFound() returned true');
        $this->assertFalse($response->isOk(), 'Response is an error, but isOk() returned true');
        $this->assertTrue($response->isServerError(), 'Response is an error, but isServerError() returned false');
        $this->assertFalse($response->isRedirect(), 'Response is an error, but isRedirect() returned true');
        $this->assertFalse($response->isSuccess(), 'Response is an error, but isSuccess() returned true');
    }

    /**
     * @group ZF-5520
     */
    public function test302LocationHeaderMatches()
    {
        $headerName  = 'Location';
        $headerValue = 'http://www.google.com/ig?hl=en';
        $response    = Response::fromString($this->readResponse('response_302'));
        $responseIis = Response::fromString($this->readResponse('response_302_iis'));

        $this->assertEquals($headerValue, $response->getHeaders()->get($headerName)->getFieldValue());
        $this->assertEquals($headerValue, $responseIis->getHeaders()->get($headerName)->getFieldValue());
    }

    public function test300isRedirect()
    {
        $response = Response::fromString($this->readResponse('response_302'));

        $this->assertEquals(302, $response->getStatusCode(), 'Response code is expected to be 302, but it\'s not.');
        $this->assertFalse($response->isClientError(), 'Response is an error, but isClientError() returned true');
        $this->assertFalse($response->isForbidden(), 'Response is an error, but isForbidden() returned true');
        $this->assertFalse($response->isInformational(), 'Response is an error, but isInformational() returned true');
        $this->assertFalse($response->isNotFound(), 'Response is an error, but isNotFound() returned true');
        $this->assertFalse($response->isOk(), 'Response is an error, but isOk() returned true');
        $this->assertFalse($response->isServerError(), 'Response is an error, but isServerError() returned true');
        $this->assertTrue($response->isRedirect(), 'Response is an error, but isRedirect() returned false');
        $this->assertFalse($response->isSuccess(), 'Response is an error, but isSuccess() returned true');
    }

    public function test200Ok()
    {
        $response = Response::fromString($this->readResponse('response_deflate'));

        $this->assertEquals(200, $response->getStatusCode(), 'Response code is expected to be 200, but it\'s not.');
        $this->assertFalse($response->isClientError(), 'Response is an error, but isClientError() returned true');
        $this->assertFalse($response->isForbidden(), 'Response is an error, but isForbidden() returned true');
        $this->assertFalse($response->isInformational(), 'Response is an error, but isInformational() returned true');
        $this->assertFalse($response->isNotFound(), 'Response is an error, but isNotFound() returned true');
        $this->assertTrue($response->isOk(), 'Response is an error, but isOk() returned false');
        $this->assertFalse($response->isServerError(), 'Response is an error, but isServerError() returned true');
        $this->assertFalse($response->isRedirect(), 'Response is an error, but isRedirect() returned true');
        $this->assertTrue($response->isSuccess(), 'Response is an error, but isSuccess() returned false');
    }

    public function test100Continue()
    {
        $this->markTestIncomplete();
    }

    public function testAutoMessageSet()
    {
        $response = Response::fromString($this->readResponse('response_403_nomessage'));

        $this->assertEquals(403, $response->getStatusCode(), 'Response status is expected to be 403, but it isn\'t');
        $this->assertEquals('Forbidden', $response->getReasonPhrase(), 'Response is 403, but message is not "Forbidden" as expected');

        // While we're here, make sure it's classified as error...
        $this->assertTrue($response->isClientError(), 'Response is an error, but isClientError() returned false');
        $this->assertTrue($response->isForbidden(), 'Response is an error, but isForbidden() returned false');
        $this->assertFalse($response->isInformational(), 'Response is an error, but isInformational() returned true');
        $this->assertFalse($response->isNotFound(), 'Response is an error, but isNotFound() returned true');
        $this->assertFalse($response->isOk(), 'Response is an error, but isOk() returned true');
        $this->assertFalse($response->isServerError(), 'Response is an error, but isServerError() returned true');
        $this->assertFalse($response->isRedirect(), 'Response is an error, but isRedirect() returned true');
        $this->assertFalse($response->isSuccess(), 'Response is an error, but isSuccess() returned true');
    }

    public function testToString()
    {
        $response_str = $this->readResponse('response_404');
        $response = Response::fromString($response_str);

        $this->assertEquals(strtolower(str_replace("\n", "\r\n", $response_str)), strtolower($response->toString()), 'Response convertion to string does not match original string');
        $this->assertEquals(strtolower(str_replace("\n", "\r\n", $response_str)), strtolower((string)$response), 'Response convertion to string does not match original string');
    }

    public function testToStringGzip()
    {
        $response_str = $this->readResponse('response_gzip');
        $response = Response::fromString($response_str);

        $this->assertEquals(strtolower($response_str), strtolower($response->toString()), 'Response convertion to string does not match original string');
        $this->assertEquals(strtolower($response_str), strtolower((string)$response), 'Response convertion to string does not match original string');
    }

    public function testGetHeaders()
    {
        $response = Response::fromString($this->readResponse('response_deflate'));
        $headers = $response->getHeaders();

        $this->assertEquals(8, count($headers), 'Header count is not as expected');
        $this->assertEquals('Apache', $headers->get('Server')->getFieldValue(), 'Server header is not as expected');
        $this->assertEquals('deflate', $headers->get('Content-encoding')->getFieldValue(), 'Content-type header is not as expected');
    }

    public function testGetVersion()
    {
        $response = Response::fromString($this->readResponse('response_chunked'));
        $this->assertEquals(1.1, $response->getVersion(), 'Version is expected to be 1.1');
    }

    public function testUnknownCode()
    {
        $response_str = $this->readResponse('response_unknown');
        $this->setExpectedException('InvalidArgumentException', 'Invalid status code provided: "550"');
        $response = Response::fromString($response_str);
    }

    public function testMultilineHeader()
    {
        $response = Response::fromString($this->readResponse('response_multiline_header'));

        // Make sure we got the corrent no. of headers
        $this->assertEquals(6, count($response->getHeaders()), 'Header count is expected to be 6');

        // Check header integrity
        $this->assertEquals('timeout=15,max=100', $response->getHeaders()->get('keep-alive')->getFieldValue());
        $this->assertEquals('text/html;charset=iso-8859-1', $response->getHeaders()->get('content-type')->getFieldValue());
    }

    /**
     * Make sure a response with some leading whitespace in the response body
     * does not get modified (see ZF-1924)
     *
     */
    public function testLeadingWhitespaceBody()
    {
        $response = Response::fromString($this->readResponse('response_leadingws'));
        $this->assertEquals($response->getContent(), "\r\n\t  \n\r\tx", 'Extracted body is not identical to expected body');
    }

    /**
     * Test that parsing a multibyte-encoded chunked response works.
     *
     * This can potentially fail on different PHP environments - for example
     * when mbstring.func_overload is set to overload strlen().
     *
     */
    public function testMultibyteChunkedResponse()
    {
        $this->markTestSkipped('Looks like the headers are split with \n and the body with \r\n');
        $md5 = 'ab952f1617d0e28724932401f2d3c6ae';

        $response = Response::fromString($this->readResponse('response_multibyte_body'));
        $this->assertEquals($md5, md5($response->getBody()));
    }

    /**
     * Helper function: read test response from file
     *
     * @param string $response
     * @return string
     */
    protected function readResponse($response)
    {
        return file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . $response);
    }
}
