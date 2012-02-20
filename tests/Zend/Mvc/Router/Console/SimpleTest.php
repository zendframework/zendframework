<?php
namespace ZendTest\Mvc\Router\Console;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Http\Request,
    Zend\Stdlib\Request as BaseRequest,
    Zend\Console\Request as ConsoleRequest,
    Zend\Mvc\Router\Console\Simple,
    ZendTest\Mvc\Router\FactoryTester;

class SimpleTestTest extends TestCase
{
    public static function routeProvider()
    {
        return array(
            // -- mandatory long flags
            'mandatory-long-flag-no-match' => array(
                new Simple('--foo --bar'),
                array('a','b','--baz'),
                null
            ),
            'mandatory-long-flag-no-partial-match' => array(
                new Simple('--foo --bar'),
                array('--foo','--baz'),
                null
            ),
            'mandatory-long-flag-match' => array(
                new Simple('--foo --bar'),
                array('--foo','--bar'),
                array('foo' => true, 'bar' => true)
            ),
            'mandatory-long-flag-mixed-order-match' => array(
                new Simple('--foo --bar'),
                array('--bar','--foo'),
                array('foo' => true, 'bar' => true)
            ),
            'mandatory-long-flag-whitespace-in-definition' => array(
                new Simple('      --foo   --bar '),
                array('--bar','--foo'),
                array(
                    'foo' => true,
                    'bar' => true,
                    'baz' => null,
                )
            ),

            // -- optional long flags
            'optional-long-flag-non-existent' => array(
                new Simple('--foo [--bar]'),
                array('--foo'),
                array(
                    'foo' => true,
                    'bar' => null,
                    'baz' => null,
                )
            ),
            'optional-long-flag-partial-match' => array(
                new Simple('--foo [--bar]'),
                array('--foo', '--baz'),
                array(
                    'foo' => true,
                    'bar' => null,
                )
            ),
            'optional-long-flag-match' => array(
                new Simple('--foo [--bar]'),
                array('--foo','--bar'),
                array(
                    'foo' => true,
                    'bar' => true
                )
            ),
            'optional-long-flag-mixed-order-match' => array(
                new Simple('--foo --bar'),
                array('--bar','--foo'),
                array('foo' => true, 'bar' => true)
            ),
            'optional-long-flag-whitespace-in-definition' => array(
                new Simple('      --foo   [--bar] '),
                array('--bar','--foo'),
                array(
                    'foo' => true,
                    'bar' => true,
                    'baz' => null,
                )
            ),
            'optional-long-flag-whitespace-in-definition2' => array(
                new Simple('      --foo     [--bar      ] '),
                array('--bar','--foo'),
                array(
                    'foo' => true,
                    'bar' => true,
                    'baz' => null,
                )
            ),
            'optional-long-flag-whitespace-in-definition3' => array(
                new Simple('      --foo   [   --bar     ] '),
                array('--bar','--foo'),
                array(
                    'foo' => true,
                    'bar' => true,
                    'baz' => null,
                )
            ),


            // -- value flags
            'required-value-flag-syntax-1' => array(
                new Simple('--foo=s'),
                array('--foo','bar'),
                array(
                    'foo' => 'bar',
                    'bar' => null
                )
            ),
            'required-value-flag-syntax-2' => array(
                new Simple('--foo='),
                array('--foo','bar'),
                array(
                    'foo' => 'bar',
                    'bar' => null
                )
            ),
            'required-value-flag-syntax-3' => array(
                new Simple('--foo=anystring'),
                array('--foo','bar'),
                array(
                    'foo' => 'bar',
                    'bar' => null
                )
            ),

            // -- literal params

            // -- value params
            'required-value-param-syntax-1' => array(
                new Simple('FOO'),
                array('bar'),
                array(
                    'foo' => 'bar',
                    'bar' => null
                )
            ),
            'required-value-param-syntax-2' => array(
                new Simple('<foo>'),
                array('bar'),
                array(
                    'foo' => 'bar',
                    'bar' => null
                )
            ),
            'required-value-param-mixed-with-anonymous' => array(
                new Simple('a b <foo> c'),
                array('a','b','bar','baz'),
                array(
                    'a' => true,
                    'b' => true,
                    'foo' => 'bar',
                    'bar' => null,
                    'c' => 'baz'
                ),
            ),

            // -- combinations
            'combined-1' => array(
                new Simple('--foo --bar'),
                array('a','b', 'c', '--foo', '--bar'),
                array(
                    0     => 'a',
                    1     => 'b',
                    2     => 'c',
                    'foo' => true,
                    'bar' => true,
                    'baz' => null
                )
            ),

        );
    }


    /**
     * @dataProvider routeProvider
     * @param        Simple $route
     * @param        string  $path
     * @param        integer $offset
     * @param        array   $params
     */
    public function testMatching(Simple $route, array $params = null)
    {
        array_unshift($params,'scriptname.php');
        $request = new ConsoleRequest($params);
        $match = $route->match($request);

        if ($params === null) {
            $this->assertNull($match);
        } else {
            $this->assertInstanceOf('Zend\Mvc\Router\Console\RouteMatch', $match);

            foreach ($params as $key => $value) {
                $this->assertEquals($value, $match->getParam($key));
            }
        }
    }

    /**
     * @dataProvider routeProvider
     * @param        Segment $route
     * @param        string  $path
     * @param        integer $offset
     * @param        array   $params
     */
    public function testAssembling(Segment $route, $path, $offset, array $params = null)
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

    /**
     * @dataProvider parseExceptionsProvider
     * @param        string $route
     * @param        string $exceptionName
     * @param        string $exceptionMessage
     */
    public function testParseExceptions($route, $exceptionName, $exceptionMessage)
    {
        $this->setExpectedException($exceptionName, $exceptionMessage);
        new Simple($route);
    }

    public function testAssemblingWithMissingParameterInRoot()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\InvalidArgumentException', 'Missing parameter "foo"');
        $route = new Simple('/:foo');
        $route->assemble();
    }

    public function testBuildTranslatedLiteral()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\RuntimeException', 'Translated literals are not implemented yet');
        $route = new Simple('/{foo}');
    }

    public function testBuildTranslatedParameter()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\RuntimeException', 'Translated parameters are not implemented yet');
        $route = new Simple('/:{foo}');
    }

    public function testNoMatchWithoutUriMethod()
    {
        $route   = new Simple('/foo');
        $request = new BaseRequest();

        $this->assertNull($route->match($request));
    }

    public function testAssemblingWithExistingChild()
    {
        $route = new Simple('/[:foo]', array(), array('foo' => 'bar'));
        $path = $route->assemble(array(), array('has_child' => true));

        $this->assertEquals('/bar', $path);
    }

    public function testFactory()
    {
        $tester = new FactoryTester($this);
        $tester->testFactory(
            'Zend\Mvc\Router\Http\Segment',
            array(
                'route' => 'Missing "route" in options array'
            ),
            array(
                'route'       => '/:foo[/:bar{-}]',
                'constraints' => array('foo' => 'bar')
            )
        );
    }
}
