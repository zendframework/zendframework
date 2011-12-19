<?php
namespace ZendTest\Mvc\Router\Http;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Http\Request as Request,
    Zend\Stdlib\Request as BaseRequest,
    Zend\Uri\Http as HttpUri,
    Zend\Mvc\Router\Http\Scheme,
    ZendTest\Mvc\Router\FactoryTester;

class SchemeTest extends TestCase
{
    public function testMatching()
    {
        $request = new Request();
        $request->setUri('https://example.com/');
        
        $route = new Scheme('https');
        $match = $route->match($request);
        
        $this->assertInstanceOf('Zend\Mvc\Router\Http\RouteMatch', $match);
    }
    
    public function testNoMatchingOnDifferentScheme()
    {
        $request = new Request();
        $request->setUri('http://example.com/');
        
        $route = new Scheme('https');
        $match = $route->match($request);
        
        $this->assertNull($match);
    }
    
    public function testAssembling()
    {
        $uri   = new HttpUri();
        $route = new Scheme('https');
        $path  = $route->assemble(array(), array('uri' => $uri));
        
        $this->assertEquals('', $path);
        $this->assertEquals('https', $uri->getScheme());
    }
    
    public function testNoMatchWithoutUriMethod()
    {
        $route   = new Scheme('https');
        $request = new BaseRequest();
        
        $this->assertNull($route->match($request));
    }
    
    public function testGetAssembledParams()
    {
        $route = new Scheme('https');
        $route->assemble(array('foo' => 'bar'));
        
        $this->assertEquals(array(), $route->getAssembledParams());
    }
    
    public function testFactory()
    {
        $tester = new FactoryTester($this);
        $tester->testFactory(
            'Zend\Mvc\Router\Http\Scheme',
            array(
                'scheme' => 'Missing "scheme" in options array',
            ),
            array(
                'scheme' => 'http',
            )
        );
    }
}

