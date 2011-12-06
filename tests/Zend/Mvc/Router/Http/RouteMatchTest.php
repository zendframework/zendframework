<?php
namespace ZendTest\Mvc\Router\Http;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Mvc\Router\Http\RouteMatch;

class RouteMatchTest extends TestCase
{
    public function testParamsAreStored()
    {
        $match = new RouteMatch(array('foo' => 'bar'));
        
        $this->assertEquals(array('foo' => 'bar'), $match->getParams());
    }
    
    public function testLengthIsStored()
    {
        $match = new RouteMatch(array(), 10);
        
        $this->assertEquals(10, $match->getLength());
    }
    
    public function testLengthIsMerged()
    {
        $match = new RouteMatch(array(), 10);
        $match->merge(new RouteMatch(array(), 5));
        
        $this->assertEquals(15, $match->getLength());
    }
    
    public function testMatchedRouteNameIsSet()
    {
        $match = new RouteMatch(array());
        $match->setMatchedRouteName('foo');
        
        $this->assertEquals('foo', $match->getMatchedRouteName());
    }
    
    public function testMatchedRouteNameIsPrependedWhenAlreadySet()
    {
        $match = new RouteMatch(array());
        $match->setMatchedRouteName('foo');
        $match->setMatchedRouteName('bar');
        
        $this->assertEquals('bar/foo', $match->getMatchedRouteName());
    }
    
    public function testMatchedRouteNameIsOverridenOnMerge()
    {
        $match = new RouteMatch(array());
        $match->setMatchedRouteName('foo');
        
        $subMatch = new RouteMatch(array());
        $subMatch->setMatchedRouteName('bar');
        
        $match->merge($subMatch);
        
        $this->assertEquals('bar', $match->getMatchedRouteName());
    }
}

