<?php

namespace ZendTest\Session;

use Zend\Session\Storage\SessionStorage,
    Zend\Session\Storage\ArrayStorage;

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
        $this->assertSame($array, (array) $_SESSION);
    }

    public function testModifyingSessionSuperglobalDirectlyUpdatesStorage()
    {
        $_SESSION['foo'] = 'bar';
        $this->assertTrue(isset($this->storage['foo']));
    }

    public function testDestructorSetsSessionToArray()
    {
        $this->storage->foo = 'bar';
        $this->storage->__destruct();
        $this->assertSame(array('foo' => 'bar'), $_SESSION);
    }
}
