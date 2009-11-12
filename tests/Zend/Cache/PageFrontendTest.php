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
require_once 'Zend/Cache/Frontend/Page.php';
require_once 'Zend/Cache/Backend/Test.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cache
 */
class Zend_Cache_PageFrontendTest extends PHPUnit_Framework_TestCase {

    private $_instance;

    public function setUp()
    {
        if (!$this->_instance) {
            $this->_instance = new Zend_Cache_Frontend_Page(array());
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
        $test = new Zend_Cache_Frontend_Page(array('lifetime' => 3600, 'caching' => true));
    }

    public function testConstructorUnimplementedOption()
    {
        try {
            $test = new Zend_Cache_Frontend_Page(array('http_conditional' => true));
        } catch (Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown');
    }

    public function testConstructorWithBadDefaultOptions()
    {
        try {
            $test = new Zend_Cache_Frontend_Page(array('default_options' => 'foo'));
        } catch (Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown');
    }

    /**
     * The only bad default options are non-string keys
     * @group ZF-5034
     */
    public function testConstructorWithBadDefaultOptions2()
    {
        try {
            $test = new Zend_Cache_Frontend_Page(array('default_options' => array('cache' => true, 1 => 'bar')));
        } catch (Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown');
    }

    public function testConstructorWithBadRegexps()
    {
        try {
            $test = new Zend_Cache_Frontend_Page(array('regexps' => 'foo'));
        } catch (Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown');
    }

    public function testConstructorWithBadRegexps2()
    {
        try {
            $test = new Zend_Cache_Frontend_Page(array('regexps' => array('foo', 'bar')));
        } catch (Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown');
    }

    /**
     * Only non-string keys should raise exceptions
     * @group ZF-5034
     */
    public function testConstructorWithBadRegexps3()
    {
        $array = array(
           '^/$' => array('cache' => true),
           '^/index/' => array('cache' => true),
           '^/article/' => array('cache' => false),
           '^/article/view/' => array(
               1 => true,
               'cache_with_post_variables' => true,
               'make_id_with_post_variables' => true,
           )
        );
        try {
            $test = new Zend_Cache_Frontend_Page(array('regexps' => $array));
        } catch (Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown');
    }

    public function testConstructorWithGoodRegexps()
    {
        $array = array(
           '^/$' => array('cache' => true),
           '^/index/' => array('cache' => true),
           '^/article/' => array('cache' => false),
           '^/article/view/' => array(
               'cache' => true,
               'cache_with_post_variables' => true,
               'make_id_with_post_variables' => true,
           )
        );
        $test = new Zend_Cache_Frontend_Page(array('regexps' => $array));
    }

    public function testConstructorWithGoodDefaultOptions()
    {
        $test = new Zend_Cache_Frontend_Page(array('default_options' => array('cache' => true)));
    }

    public function testStartEndCorrectCall1()
    {
        ob_start();
        ob_implicit_flush(false);
        if (!($this->_instance->start('serialized2', true))) {
            echo('foobar');
            ob_end_flush();
        }
        $data = ob_get_contents();
        ob_end_clean();
        ob_implicit_flush(true);
        $this->assertEquals('foo', $data);
    }

    public function testStartEndCorrectCall2()
    {
        ob_start();
        ob_implicit_flush(false);
        if (!($this->_instance->start('false', true))) {
            echo('foobar');
            ob_end_flush();
        }
        $data = ob_get_contents();
        ob_end_clean();
        ob_implicit_flush(true);
        $this->assertEquals('foobar', $data);
    }

    public function testStartEndCorrectCallWithDebug()
    {
        $this->_instance->setOption('debug_header', true);
        ob_start();
        ob_implicit_flush(false);
        if (!($this->_instance->start('serialized2', true))) {
            echo('foobar');
            ob_end_flush();
        }
        $data = ob_get_contents();
        ob_end_clean();
        ob_implicit_flush(true);
        $this->assertEquals('DEBUG HEADER : This is a cached page !foo', $data);
    }
}

