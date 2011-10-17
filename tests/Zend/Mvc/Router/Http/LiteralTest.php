<?php
namespace ZendTest\Mvc\Router\Http;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Http\Request as Request,
    Zend\Mvc\Router\Http\Literal;

class LiteralTest extends TestCase
{
    public static function matchProvider()
    {
        return array(
            array(array(
                'route'  => '/blog',
                'uri'    => 'http://test.net/blog/',
                'offset' => null,
                'match'  => false,
            )),
            array(array(
                'route'  => '/blog',
                'uri'    => 'http://test.net/blog/',
                'offset' => 0,
                'match'  => true,
            )),
            array(array(
                'route'  => '/blog',
                'uri'    => 'http://test.net/blog/blog',
                'offset' => 5,
                'match'  => true,
            )),
            array(array(
                'route'  => 'page',
                'uri'    => 'http://test.net/blog/page',
                'offset' => null,
                'match'  => false,
            )),
            array(array(
                'route'  => '/blog',
                'uri'    => 'http://test.net/blog/',
                'offset' => 7,
                'match'  => false,
            )),
            array(array(
                'route'  => '/',
                'uri'    => 'http://test.net/blog/',
                'offset' => 5,
                'match'  => true,
            )),
            array(array(
                'route'  => '/blog',
                'uri'    => 'http://test.net/blog/',
                'offset' => -1,
                'match'  => false,
            )),
            array(array(
                'route'  => '/',
                'uri'    => 'http://test.net',
                'offset' => null,
                'match'  => false,
            )),
        );
    }
    
    /**
     * @dataProvider matchProvider
     */
    public function testMatch(array $params)
    {
        $request = new Request();
        $route   = Literal::factory(array(
            'route'    => $params['route'],
            'defaults' => array(),
        ));

        $request->setUri($params['uri']);
        $match = $route->match($request, $params['offset']);
        
        if ($params['match'] === false) {
            $this->assertNull($match);
        } else {
            $this->assertInstanceOf('Zend\Mvc\Router\Http\RouteMatch', $match);
        }
    }
}

