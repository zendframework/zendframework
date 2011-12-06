<?php

/**
 * @namespace
 */
namespace ZendTest\Http;

use Zend\Http\Request;

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
        $this->assertInstanceOf('Zend\Stdlib\Parameters', $request->query());
        $this->assertInstanceOf('Zend\Stdlib\Parameters', $request->post());
        $this->assertInstanceOf('Zend\Stdlib\Parameters', $request->file());
        $this->assertInstanceOf('Zend\Stdlib\Parameters', $request->server());
        $this->assertInstanceOf('Zend\Stdlib\Parameters', $request->env());
    }

    public function testRequestAllowsSettingOfParameterContainer()
    {
        $request = new Request();
        $p = new \Zend\Stdlib\Parameters();
        $request->setQuery($p);
        $request->setPost($p);
        $request->setFile($p);
        $request->setServer($p);
        $request->setEnv($p);

        $this->assertSame($p, $request->query());
        $this->assertSame($p, $request->post());
        $this->assertSame($p, $request->file());
        $this->assertSame($p, $request->server());
        $this->assertSame($p, $request->env());
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
        $this->assertInstanceOf('Zend\Http\Headers', $request->headers());
    }

    public function testRequestCanSetHeaders()
    {
        $request = new Request();
        $headers = new \Zend\Http\Headers();

        $ret = $request->setHeaders($headers);
        $this->assertInstanceOf('Zend\Http\Request', $ret);
        $this->assertSame($headers, $request->headers());
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

    public function testRequestCanSetAndRetrieveUri()
    {
        $request = new Request();
        $request->setUri('/foo');
        $this->assertEquals('/foo', $request->getUri());
        $this->assertInstanceOf('Zend\Uri\Uri', $request->uri());
        $this->assertEquals('/foo', $request->uri()->toString());
        $this->assertEquals('/foo', $request->getUri());
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

        $this->setExpectedException('Zend\Http\Exception\InvalidArgumentException', 'not a valid version');
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
