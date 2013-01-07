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
use Zend\Http\Request as Request;
use Zend\Stdlib\Request as BaseRequest;
use Zend\Mvc\Router\Http\Literal;
use ZendTest\Mvc\Router\FactoryTester;

class LiteralTest extends TestCase
{
    public static function routeProvider()
    {
        return array(
            'simple-match' => array(
                new Literal('/foo'),
                '/foo',
                null,
                true
            ),
            'no-match-without-leading-slash' => array(
                new Literal('foo'),
                '/foo',
                null,
                false
            ),
            'no-match-with-trailing-slash' => array(
                new Literal('/foo'),
                '/foo/',
                null,
                false
            ),
            'offset-skips-beginning' => array(
                new Literal('foo'),
                '/foo',
                1,
                true
            ),
            'offset-enables-partial-matching' => array(
                new Literal('/foo'),
                '/foo/bar',
                0,
                true
            ),
        );
    }

    /**
     * @dataProvider routeProvider
     * @param        Literal $route
     * @param        string  $path
     * @param        integer $offset
     * @param        boolean $shouldMatch
     */
    public function testMatching(Literal $route, $path, $offset, $shouldMatch)
    {
        $request = new Request();
        $request->setUri('http://example.com' . $path);
        $match = $route->match($request, $offset);

        if (!$shouldMatch) {
            $this->assertNull($match);
        } else {
            $this->assertInstanceOf('Zend\Mvc\Router\Http\RouteMatch', $match);

            if ($offset === null) {
                $this->assertEquals(strlen($path), $match->getLength());
            }
        }
    }

    /**
     * @dataProvider routeProvider
     * @param        Literal $route
     * @param        string  $path
     * @param        integer $offset
     * @param        boolean $shouldMatch
     */
    public function testAssembling(Literal $route, $path, $offset, $shouldMatch)
    {
        if (!$shouldMatch) {
            // Data which will not match are not tested for assembling.
            return;
        }

        $result = $route->assemble();

        if ($offset !== null) {
            $this->assertEquals($offset, strpos($path, $result, $offset));
        } else {
            $this->assertEquals($path, $result);
        }
    }

    public function testNoMatchWithoutUriMethod()
    {
        $route   = new Literal('/foo');
        $request = new BaseRequest();

        $this->assertNull($route->match($request));
    }

    public function testGetAssembledParams()
    {
        $route = new Literal('/foo');
        $route->assemble(array('foo' => 'bar'));

        $this->assertEquals(array(), $route->getAssembledParams());
    }

    public function testFactory()
    {
        $tester = new FactoryTester($this);
        $tester->testFactory(
            'Zend\Mvc\Router\Http\Literal',
            array(
                'route' => 'Missing "route" in options array'
            ),
            array(
                'route' => '/foo'
            )
        );
    }

    /**
     * @group ZF2-436
     */
    public function testEmptyLiteral()
    {
        $request = new Request();
        $route = new Literal('');
        $this->assertNull($route->match($request, 0));
    }
}
