<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Console
 */

namespace ZendTest\Console\RouteMatcher;

use Zend\Console\RouteMatcher\DefaultRouteMatcher;

/**
 * @category   Zend
 * @package    Zend_Console
 * @subpackage UnitTests
 * @group      Zend_Console
 */
class DefaultRouteMatcherTest extends \PHPUnit_Framework_TestCase
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
            'mandatory-long-flag-match-with-zero-value' => array(
                '--foo=',
                array('--foo=0'),
                array('foo' => 0)
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
            'literal-optional-long-flag' => array(
                'foo [--bar]',
                array('foo', '--bar'),
                array(
                    'foo' => null,
                    'bar' => true,
                )
            ),
            'optional-long-flag-partial-mismatch' => array(
                '--foo [--bar]',
                array('--foo', '--baz'),
                null
            ),
            'optional-long-flag-match' => array(
                '--foo [--bar]',
                array('--foo','--bar'),
                array(
                    'foo' => true,
                    'bar' => true
                )
            ),
            'optional-long-value-flag-non-existent' => array(
                '--foo [--bar=]',
                array('--foo'),
                array(
                    'foo' => true,
                    'bar' => false
                )
            ),
            'optional-long-flag-match-with-zero-value' => array(
                '[--foo=]',
                array('--foo=0'),
                array('foo' => 0)
            ),
            'optional-long-value-flag' => array(
                '--foo [--bar=]',
                array('--foo', '--bar=4'),
                array(
                    'foo' => true,
                    'bar' => 4
                )
            ),
            'optional-long-value-flag-non-existent-mixed-case' => array(
                '--foo [--barBaz=]',
                array('--foo', '--barBaz=4'),
                array(
                    'foo'    => true,
                    'barBaz' => 4
                )
            ),
            'value-optional-long-value-flag' => array(
                '<foo> [--bar=]',
                array('value', '--bar=4'),
                array(
                    'foo' => 'value',
                    'bar' => 4
                )
            ),
            'literal-optional-long-value-flag' => array(
                'foo [--bar=]',
                array('foo', '--bar=4'),
                array(
                    'foo' => null,
                    'bar' => 4,
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
                array('foo' => null)
            ),
            'mandatory-literal-match-2' => array(
                'foo bar baz',
                array('foo','bar','baz'),
                array('foo' => null, 'bar' => null, 'baz' => null, 'bazinga' => null)
            ),
            'mandatory-literal-mismatch' => array(
                'foo',
                array('fooo'),
                null
            ),
            'mandatory-literal-colon-match' => array(
                'foo:bar',
                array('foo:bar'),
                array('foo:bar' => null)
            ),
            'mandatory-literal-colon-match-2' => array(
                'foo:bar baz',
                array('foo:bar', 'baz'),
                array('foo:bar' => null, 'baz' => null)
            ),
            'mandatory-literal-order' => array(
                'foo bar',
                array('bar','foo'),
                null
            ),
            'mandatory-literal-order-colon' => array(
                'foo bar baz:inga',
                array('bar','foo', 'baz:inga'),
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
                array('foo' => null, 'bar' => true, 'baz' => false)
            ),
            'mandatory-literal-alternative-match-2' => array(
                'foo (bar|baz)',
                array('foo','bar'),
                array('foo' => null, 'bar' => true, 'baz' => false)
            ),
            'mandatory-literal-alternative-match-3' => array(
                'foo ( bar    |   baz )',
                array('foo','baz'),
                array('foo' => null, 'bar' => false, 'baz' => true)
            ),
            'mandatory-literal-alternative-mismatch' => array(
                'foo ( bar |   baz )',
                array('foo','bazinga'),
                null
            ),
            'mandatory-literal-namedAlternative-match-1' => array(
                'foo ( bar | baz ):altGroup',
                array('foo','bar'),
                array('foo' => null, 'altGroup'=>'bar', 'bar' => true, 'baz' => false)
            ),
            'mandatory-literal-namedAlternative-match-2' => array(
                'foo ( bar |   baz   ):altGroup9',
                array('foo','baz'),
                array('foo' => null, 'altGroup9'=>'baz', 'bar' => false, 'baz' => true)
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
                array('foo' => null, 'bar' => true, 'baz' => null)
            ),
            'optional-literal-colon-match' => array(
                'foo [bar] [baz:inga]',
                array('foo','bar'),
                array('foo' => null, 'bar' => true, 'baz:inga' => null)
            ),
            'optional-literal-mismatch' => array(
                'foo [bar] [baz]',
                array('baz','bar'),
                null
            ),
            'optional-literal-colon-mismatch' => array(
                'foo [bar] [baz:inga]',
                array('baz:inga','bar'),
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
                array('foo' => null, 'baz' => true, 'bar' => false)
            ),
            'optional-literal-alternative-mismatch' => array(
                'foo [bar | baz]',
                array('foo'),
                array('foo' => null, 'baz' => false, 'bar' => false)
            ),
            'optional-literal-namedAlternative-match-1' => array(
                'foo [bar | baz]:altGroup1',
                array('foo','baz'),
                array('foo' => null, 'altGroup1' => 'baz', 'baz' => true, 'bar' => false)
            ),
            'optional-literal-namedAlternative-match-2' => array(
                'foo [bar | baz | bazinga]:altGroup100',
                array('foo','bazinga'),
                array('foo' => null, 'altGroup100' => 'bazinga', 'bazinga' => true, 'baz' => false, 'bar' => false)
            ),
            'optional-literal-namedAlternative-match-3' => array(
                'foo [ bar ]:altGroup100',
                array('foo','bar'),
                array('foo' => null, 'altGroup100' => 'bar', 'bar' => true, 'baz' => null)
            ),
            'optional-literal-namedAlternative-mismatch' => array(
                'foo [ bar | baz ]:altGroup9',
                array('foo'),
                array('foo' => null, 'altGroup9'=> null, 'bar' => false, 'baz' => false)
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
                    'a' => null,
                    'b' => null,
                    'foo' => 'bar',
                    'bar' => null,
                    'c' => null,
                ),
            ),
            'optional-value-param-1' => array(
                'a b [<c>]',
                array('a','b','bar'),
                array(
                    'a'   => null,
                    'b'   => null,
                    'c'   => 'bar',
                    'bar' => null,
                ),
            ),
            'optional-value-param-2' => array(
                'a b [<c>]',
                array('a','b'),
                array(
                    'a'   => null,
                    'b'   => null,
                    'c'   => null,
                    'bar' => null,
                ),
            ),
            'optional-value-param-3' => array(
                'a b [<c>]',
                array('a','b','--c'),
                null
            ),

            // -- combinations
            'mandatory-long-short-alternative-1' => array(
                ' ( --foo | -f )',
                array('--foo'),
                array(
                    'foo' => true,
                    'f'   => false,
                    'baz' => null,
                )
            ),
            'mandatory-long-short-alternative-2' => array(
                ' ( --foo | -f )',
                array('-f'),
                array(
                    'foo' => false,
                    'f'   => true,
                    'baz' => null,
                )
            ),
            'optional-long-short-alternative-1' => array(
                'a <b> [ --foo | -f ]',
                array('a','bar'),
                array(
                    'a'   => null,
                    'b'   => 'bar',
                    'foo' => false,
                    'f'   => false,
                    'baz' => null,
                )
            ),
            'optional-long-short-alternative-2' => array(
                'a <b> [ --foo | -f ]',
                array('a','bar', '-f'),
                array(
                    'a'   => null,
                    'b'   => 'bar',
                    'foo' => false,
                    'f'   => true,
                    'baz' => null,
                )
            ),
            'optional-long-short-alternative-3' => array(
                'a <b> [ --foo | -f ]',
                array('a','--foo', 'bar'),
                array(
                    'a'   => null,
                    'b'   => 'bar',
                    'foo' => true,
                    'f'   => false,
                    'baz' => null,
                )
            ),


            'mandatory-and-optional-value-params-with-flags-1' => array(
                'a b <c> [<d>] [--eee|-e] [--fff|-f]',
                array('a','b','foo','bar'),
                array(
                    'a'   => null,
                    'b'   => null,
                    'c'   => 'foo',
                    'd'   => 'bar',
                    'e'   => false,
                    'eee' => false,
                    'fff' => false,
                    'f'   => false,
                ),
            ),
            'mandatory-and-optional-value-params-with-flags-2' => array(
                'a b <c> [<d>] [--eee|-e] [--fff|-f]',
                array('a','b','--eee', 'foo','bar'),
                array(
                    'a'   => null,
                    'b'   => null,
                    'c'   => 'foo',
                    'd'   => 'bar',
                    'e'   => false,
                    'eee' => true,
                    'fff' => false,
                    'f'   => false,
                ),
            ),


            // -- overflows
            'too-many-arguments1' => array(
                'foo bar',
                array('foo','bar','baz'),
                null
            ),
            'too-many-arguments2' => array(
                'foo bar [baz]',
                array('foo','bar','baz','woo'),
                null,
            ),
            'too-many-arguments3' => array(
                'foo bar [--baz]',
                array('foo','bar','--baz','woo'),
                null,
            ),
            'too-many-arguments4' => array(
                'foo bar [--baz] woo',
                array('foo','bar','woo'),
                array(
                    'foo' => null,
                    'bar' => null,
                    'baz' => false,
                    'woo' => null
                )
            ),
            'too-many-arguments5' => array(
                '--foo --bar [--baz] woo',
                array('--bar','--foo','woo'),
                array(
                    'foo' => true,
                    'bar' => true,
                    'baz' => false,
                    'woo' => null
                )
            ),
            'too-many-arguments6' => array(
                '--foo --bar [--baz]',
                array('--bar','--foo','woo'),
                null
            ),

            // other (combination)
            'combined-1' => array(
                'literal <bar> [--foo=] --baz',
                array('literal', 'oneBar', '--foo=4', '--baz'),
                array(
                    'literal' => null,
                    'bar' => 'oneBar',
                    'foo' => 4,
                    'baz' => true
                )
            ),
            // group with group name diferent than options (short)
            'group-1' => array(
                'group [-t|--test]:testgroup',
                array('group', '-t'),
                array(
                    'group' => null,
                    'testgroup' => true,
                )
            ),
            // group with group name diferent than options (long)
            'group-2' => array(
                'group [-t|--test]:testgroup',
                array('group', '--test'),
                array(
                    'group' => null,
                    'testgroup' => true,
                )
            ),
            // group with same name as option (short)
            'group-3' => array(
                'group [-t|--test]:test',
                array('group', '-t'),
                array(
                    'group' => null,
                    'test' => true,
                )
            ),
            // group with same name as option (long)
            'group-4' => array(
                'group [-t|--test]:test',
                array('group', '--test'),
                array(
                    'group' => null,
                    'test' => true,
                )
            ),
            'group-5' => array(
                'group (-t | --test ):test',
                array('group', '--test'),
                array(
                    'group' => null,
                    'test' => true,
                ),
            ),
            'group-6' => array(
                'group (-t | --test ):test',
                array('group', '-t'),
                array(
                    'group' => null,
                    'test' => true,
                ),
            ),
            'group-7' => array(
                'group [-x|-y|-z]:test',
                array('group', '-y'),
                array(
                    'group' => null,
                    'test' => true,
                ),
            ),
            'group-8' => array(
                'group [--foo|--bar|--baz]:test',
                array('group', '--foo'),
                array(
                    'group' => null,
                    'test' => true,
                ),
            ),
            'group-9' => array(
                'group (--foo|--bar|--baz):test',
                array('group', '--foo'),
                array(
                    'group' => null,
                    'test' => true,
                ),
            ),

            /**
             * @bug ZF2-4315
             * @link https://github.com/zendframework/zf2/issues/4315
             */
            'literal-with-dashes' => array(
                'foo-bar-baz [--bar=]',
                array('foo-bar-baz',),
                array(
                    'foo-bar-baz' => null,
                    'foo'         => null,
                    'bar'         => null,
                    'baz'         => null,
                    'something'   => null,
                )
            ),

            'literal-optional-with-dashes' => array(
                '[foo-bar-baz] [--bar=]',
                array('foo-bar-baz'),
                array(
                    'foo-bar-baz' => true,
                    'foo'         => null,
                    'bar'         => null,
                    'baz'         => null,
                    'something'   => null,
                )
            ),
            'literal-optional-with-dashes2' => array(
                'foo [foo-bar-baz] [--bar=]',
                array('foo'),
                array(
                    'foo-bar-baz' => false,
                    'foo'         => null,
                    'bar'         => null,
                    'baz'         => null,
                    'something'   => null,
                )
            ),
            'literal-alternative-with-dashes' => array(
                '(foo-bar|foo-baz) [--bar=]',
                array('foo-bar',),
                array(
                    'foo-bar'     => true,
                    'foo-baz'     => false,
                    'bar'         => null,
                    'baz'         => null,
                    'something'   => null,
                )
            ),
            'literal-optional-alternative-with-dashes' => array(
                '[foo-bar|foo-baz] [--bar=]',
                array('foo-baz',),
                array(
                    'foo-bar'     => false,
                    'foo-baz'     => true,
                    'bar'         => null,
                    'baz'         => null,
                    'something'   => null,
                )
            ),
            'literal-optional-alternative-with-dashes2' => array(
                'foo [foo-bar|foo-baz] [--bar=]',
                array('foo',),
                array(
                    'foo'         => null,
                    'foo-bar'     => false,
                    'foo-baz'     => false,
                    'bar'         => null,
                    'baz'         => null,
                    'something'   => null,
                )
            ),
            'literal-flag-with-dashes' => array(
                'foo --bar-baz',
                array('foo','--bar-baz'),
                array(
                    'foo'         => null,
                    'bar-baz'     => true,
                    'bar'         => null,
                    'baz'         => null,
                    'something'   => null,
                )
            ),
            'literal-optional-flag-with-dashes' => array(
                'foo [--bar-baz]',
                array('foo','--bar-baz'),
                array(
                    'foo'         => null,
                    'bar-baz'     => true,
                    'bar'         => null,
                    'baz'         => null,
                    'something'   => null,
                )
            ),
            'literal-optional-flag-with-dashes2' => array(
                'foo [--bar-baz]',
                array('foo'),
                array(
                    'foo'         => null,
                    'bar-baz'     => false,
                    'bar'         => null,
                    'baz'         => null,
                    'something'   => null,
                )
            ),
            'literal-optional-flag-alternative-with-dashes' => array(
                'foo [--foo-bar|--foo-baz]',
                array('foo','--foo-baz'),
                array(
                    'foo'         => null,
                    'foo-bar'     => false,
                    'foo-baz'     => true,
                    'bar'         => null,
                    'baz'         => null,
                    'something'   => null,
                )
            ),
            'literal-optional-flag-alternative-with-dashes2' => array(
                'foo [--foo-bar|--foo-baz]',
                array('foo'),
                array(
                    'foo'         => null,
                    'foo-bar'     => false,
                    'foo-baz'     => false,
                    'bar'         => null,
                    'baz'         => null,
                    'something'   => null,
                )
            ),
            'value-with-dashes' => array(
                '<foo-bar-baz> [--bar=]',
                array('abc',),
                array(
                    'foo-bar-baz' => 'abc',
                    'foo'         => null,
                    'bar'         => null,
                    'baz'         => null,
                    'something'   => null,
                )
            ),

            'value-optional-with-dashes' => array(
                '[<foo-bar-baz>] [--bar=]',
                array('abc'),
                array(
                    'foo-bar-baz' => 'abc',
                    'foo'         => null,
                    'bar'         => null,
                    'baz'         => null,
                    'something'   => null,
                )
            ),
            'value-optional-with-dashes2' => array(
                '[<foo-bar-baz>] [--bar=]',
                array('--bar','abc'),
                array(
                    'foo-bar-baz' => null,
                    'foo'         => null,
                    'bar'         => 'abc',
                    'baz'         => null,
                    'something'   => null,
                )
            ),
            'value-optional-with-mixed-case' => array(
                '[<mixedCaseParam>] [--bar=]',
                array('aBc', '--bar','abc'),
                array(
                    'mixedCaseParam' => 'aBc',
                    'foo'            => null,
                    'bar'            => 'abc',
                    'baz'            => null,
                    'something'      => null,
                )
            ),
            'value-optional-with-upper-case' => array(
                '[<UPPERCASEPARAM>] [--bar=]',
                array('aBc', '--bar', 'abc'),
                array(
                    'UPPERCASEPARAM' => 'aBc',
                    'foo'            => null,
                    'bar'            => 'abc',
                    'baz'            => null,
                    'something'      => null,
                )
            ),
            /**
             * @bug ZF2-5671
             * @link https://github.com/zendframework/zf2/issues/5671
             */
            'mandatory-literal-camel-case' => array(
                'FooBar',
                array('FooBar'),
                array('FooBar' => null),
            ),
            'mandatory-literal-camel-case-no-match' => array(
                'FooBar',
                array('foobar'),
                null,
            ),
            'optional-literal-camel-case' => array(
                '[FooBar]',
                array('FooBar'),
                array('FooBar' => true),
            ),
            'optional-literal-camel-case-no-match' => array(
                '[FooBar]',
                array('foobar'),
                null,
            ),
            'optional-literal-alternative-camel-case' => array(
                '[ FooBar | FoozBar ]',
                array('FooBar'),
                array('FooBar' => true),
            ),
            'mandatory-literal-alternative-camel-case' => array(
                '( FooBar | FoozBar )',
                array('FooBar'),
                array('FooBar' => true),
            ),
            'mandatory-literal-alternative-camel-case-no-match' => array(
                '( FooBar | FoozBar )',
                array('baz'),
                null,
            ),
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
        $route = new DefaultRouteMatcher($routeDefinition);
        $match = $route->match($arguments);


        if ($params === null) {
            $this->assertNull($match, "The route must not match");
        } else {
            $this->assertInternalType('array', $match);

            foreach ($params as $key => $value) {
                $this->assertEquals(
                    $value,
                    isset($match[$key])?$match[$key]:null,
                    $value === null ? "Param $key is not present" : "Param $key is present and is equal to $value"
                );
            }
        }
    }

    public function testCannotMatchWithEmptyMandatoryParam()
    {
        $arguments = array('--foo=');
        $route = new DefaultRouteMatcher('--foo=');
        $match = $route->match($arguments);
        $this->assertEquals(null, $match);
    }

    public static function routeDefaultsProvider()
    {
        return array(
            'required-literals-no-defaults' => array(
                'create controller',
                array(),
                array('create', 'controller'),
                array('create' => null, 'controller' => null),
            ),
            'required-literals-defaults' => array(
                'create controller',
                array('controller' => 'value'),
                array('create', 'controller'),
                array('create' => null, 'controller' => 'value'),
            ),
            'value-param-no-defaults' => array(
                'create controller <controller>',
                array(),
                array('create', 'controller', 'foo'),
                array('create' => null, 'controller' => 'foo'),
            ),
            'value-param-defaults-overridden' => array(
                'create controller <controller>',
                array('controller' => 'defaultValue'),
                array('create', 'controller', 'foo'),
                array('create' => null, 'controller' => 'foo'),
            ),
            'optional-value-param-defaults' => array(
                'create controller [<controller>]',
                array('controller' => 'defaultValue'),
                array('create', 'controller'),
                array('create' => null, 'controller' => 'defaultValue'),
            ),
            'alternative-literal-non-present' => array(
                '(foo | bar)',
                array('bar' => 'something'),
                array('foo'),
                array('foo' => true, 'bar' => false),
            ),
            'alternative-literal-present' => array(
                '(foo | bar)',
                array('bar' => 'something'),
                array('bar'),
                array('foo' => false, 'bar' => 'something'),
            ),
            'alternative-flag-non-present' => array(
                '(--foo | --bar)',
                array('bar' => 'something'),
                array('--foo'),
                array('foo' => true, 'bar' => false),
            ),
            'alternative-flag-present' => array(
                '(--foo | --bar)',
                array('bar' => 'something'),
                array('--bar'),
                array('foo' => false, 'bar' => 'something'),
            ),
            'optional-literal-non-present' => array(
                'foo [bar]',
                array('bar' => 'something'),
                array('foo'),
                array('foo' => null, 'bar' => false),
            ),
            'optional-literal-present' => array(
                'foo [bar]',
                array('bar' => 'something'),
                array('foo', 'bar'),
                array('foo' => null, 'bar' => 'something'),
            ),
        );
    }

    /**
     * @dataProvider routeDefaultsProvider
     * @param        string         $routeDefinition
     * @param        array          $defaults
     * @param        array          $arguments
     * @param        array|null     $params
     */
    public function testMatchingWithDefaults(
        $routeDefinition,
        array $defaults = array(),
        array $arguments = array(),
        array $params = null
    ) {
        $route = new DefaultRouteMatcher($routeDefinition, array(), $defaults);
        $match = $route->match($arguments);

        if ($params === null) {
            $this->assertNull($match, "The route must not match");
        } else {
            $this->assertInternalType('array', $match);

            foreach ($params as $key => $value) {
                $this->assertSame(
                    $value,
                    isset($match[$key])?$match[$key]:null,
                    $value === null ? "Param $key is not present" : "Param $key is present and is equal to '$value'"
                );
            }
        }
    }

    public static function routeConstraintsProvider()
    {
        return array(
            'simple-constraints' => array(
                '<numeric> <alpha>',
                array(
                    'numeric' => '/^[0-9]+$/',
                    'alpha'   => '/^[a-zA-Z]+$/',
                ),
                array('1234', 'test'),
                true
            ),
            'constraints-on-optional-param' => array(
                '<alpha> [<numeric>]',
                array(
                    'numeric' => '/^[0-9]+$/',
                    'alpha'   => '/^[a-zA-Z]+$/',
                ),
                array('test', '1234'),
                true
            ),
            'optional-empty-param' => array(
                '<alpha> [<numeric>]',
                array(
                    'numeric' => '/^[0-9]+$/',
                    'alpha'   => '/^[a-zA-Z]+$/',
                ),
                array('test'),
                true
            ),
            'named-param' => array(
                '--foo=',
                array(
                    'foo' => '/^bar$/'
                ),
                array('--foo=bar'),
                true,
            ),
            'failing-param' => array(
                '<good1> <good2> <bad>',
                array(
                    'good1'   => '/^[a-zA-Z]+$/',
                    'good2'   => '/^[a-zA-Z]+$/',
                    'bad'   => '/^[a-zA-Z]+$/',
                ),
                array('foo', 'bar', 'foo123bar'),
                false
            ),
            'failing-optional-param' => array(
                '<good> [<bad>]',
                array(
                    'good2'   => '/^(foo|bar)$/',
                    'bad'   => '/^(foo|bar)$/',
                ),
                array('foo', 'baz'),
                false
            ),
            'failing-named-param' => array(
                '--foo=',
                array(
                    'foo' => '/^bar$/'
                ),
                array('--foo=baz'),
                false,
            ),
        );
    }

    /**
     * @dataProvider routeConstraintsProvider
     * @param        string $routeDefinition
     * @param        array  $constraints
     * @param        array  $arguments
     * @param        bool   $shouldMatch
     */
    public function testMatchingWithConstraints(
        $routeDefinition,
        array $constraints = array(),
        array $arguments = array(),
        $shouldMatch = true
    ) {
        $route = new DefaultRouteMatcher($routeDefinition, $constraints);
        $match = $route->match($arguments);

        if ($shouldMatch === false) {
            $this->assertNull($match, "The route must not match");
        } else {
            $this->assertInternalType('array', $match);
        }
    }

    public static function routeAliasesProvider()
    {
        return array(
            'simple-alias' => array(
                '--user=',
                array(
                    'username' => 'user'
                ),
                array('--username=JohnDoe'),
                array(
                    'user' => 'JohnDoe'
                )
            ),
            'multiple-aliases' => array(
                '--name= --email=',
                array(
                    'username' => 'name',
                    'useremail' => 'email'
                ),
                array('--username=JohnDoe', '--useremail=johndoe@domain.com'),
                array(
                    'name' => 'JohnDoe',
                    'email' => 'johndoe@domain.com',
                )
            ),
            'flags' => array(
                'foo --bar',
                array(
                    'baz' => 'bar'
                ),
                array('foo', '--baz'),
                array(
                    'bar' => true
                )
            ),
            'with-alternatives' => array(
                'do-something (--remove|--update)',
                array(
                    'delete' => 'remove'
                ),
                array('do-something', '--delete'),
                array(
                    'remove' => true,
                )
            ),
            'with-alternatives-2' => array(
                'do-something (--update|--remove)',
                array(
                    'delete' => 'remove'
                ),
                array('do-something', '--delete'),
                array(
                    'remove' => true,
                )
            )
        );
    }

    /**
     * @dataProvider routeAliasesProvider
     * @param        string     $routeDefinition
     * @param        array      $aliases
     * @param        array      $arguments
     * @param        array|null $params
     */
    public function testMatchingWithAliases(
        $routeDefinition,
        array $aliases = array(),
        array $arguments = array(),
        array $params = null
    ) {
        $route = new DefaultRouteMatcher($routeDefinition, array(), array(), $aliases);
        $match = $route->match($arguments);

        if ($params === null) {
            $this->assertNull($match, "The route must not match");
        } else {
            $this->assertInternalType('array', $match);

            foreach ($params as $key => $value) {
                $this->assertEquals(
                    $value,
                    isset($match[$key])?$match[$key]:null,
                    $value === null ? "Param $key is not present" : "Param $key is present and is equal to $value"
                );
            }
        }
    }

    public function routeValidatorsProvider()
    {
        return array(
            'validators-valid' => array(
                '<string> <number>',
                array(
                    'string' => new \Zend\Validator\StringLength(array('min' => 5, 'max' => 12)),
                    'number' => new \Zend\Validator\Digits()
                ),
                array('foobar', '12345'),
                true
            ),
            'validators-invalid' => array(
                '<string> <number>',
                array(
                    'string' => new \Zend\Validator\StringLength(array('min' => 5, 'max' => 12)),
                    'number' => new \Zend\Validator\Digits()
                ),
                array('foo', '12345'),
                false
            ),
            'validators-invalid2' => array(
                '<number> <string>',
                array(
                    'string' => new \Zend\Validator\StringLength(array('min' => 5, 'max' => 12)),
                    'number' => new \Zend\Validator\Digits()
                ),
                array('foozbar', 'not_digits'),
                false
            ),

        );
    }

    /**
     * @dataProvider routeValidatorsProvider
     * @param string $routeDefinition
     * @param array $validators
     * @param array $arguments
     * @param bool $shouldMatch
     */
    public function testParamsCanBeValidated($routeDefinition, $validators, $arguments, $shouldMatch)
    {
        $matcher = new DefaultRouteMatcher($routeDefinition, array(), array(), array(), null, $validators);
        $match = $matcher->match($arguments);
        if ($shouldMatch === false) {
            $this->assertNull($match, "The route must not match");
        } else {
            $this->assertInternalType('array', $match);
        }
    }

    public function routeFiltersProvider()
    {
        $genericFilter = $this->getMock('Zend\Filter\FilterInterface', array('filter'));
        $genericFilter->expects($this->once())->method('filter')
            ->with('foobar')->will($this->returnValue('foobaz'));

        return array(
            'filters-generic' => array(
                '<param>',
                array(
                    'param' => $genericFilter
                ),
                array('foobar'),
                array(
                    'param' => 'foobaz'
                )
            ),
            'filters-single' => array(
                '<number>',
                array(
                    'number' => new \Zend\Filter\ToInt()
                ),
                array('123four'),
                array(
                    'number' => 123
                )
            ),
            'filters-multiple' => array(
                '<number> <strtolower>',
                array(
                    'number' => new \Zend\Filter\ToInt(),
                    'strtolower' => new \Zend\Filter\StringToLower(),
                ),
                array('nan', 'FOOBAR'),
                array(
                    'number' => 0,
                    'strtolower' => 'foobar'
                )
            ),
        );
    }

    /**
     * @dataProvider routeFiltersProvider
     * @param string $routeDefinition
     * @param array $filters
     * @param array $arguments
     * @param array $params
     */
    public function testParamsCanBeFiltered($routeDefinition, $filters, $arguments, $params)
    {
        $matcher = new DefaultRouteMatcher($routeDefinition, array(), array(), array(), $filters);
        $match = $matcher->match($arguments);

        if (null === $match) {
            $this->fail("Route '$routeDefinition' must match.'");
        }

        $this->assertInternalType('array', $match);

        foreach ($params as $key => $value) {
            $this->assertEquals(
                $value,
                isset($match[$key])?$match[$key]:null,
                $value === null ? "Param $key is not present" : "Param $key is present and is equal to $value"
            );
        }
    }

    public function testConstructorDoesNotAcceptInvalidFilters()
    {
        $this->setExpectedException('Zend\Console\Exception\InvalidArgumentException');
        new DefaultRouteMatcher('<foo>', array(), array(), array(), array(
            new \stdClass()
        ));
    }

    public function testConstructorDoesNotAcceptInvalidValidators()
    {
        $this->setExpectedException('Zend\Console\Exception\InvalidArgumentException');
        new DefaultRouteMatcher('<foo>', array(), array(), array(), array(), array(
            new \stdClass()
        ));
    }
}
