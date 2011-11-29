<?php
namespace ZendTest\Mvc\Router;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Mvc\Router\RouteMatch;

class RouteMatchTest extends TestCase
{
    public function testParamsAreStored()
    {
        $match = new RouteMatch(array('foo' => 'bar'));
        
        $this->assertEquals(array('foo' => 'bar'), $match->getParams());
    }
    
    public function testMatchedRouteNameIsSet()
    {
        $match = new RouteMatch(array());
        $match->setMatchedRouteName('foo');
        
        $this->assertEquals('foo', $match->getMatchedRouteName());
    }
    
    public function testSetParam()
    {
        $match = new RouteMatch(array());
        $match->setParam('foo', 'bar');
        
        $this->assertEquals(array('foo' => 'bar'), $match->getParams());
    }
    
    public function testGetParam()
    {
        $match = new RouteMatch(array('foo' => 'bar'));
        
        $this->assertEquals('bar', $match->getParam('foo'));
    }
    
    public function testGetNonExistentParamWithoutDefault()
    {
        $match = new RouteMatch(array());
        
        $this->assertNull($match->getParam('foo'));
    }
    
    public function testGetNonExistentParamWithDefault()
    {
        $match = new RouteMatch(array());
        
        $this->assertEquals('bar', $match->getParam('foo', 'bar'));
    }
}

