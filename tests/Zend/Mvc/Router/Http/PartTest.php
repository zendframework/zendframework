<?php
namespace ZendTest\Mvc\Router\Http;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Http\Request as Request,
    Zend\Mvc\Router\RouteBroker,
    Zend\Mvc\Router\Http\Part;

class PartTest extends TestCase
{
    public static function getRoute()
    {
        $routeBroker = new RouteBroker();
        $routeBroker->getClassLoader()->registerPlugins(array(
            'part' => 'Zend\Mvc\Router\Http\Part'
        ));
        
        return new Part(
            array(
                'type'    => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/foo',
                    'defaults' => array(
                        'controller' => 'foo'
                    )
                )
            ),
            true,
            $routeBroker,
            array(
                'bar' => array(
                    'type'    => 'Zend\Mvc\Router\Http\Literal',
                    'options' => array(
                        'route'    => '/bar',
                        'defaults' => array(
                            'controller' => 'bar'
                        )
                    )
                ),
                'baz' => array(
                    'type'    => 'Zend\Mvc\Router\Http\Literal',
                    'options' => array(
                        'route' => '/baz'
                    ),
                    'child_routes' => array(
                        'bat' => array(
                            'type'    => 'Zend\Mvc\Router\Http\Segment',
                            'options' => array(
                                'route' => '/:controller'
                            ),
                            'may_terminate' => true,
                            'child_routes'  => array(
                                'wildcard' => array(
                                    'type' => 'Zend\Mvc\Router\Http\Wildcard'
                                )
                            )
                        )                   
                    )
                )
            )
        );
    }
    
    public static function routeProvider()
    {       
        return array(
            'simple-match' => array(
                self::getRoute(),
                '/foo',
                null,
                null,
                array('controller' => 'foo')
            ),
            'offset-skips-beginning' => array(
                self::getRoute(),
                '/bar/foo',
                4,
                null,
                array('controller' => 'foo')
            ),
            'simple-child-match' => array(
                self::getRoute(),
                '/foo/bar',
                null,
                'bar',
                array('controller' => 'bar')
            ),
            'offset-does-not-enable-partial-matching' => array(
                self::getRoute(),
                '/foo/foo',
                0,
                null,
                null
            ),
            'non-terminating-part-does-not-match' => array(
                self::getRoute(),
                '/foo/bat',
                null,
                null,
                null
            ),
            'child-of-non-terminating-part-does-match' => array(
                self::getRoute(),
                '/foo/baz/bat',
                null,
                'baz/bat',
                array('controller' => 'bat')
            ),
            'parameters-are-used-only-once' => array(
                self::getRoute(),
                '/foo/baz/wildcard/foo/bar',
                null,
                'baz/bat/wildcard',
                array('controller' => 'wildcard', 'foo' => 'bar')
            ),
        );
    }

    /**
     * @dataProvider routeProvider
     * @param        Part    $route
     * @param        string  $path
     * @param        integer $offset
     * @param        string  $routeName
     * @param        array   $params
     */
    public function testMatching(Part $route, $path, $offset, $routeName, array $params = null)
    {
        $request = new Request();
        $request->setUri('http://example.com' . $path);
        $match = $route->match($request, $offset);
        
        if ($params === null) {
            $this->assertNull($match);
        } else {
            $this->assertInstanceOf('Zend\Mvc\Router\Http\RouteMatch', $match);
            
            if ($offset === null) {
                $this->assertEquals(strlen($path), $match->getLength());            
            }
            
            $this->assertEquals($routeName, $match->getMatchedRouteName());
            
            foreach ($params as $key => $value) {
                $this->assertEquals($value, $match->getParam($key));
            }
        }
    }
    
    /**
     * @dataProvider routeProvider
     * @param        Part    $route
     * @param        string  $path
     * @param        integer $offset
     * @param        string  $routeName
     * @param        array   $params
     */
    public function testAssembling(Part $route, $path, $offset, $routeName, array $params = null)
    {
        if ($params === null) {
            // Data which will not match are not tested for assembling.
            return;
        }
                
        $result = $route->assemble($params, array('name' => $routeName));
        
        if ($offset !== null) {
            $this->assertEquals($offset, strpos($path, $result, $offset));
        } else {
            $this->assertEquals($path, $result);
        }
    }
    
    /**
     * @expectedException Zend\Mvc\Router\Exception\RuntimeException
     */
    public function testAssembleNonTerminatedRoute()
    {
        self::getRoute()->assemble(array(), array('name' => 'bat'));
    }
}
