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

require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cache
 */
class Zend_Cache_CommonBackendTest extends PHPUnit_Framework_TestCase {

    protected $_instance;
    protected $_className;
    protected $_root;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        $this->_className = $name;
        $this->_root = dirname(__FILE__);
        date_default_timezone_set('UTC');
        parent::__construct($name, $data, $dataName);
    }

    public function setUp($notag = false)
    {
        $this->mkdir();
        $this->_instance->setDirectives(array('logging' => true));
        if ($notag) {
            $this->_instance->save('bar : data to cache', 'bar');
            $this->_instance->save('bar2 : data to cache', 'bar2');
            $this->_instance->save('bar3 : data to cache', 'bar3');
        } else {
            $this->_instance->save('bar : data to cache', 'bar', array('tag3', 'tag4'));
            $this->_instance->save('bar2 : data to cache', 'bar2', array('tag3', 'tag1'));
            $this->_instance->save('bar3 : data to cache', 'bar3', array('tag2', 'tag3'));
        }
    }

    public function mkdir()
    {
        @mkdir($this->getTmpDir());
    }

    public function rmdir()
    {
        $tmpDir = $this->getTmpDir(false);
        foreach (glob("$tmpDir*") as $dirname) {
            @rmdir($dirname);
        }
    }

    public function getTmpDir($date = true)
    {
        $suffix = '';
        if ($date) {
            $suffix = date('mdyHis');
        }
        if (is_writeable($this->_root)) {
            return $this->_root . DIRECTORY_SEPARATOR . 'zend_cache_tmp_dir_' . $suffix;
        } else {
            if (getenv('TMPDIR')){
                return getenv('TMPDIR') . DIRECTORY_SEPARATOR . 'zend_cache_tmp_dir_' . $suffix;
            } else {
                die("no writable tmpdir found");
            }
        }
    }

    public function tearDown()
    {
        $this->_instance->clean();
        $this->rmdir();
    }

    public function testConstructorCorrectCall()
    {
        $this->fail('PLEASE IMPLEMENT A testConstructorCorrectCall !!!');
    }

    public function testConstructorBadOption()
    {
        try {
            $class = $this->_className;
            $test = new $class(array(1 => 'bar'));
        } catch (Zend_Cache_Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown');
    }

    public function testSetDirectivesCorrectCall()
    {
        $this->_instance->setDirectives(array('lifetime' => 3600, 'logging' => true));
    }

    public function testSetDirectivesBadArgument()
    {
        try {
            $this->_instance->setDirectives('foo');
        } catch (Zend_Cache_Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown');
    }

    public function testSetDirectivesBadDirective()
    {
        // A bad directive (not known by a specific backend) is possible
        // => so no exception here
        $this->_instance->setDirectives(array('foo' => true, 'lifetime' => 3600));
    }

    public function testSetDirectivesBadDirective2()
    {
        try {
            $this->_instance->setDirectives(array('foo' => true, 12 => 3600));
        } catch (Zend_Cache_Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown');
    }

    public function testSaveCorrectCall()
    {
        $res = $this->_instance->save('data to cache', 'foo', array('tag1', 'tag2'));
        $this->assertTrue($res);
    }

    public function testSaveWithNullLifeTime()
    {
        $this->_instance->setDirectives(array('lifetime' => null));
        $res = $this->_instance->save('data to cache', 'foo', array('tag1', 'tag2'));
        $this->assertTrue($res);
    }

    public function testSaveWithSpecificLifeTime()
    {
        $this->_instance->setDirectives(array('lifetime' => 3600));
        $res = $this->_instance->save('data to cache', 'foo', array('tag1', 'tag2'), 10);
        $this->assertTrue($res);
    }

    public function testRemoveCorrectCall()
    {
        $this->assertTrue($this->_instance->remove('bar'));
        $this->assertFalse($this->_instance->test('bar'));
        $this->assertFalse($this->_instance->remove('barbar'));
        $this->assertFalse($this->_instance->test('barbar'));
    }

    public function testTestWithAnExistingCacheId()
    {
        $res = $this->_instance->test('bar');
        if (!$res) {
            $this->fail('test() return false');
        }
        if (!($res > 999999)) {
            $this->fail('test() return an incorrect integer');
        }
        return;
    }

    public function testTestWithANonExistingCacheId()
    {
        $this->assertFalse($this->_instance->test('barbar'));
    }

    public function testTestWithAnExistingCacheIdAndANullLifeTime()
    {
        $this->_instance->setDirectives(array('lifetime' => null));
        $res = $this->_instance->test('bar');
        if (!$res) {
            $this->fail('test() return false');
        }
        if (!($res > 999999)) {
            $this->fail('test() return an incorrect integer');
        }
        return;
    }

    public function testGetWithANonExistingCacheId()
    {
        $this->assertFalse($this->_instance->load('barbar'));
    }

    public function testGetWithAnExistingCacheId()
    {
        $this->assertEquals('bar : data to cache', $this->_instance->load('bar'));
    }

    public function testGetWithAnExistingCacheIdAndUTFCharacters()
    {
        $data = '"""""' . "'" . '\n' . 'ééééé';
        $this->_instance->save($data, 'foo');
        $this->assertEquals($data, $this->_instance->load('foo'));
    }

    public function testGetWithAnExpiredCacheId()
    {
        $this->_instance->___expire('bar');
        $this->_instance->setDirectives(array('lifetime' => -1));
        $this->assertFalse($this->_instance->load('bar'));
        $this->assertEquals('bar : data to cache', $this->_instance->load('bar', true));
    }

    public function testCleanModeAll()
    {
        $this->assertTrue($this->_instance->clean('all'));
        $this->assertFalse($this->_instance->test('bar'));
        $this->assertFalse($this->_instance->test('bar2'));
    }

    public function testCleanModeOld()
    {
        $this->_instance->___expire('bar2');
        $this->assertTrue($this->_instance->clean('old'));
        $this->assertTrue($this->_instance->test('bar') > 999999);
        $this->assertFalse($this->_instance->test('bar2'));
    }

    public function testCleanModeMatchingTags()
    {
        $this->assertTrue($this->_instance->clean('matchingTag', array('tag3')));
        $this->assertFalse($this->_instance->test('bar'));
        $this->assertFalse($this->_instance->test('bar2'));
    }

    public function testCleanModeMatchingTags2()
    {
        $this->assertTrue($this->_instance->clean('matchingTag', array('tag3', 'tag4')));
        $this->assertFalse($this->_instance->test('bar'));
        $this->assertTrue($this->_instance->test('bar2') > 999999);
    }

    public function testCleanModeNotMatchingTags()
    {
        $this->assertTrue($this->_instance->clean('notMatchingTag', array('tag3')));
        $this->assertTrue($this->_instance->test('bar') > 999999);
        $this->assertTrue($this->_instance->test('bar2') > 999999);
    }

    public function testCleanModeNotMatchingTags2()
    {
        $this->assertTrue($this->_instance->clean('notMatchingTag', array('tag4')));
        $this->assertTrue($this->_instance->test('bar') > 999999);
        $this->assertFalse($this->_instance->test('bar2'));
    }

    public function testCleanModeNotMatchingTags3()
    {
        $this->assertTrue($this->_instance->clean('notMatchingTag', array('tag4', 'tag1')));
        $this->assertTrue($this->_instance->test('bar') > 999999);
        $this->assertTrue($this->_instance->test('bar2') > 999999);
        $this->assertFalse($this->_instance->test('bar3'));
    }

}


