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
use Zend\Mvc\Router\Http\Method as HttpMethod;
use ZendTest\Mvc\Router\FactoryTester;

class MethodTest extends TestCase
{
    public static function routeProvider()
    {
        return array(
            'simple-match' => array(
                new HttpMethod('get'),
                'get'
            ),
            'match-comma-separated-verbs' => array(
                new HttpMethod('get,post'),
                'get'
            ),
            'match-comma-separated-verbs-ws' => array(
                new HttpMethod('get ,   post , put'),
                'post'
            ),
            'match-ignores-case' => array(
                new HttpMethod('Get'),
                'get'
            )
        );
    }

    /**
     * @dataProvider routeProvider
     * @param    HttpMethod $route
     * @param    $verb
     * @internal param string $path
     * @internal param int $offset
     * @internal param bool $shouldMatch
     */
    public function testMatching(HttpMethod $route, $verb)
    {
        $request = new Request();
        $request->setUri('http://example.com');
        $request->setMethod($verb);

        $match = $route->match($request);
        $this->assertInstanceOf('Zend\Mvc\Router\Http\RouteMatch', $match);
    }

    public function testNoMatchWithoutVerb()
    {
        $route   = new HttpMethod('get');
        $request = new BaseRequest();

        $this->assertNull($route->match($request));
    }

    public function testFactory()
    {
        $tester = new FactoryTester($this);
        $tester->testFactory(
            'Zend\Mvc\Router\Http\Method',
            array(
                'verb' => 'Missing "verb" in options array'
            ),
            array(
                'verb' => 'get'
            )
        );
    }
}
