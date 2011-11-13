<?php
namespace ZendTest\Mvc\Router\Http;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Http\Request,
    Zend\Mvc\Router\Http\Segment;

class SegmentTest extends TestCase
{
    public static function routeProvider()
    {
        return array(
            'simple-match' => array(
                '/:foo',
                array(),
                array(),
                '/bar',
                null,
                array(
                    'foo' => 'bar'
                )
            ),
            'no-match-without-leading-slash' => array(
                ':foo',
                array(),
                array(),
                '/bar/',
                null,
                null
            ),
            'no-match-with-trailing-slash' => array(
                '/:foo',
                array(),
                array(),
                '/bar/',
                null,
                null
            ),
            'offset-skips-beginning' => array(
                ':foo',
                array(),
                array(),
                '/bar',
                1,
                array(
                    'foo' => 'bar'
                )
            ),
            'offset-enables-partial-matching' => array(
                '/:foo',
                array(),
                array(),
                '/bar/baz',
                0,
                array(
                    'foo' => 'bar'
                )
            ),
            'match-overrides-default' => array(
                '/:foo',
                array(),
                array('foo' => 'baz'),
                '/bar',
                null,
                array(
                    'foo' => 'bar'
                )
            ),
            'constraints-prevent-match' => array(
                '/:foo',
                array('foo' => '\d+'),
                array(),
                '/bar',
                null,
                null
            ),
            'constraints-allow-match' => array(
                '/:foo',
                array('foo' => '\d+'),
                array(),
                '/123',
                null,
                array(
                    'foo' => '123'
                )
            ),
            'constraints-override-non-standard-delimiter' => array(
                '/:foo{-}/bar',
                array('foo' => '[^/]+'),
                array(),
                '/foo-bar/bar',
                null,
                array(
                    'foo' => 'foo-bar'
                )
            ),
            'optional-parameter-is-ignored' => array(
                '/:foo[/:bar]',
                array(),
                array(),
                '/bar',
                null,
                array(
                    'foo' => 'bar'
                )
            ),
            'optional-parameter-is-provided-with-default' => array(
                '/:foo[/:bar]',
                array(),
                array('bar' => 'baz'),
                array('/bar', '/bar/baz'),
                null,
                array(
                    'foo' => 'bar',
                    'bar' => 'baz',
                )
            ),
            'optional-parameter-is-consumed' => array(
                '/:foo[/:bar]',
                array(),
                array(),
                '/bar/baz',
                null,
                array(
                    'foo' => 'bar',
                    'bar' => 'baz'
                )
            ),
            'non-standard-delimiter-before-parameter' => array(
                '/foo-:bar',
                array(),
                array(),
                '/foo-baz',
                null,
                array(
                    'bar' => 'baz'
                )
            ),
            'non-standard-delimiter-between-parameters' => array(
                '/:foo{-}-:bar',
                array(),
                array(),
                '/bar-baz',
                null,
                array(
                    'foo' => 'bar',
                    'bar' => 'baz'
                )
            ),
            'non-standard-delimiter-before-optional-parameter' => array(
                '/:foo{-/}[-:bar]/:baz',
                array(),
                array(),
                '/bar-baz/bat',
                null,
                array(
                    'foo' => 'bar',
                    'bar' => 'baz',
                    'baz' => 'bat',
                )
            ),
            'non-standard-delimiter-before-ignored-optional-parameter' => array(
                '/:foo{-/}[-:bar]/:baz',
                array(),
                array(),
                '/bar/bat',
                null,
                array(
                    'foo' => 'bar',
                    'baz' => 'bat',
                )
            ),
        );
    }
    
    /**
     * @dataProvider routeProvider
     * @param        string  $route
     * @param        array   $constraints
     * @param        array   $defaults
     * @param        mixed   $path
     * @param        integer $offset
     * @param        array   $params
     */
    public function testMatching($route, array $constraints, array $defaults, $path, $offset, array $params = null)
    {
        if (is_array($path)) {
            $path = $path[0];
        }
        
        $request = new Request();
        $route   = new Segment($route, $constraints, $defaults);
        
        $request->setUri('http://example.com' . $path);
        $match = $route->match($request, $offset);
        
        if ($params === null) {
            $this->assertNull($match);
        } else {
            $this->assertInstanceOf('Zend\Mvc\Router\Http\RouteMatch', $match);
            
            foreach($params as $key => $value) {
                $this->assertEquals($value, $match->getParam($key));
            }
        }
    }
    
    /**
     * @dataProvider routeProvider
     * @param        string  $route
     * @param        array   $constraints
     * @param        array   $defaults
     * @param        mixed   $path
     * @param        integer $offset
     * @param        array   $params
     */
    public function testAssembling($route, array $constraints, array $defaults, $path, $offset, array $params = null)
    {
        if ($params === null) {
            // Data which will not match are not tested for assembling.
            return;
        }
        
        if (is_array($path)) {
            $path = $path[1];
        }
        
        $route  = new Segment($route, $constraints, $defaults);
        $result = $route->assemble($params);
        
        if ($offset !== null) {
            $this->assertEquals($offset, strpos($path, $result, $offset));
        } else {
            $this->assertEquals($path, $result);
        }
    }
}
