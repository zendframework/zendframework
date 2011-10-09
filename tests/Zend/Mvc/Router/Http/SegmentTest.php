<?php
namespace ZendTest\Mvc\Router\Http;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Http\Request as Request,
    Zend\Mvc\Router\Http\RouteMatch as Match,
    Zend\Mvc\Router\Http\Segment;

class SegmentTest extends TestCase
{
    public function testConstruct()
    {
        $route = new Segment(array(
            'defaults' => array(),
            'route'    => '/:foo[/:bar]'
        ));
        $this->assertTrue($route instanceof Segment);
    }
    
    public function testMatch() {
        $RouteForTest = array(
            0 => array(
                'route' => '/:foo[/:bar]',
                'defaults' => array(),
                'uri'       => 'http://test.net/blog/record1',
                'offset'    => null,
                'match'     => array(
                    'foo' => 'blog',
                    'bar' => 'record1'
                )
            ),
            1 => array(
                'route' => '/:foo[/:bar]',
                'defaults' => array(),
                'uri'       => 'http://test.net/blog',
                'offset'    => null,
                'match'     => array(
                    'foo' => 'blog'
                )
            ),
            2 => array(
                'route' => '/:foo/:bar',
                'defaults' => array(),
                'uri'       => 'http://test.net/blog/record1',
                'offset'    => null,
                'match'     => array(
                    'foo' => 'blog',
                    'bar' => 'record1'
                )
            ),
            3 => array(
                'route' => '/:foo',
                'defaults' => array(),
                'uri'       => 'http://test.net/blog/record1',
                'offset'    => null,
                'match'     => array(
                    'foo' => 'blog/record1'
                )
            ),
            4 => array(
                'route' => '/blog-:foo',
                'defaults' => array(),
                'uri'       => 'http://test.net/blog-record1',
                'offset'    => null,
                'match'     => array(
                    'foo' => 'record1'
                )
            ),
            5 => array( // test fail!
                'route' => 'blog-:foo',
                'defaults' => array(),
                'uri'       => 'http://test.net/blog-record1',
                'offset'    => 1,
                'match'     => array(
                    'foo' => 'record1'
                )
            ),
        );
        
        $request = new Request();
        foreach($RouteForTest as $index => $params) {
            $route = new Segment(array(
                'route'    => $params['route'],
                'defaults' => $params['defaults']
            ));
            
            $request->setUri($params['uri']);
            $match = $route->match($request,$params['offset']);
            if ($params['match'] == Null) {
                $this->assertNull($match, "assert №".$index);
            } else {
                $this->assertNotNull($match, "assert №".$index);
                foreach($params['match'] as $key=>$value) {
                    $this->assertEquals($match->getParam($key), $value, "assert №".$index);
                }
            }
        }
    }
}
