<?php
namespace ZendTest\Mvc\Router\Http;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Http\Request as Request,
    Zend\Mvc\Router\Http\RouteMatch as Match,
    Zend\Mvc\Router\Http\Literal;

class LiteralTest extends TestCase
{
    
    public function testMatch()
    {
        $RouteForTest = array(
            0 => array(
                'route' => '/blog',
                'uri'       => 'http://test.net/blog/',
                'offset'    => null,
                'match'     => false
            ),
            1 => array(
                'route' => '/blog',
                'uri'       => 'http://test.net/blog/',
                'offset'    => 0,
                'match'     => true
            ),
            2 => array(
                'route' => '/blog',
                'uri'       => 'http://test.net/blog/blog',
                'offset'    => 5,
                'match'     => true
            ),
            3 => array(
                'route' => 'page',
                'uri'       => 'http://test.net/blog/page',
                'offset'    => null,
                'match'     => false
            ),
            4 => array(
                'route' => '/blog',
                'uri'       => 'http://test.net/blog/',
                'offset'    => 7,
                'match'     => false
            ),
            5 => array(
                'route' => '/',
                'uri'       => 'http://test.net/blog/',
                'offset'    => 5,
                'match'     => true
            ),
            6 => array(
                'route' => '/blog',
                'uri'       => 'http://test.net/blog/',
                'offset'    => -1,
                'match'     => false
            ),
            7 => array(
                'route' => '/',
                'uri'       => 'http://test.net',  // Request not valid
                'offset'    => null,
                'match'     => false
            ),
        );
        
        $request = new Request();
        foreach($RouteForTest as $key => $params) {
            $route = new Literal(array(
                'route'    => $params['route'],
                'defaults' => array()
            ));
            
            $request->setUri($params['uri']);
            $match = $route->match($request,$params['offset']);
            if ($params['match']) {
                $this->assertTrue($match instanceof Match, "assert №".$key);
            } else {
                $this->assertNull($match, "assert №".$key);
            }
        }
    }
    
}

