<?php
namespace ZendTest\Mvc\Router\Console;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Http\Request;
use Zend\Stdlib\Request as BaseRequest;
use Zend\Console\Request as ConsoleRequest;
use Zend\Mvc\Router\Console\Simple;
use ZendTest\Mvc\Router\FactoryTester;

class SimpleTestTest extends TestCase
{
    public static function routeProvider()
    {
        return array(
            // -- mandatory long flags
            'mandatory-long-flag-no-match' => array(
                '--foo --bar',
                array('a','b','--baz'),
                null
            ),
            'mandatory-long-flag-no-partial-match' => array(
                '--foo --bar',
                array('--foo','--baz'),
                null
            ),
            'mandatory-long-flag-match' => array(
                '--foo --bar',
                array('--foo','--bar'),
                array('foo' => true, 'bar' => true)
            ),
            'mandatory-long-flag-mixed-order-match' => array(
                '--foo --bar',
                array('--bar','--foo'),
                array('foo' => true, 'bar' => true)
            ),
            'mandatory-long-flag-whitespace-in-definition' => array(
                '      --foo   --bar ',
                array('--bar','--foo'),
                array(
                    'foo' => true,
                    'bar' => true,
                    'baz' => null,
                )
            ),
            'mandatory-long-flag-alternative1' => array(
                ' ( --foo | --bar )',
                array('--foo'),
                array(
                    'foo' => true,
                    'bar' => false,
                    'baz' => null,
                )
            ),
            'mandatory-long-flag-alternative2' => array(
                ' ( --foo | --bar )',
                array('--bar'),
                array(
                    'foo' => false,
                    'bar' => true,
                    'baz' => null,
                )
            ),
            'mandatory-long-flag-alternative3' => array(
                ' ( --foo | --bar )',
                array('--baz'),
                null
            ),

            // -- mandatory short flags
            'mandatory-short-flag-no-match' => array(
                '-f -b',
                array('a','b','-f'),
                null
            ),
            'mandatory-short-flag-no-partial-match' => array(
                '-f -b',
                array('-f','-z'),
                null
            ),
            'mandatory-short-flag-match' => array(
                '-f -b',
                array('-f','-b'),
                array('f' => true, 'b' => true)
            ),
            'mandatory-short-flag-mixed-order-match' => array(
                '-f -b',
                array('-b','-f'),
                array('f' => true, 'b' => true)
            ),
            'mandatory-short-flag-whitespace-in-definition' => array(
                '      -f   -b ',
                array('-b','-f'),
                array(
                    'f' => true,
                    'b' => true,
                    'baz' => null,
                )
            ),
            'mandatory-short-flag-alternative1' => array(
                ' ( -f | -b )',
                array('-f'),
                array(
                    'f' => true,
                    'b' => false,
                    'baz' => null,
                )
            ),
            'mandatory-short-flag-alternative2' => array(
                ' ( -f | -b )',
                array('-b'),
                array(
                    'f' => false,
                    'b' => true,
                    'baz' => null,
                )
            ),
            'mandatory-short-flag-alternative3' => array(
                ' ( -f | -b )',
                array('--baz'),
                null
            ),

            // -- optional long flags
            'optional-long-flag-non-existent' => array(
                '--foo [--bar]',
                array('--foo'),
                array(
                    'foo' => true,
                    'bar' => null,
                    'baz' => null,
                )
            ),
            'optional-long-flag-partial-match' => array(
                '--foo [--bar]',
                array('--foo', '--baz'),
                array(
                    'foo' => true,
                    'bar' => null,
                )
            ),
            'optional-long-flag-match' => array(
                '--foo [--bar]',
                array('--foo','--bar'),
                array(
                    'foo' => true,
                    'bar' => true
                )
            ),
            'optional-long-flag-mixed-order-match' => array(
                '--foo --bar',
                array('--bar','--foo'),
                array('foo' => true, 'bar' => true)
            ),
            'optional-long-flag-whitespace-in-definition' => array(
                '      --foo   [--bar] ',
                array('--bar','--foo'),
                array(
                    'foo' => true,
                    'bar' => true,
                    'baz' => null,
                )
            ),
            'optional-long-flag-whitespace-in-definition2' => array(
                '      --foo     [--bar      ] ',
                array('--bar','--foo'),
                array(
                    'foo' => true,
                    'bar' => true,
                    'baz' => null,
                )
            ),
            'optional-long-flag-whitespace-in-definition3' => array(
                '      --foo   [   --bar     ] ',
                array('--bar','--foo'),
                array(
                    'foo' => true,
                    'bar' => true,
                    'baz' => null,
                )
            ),


            // -- value flags
            'mandatory-value-flag-syntax-1' => array(
                '--foo=s',
                array('--foo','bar'),
                array(
                    'foo' => 'bar',
                    'bar' => null
                )
            ),
            'mandatory-value-flag-syntax-2' => array(
                '--foo=',
                array('--foo','bar'),
                array(
                    'foo' => 'bar',
                    'bar' => null
                )
            ),
            'mandatory-value-flag-syntax-3' => array(
                '--foo=anystring',
                array('--foo','bar'),
                array(
                    'foo' => 'bar',
                    'bar' => null
                )
            ),

            // -- edge cases for value flags values
            'mandatory-value-flag-equals-complex-1' => array(
                '--foo=',
                array('--foo=SomeComplexValue=='),
                array('foo' => 'SomeComplexValue==')
            ),
            'mandatory-value-flag-equals-complex-2' => array(
                '--foo=',
                array('--foo=...,</\/\\//""\'\'\'"\"'),
                array('foo' => '...,</\/\\//""\'\'\'"\"')
            ),
            'mandatory-value-flag-equals-complex-3' => array(
                '--foo=',
                array('--foo====--'),
                array('foo' => '===--')
            ),
            'mandatory-value-flag-space-complex-1' => array(
                '--foo=',
                array('--foo','SomeComplexValue=='),
                array('foo' => 'SomeComplexValue==')
            ),
            'mandatory-value-flag-space-complex-2' => array(
                '--foo=',
                array('--foo','...,</\/\\//""\'\'\'"\"'),
                array('foo' => '...,</\/\\//""\'\'\'"\"')
            ),
            'mandatory-value-flag-space-complex-3' => array(
                '--foo=',
                array('--foo','===--'),
                array('foo' => '===--')
            ),

            // -- required literal params
            'mandatory-literal-match-1' => array(
                'foo',
                array('foo'),
                array('foo' => true)
            ),
            'mandatory-literal-match-2' => array(
                'foo bar baz',
                array('foo','bar','baz'),
                array('foo' => true,'bar'=>true,'baz'=>true,'bazinga'=>null)
            ),
            'mandatory-literal-mismatch' => array(
                'foo',
                array('fooo'),
                null
            ),
            'mandatory-literal-order' => array(
                'foo bar',
                array('bar','foo'),
                null
            ),
            'mandatory-literal-partial-mismatch' => array(
                'foo bar baz',
                array('foo','bar'),
                null
            ),
            'mandatory-literal-alternative-match-1' => array(
                'foo ( bar | baz )',
                array('foo','bar'),
                array('foo' => true, 'bar' => true, 'baz' => false)
            ),
            'mandatory-literal-alternative-match-2' => array(
                'foo (bar|baz)',
                array('foo','bar'),
                array('foo' => true, 'bar' => true, 'baz' => false)
            ),
            'mandatory-literal-alternative-match-3' => array(
                'foo ( bar    |   baz )',
                array('foo','baz'),
                array('foo' => true, 'bar' => false, 'baz' => true)
            ),
            'mandatory-literal-alternative-mismatch' => array(
                'foo ( bar |   baz )',
                array('foo','bazinga'),
                null
            ),
            'mandatory-literal-alternative-mismatch' => array(
                'foo ( bar | baz )',
                array('foo'),
                null
            ),
            'mandatory-literal-namedAlternative-match-1' => array(
                'foo ( bar | baz ):altGroup',
                array('foo','bar'),
                array('foo' => true, 'altGroup'=>'bar', 'bar' => true, 'baz' => false)
            ),
            'mandatory-literal-namedAlternative-match-2' => array(
                'foo ( bar |   baz   ):altGroup9',
                array('foo','baz'),
                array('foo' => true, 'altGroup9'=>'baz', 'bar' => false, 'baz' => true)
            ),
            'mandatory-literal-namedAlternative-mismatch' => array(
                'foo ( bar |   baz   ):altGroup9',
                array('foo','bazinga'),
                null
            ),

            // -- optional literal params
            'optional-literal-match' => array(
                'foo [bar] [baz]',
                array('foo','bar'),
                array('foo' => true, 'bar' => true, 'baz' => null)
            ),
            'optional-literal-mismatch' => array(
                'foo [bar] [baz]',
                array('baz','bar'),
                null
            ),
            'optional-literal-shuffled-mismatch' => array(
                'foo [bar] [baz]',
                array('foo','baz','bar'),
                null
            ),
            'optional-literal-alternative-match' => array(
                'foo [bar | baz]',
                array('foo','baz'),
                array('foo' => true, 'baz' => true, 'bar' => false)
            ),
            'optional-literal-alternative-mismatch' => array(
                'foo [bar | baz]',
                array('foo'),
                array('foo' => true, 'baz' => false, 'bar' => false)
            ),
            'optional-literal-namedAlternative-match-1' => array(
                'foo [bar | baz]:altGroup1',
                array('foo','baz'),
                array('foo' => true, 'altGroup1' => 'baz', 'baz' => true, 'bar' => false)
            ),
            'optional-literal-namedAlternative-match-2' => array(
                'foo [bar | baz | bazinga]:altGroup100',
                array('foo','bazinga'),
                array('foo' => true, 'altGroup100' => 'bazinga', 'bazinga' => true, 'baz' => false, 'bar' => false)
            ),
            'optional-literal-namedAlternative-match-3' => array(
                'foo [ bar ]:altGroup100',
                array('foo','bar'),
                array('foo' => true, 'altGroup100' => 'bar', 'bar' => true, 'baz' => null)
            ),
            'optional-literal-namedAlternative-mismatch' => array(
                'foo [ bar | baz ]:altGroup9',
                array('foo'),
                array('foo' => true, 'altGroup9'=> null, 'bar' => false, 'baz' => false)
            ),

            // -- value params
            'mandatory-value-param-syntax-1' => array(
                'FOO',
                array('bar'),
                array(
                    'foo' => 'bar',
                    'bar' => null
                )
            ),
            'mandatory-value-param-syntax-2' => array(
                '<foo>',
                array('bar'),
                array(
                    'foo' => 'bar',
                    'bar' => null
                )
            ),
            'mandatory-value-param-mixed-with-literal' => array(
                'a b <foo> c',
                array('a','b','bar','c'),
                array(
                    'a' => true,
                    'b' => true,
                    'foo' => 'bar',
                    'bar' => null,
                    'c' => true,
                ),
            ),

            // -- combinations
            'mandatory-long-short-alternative1' => array(
                ' ( --foo | -f )',
                array('--foo'),
                array(
                    'foo' => true,
                    'f'   => false,
                    'baz' => null,
                )
            ),
            'mandatory-long-short-alternative2' => array(
                ' ( --foo | -f )',
                array('-f'),
                array(
                    'foo' => false,
                    'f'   => true,
                    'baz' => null,
                )
            ),


            /*'combined-1' => array(
                '--foo --bar',
                array('a','b', 'c', '--foo', '--bar'),
                array(
                    0     => 'a',
                    1     => 'b',
                    2     => 'c',
                    'foo' => true,
                    'bar' => true,
                    'baz' => null
                )
            ),*/

        );
    }


    /**
     * @dataProvider routeProvider
     * @param        string         $routeDefinition
     * @param        array          $arguments
     * @param        array|null     $params
     */
    public function testMatching($routeDefinition, array $arguments = array(), array $params = null)
    {
        array_unshift($arguments,'scriptname.php');
        $request = new ConsoleRequest($arguments);
        $route = new Simple($routeDefinition);
        $match = $route->match($request);

        if ($params === null) {
            $this->assertNull($match, "The route must not match");
        } else {
            $this->assertInstanceOf('Zend\Mvc\Router\Console\RouteMatch', $match, "The route matches");

            foreach ($params as $key => $value) {
                $this->assertEquals(
                    $value,
                    $match->getParam($key),
                    $value === null ? "Param $key is not present" : "Param $key is present and is equal to $value"
                );
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
    public function __testAssembling(Segment $route, $path, $offset, array $params = null)
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
    public function __testParseExceptions($route, $exceptionName, $exceptionMessage)
    {
        $this->setExpectedException($exceptionName, $exceptionMessage);
        new Simple($route);
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
