<?php
namespace ZendTest\Mvc\Router\Http;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Http\Request as Request,
    Zend\Mvc\Router\Http\Literal,
    Zend\Mvc\Router\Http\Part,
    Zend\Mvc\Router\RouteBroker;

class PartTest extends TestCase
{
    public static function matchProvider()
    {
        return array(
            array(array(
                'uri'    => 'http://test.net/',
                'offset' => 0,
                'match'  => array(
                    'controller' => 'ItsHomePage',
                ),
            )),
            array(array(
                'uri'    => 'http://test.net/blog',
                'offset' => 0,
                'match'  => array(
                    'controller' => 'ItsBlog',
                ),
            )),
            array(array(
                'uri'    => 'http://test.net/forum',
                'offset' => 0,
                'match'  => array(
                    'controller' => 'ItsForum',
                ),
            )),
            array(array(
                'uri'    => 'http://test.net/blog/rss',
                'offset' => 0,
                'match'  => null
            )),
            array(array(
                'uri'    => 'http://test.net/notfound',
                'offset' => 0,
                'match'  => null,
            )),
            array(array(
                'uri'    => 'http://test.net/blog/',
                'offset' => 0,
                'match'  => null,
            )),
            array(array(
                'uri'    => 'http://test.net/forum/notfound',
                'offset' => 0,
                'match'  => null,
            )),
            array(array(
                'uri'    => 'http://test.net/blog/rss/sub',
                'offset' => 0,
                'match'  => array(
                    'controller' => 'ItsRssBlog',
                    'action'     => 'ItsSubRss',
                ),
            )),
        );
    }

    public function getRoute()
    {
        $routeBroker = new RouteBroker();
        $routeBroker->getClassLoader()->registerPlugins(array(
            'literal' => 'Zend\Mvc\Router\Http\Literal',
            'regex'   => 'Zend\Mvc\Router\Http\Regex',
            'segment' => 'Zend\Mvc\Router\Http\Segment',
            'part'    => 'Zend\Mvc\Router\Http\Part',
        ));

        $route = Part::factory(array(
            'route' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'ItsHomePage',
                    ),
                )
            ),
            'may_terminate' => true,
            'route_broker'  => $routeBroker,
            'child_routes'  => array(
                'blog' => Part::factory(array(
                    'route' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route'    => 'blog',
                            'defaults' => array(
                                'controller' => 'ItsBlog',
                            ),
                        ),
                    ),
                    'may_terminate' => true,
                    'route_broker'  => $routeBroker,
                    'child_routes'  => array(
                        'rss' => Part::factory(array(
                            'route' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route'    => '/rss',
                                    'defaults' => array(
                                        'controller' => 'ItsRssBlog',
                                    ),
                                ),
                            ),
                            'route_broker'  => $routeBroker,
                            'child_routes'  => array(
                                'sub' => array(
                                    'type'    => 'literal',
                                    'options' => array(
                                        'route'    => '/sub',
                                        'defaults' => array(
                                            'action' => 'ItsSubRss',
                                        ),
                                    )
                                ),
                            ),
                        )),
                    ),
                )),
                'forum' => array(
                    'type'    => 'literal',
                    'options' => array(
                        'route'    => 'forum',
                        'defaults' => array(
                            'controller' => 'ItsForum',
                        ),
                    ),
                ),
            ),
        ));

        return $route;
    }

    /**
     * @dataProvider matchProvider
     */
    public function testMatch(array $params)
    {
        $route   = $this->getRoute();
        $request = new Request();
        
        $request->setUri($params['uri']);
        $match = $route->match($request, $params['offset']);

        if ($params['match'] === null) {
            $this->assertNull($match);
        } else {
            $this->assertInstanceOf('Zend\Mvc\Router\Http\RouteMatch', $match);
            
            foreach ($params['match'] as $key => $value) {
                $this->assertEquals($match->getParam($key), $value);
            }
        }
    }
    
    public function testAssembleCompleteRoute()
    {
        $uri = $this->getRoute()->assemble(array(), array('name' => 'blog/rss/sub'));
        
        $this->assertEquals('/blog/rss/sub', $uri);
    }
    
    public function testAssembleTerminatedRoute()
    {
        $uri = $this->getRoute()->assemble(array(), array('name' => 'blog'));
        
        $this->assertEquals('/blog', $uri);
    }
    
    /**
     * @expectedException Zend\Mvc\Router\Exception\RuntimeException
     */
    public function testAssembleNonTerminatedRoute()
    {
        $this->getRoute()->assemble(array(), array('name' => 'blog/rss'));
    }
}
