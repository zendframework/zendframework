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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Zend_Cache
 */
require_once 'Zend/Cache.php';
require_once 'Zend/Cache/Frontend/Function.php';
require_once 'Zend/Cache/Backend/Test.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

function foobar($param1, $param2) {
    echo "foobar_output($param1, $param2)";
    return "foobar_return($param1, $param2)";
}

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cache
 */
class Zend_Cache_FunctionFrontendTest extends PHPUnit_Framework_TestCase {

    private $_instance;

    public function setUp()
    {
        if (!$this->_instance) {
            $this->_instance = new Zend_Cache_Frontend_Function(array());
            $this->_backend = new Zend_Cache_Backend_Test();
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
        $test = new Zend_Cache_Frontend_Function($options);
    }

    public function testConstructorBadCall()
    {
        $options = array(
            'cache_by_default' => false,
            0 => array('foo', 'bar')
        );
        try {
            $test = new Zend_Cache_Frontend_Function($options);
        } catch (Zend_Cache_Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown');
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
        $return = $this->_instance->call('foobar', array('param3', 'param4'));
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
        $return = $this->_instance->call('foobar', array('param1', 'param2'));
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
        $return = $this->_instance->call('foobar', array('param1', 'param2'));
        $data = ob_get_contents();
        ob_end_clean();
        ob_implicit_flush(true);
        $this->assertEquals('foobar_return(param1, param2)', $return);
        $this->assertEquals('foobar_output(param1, param2)', $data);
    }

    public function testCallWithABadSyntax1()
    {
        try {
            $this->_instance->call(1, array());
        } catch (Zend_Cache_Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown');
    }

    public function testCallWithABadSyntax2()
    {
        try {
            $this->_instance->call('foo', 1);
        } catch (Zend_Cache_Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown');
    }
}

