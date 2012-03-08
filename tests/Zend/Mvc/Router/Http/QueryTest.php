<?php
namespace ZendTest\Mvc\Router\Http;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Http\Request as Request,
    Zend\Stdlib\Request as BaseRequest,
    Zend\Mvc\Router\Http\Query,
    ZendTest\Mvc\Router\FactoryTester;

class QueryTest extends TestCase
{
    public static function routeProvider()
    {
        return array(
            'simple-match' => array(
                new Query(),
                '?foo=bar&baz=bat',
                null,
                array('foo' => 'bar', 'baz' => 'bat')
            ),
            'empty-match' => array(
                new Query(),
                '',
                null,
                array()
            ),
            'url-encoded-parameters-are-decoded' => array(
                new Query(),
                '?foo=foo%20bar',
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
    public function testMatching(Query $route, $path, $offset, array $params = null)
    {
        $request = new Request();
        $request->setUri('http://example.com' . $path);
        $match = $route->match($request, $offset);
        
        $this->assertNull($match);
    }
    
    /**
     * @dataProvider routeProvider
     * @param        Wildcard $route
     * @param        string   $path
     * @param        integer  $offset
     * @param        array    $params
     * @param        boolean  $skipAssembling
     */
    public function testAssembling(Query $route, $path, $offset, array $params = null, $skipAssembling = false)
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
        $route   = new Query();
        $request = new BaseRequest();
        
        $this->assertNull($route->match($request));
    }
    
    public function testGetAssembledParams()
    {
        $route = new Query();
        $route->assemble(array('foo' => 'bar'));
        
        
        $this->assertEquals(array(), $route->getAssembledParams());
    }
    
    public function testFactory()
    {
        $tester = new FactoryTester($this);
        $tester->testFactory(
            'Zend\Mvc\Router\Http\Query',
            array(),
            array()
        );
    }
}

