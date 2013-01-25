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

use Zend\Http\Request;
use Zend\Http\Headers;
use Zend\Http\Header\GenericHeader;

class RequestTest extends \PHPUnit_Framework_TestCase
{

    public function testRequestFromStringFactoryCreatesValidRequest()
    {
        $string = "GET /foo HTTP/1.1\r\n\r\nSome Content";
        $request = Request::fromString($string);

        $this->assertEquals(Request::METHOD_GET, $request->getMethod());
        $this->assertEquals('/foo', $request->getUri());
        $this->assertEquals(Request::VERSION_11, $request->getVersion());
        $this->assertEquals('Some Content', $request->getContent());
    }

    public function testRequestUsesParametersContainerByDefault()
    {
        $request = new Request();
        $this->assertInstanceOf('Zend\Stdlib\Parameters', $request->getQuery());
        $this->assertInstanceOf('Zend\Stdlib\Parameters', $request->getPost());
        $this->assertInstanceOf('Zend\Stdlib\Parameters', $request->getFiles());
    }

    public function testRequestAllowsSettingOfParameterContainer()
    {
        $request = new Request();
        $p = new \Zend\Stdlib\Parameters();
        $request->setQuery($p);
        $request->setPost($p);
        $request->setFiles($p);

        $this->assertSame($p, $request->getQuery());
        $this->assertSame($p, $request->getPost());
        $this->assertSame($p, $request->getFiles());

        $headers = new Headers();
        $request->setHeaders($headers);
        $this->assertSame($headers, $request->getHeaders());
    }

    public function testRetrievingASingleValueForParameters()
    {
        $request = new Request();
        $p = new \Zend\Stdlib\Parameters(array(
            'foo' => 'bar'
        ));
        $request->setQuery($p);
        $request->setPost($p);
        $request->setFiles($p);

        $this->assertSame('bar', $request->getQuery('foo'));
        $this->assertSame('bar', $request->getPost('foo'));
        $this->assertSame('bar', $request->getFiles('foo'));

        $headers = new Headers();
        $h = new GenericHeader('foo','bar');
        $headers->addHeader($h);

        $request->setHeaders($headers);
        $this->assertSame($headers, $request->getHeaders());
        $this->assertSame($h, $request->getHeaders()->get('foo'));
        $this->assertSame($h, $request->getHeader('foo'));
    }

    public function testParameterRetrievalDefaultValue()
    {
        $request = new Request();
        $p = new \Zend\Stdlib\Parameters(array(
            'foo' => 'bar'
        ));
        $request->setQuery($p);
        $request->setPost($p);
        $request->setFiles($p);

        $default = 15;
        $this->assertSame($default, $request->getQuery('baz', $default));
        $this->assertSame($default, $request->getPost('baz', $default));
        $this->assertSame($default, $request->getFiles('baz', $default));
        $this->assertSame($default, $request->getHeaders('baz', $default));
        $this->assertSame($default, $request->getHeader('baz', $default));
    }

    public function testRequestPersistsRawBody()
    {
        $request = new Request();
        $request->setContent('foo');
        $this->assertEquals('foo', $request->getContent());
    }

    public function testRequestUsesHeadersContainerByDefault()
    {
        $request = new Request();
        $this->assertInstanceOf('Zend\Http\Headers', $request->getHeaders());
    }

    public function testRequestCanSetHeaders()
    {
        $request = new Request();
        $headers = new \Zend\Http\Headers();

        $ret = $request->setHeaders($headers);
        $this->assertInstanceOf('Zend\Http\Request', $ret);
        $this->assertSame($headers, $request->getHeaders());
    }

    public function testRequestCanSetAndRetrieveValidMethod()
    {
        $request = new Request();
        $request->setMethod('POST');
        $this->assertEquals('POST', $request->getMethod());
    }

    public function testRequestCanAlwaysForcesUppecaseMethodName()
    {
        $request = new Request();
        $request->setMethod('get');
        $this->assertEquals('GET', $request->getMethod());
    }

    /**
     * @dataProvider uriDataProvider
     */
    public function testRequestCanSetAndRetrieveUri($uri)
    {
        $request = new Request();
        $request->setUri($uri);
        $this->assertEquals($uri, $request->getUri());
        $this->assertInstanceOf('Zend\Uri\Uri', $request->getUri());
        $this->assertEquals($uri, $request->getUri()->toString());
        $this->assertEquals($uri, $request->getUriString());
    }

    public function uriDataProvider()
    {
        return array(
            array('/foo'),
            array('/foo#test'),
            array('/hello?what=true#noway')
        );
    }

    public function testRequestSetUriWillThrowExceptionOnInvalidArgument()
    {
        $request = new Request();

        $this->setExpectedException('Zend\Http\Exception\InvalidArgumentException', 'must be an instance of');
        $request->setUri(new \stdClass());
    }

    public function testRequestCanSetAndRetrieveVersion()
    {
        $request = new Request();
        $this->assertEquals('1.1', $request->getVersion());
        $request->setVersion(Request::VERSION_10);
        $this->assertEquals('1.0', $request->getVersion());
    }

    public function testRequestSetVersionWillThrowExceptionOnInvalidArgument()
    {
        $request = new Request();

        $this->setExpectedException('Zend\Http\Exception\InvalidArgumentException',
                                    'Not valid or not supported HTTP version');
        $request->setVersion('1.2');
    }

    /**
     * @dataProvider getMethods
     */
    public function testRequestMethodCheckWorksForAllMethods($methodName)
    {
        $request = new Request;
        $request->setMethod($methodName);

        foreach ($this->getMethods(false, $methodName) as $testMethodName => $testMethodValue) {
            $this->assertEquals($testMethodValue, $request->{'is' . $testMethodName}());
        }
    }

    public function testRequestCanBeCastToAString()
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_GET);
        $request->setUri('/');
        $request->setContent('foo=bar&bar=baz');
        $this->assertEquals("GET / HTTP/1.1\r\n\r\nfoo=bar&bar=baz", $request->toString());
    }

    public function testRequestIsXmlHttpRequest()
    {
        $request = new Request();
        $this->assertFalse($request->isXmlHttpRequest());

        $request = new Request();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'FooBazBar');
        $this->assertFalse($request->isXmlHttpRequest());

        $request = new Request();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $this->assertTrue($request->isXmlHttpRequest());
    }

    public function testRequestIsFlashRequest()
    {
        $request = new Request();
        $this->assertFalse($request->isFlashRequest());

        $request = new Request();
        $request->getHeaders()->addHeaderLine('USER_AGENT', 'FooBazBar');
        $this->assertFalse($request->isFlashRequest());

        $request = new Request();
        $request->getHeaders()->addHeaderLine('USER_AGENT', 'Shockwave Flash');
        $this->assertTrue($request->isFlashRequest());
    }

    /**
     * PHPUNIT DATA PROVIDER
     *
     * @param $providerContext
     * @param null $trueMethod
     * @return array
     */
    public function getMethods($providerContext, $trueMethod = null)
    {
        $refClass = new \ReflectionClass('Zend\Http\Request');
        $return = array();
        foreach ($refClass->getConstants() as $cName => $cValue) {
            if (substr($cName, 0, 6) == 'METHOD') {
                if ($providerContext) {
                    $return[] = array($cValue);
                } else {
                    $return[strtolower($cValue)] = ($trueMethod == $cValue) ? true : false;
                }
            }
        }
        return $return;
    }
}
