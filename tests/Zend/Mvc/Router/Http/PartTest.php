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
            array(0, array(
                'uri'    => 'http://test.net/',
                'offset' => 0,
                'match'  => array(
                    'controller' => 'ItsHomePage',
                ),
            )),
            array(1, array(
                'uri'    => 'http://test.net/blog',
                'offset' => 0,
                'match'  => array(
                    'controller' => 'ItsBlog',
                ),
            )),
            array(2, array(
                'uri'    => 'http://test.net/forum',
                'offset' => 0,
                'match'  => array(
                    'controller' => 'ItsForum',
                ),
            )),
            array(3, array(
                'uri'    => 'http://test.net/blog/rss',
                'offset' => 0,
                'match'  => array(
                    'controller' => 'ItsRssBlog',
                ),
            )),
            array(4, array(
                'uri'    => 'http://test.net/notfound',
                'offset' => 0,
                'match'  => null,
            )),
            array(5, array(
                'uri'    => 'http://test.net/blog/', //http://test.net/blog/ and http://test.net/blog - Its Not Same!
                'offset' => 0,
                'match'  => null,
            )),
            array(6, array(
                'uri'    => 'http://test.net/forum/notfound',
                'offset' => 0,
                'match'  => null,
            )),
            array(7, array(
                'uri'    => 'http://test.net/blog/rss/sub',
                'offset' => 0,
                'match'  => array(
                    'controller' => 'ItsSubRss',
                ),
            )),
        );
    }

    public function getRoute()
    {
        $HomePageRoute = Literal::factory(array(
            'route'    => '/',
            'defaults' => array(
                'controller' => 'ItsHomePage',
            ),
        ));
        $RssBlogRoute = Literal::factory(array(
            'route'    => '/rss',
            'defaults' => array(
                'controller' => 'ItsRssBlog',
            ),
        ));
        $SubRssRoute = Literal::factory(array(
            'route'    => '/sub',
            'defaults' => array(
                'controller' => 'ItsSubRss',
            ),
        ));
        $BlogRoute = Literal::factory(array(
            'route'    => 'blog',
            'defaults' => array(
                'controller' => 'ItsBlog',
            ),
        ));
        $ForumRoute = Literal::factory(array(
            'route'    => 'forum',
            'defaults' => array(
                'controller' => 'ItsForum',
            ),
        ));
        
        $route = Part::factory(array(
            'route'         => $HomePageRoute,
            'may_terminate' => true,
            'child_routes'  => array(
                Part::factory(array(
                    'route'         => $BlogRoute,
                    'may_terminate' => true,
                    'child_routes'  => array(
                        Part::factory(array(
                            'route'         => $RssBlogRoute,
                            'may_terminate' => true,
                            'child_routes'  => array(
                                $SubRssRoute,
                            ),
                        )),
                    ),
                )),
                $ForumRoute,
            ),
        ));

        return $route;
    }

    /**
     * @dataProvider matchProvider
     */
    public function testMatch($index, $params)
    {
        $route   = $this->getRoute();
        $request = new Request();
        $request->setUri($params['uri']);
        $match = $route->match($request, $params['offset']);

        if ($params['match'] === null) {
            $this->assertNull($match, "assertion " . $index);
            return;
        } 

        $this->assertNotNull($match, "assertion " . $index);
        foreach ($params['match'] as $key => $value) {
            $this->assertEquals($match->getParam($key), $value, "assertion " . $index);
        }
    }
    
}
