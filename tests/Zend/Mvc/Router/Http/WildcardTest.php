<?php
namespace ZendTest\Mvc\Router\Http;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Http\Request as Request,
    Zend\Stdlib\Request as BaseRequest,
    Zend\Mvc\Router\Http\Wildcard,
    ZendTest\Mvc\Router\FactoryTester;

class WildcardTest extends TestCase
{
    public static function routeProvider()
    {
        return array(
            'simple-match' => array(
                new Wildcard(),
                '/foo/bar/baz/bat',
                null,
                array('foo' => 'bar', 'baz' => 'bat')
            ),
            'empty-match' => array(
                new Wildcard(),
                '',
                null,
                array()
            ),
            'no-match-without-leading-delimiter' => array(
                new Wildcard(),
                '/foo/foo/bar/baz/bat',
                5,
                null
            ),
            'no-match-with-trailing-slash' => array(
                new Wildcard(),
                '/foo/bar/baz/bat/',
                null,
                null
            ),
            'match-overrides-default' => array(
                new Wildcard('/', '/', array('foo' => 'baz')),
                '/foo/bat',
                null,
                array('foo' => 'bat')
            ),
            'offset-skips-beginning' => array(
                new Wildcard(),
                '/bat/foo/bar',
                4,
                array('foo' => 'bar')
            ),
            'non-standard-key-value-delimiter' => array(
                new Wildcard('-'),
                '/foo-bar/baz-bat',
                null,
                array('foo' => 'bar', 'baz' => 'bat')
            ),
            'non-standard-parameter-delimiter' => array(
                new Wildcard('/', '-'),
                '/foo/-foo/bar-baz/bat',
                5,
                array('foo' => 'bar', 'baz' => 'bat')
            ),
            'empty-values-with-non-standard-key-value-delimiter-are-omitted' => array(
                new Wildcard('-'),
                '/foo',
                null,
                array(),
                true
            ),
            'url-encoded-parameters-are-decoded' => array(
                new Wildcard(),
                '/foo/foo+bar',
                null,
                array('foo' => 'foo bar')
            ),
        );
    }

    /**
     * @dataProvider routeProvider
     * @param        Wildcard $route
     * @param        string   $path
     * @param        integer  $offset
     * @param        array    $params
     */
    public function testMatching(Wildcard $route, $path, $offset, array $params = null)
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
     * @param        Wildcard $route
     * @param        string   $path
     * @param        integer  $offset
     * @param        array    $params
     * @param        boolean  $skipAssembling
     */
    public function testAssembling(Wildcard $route, $path, $offset, array $params = null, $skipAssembling = false)
    {
        if ($params === null || $skipAssembling) {
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
        $route   = new Wildcard();
        $request = new BaseRequest();
        
        $this->assertNull($route->match($request));
    }
    
    public function testGetAssembledParams()
    {
        $route = new Wildcard();
        $route->assemble(array('foo' => 'bar'));
        
        $this->assertEquals(array('foo'), $route->getAssembledParams());
    }
    
    public function testFactory()
    {
        $tester = new FactoryTester($this);
        $tester->testFactory(
            'Zend\Mvc\Router\Http\Wildcard',
            array(),
            array()
        );
    }
}

