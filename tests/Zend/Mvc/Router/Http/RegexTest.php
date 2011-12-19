<?php
namespace ZendTest\Mvc\Router\Http;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Http\Request as Request,
    Zend\Stdlib\Request as BaseRequest,
    Zend\Mvc\Router\Http\Regex,
    ZendTest\Mvc\Router\FactoryTester;

class RegexTest extends TestCase
{
    public static function routeProvider()
    {
        return array(
            'simple-match' => array(
                new Regex('/(?<foo>[^/]+)', '/%foo%'),
                '/bar',
                null,
                array('foo' => 'bar')
            ),
            'no-match-without-leading-slash' => array(
                new Regex('(?<foo>[^/]+)', '%foo%'),
                '/bar',
                null,
                null
            ),
            'no-match-with-trailing-slash' => array(
                new Regex('/(?<foo>[^/]+)', '/%foo%'),
                '/bar/',
                null,
                null
            ),
            'offset-skips-beginning' => array(
                new Regex('(?<foo>[^/]+)', '%foo%'),
                '/bar',
                1,
                array('foo' => 'bar')
            ),
            'offset-enables-partial-matching' => array(
                new Regex('/(?<foo>[^/]+)', '/%foo%'),
                '/bar/baz',
                0,
                array('foo' => 'bar')
            ),
        );
    }

    /**
     * @dataProvider routeProvider
     * @param        Regex   $route
     * @param        string  $path
     * @param        integer $offset
     * @param        array   $params
     */
    public function testMatching(Regex $route, $path, $offset, array $params = null)
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
            
            foreach ($params as $key => $value) {
                $this->assertEquals($value, $match->getParam($key));
            }
        }
    }
    
    /**
     * @dataProvider routeProvider
     * @param        Regex   $route
     * @param        string  $path
     * @param        integer $offset
     * @param        array   $params
     */
    public function testAssembling(Regex $route, $path, $offset, array $params = null)
    {
        if ($params === null) {
            // Data which will not match are not tested for assembling.
            return;
        }
                
        $result = $route->assemble($params);
        
        if ($offset !== null) {
            $this->assertEquals($offset, strpos($path, $result, $offset));
        } else {
            $this->assertEquals($path, $result);
        }
    }
    
    public function testNoMatchWithoutUriMethod()
    {
        $route   = new Regex('/foo', '/foo');
        $request = new BaseRequest();
        
        $this->assertNull($route->match($request));
    }
    
    public function testGetAssembledParams()
    {
        $route = new Regex('/(?<foo>.+)', '/%foo%');
        $route->assemble(array('foo' => 'bar', 'baz' => 'bat'));
        
        $this->assertEquals(array('foo'), $route->getAssembledParams());
    }
    
    public function testFactory()
    {
        $tester = new FactoryTester($this);
        $tester->testFactory(
            'Zend\Mvc\Router\Http\Regex',
            array(
                'regex' => 'Missing "regex" in options array',
                'spec'  => 'Missing "spec" in options array'
            ),
            array(
                'regex' => '/foo',
                'spec'  => '/foo'
            )
        );
    }
}

