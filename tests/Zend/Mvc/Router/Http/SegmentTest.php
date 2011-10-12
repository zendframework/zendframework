<?php
namespace ZendTest\Mvc\Router\Http;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Http\Request,
    Zend\Mvc\Router\Http\Segment;

class SegmentTest extends TestCase
{
    public static function routesForTestMatch()
    {
        return array(
            array(array(
                'route'    => '/:foo[/:bar]',
                'defaults' => array(),
                'uri'      => 'http://example.com/blog/record1',
                'offset'   => null,
                'match'    => array(
                    'foo' => 'blog',
                    'bar' => 'record1'
                )
            )),
            array(array(
                'route'    => '/:foo[/:bar]',
                'defaults' => array(),
                'uri'      => 'http://example.com/blog',
                'offset'   => null,
                'match'    => array(
                    'foo' => 'blog'
                )
            )),
            array(array(
                'route'    => '/:foo/:bar',
                'defaults' => array(),
                'uri'      => 'http://example.com/blog/record1',
                'offset'   => null,
                'match'    => array(
                    'foo' => 'blog',
                    'bar' => 'record1'
                )
            )),
            array(array(
                'route'    => '/:foo',
                'defaults' => array(),
                'uri'      => 'http://example.com/blog/record1',
                'offset'   => null,
                'match'    => null
            )),
            array(array(
                'route'    => '/blog-:foo',
                'defaults' => array(),
                'uri'      => 'http://example.com/blog-record1',
                'offset'   => null,
                'match'    => array(
                    'foo' => 'record1'
                )
            )),
            array(array(
                'route'    => 'blog-:foo',
                'defaults' => array(),
                'uri'      => 'http://example.com/blog-record1',
                'offset'   => 1,
                'match'    => array(
                    'foo' => 'record1'
                )
            )),
            array(array(
                'route'    => 'blog-:foo{-}[-bar]',
                'defaults' => array(),
                'uri'      => 'http://example.com/blog-record1',
                'offset'   => 1,
                'match'    => array(
                    'foo' => 'record1'
                )
            )),
            array(array(
                'route'    => 'blog-:foo{-}',
                'defaults' => array(),
                'uri'      => 'http://example.com/blog-record1',
                'offset'   => 1,
                'match'    => array(
                    'foo' => 'record1'
                )
            )),
        );
    }
    
    /**
     * @dataProvider routesForTestMatch
     */
    public function testMatch($params) 
    {
        $request = new Request();
        $route   = new Segment($params['route'], array(), $params['defaults']);
        
        $request->setUri($params['uri']);
        $match = $route->match($request, $params['offset']);
        
        if ($params['match'] == null) {
            $this->assertNull($match);
        } else {
            $this->assertInstanceOf('Zend\Mvc\Router\Http\RouteMatch', $match);
            
            foreach($params['match'] as $key => $value) {
                $this->assertEquals($match->getParam($key), $value);
            }
        }
    }
}
