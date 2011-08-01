<?php

/**
 * @namespace
 */
namespace ZendTest\Http;

use Zend\Http\Request;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function testRequestUsesParametersContainerByDefault()
    {
        $request = new Request();
        $this->assertInstanceOf('Zend\Stdlib\Parameters', $request->query());
        $this->assertInstanceOf('Zend\Stdlib\Parameters', $request->post());
        $this->assertInstanceOf('Zend\Stdlib\Parameters', $request->cookie());
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
        $request->setCookie($p);
        $request->setFile($p);
        $request->setServer($p);
        $request->setEnv($p);

        $this->assertSame($p, $request->query());
        $this->assertSame($p, $request->post());
        $this->assertSame($p, $request->cookie());
        $this->assertSame($p, $request->file());
        $this->assertSame($p, $request->server());
        $this->assertSame($p, $request->env());
    }

    public function testRequestPersistsRawBody()
    {
        $request = new Request();
        $request->setRawBody('foo');
        $this->assertEquals('foo', $request->getRawBody());
    }

    public function testRequestUsesRequestHeadersContainerByDefault()
    {
        $request = new Request();
        $this->assertInstanceOf('Zend\Http\RequestHeaders', $request->headers());
    }

    public function testRequestCanSetRequestHeaders()
    {
        $request = new Request();
        $rHeaders = new \Zend\Http\RequestHeaders();

        $ret = $request->setHeaders($rHeaders);
        $this->assertInstanceOf('Zend\Http\Request', $ret);
        $this->assertSame($rHeaders, $request->headers());
    }

    public function testRequestCanSetAndRetrieveValidMethod()
    {
        $request = new Request();
        $request->setMethod('POST');
        $this->assertEquals('POST', $request->getMethod());
    }

    /**
     * @dataProvider getMethods
     */
    public function testMethodCheckWorksForAllMethods($methodName)
    {
        $request = new Request;
        $request->setMethod($methodName);

        foreach ($this->getMethods(false, $methodName) as $testMethodName => $testMethodValue) {
            $this->assertEquals($testMethodValue, $request->{'is' . $testMethodName}());
        }
    }

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
