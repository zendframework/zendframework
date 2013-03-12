<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZendTest\Mvc\Router\Http;

use PHPUnit_Framework_TestCase as TestCase;
use ArrayIterator;
use Zend\Http\Request as Request;
use Zend\Http\PhpEnvironment\Request as PhpRequest;
use Zend\Stdlib\Request as BaseRequest;
use Zend\Uri\Http as HttpUri;
use Zend\Mvc\Router\Http\TreeRouteStack;
use Zend\Mvc\Router\Http\Hostname;
use ZendTest\Mvc\Router\FactoryTester;

class TreeRouteStackTest extends TestCase
{
    public function testAddRouteRequiresHttpSpecificRoute()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\InvalidArgumentException', 'Route definition must be an array or Traversable object');
        $stack = new TreeRouteStack();
        $stack->addRoute('foo', new \ZendTest\Mvc\Router\TestAsset\DummyRoute());
    }

    public function testAddRouteViaStringRequiresHttpSpecificRoute()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\RuntimeException', 'Given route does not implement HTTP route interface');
        $stack = new TreeRouteStack();
        $stack->addRoute('foo', array(
            'type' => '\ZendTest\Mvc\Router\TestAsset\DummyRoute'
        ));
    }

    public function testAddRouteAcceptsTraversable()
    {
        $stack = new TreeRouteStack();
        $stack->addRoute('foo', new ArrayIterator(array(
            'type' => '\ZendTest\Mvc\Router\Http\TestAsset\DummyRoute'
        )));
    }

    public function testNoMatchWithoutUriMethod()
    {
        $stack  = new TreeRouteStack();
        $request = new BaseRequest();

        $this->assertNull($stack->match($request));
    }

    public function testSetBaseUrlFromFirstMatch()
    {
        $stack = new TreeRouteStack();

        $request = new PhpRequest();
        $request->setBaseUrl('/foo');
        $stack->match($request);
        $this->assertEquals('/foo', $stack->getBaseUrl());

        $request = new PhpRequest();
        $request->setBaseUrl('/bar');
        $stack->match($request);
        $this->assertEquals('/foo', $stack->getBaseUrl());
    }

    public function testBaseUrlLengthIsPassedAsOffset()
    {
        $stack = new TreeRouteStack();
        $stack->setBaseUrl('/foo');
        $stack->addRoute('foo', array(
            'type' => '\ZendTest\Mvc\Router\Http\TestAsset\DummyRoute'
        ));

        $this->assertEquals(4, $stack->match(new Request())->getParam('offset'));
    }

    public function testNoOffsetIsPassedWithoutBaseUrl()
    {
        $stack = new TreeRouteStack();
        $stack->addRoute('foo', array(
            'type' => '\ZendTest\Mvc\Router\Http\TestAsset\DummyRoute'
        ));

        $this->assertEquals(null, $stack->match(new Request())->getParam('offset'));
    }

    public function testAssemble()
    {
        $stack = new TreeRouteStack();
        $stack->addRoute('foo', new TestAsset\DummyRoute());
        $this->assertEquals('', $stack->assemble(array(), array('name' => 'foo')));
    }

    public function testAssembleCanonicalUriWithoutRequestUri()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\RuntimeException', 'Request URI has not been set');
        $stack = new TreeRouteStack();

        $stack->addRoute('foo', new TestAsset\DummyRoute());
        $this->assertEquals('http://example.com:8080/', $stack->assemble(array(), array('name' => 'foo', 'force_canonical' => true)));
    }

    public function testAssembleCanonicalUriWithRequestUri()
    {
        $uri   = new HttpUri('http://example.com:8080/');
        $stack = new TreeRouteStack();
        $stack->setRequestUri($uri);

        $stack->addRoute('foo', new TestAsset\DummyRoute());
        $this->assertEquals('http://example.com:8080/', $stack->assemble(array(), array('name' => 'foo', 'force_canonical' => true)));
    }

    public function testAssembleCanonicalUriWithGivenUri()
    {
        $uri   = new HttpUri('http://example.com:8080/');
        $stack = new TreeRouteStack();

        $stack->addRoute('foo', new TestAsset\DummyRoute());
        $this->assertEquals('http://example.com:8080/', $stack->assemble(array(), array('name' => 'foo', 'uri' => $uri, 'force_canonical' => true)));
    }

    public function testAssembleCanonicalUriWithHostnameRoute()
    {
        $stack = new TreeRouteStack();
        $stack->addRoute('foo', new Hostname('example.com'));
        $uri   = new HttpUri();
        $uri->setScheme('http');

        $this->assertEquals('http://example.com/', $stack->assemble(array(), array('name' => 'foo', 'uri' => $uri)));
    }

    public function testAssembleCanonicalUriWithHostnameRouteWithoutScheme()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\RuntimeException', 'Request URI has not been set');
        $stack = new TreeRouteStack();
        $stack->addRoute('foo', new Hostname('example.com'));
        $uri   = new HttpUri();

        $this->assertEquals('http://example.com/', $stack->assemble(array(), array('name' => 'foo', 'uri' => $uri)));
    }

    public function testAssembleCanonicalUriWithHostnameRouteAndRequestUriWithoutScheme()
    {
        $uri   = new HttpUri();
        $uri->setScheme('http');
        $stack = new TreeRouteStack();
        $stack->setRequestUri($uri);
        $stack->addRoute('foo', new Hostname('example.com'));

        $this->assertEquals('http://example.com/', $stack->assemble(array(), array('name' => 'foo')));
    }

    public function testAssembleCanonicalUriWithHostnameRouteAndQueryRoute()
    {
        $uri   = new HttpUri();
        $uri->setScheme('http');
        $stack = new TreeRouteStack();
        $stack->setRequestUri($uri);
        $stack->addRoute(
            'foo',
            array(
                'type' => 'Hostname',
                'options' => array(
                    'route' => 'example.com',
                ),
                'child_routes' => array(
                    'index' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/',
                        ),
                        'child_routes' => array(
                            'query' => array(
                                'type' => 'Query',
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->assertEquals('http://example.com/?bar=baz', $stack->assemble(array('bar' => 'baz'), array('name' => 'foo/index/query')));
    }

    public function testAssembleWithQueryRoute()
    {
        $uri   = new HttpUri();
        $uri->setScheme('http');
        $stack = new TreeRouteStack();
        $stack->setRequestUri($uri);
        $stack->addRoute(
            'index',
            array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/',
                ),
                'child_routes' => array(
                    'query' => array(
                        'type' => 'Query',
                    ),
                ),
            )
        );

        $this->assertEquals('/?bar=baz', $stack->assemble(array('bar' => 'baz'), array('name' => 'index/query')));
    }

    public function testAssembleWithQueryParams()
    {
        $stack = new TreeRouteStack();
        $stack->addRoute(
            'index',
            array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/',
                ),
            )
        );

        $this->assertEquals('/?foo=bar', $stack->assemble(array(), array('name' => 'index', 'query' => array('foo' => 'bar'))));
    }

    public function testAssembleWithScheme()
    {
        $uri   = new HttpUri();
        $uri->setScheme('http');
        $uri->setHost('example.com');
        $stack = new TreeRouteStack();
        $stack->setRequestUri($uri);
        $stack->addRoute(
            'secure',
            array(
                'type' => 'Scheme',
                'options' => array(
                    'scheme' => 'https'
                ),
                'child_routes' => array(
                    'index' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/',
                        ),
                    ),
                ),
            )
        );
        $this->assertEquals('https://example.com/', $stack->assemble(array(), array('name' => 'secure/index')));
    }

    public function testAssembleWithFragment()
    {
        $stack = new TreeRouteStack();
        $stack->addRoute(
            'index',
            array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/',
                ),
            )
        );

        $this->assertEquals('/#foobar', $stack->assemble(array(), array('name' => 'index', 'fragment' => 'foobar')));
    }

    public function testAssembleWithoutNameOption()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\InvalidArgumentException', 'Missing "name" option');
        $stack = new TreeRouteStack();
        $stack->assemble();
    }

    public function testAssembleNonExistentRoute()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\RuntimeException', 'Route with name "foo" not found');
        $stack = new TreeRouteStack();
        $stack->assemble(array(), array('name' => 'foo'));
    }

    public function testDefaultParamIsAddedToMatch()
    {
        $stack = new TreeRouteStack();
        $stack->setBaseUrl('/foo');
        $stack->addRoute('foo', new TestAsset\DummyRoute());
        $stack->setDefaultParam('foo', 'bar');

        $this->assertEquals('bar', $stack->match(new Request())->getParam('foo'));
    }

    public function testDefaultParamDoesNotOverrideParam()
    {
        $stack = new TreeRouteStack();
        $stack->setBaseUrl('/foo');
        $stack->addRoute('foo', new TestAsset\DummyRouteWithParam());
        $stack->setDefaultParam('foo', 'baz');

        $this->assertEquals('bar', $stack->match(new Request())->getParam('foo'));
    }

    public function testDefaultParamIsUsedForAssembling()
    {
        $stack = new TreeRouteStack();
        $stack->addRoute('foo', new TestAsset\DummyRouteWithParam());
        $stack->setDefaultParam('foo', 'bar');

        $this->assertEquals('bar', $stack->assemble(array(), array('name' => 'foo')));
    }

    public function testDefaultParamDoesNotOverrideParamForAssembling()
    {
        $stack = new TreeRouteStack();
        $stack->addRoute('foo', new TestAsset\DummyRouteWithParam());
        $stack->setDefaultParam('foo', 'baz');

        $this->assertEquals('bar', $stack->assemble(array('foo' => 'bar'), array('name' => 'foo')));
    }

    public function testSetBaseUrl()
    {
        $stack = new TreeRouteStack();

        $this->assertEquals($stack, $stack->setBaseUrl('/foo/'));
        $this->assertEquals('/foo', $stack->getBaseUrl());
    }

    public function testSetRequestUri()
    {
        $uri   = new HttpUri();
        $stack = new TreeRouteStack();

        $this->assertEquals($stack, $stack->setRequestUri($uri));
        $this->assertEquals($uri, $stack->getRequestUri());
    }

    public function testPriorityIsPassedToPartRoute()
    {
        $stack = new TreeRouteStack();
        $stack->addRoutes(array(
            'foo' => array(
                'type' => 'Literal',
                'priority' => 1000,
                'options' => array(
                    'route' => '/foo',
                    'defaults' => array(
                        'controller' => 'foo',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'bar' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/bar',
                            'defaults' => array(
                                'controller' => 'foo',
                                'action'     => 'bar',
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $reflectedClass    = new \ReflectionClass($stack);
        $reflectedProperty = $reflectedClass->getProperty('routes');
        $reflectedProperty->setAccessible(true);
        $routes = $reflectedProperty->getValue($stack);

        $this->assertEquals(1000, $routes->get('foo')->priority);
    }

    public function testFactory()
    {
        $tester = new FactoryTester($this);
        $tester->testFactory(
            'Zend\Mvc\Router\Http\TreeRouteStack',
            array(),
            array()
        );
    }
}
