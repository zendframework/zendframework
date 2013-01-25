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
use Zend\Mvc\Router\Http\Wildcard;
use ZendTest\Mvc\Router\FactoryTester;

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
                '/foo/foo%20bar',
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

    public function testRawDecode()
    {
        // verify all characters which don't absolutely require encoding pass through match unchanged
        // this includes every character other than #, %, / and ?
        $raw = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789`-=[]\\;\',.~!@$^&*()_+{}|:"<>';
        $request = new Request();
        $request->setUri('http://example.com/foo/' . $raw);
        $route   = new Wildcard();
        $match   = $route->match($request);

        $this->assertSame($raw, $match->getParam('foo'));
    }

    public function testEncodedDecode()
    {
        // every character
        $in  = '%61%62%63%64%65%66%67%68%69%6a%6b%6c%6d%6e%6f%70%71%72%73%74%75%76%77%78%79%7a%41%42%43%44%45%46%47%48%49%4a%4b%4c%4d%4e%4f%50%51%52%53%54%55%56%57%58%59%5a%30%31%32%33%34%35%36%37%38%39%60%2d%3d%5b%5d%5c%3b%27%2c%2e%2f%7e%21%40%23%24%25%5e%26%2a%28%29%5f%2b%7b%7d%7c%3a%22%3c%3e%3f';
        $out = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789`-=[]\\;\',./~!@#$%^&*()_+{}|:"<>?';
        $request = new Request();
        $request->setUri('http://example.com/foo/' . $in);
        $route   = new Wildcard();
        $match   = $route->match($request);

        $this->assertSame($out, $match->getParam('foo'));
    }
}
