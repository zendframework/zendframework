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
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

namespace ZendTest\Cache;

use Zend\Cache,
    Zend\Cache\Backend\TestBackend;

function foobar($param1, $param2) {
    echo "foobar_output($param1, $param2)";
    return "foobar_return($param1, $param2)";
}

class fooclass {
    private static $_instanceCounter = 0;

    public function __construct()
    {
        self::$_instanceCounter++;
    }

    public function foobar($param1, $param2)
    {
        return foobar($param1, $param2)
               . ':' . self::$_instanceCounter;
    }
}

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cache
 */
class FunctionFrontendTest extends \PHPUnit_Framework_TestCase
{

    private $_instance;

    public function setUp()
    {
        if (!$this->_instance) {
            $this->_instance = new Cache\Frontend\FunctionFrontend(array());
            $this->_backend = new TestBackend();
            $this->_instance->setBackend($this->_backend);
        }
    }

    public function tearDown()
    {
        unset($this->_instance);
    }

    public function testConstructorCorrectCall()
    {
        $options = array(
            'cache_by_default' => false,
            'cached_functions' => array('foo', 'bar')
        );
        $test = new Cache\Frontend\FunctionFrontend($options);
    }

    public function testConstructorBadCall()
    {
        $options = array(
            'cache_by_default' => false,
            0 => array('foo', 'bar')
        );
        try {
            $test = new Cache\Frontend\FunctionFrontend($options);
        } catch (Cache\Exception $e) {
            return;
        }
        $this->fail('Cache\Exception was expected but not thrown');
    }

    public function testCallCorrectCall1()
    {
        ob_start();
        ob_implicit_flush(false);
        $return = $this->_instance->call('foobar', array('param1', 'param2'));
        $data = ob_get_contents();
        ob_end_clean();
        ob_implicit_flush(true);
        $this->assertEquals('bar', $return);
        $this->assertEquals('foo', $data);
    }

    public function testCallCorrectCall2()
    {
        ob_start();
        ob_implicit_flush(false);
        $return = $this->_instance->call('\ZendTest\Cache\foobar', array('param3', 'param4'));
        $data = ob_get_contents();
        ob_end_clean();
        ob_implicit_flush(true);
        $this->assertEquals('foobar_return(param3, param4)', $return);
        $this->assertEquals('foobar_output(param3, param4)', $data);
    }

    public function testCallCorrectCall3()
    {
        // cacheByDefault = false
        $this->_instance->setOption('cache_by_default', false);
        ob_start();
        ob_implicit_flush(false);
        $return = $this->_instance->call('\ZendTest\Cache\foobar', array('param1', 'param2'));
        $data = ob_get_contents();
        ob_end_clean();
        ob_implicit_flush(true);
        $this->assertEquals('foobar_return(param1, param2)', $return);
        $this->assertEquals('foobar_output(param1, param2)', $data);
    }

    public function testCallCorrectCall4()
    {
        // cacheByDefault = false
        // cachedFunctions = array('foobar')
        $this->_instance->setOption('cache_by_default', false);
        $this->_instance->setOption('cached_functions', array('foobar'));
        ob_start();
        ob_implicit_flush(false);
        $return = $this->_instance->call('foobar', array('param1', 'param2'));
        $data = ob_get_contents();
        ob_end_clean();
        ob_implicit_flush(true);
        $this->assertEquals('bar', $return);
        $this->assertEquals('foo', $data);
    }

    public function testCallCorrectCall5()
    {
        // cacheByDefault = true
        // nonCachedFunctions = array('foobar')
        $this->_instance->setOption('cache_by_default', true);
        $this->_instance->setOption('non_cached_functions', array('foobar'));
        ob_start();
        ob_implicit_flush(false);
        $return = $this->_instance->call('\ZendTest\Cache\foobar', array('param1', 'param2'));
        $data = ob_get_contents();
        ob_end_clean();
        ob_implicit_flush(true);
        $this->assertEquals('foobar_return(param1, param2)', $return);
        $this->assertEquals('foobar_output(param1, param2)', $data);
    }

    public function testCallObjectMethodCorrectCall1()
    {
        // cacheByDefault = true
        // nonCachedFunctions = array('foobar')
        $this->_instance->setOption('cache_by_default', true);
        $this->_instance->setOption('non_cached_functions', array('foobar'));
        ob_start();
        ob_implicit_flush(false);
        $object = new fooclass();
        $return = $this->_instance->call(array($object, 'foobar'), array('param1', 'param2'));
        $data = ob_get_contents();
        ob_end_clean();
        ob_implicit_flush(true);
        $this->assertEquals('foobar_return(param1, param2):1', $return);
        $this->assertEquals('foobar_output(param1, param2)', $data);
    }

    public function testCallObjectMethodCorrectCall2()
    {
        // cacheByDefault = true
        // nonCachedFunctions = array('foobar')
        $this->_instance->setOption('cache_by_default', true);
        $this->_instance->setOption('non_cached_functions', array('foobar'));
        ob_start();
        ob_implicit_flush(false);
        $object = new fooclass();
        $return = $this->_instance->call(array($object, 'foobar'), array('param1', 'param2'));
        $data = ob_get_contents();
        ob_end_clean();
        ob_implicit_flush(true);
        $this->assertEquals('foobar_return(param1, param2):2', $return);
        $this->assertEquals('foobar_output(param1, param2)', $data);
    }

    public function testCallClosureThrowsException()
    {
        $this->setExpectedException('Zend\Cache\Exception');
        $closure = function () {}; // no parse error on php < 5.3
        $this->_instance->call($closure);
    }

    public function testCallWithABadSyntax1()
    {
        try {
            $this->_instance->call(1, array());
        } catch (Cache\Exception $e) {
            return;
        }
        $this->fail('Cache\Exception was expected but not thrown');
    }

}

