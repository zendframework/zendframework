<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Session
 */

namespace ZendTest\Session;

use Zend\Session\Storage\SessionStorage;
use Zend\Session\Storage\ArrayStorage;

/**
 * @category   Zend
 * @package    Zend_Session
 * @subpackage UnitTests
 * @group      Zend_Session
 */
class SessionStorageTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $_SESSION = array();
        $this->storage = new SessionStorage;
    }

    public function tearDown()
    {
        $_SESSION = array();
    }

    public function testSessionStorageInheritsFromArrayStorage()
    {
        $this->assertTrue($this->storage instanceof SessionStorage);
        $this->assertTrue($this->storage instanceof ArrayStorage);
    }

    public function testStorageWritesToSessionSuperglobal()
    {
        $this->storage['foo'] = 'bar';
        $this->assertSame($_SESSION, $this->storage);
        unset($this->storage['foo']);
        $this->assertFalse(array_key_exists('foo', $_SESSION));
    }

    public function testPassingArrayToConstructorOverwritesSessionSuperglobal()
    {
        $_SESSION['foo'] = 'bar';
        $array   = array('foo' => 'FOO');
        $storage = new SessionStorage($array);
        $expected = array(
            'foo' => 'FOO',
            '__ZF' => array(
                '_REQUEST_ACCESS_TIME' => $storage->getRequestAccessTime(),
            ),
        );
        $this->assertSame($expected, $_SESSION->getArrayCopy());
    }

    public function testModifyingSessionSuperglobalDirectlyUpdatesStorage()
    {
        $_SESSION['foo'] = 'bar';
        $this->assertTrue(isset($this->storage['foo']));
    }

    public function testDestructorSetsSessionToArray()
    {
        $this->storage->foo = 'bar';
        $expected = array(
            '__ZF' => array(
                '_REQUEST_ACCESS_TIME' => $this->storage->getRequestAccessTime(),
            ),
            'foo' => 'bar',
        );
        $this->storage->__destruct();
        $this->assertSame($expected, $_SESSION);
    }

    public function testModifyingOneSessionObjectModifiesTheOther()
    {
        $this->storage->foo = 'bar';
        $storage = new SessionStorage();
        $storage->bar = 'foo';
        $this->assertEquals('foo', $this->storage->bar);
    }

    public function testMarkingOneSessionObjectImmutableShouldMarkOtherInstancesImmutable()
    {
        $this->storage->foo = 'bar';
        $storage = new SessionStorage();
        $this->assertEquals('bar', $storage['foo']);
        $this->storage->markImmutable();
        $this->assertTrue($storage->isImmutable(), var_export($_SESSION, 1));
    }

    public function testMultiDimensionalUnset()
    {
        if (version_compare(PHP_VERSION, '5.3.4') < 0) {
            $this->markTestSkipped('Known issue on versions of PHP less than 5.3.4');
        }
        $this->storage['foo'] = array('bar' => array('baz' => 'boo'));
        unset($this->storage['foo']['bar']['baz']);
        $this->assertFalse(isset($this->storage['foo']['bar']['baz']));
        unset($this->storage['foo']['bar']);
        $this->assertFalse(isset($this->storage['foo']['bar']));
    }
}
