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

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cache
 */
class FileFrontendTest extends \PHPUnit_Framework_TestCase {

    private $_instance1;
    private $_instance2;
    private $_instance3;
    private $_instance4;
    private $_masterFile;
    private $_masterFile1;
    private $_masterFile2;


    public function setUp()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->_masterFile = $this->_getTmpDirWindows() . DIRECTORY_SEPARATOR . 'zend_cache_master';
            $this->_masterFile1 = $this->_getTmpDirWindows() . DIRECTORY_SEPARATOR . 'zend_cache_master1';
            $this->_masterFile2 = $this->_getTmpDirWindows() . DIRECTORY_SEPARATOR . 'zend_cache_master2';
        } else {
            $this->_masterFile = $this->_getTmpDirUnix() . DIRECTORY_SEPARATOR . 'zend_cache_master';
            $this->_masterFile1 = $this->_getTmpDirUnix() . DIRECTORY_SEPARATOR . 'zend_cache_master1';
            $this->_masterFile2 = $this->_getTmpDirUnix() . DIRECTORY_SEPARATOR . 'zend_cache_master2';
        }
        if (!$this->_instance1) {
            touch($this->_masterFile, 123455);
            $this->_instance1 = new Cache\Frontend\File(array('master_file' => $this->_masterFile));
            $this->_backend = new TestBackend();
            $this->_instance1->setBackend($this->_backend);
        }
        if (!$this->_instance2) {
            touch($this->_masterFile);
            $this->_instance2 = new Cache\Frontend\File(array('master_file' => $this->_masterFile));
            $this->_backend = new TestBackend();
            $this->_instance2->setBackend($this->_backend);
        }
        if (!$this->_instance3) {
            touch($this->_masterFile1, 123455);
            touch($this->_masterFile2, 123455);
            $this->_instance3 = new Cache\Frontend\File(array('master_files' => array($this->_masterFile1, $this->_masterFile2)));
            $this->_backend = new TestBackend();
            $this->_instance3->setBackend($this->_backend);
        }
        if (!$this->_instance4) {
            touch($this->_masterFile1);
            touch($this->_masterFile2);
            $this->_instance4 = new Cache\Frontend\File(array('master_files' => array($this->_masterFile1, $this->_masterFile2)));
            $this->_backend = new TestBackend();
            $this->_instance4->setBackend($this->_backend);
        }
    }

    public function tearDown()
    {
        unset($this->_instance1);
        unlink($this->_masterFile);
        unlink($this->_masterFile1);
        unlink($this->_masterFile2);
    }

    private function _getTmpDirWindows()
    {
        if (isset($_ENV['TEMP'])) {
            return $_ENV['TEMP'];
        }
        if (isset($_ENV['TMP'])) {
            return $_ENV['TMP'];
        }
        if (isset($_ENV['windir'])) {
            return $_ENV['windir'] . '\\temp';
        }
        if (isset($_ENV['SystemRoot'])) {
            return $_ENV['SystemRoot'] . '\\temp';
        }
        if (isset($_SERVER['TEMP'])) {
            return $_SERVER['TEMP'];
        }
        if (isset($_SERVER['TMP'])) {
            return $_SERVER['TMP'];
        }
        if (isset($_SERVER['windir'])) {
            return $_SERVER['windir'] . '\\temp';
        }
        if (isset($_SERVER['SystemRoot'])) {
            return $_SERVER['SystemRoot'] . '\\temp';
        }
        return '\temp';
    }

    private function _getTmpDirUnix()
    {
        if (isset($_ENV['TMPDIR'])) {
            return $_ENV['TMPDIR'];
        }
        if (isset($_SERVER['TMPDIR'])) {
            return $_SERVER['TMPDIR'];
        }
        return '/tmp';
    }

    public function testConstructorCorrectCall()
    {
        $test = new Cache\Frontend\File(array('master_file' => $this->_masterFile, 'lifetime' => 3600, 'caching' => true));
    }

    public function testConstructorBadCall1()
    {
        # no masterfile
        try {
            $test = new Cache\Frontend\File(array('lifetime' => 3600, 'caching' => true));
        } catch (Cache\Exception $e) {
            return;
        }
        $this->fail('Cache\Exception was expected but not thrown');
    }

    public function testConstructorBadCall2()
    {
        # incorrect option
        try {
            $test = new Cache\Frontend\File(array('master_file' => $this->_masterFile, 0 => 3600));
        } catch (Cache\Exception $e) {
            return;
        }
        $this->fail('Cache\Exception was expected but not thrown');
    }

    public function testTestCorrectCall1()
    {
        $this->assertFalse($this->_instance1->test('false'));
    }

    public function testTestCorrectCall2()
    {
        $this->assertTrue($this->_instance1->test('cache_id') > 1);
    }

    public function testTestCorrectCall3()
    {
        $this->assertFalse($this->_instance2->test('cache_id'));
    }

    public function testGetCorrectCall1()
    {
        $this->assertFalse($this->_instance1->load('false'));
    }

    public function testGetCorrectCall2()
    {
        $this->assertEquals('foo', $this->_instance1->load('cache_id'));
    }

    public function testTestCorrectCall4()
    {
        $this->assertFalse($this->_instance4->test('cache_id'));
    }

    public function testTestCorrectCall5()
    {
        $this->assertFalse($this->_instance3->load('false'));
    }

    public function testGetCorrectCall3()
    {
        $this->assertFalse($this->_instance2->load('cache_id'));
    }

    public function testConstructorWithABadMasterFile()
    {
        try {
            $instance = new Cache\Frontend\File(array('master_file' => '/foo/bar/ljhfdjh/qhskldhqjk'));
        } catch (Cache\Exception $e) {
            return;
        }
        $this->fail('Cache\Exception was expected but not thrown');
    }

    public function testGetWithDoNotTestCacheValidity()
    {
        $this->assertEquals('foo', $this->_instance1->load('cache_id', true));
    }

}


