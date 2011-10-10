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

    public static function routesForTestMatch()
    {
        return array(
            array(0, array(
                'route' => '/:foo[/:bar]',
                'defaults' => array(),
                'uri'       => 'http://test.net/blog/record1',
                'offset'    => null,
                'match'     => array(
                    'foo' => 'blog',
                    'bar' => 'record1'
                )
            )),
            array(1, array(
                'route' => '/:foo[/:bar]',
                'defaults' => array(),
                'uri'       => 'http://test.net/blog',
                'offset'    => null,
                'match'     => array(
                    'foo' => 'blog'
                )
            )),
            array(2, array(
                'route' => '/:foo/:bar',
                'defaults' => array(),
                'uri'       => 'http://test.net/blog/record1',
                'offset'    => null,
                'match'     => array(
                    'foo' => 'blog',
                    'bar' => 'record1'
                )
            )),
            array(3, array(
                'route' => '/:foo',
                'defaults' => array(),
                'uri'       => 'http://test.net/blog/record1',
                'offset'    => null,
                'match'     => array(
                    'foo' => 'blog/record1'
                )
            )),
            array(4, array(
                'route' => '/blog-:foo',
                'defaults' => array(),
                'uri'       => 'http://test.net/blog-record1',
                'offset'    => null,
                'match'     => array(
                    'foo' => 'record1'
                )
            )),
            array(5, array( // test fail!
                'route' => 'blog-:foo',
                'defaults' => array(),
                'uri'       => 'http://test.net/blog-record1',
                'offset'    => 1,
                'match'     => array(
                    'foo' => 'record1'
                )
            )),
        );
    }
    
    /**
     * @dataProvider routesForTestMatch
     */
    public function testMatch($index, $params) 
    {
        $request = new Request();

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
