<?php
namespace ZendTest\Mvc\Router\Http;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Http\Request as Request,
    Zend\Mvc\Router\Http\RouteMatch as Match,
    Zend\Mvc\Router\Http\Literal,
    Zend\Mvc\Router\Http\Part;

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
        $homePageRoute = Literal::factory(array(
            'route'    => '/',
            'defaults' => array(
                'controller' => 'ItsHomePage',
            ),
        ));
        $rssBlogRoute = Literal::factory(array(
            'route'    => '/rss',
            'defaults' => array(
                'controller' => 'ItsRssBlog',
            ),
        ));
        $subRssRoute = Literal::factory(array(
            'route'    => '/sub',
            'defaults' => array(
                'action' => 'ItsSubRss',
            ),
        ));
        $blogRoute = Literal::factory(array(
            'route'    => 'blog',
            'defaults' => array(
                'controller' => 'ItsBlog',
            ),
        ));
        $forumRoute = Literal::factory(array(
            'route'    => 'forum',
            'defaults' => array(
                'controller' => 'ItsForum',
            ),
        ));
        
        $route = Part::factory(array(
            'route'         => $homePageRoute,
            'may_terminate' => true,
            'child_routes'  => array(
                'blog' => Part::factory(array(
                    'route'         => $blogRoute,
                    'may_terminate' => true,
                    'child_routes'  => array(
                        'rss' => Part::factory(array(
                            'route'         => $rssBlogRoute,
                            'child_routes'  => array(
                                'sub' => $subRssRoute,
                            ),
                        )),
                    ),
                )),
                $forumRoute,
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
