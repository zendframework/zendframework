<?php
namespace ZendTest\Mvc\Router\Http;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Http\Request,
    Zend\Mvc\Router\Http\Segment;

class SegmentTest extends TestCase
{
    public static function matchingProvider()
    {
        return array(
            'simple-match' => array(
                '/:foo',
                array(),
                array(),
                'http://example.com/bar',
                null,
                array(
                    'foo' => 'bar'
                )
            ),
            'no-match-without-leading-slash' => array(
                ':foo',
                array(),
                array(),
                'http://example.com/bar/',
                null,
                null
            ),
            'no-match-with-trailing-slash' => array(
                '/:foo',
                array(),
                array(),
                'http://example.com/bar/',
                null,
                null
            ),
            'offset-skips-beginning' => array(
                ':foo',
                array(),
                array(),
                'http://example.com/bar',
                1,
                array(
                    'foo' => 'bar'
                )
            ),
            'offset-enables-partial-matching' => array(
                '/:foo',
                array(),
                array(),
                'http://example.com/bar/baz',
                0,
                array(
                    'foo' => 'bar'
                )
            ),
            'match-overrides-default' => array(
                '/:foo',
                array(),
                array('foo' => 'baz'),
                'http://example.com/bar',
                null,
                array(
                    'foo' => 'bar'
                )
            ),
            'constraints-prevent-match' => array(
                '/:foo',
                array('foo' => '\d+'),
                array(),
                'http://example.com/bar',
                null,
                null
            ),
            'constraints-allow-match' => array(
                '/:foo',
                array('foo' => '\d+'),
                array(),
                'http://example.com/123',
                null,
                array(
                    'foo' => '123'
                )
            ),
            'constraints-override-non-standard-delimiter' => array(
                '/:foo{-}/bar',
                array('foo' => '[^/]+'),
                array(),
                'http://example.com/foo-bar/bar',
                null,
                array(
                    'foo' => 'foo-bar'
                )
            ),
            'optional-parameter-is-ignored' => array(
                '/:foo[/:bar]',
                array(),
                array(),
                'http://example.com/bar',
                null,
                array(
                    'foo' => 'bar'
                )
            ),
            'optional-parameter-is-provided-with-default' => array(
                '/:foo[/:bar]',
                array(),
                array('bar' => 'baz'),
                'http://example.com/bar',
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
                'http://example.com/bar/baz',
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
                'http://example.com/foo-baz',
                null,
                array(
                    'bar' => 'baz'
                )
            ),
            'non-standard-delimiter-between-parameters' => array(
                '/:foo{-}-:bar',
                array(),
                array(),
                'http://example.com/bar-baz',
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
                'http://example.com/bar-baz/bat',
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
                'http://example.com/bar/bat',
                null,
                array(
                    'foo' => 'bar',
                    'baz' => 'bat',
                )
            ),
        );
    }
    
    /**
     * @dataProvider matchingProvider
     * @param        string  $route
     * @param        array   $constraints
     * @param        array   $defaults
     * @param        string  $uri
     * @param        integer $offset
     * @param        array   $params
     */
    public function testMatching($route, array $constraints, array $defaults, $uri, $offset, array $params = null)
    {
        $request = new Request();
        $route   = new Segment($route, $constraints, $defaults);
        
        $request->setUri($uri);
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
}
