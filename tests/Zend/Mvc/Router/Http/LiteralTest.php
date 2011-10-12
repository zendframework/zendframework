<?php
namespace ZendTest\Mvc\Router\Http;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Http\Request as Request,
    Zend\Mvc\Router\Http\RouteMatch as Match,
    Zend\Mvc\Router\Http\Literal;

class LiteralTest extends TestCase
{
    public static function matchProvider()
    {
        return array(
            array(0, array(
                'route'  => '/blog',
                'uri'    => 'http://test.net/blog/',
                'offset' => null,
                'match'  => false,
            )),
            array(1, array(
                'route'  => '/blog',
                'uri'    => 'http://test.net/blog/',
                'offset' => 0,
                'match'  => true,
            )),
            array(2, array(
                'route'  => '/blog',
                'uri'    => 'http://test.net/blog/blog',
                'offset' => 5,
                'match'  => true,
            )),
            array(3, array(
                'route'  => 'page',
                'uri'    => 'http://test.net/blog/page',
                'offset' => null,
                'match'  => false,
            )),
            array(4, array(
                'route'  => '/blog',
                'uri'    => 'http://test.net/blog/',
                'offset' => 7,
                'match'  => false,
            )),
            array(5, array(
                'route'  => '/',
                'uri'    => 'http://test.net/blog/',
                'offset' => 5,
                'match'  => true,
            )),
            array(6, array(
                'route'  => '/blog',
                'uri'    => 'http://test.net/blog/',
                'offset' => -1,
                'match'  => false,
            )),
            array(7, array(
                'route'  => '/',
                'uri'    => 'http://test.net',  // Request not valid
                'offset' => null,
                'match'  => false,
            )),
        );
    }
    
    /**
     * @dataProvider matchProvider
     */
    public function testMatch($index, $params)
    {
        $request = new Request();
        $route   = Literal::factory(array(
            'route'    => $params['route'],
            'defaults' => array(),
        ));

        $request->setUri($params['uri']);
        $match = $route->match($request ,$params['offset']);
        if ($params['match']) {
            $this->assertTrue($match instanceof Match, "assertion " . $index);
            return;
        }

        $this->assertNull($match, "assertion " . $index);
    }
    
}

