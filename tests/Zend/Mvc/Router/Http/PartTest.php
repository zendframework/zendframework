<?php
namespace ZendTest\Mvc\Router\Http;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Http\Request as Request,
    Zend\Mvc\Router\Http\RouteMatch as Match,
    Zend\Mvc\Router\Http\Literal,
    Zend\Mvc\Router\Http\Part;

class PartTest extends TestCase
{
        
    public function testMatch() // Maybe hard for debug...
    {
         $HomePageRoute = new Literal(array(
            'route'=> '/',
            'defaults' => array(
                'controller' => 'ItsHomePage'
                )
        ));
        $RssBlogRoute = new Literal(array(
            'route'=> '/rss',
            'defaults' => array(
                'controller' => 'ItsRssBlog'
                )
        ));
        $SubRssRoute = new Literal(array(
            'route'=> '/sub',
            'defaults' => array(
                'controller' => 'ItsSubRss'
                )
        ));
        $BlogRoute = new Literal(array(
            'route'=> 'blog',
            'defaults' => array(
                'controller' => 'ItsBlog'
                )
        ));
        $ForumRoute = new Literal(array(
            'route'=> 'forum',
            'defaults' => array(
                'controller' => 'ItsForum'
                )
        ));
        
        $route = new Part(array(
            'route'      => $HomePageRoute,
            'may_terminate' => true,
            'child_routes'   => array(
                new Part(array (
                    'route'      => $BlogRoute,
                    'may_terminate' => true,
                    'child_routes'   => array(
                        new Part(array (
                            'route'      => $RssBlogRoute,
                            'may_terminate' => true,
                            'child_routes'   => array(
                                $SubRssRoute
                             )
                        ))
                    )
                )),
                $ForumRoute
            )
        ));
        $request = new Request();
        
        
        $UriForTest = array(
            0 => array(
                'uri' => 'http://test.net/',
                'offset' => 0,
                'match' => array(
                    'controller' => 'ItsHomePage'
                    )
            ),
            1 => array(
                'uri' => 'http://test.net/blog',
                'offset' => 0,
                'match' => array(
                    'controller' => 'ItsBlog'
                    )
            ),
            2 => array(
                'uri' => 'http://test.net/forum',
                'offset' => 0,
                'match' => array(
                    'controller' => 'ItsForum'
                    )
            ),
            3 => array(
                'uri' => 'http://test.net/blog/rss',
                'offset' => 0,
                'match' => array(
                    'controller' => 'ItsRssBlog'
                    )
            ),
            4 => array(
                'uri' => 'http://test.net/notfound',
                'offset' => 0,
                'match' => null
            ),
            5 => array(
                'uri' => 'http://test.net/blog/', //http://test.net/blog/ and http://test.net/blog - Its Not Same!
                'offset' => 0,
                'match' => null
            ),
            6 => array(
                'uri' => 'http://test.net/forum/notfound',
                'offset' => 0,
                'match' => null
            ),
            7 => array(
                'uri' => 'http://test.net/blog/rss/sub',
                'offset' => 0,
                'match' => array(
                    'controller' => 'ItsSubRss'
                    )
            ),
        );
        
        foreach($UriForTest as $index => $params) {
            $request->setUri($params['uri']);
            $match = $route->match($request,$params['offset']);
            if ($params['match'] == null) {
                $this->assertNull($match, "assert №".$index);
            } elseif(is_array($params['match'])) {
                $this->assertNotNull($match, "assert №".$index);
                foreach($params['match'] as $key=>$value) {
                    $this->assertEquals($match->getParam($key), $value, "assert №".$index);
                }
            }
        }
    }
    
}
