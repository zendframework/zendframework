<?php
namespace ZendTest\Mvc\Router\Http;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Http\Request as Request,
    Zend\Uri\Http as HttpUri,
    Zend\Mvc\Router\Http\Hostname;

class HostnameTest extends TestCase
{
    public static function routeProvider()
    {
        return array(
            'simple-match' => array(
                new Hostname(':foo.example.com'),
                'bar.example.com',
                array('foo' => 'bar')
            ),
            'no-match-on-different-hostname' => array(
                new Hostname('foo.example.com'),
                'bar.example.com',
                null
            ),
            'constraints-prevent-match' => array(
                new Hostname(':foo.example.com', array('foo' => '\d+')),
                'bar.example.com',
                null
            ),
            'constraints-allow-match' => array(
                new Hostname(':foo.example.com', array('foo' => '\d+')),
                '123.example.com',
                array('foo' => '123')
            ),
        );
    }

    /**
     * @dataProvider routeProvider
     * @param        Hostname $route
     * @param        string   $hostname
     * @param        array    $params
     */
    public function testMatching(Hostname $route, $hostname, array $params = null)
    {
        $request = new Request();
        $request->setUri('http://' . $hostname . '/');
        $match = $route->match($request);
        
        if ($params === null) {
            $this->assertNull($match);
        } else {
            $this->assertInstanceOf('Zend\Mvc\Router\Http\RouteMatch', $match);
                       
            foreach ($params as $key => $value) {
                $this->assertEquals($value, $match->getParam($key));
            }
        }
    }
    
    /**
     * @dataProvider routeProvider
     * @param        Hostname $route
     * @param        string   $hostname
     * @param        array    $params
     */
    public function testAssembling(Hostname $route, $hostname, array $params = null)
    {
        if ($params === null) {
            // Data which will not match are not tested for assembling.
            return;
        }
        
        $uri  = new HttpUri();
        $path = $route->assemble($params, array('uri' => $uri));
        
        $this->assertEquals('', $path);
        $this->assertEquals($hostname, $uri->getHost());
    }
}

