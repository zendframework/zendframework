<?php
namespace ZendTest\Mvc\Router\Http;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Http\Request as Request,
    Zend\Uri\Http as HttpUri,
    Zend\Mvc\Router\Http\Scheme;

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
    
    public function testAssembling()
    {
        $uri   = new HttpUri();       
        $route = new Scheme('https');
        $path  = $route->assemble(array(), array('uri' => $uri));
        
        $this->assertEquals('', $path);
        $this->assertEquals('https', $uri->getScheme());
    }
}

