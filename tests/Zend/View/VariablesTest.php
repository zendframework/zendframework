<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\View;

use Zend\View\Variables,
    Zend\Config\Config;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 */
class VariablesTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->error = false;
        $this->vars = new Variables;
    }

    public function testEncodingIsUtf8ByDefault()
    {
        $this->assertEquals('UTF-8', $this->vars->getEncoding());
    }

    public function testRawValuesAreEmptyByDefault()
    {
        $rawValues = $this->vars->getRawValues();
        $this->assertTrue(empty($rawValues));
    }

    public function testStrictVarsAreDisabledByDefault()
    {
        $this->assertFalse($this->vars->isStrict());
    }

    public function testHasDefaultEscapeCallback()
    {
        $callback = $this->vars->getEscapeCallback();
        $this->assertTrue(is_callable($callback));
    }

    public function testEscapeUsesHtmlSpecialCharsByDefault()
    {
        $string   = '<tag foo="bar">\'some string\'</tag>';
        $expected = htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
        $test     = $this->vars->escape($string);
        $this->assertEquals($expected, $test);
    }

    public function testCanSetEncoding()
    {
        $this->vars->setEncoding('iso-8859-1');
        $this->assertEquals('iso-8859-1', $this->vars->getEncoding());
    }

    public function testCanSetStrictFlag()
    {
        $this->vars->setStrictVars(true);
        $this->assertTrue($this->vars->isStrict());
    }

    public function testPassingNonCallableArgumentToEscapeCallbackRaisesException()
    {
        $this->setExpectedException('Zend\View\Exception', 'callable');
        $this->vars->setEscapeCallback('__foo__');
    }

    public function testCanSetEscapeCallback()
    {
        $callback = 'htmlspecialchars';
        $this->vars->setEscapeCallback($callback);
        $this->assertEquals($callback, $this->vars->getEscapeCallback());
    }

    public function testAssignMergesValuesWithObject()
    {
        $this->vars['foo'] = 'bar';
        $this->vars->assign(array(
            'bar' => 'baz',
            'baz' => 'foo',
        ));
        $this->assertEquals('bar', $this->vars['foo']);
        $this->assertEquals('baz', $this->vars['bar']);
        $this->assertEquals('foo', $this->vars['baz']);
    }

    public function testAssignCastsPlainObjectToArrayBeforeMerging()
    {
        $vars = new \stdClass;
        $vars->foo = 'bar';
        $vars->bar = 'baz';

        $this->vars->assign($vars);
        $this->assertEquals('bar', $this->vars['foo']);
        $this->assertEquals('baz', $this->vars['bar']);
    }

    public function testAssignCallsToArrayWhenPresentBeforeMerging()
    {
        $vars = array(
            'foo' => 'bar',
            'bar' => 'baz',
        );
        $config = new Config($vars);
        $this->vars->assign($config);
        $this->assertEquals('bar', $this->vars['foo']);
        $this->assertEquals('baz', $this->vars['bar']);
    }

    public function testValuesAssignedViaConstructorAreEscapedByDefault()
    {
        $string = '<tag id="foo">\'some string\'</tag>';
        $vars = new Variables(array('foo'=>$string));
        $expected = htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
        $this->assertEquals($expected, $vars['foo']);
    }

    public function testValuesAreEscapedByDefault()
    {
        $string = '<tag id="foo">\'some string\'</tag>';
        $this->vars['foo'] = $string;
        $expected = htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
        $this->assertEquals($expected, $this->vars['foo']);
    }

    public function testNonStringValuesAreNotEscaped()
    {
        $value = array(
            '<tag id="foo">\'some string\'</tag>',
        );
        $this->vars['foo'] = $value;
        $this->assertEquals($value[0], $this->vars['foo'][0]);
    }

    public function testCanForceUsingUnsanitizedStringsPerVariable()
    {
        $string = '<tag id="foo">\'some string\'</tag>';
        $this->vars->setCleanValue('foo', $string);
        $this->assertEquals($string, $this->vars['foo']);
    }

    public function testNullIsReturnedForUndefinedVariables()
    {
        $this->assertNull($this->vars['foo']);
    }

    public function handleErrors($errcode, $errmsg)
    {
        $this->error = $errmsg;
    }

    public function testRetrievingUndefinedVariableRaisesErrorWhenStrictVarsIsRequested()
    {
        $this->vars->setStrictVars(true);
        set_error_handler(array($this, 'handleErrors'), E_USER_NOTICE);
        $this->assertNull($this->vars['foo']);
        restore_error_handler();
        $this->assertContains('does not exist', $this->error);
    }

    public function testRetrievingUndefinedRawValueRaisesErrorWhenStrictVarsIsRequested()
    {
        $this->vars->setStrictVars(true);
        set_error_handler(array($this, 'handleErrors'), E_USER_NOTICE);
        $this->assertNull($this->vars->getRawValue('foo'));
        restore_error_handler();
        $this->assertContains('does not exist', $this->error);
    }

    public function values()
    {
        return array(
            array('foo', 'bar'),
            array('xss', '<tag id="foo">\'value\'</tag>'),
        );
    }

    /**
     * @dataProvider values
     */
    public function testRawValueIsStoredForEachValue($key, $arg)
    {
        $this->vars[$key] = $arg;
        $this->assertEquals($arg, $this->vars->getRawValue($key));
        $this->assertNotNull($this->vars[$key]);

        $rawValues = $this->vars->getRawValues();
        $this->assertEquals(count($this->vars), count($rawValues));
    }

    public function testCallingClearEmptiesObject()
    {
        $this->vars->assign(array(
            'bar' => 'baz',
            'baz' => 'foo',
        ));
        $this->assertEquals(2, count($this->vars));
        $this->vars->clear();
        $this->assertEquals(0, count($this->vars));
        $rawValues = $this->vars->getRawValues();
        $this->assertEquals(0, count($rawValues));
    }
}
