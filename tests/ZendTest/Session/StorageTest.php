<?php

namespace ZendTest\Session;

use Zend\Session\Storage\ArrayStorage;

class StorageTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->storage = new ArrayStorage;
    }

    public function testStorageAllowsArrayAccess()
    {
        $this->storage['foo'] = 'bar';
        $this->assertTrue(isset($this->storage['foo']));
        $this->assertEquals('bar', $this->storage['foo']);
        unset($this->storage['foo']);
        $this->assertFalse(isset($this->storage['foo']));
    }

    public function testStorageAllowsPropertyAccess()
    {
        $this->storage->foo = 'bar';
        $this->assertTrue(isset($this->storage->foo));
        $this->assertEquals('bar', $this->storage->foo);
        unset($this->storage->foo);
        $this->assertFalse(isset($this->storage->foo));
    }

    public function testStorageAllowsSettingMetadata()
    {
        $this->storage->setMetadata('TEST', 'foo');
        $this->assertEquals('foo', $this->storage->getMetadata('TEST'));
    }

    public function testSettingArrayMetadataMergesOnSubsequentRequests()
    {
        $this->storage->setMetadata('TEST', array('foo' => 'bar', 'bar' => 'baz'));
        $this->storage->setMetadata('TEST', array('foo' => 'baz', 'baz' => 'bat', 'lonesome'));
        $expected = array('foo' => 'baz', 'bar' => 'baz', 'baz' => 'bat', 'lonesome');
        $this->assertEquals($expected, $this->storage->getMetadata('TEST'));
    }

    public function testSettingArrayMetadataWithOverwriteFlagOverwritesExistingData()
    {
        $this->storage->setMetadata('TEST', array('foo' => 'bar', 'bar' => 'baz'));
        $this->storage->setMetadata('TEST', array('foo' => 'baz', 'baz' => 'bat', 'lonesome'), true);
        $expected = array('foo' => 'baz', 'baz' => 'bat', 'lonesome');
        $this->assertEquals($expected, $this->storage->getMetadata('TEST'));
    }

    public function testLockWithNoKeyMakesStorageReadOnly()
    {
        $this->storage->foo = 'bar';
        $this->storage->lock();
        $this->setExpectedException('Zend\\Session\\Exception', 'lock');
        $this->storage->foo = 'baz';
    }

    public function testLockWithNoKeyMarksEntireStorageLocked()
    {
        $this->storage->foo = 'bar';
        $this->storage->bar = 'baz';
        $this->storage->lock();
        $this->assertTrue($this->storage->isLocked());
        $this->assertTrue($this->storage->isLocked('foo'));
        $this->assertTrue($this->storage->isLocked('bar'));
    }

    public function testLockWithKeyMakesOnlyThatKeyReadOnly()
    {
        $this->storage->foo = 'bar';
        $this->storage->lock('foo');
        try {
            $this->storage->bar = 'baz';
            $this->assertEquals('baz', $this->storage->bar);
        } catch (\Zend\Session\Exception $e) {
            $this->fail('Writing to key not specified in lock should be permitted');
        }
        try {
            $this->storage->foo = 'baz';
            $this->fail('Writing to locked key should not be permitted');
        } catch (\Zend\Session\Exception $e) {
            $this->assertContains('lock', $e->getMessage());
        }
    }

    public function testLockWithKeyMarksOnlyThatKeyLocked()
    {
        $this->storage->foo = 'bar';
        $this->storage->bar = 'baz';
        $this->storage->lock('foo');
        $this->assertTrue($this->storage->isLocked('foo'));
        $this->assertFalse($this->storage->isLocked('bar'));
    }

    public function testLockWithNoKeyShouldWriteToMetadata()
    {
        $this->storage->foo = 'bar';
        $this->storage->lock();
        $locked = $this->storage->getMetadata('_READONLY');
        $this->assertTrue($locked);
    }

    public function testLockWithKeyShouldWriteToMetadata()
    {
        $this->storage->foo = 'bar';
        $this->storage->lock('foo');
        $locks = $this->storage->getMetadata('_LOCKS');
        $this->assertTrue(is_array($locks));
        $this->assertTrue(array_key_exists('foo', $locks));
    }

    public function testUnlockShouldUnlockEntireObject()
    {
        $this->storage->foo = 'bar';
        $this->storage->bar = 'baz';
        $this->storage->lock();
        $this->storage->unlock();
        $this->assertFalse($this->storage->isLocked('foo'));
        $this->assertFalse($this->storage->isLocked('bar'));
    }

    public function testUnlockShouldUnlockSelectivelyLockedKeys()
    {
        $this->storage->foo = 'bar';
        $this->storage->bar = 'baz';
        $this->storage->lock('foo');
        $this->storage->unlock();
        $this->assertFalse($this->storage->isLocked('foo'));
        $this->assertFalse($this->storage->isLocked('bar'));
    }

    public function testUnlockWithKeyShouldUnlockOnlyThatKey()
    {
        $this->storage->foo = 'bar';
        $this->storage->bar = 'baz';
        $this->storage->lock();
        $this->storage->unlock('foo');
        $this->assertFalse($this->storage->isLocked('foo'));
        $this->assertTrue($this->storage->isLocked('bar'));
    }

    public function testUnlockWithKeyOfSelectiveLockShouldUnlockThatKey()
    {
        $this->storage->foo = 'bar';
        $this->storage->lock('foo');
        $this->storage->unlock('foo');
        $this->assertFalse($this->storage->isLocked('foo'));
    }

    public function testClearWithNoArgumentsRemovesExistingData()
    {
        $this->storage->foo = 'bar';
        $this->storage->bar = 'baz';

        $this->storage->clear();
        $data = $this->storage->toArray();
        $this->assertSame(array(), $data);
    }

    public function testClearWithNoArgumentsRemovesExistingMetadata()
    {
        $this->storage->foo = 'bar';
        $this->storage->lock('foo');
        $this->storage->setMetadata('FOO', 'bar');
        $this->storage->clear();

        $this->assertFalse($this->storage->isLocked('foo'));
        $this->assertFalse($this->storage->getMetadata('FOO'));
    }

    public function testClearWithArgumentRemovesExistingDataForThatKeyOnly()
    {
        $this->storage->foo = 'bar';
        $this->storage->bar = 'baz';

        $this->storage->clear('foo');
        $data = $this->storage->toArray();
        $this->assertSame(array('bar' => 'baz'), $data);
    }

    public function testClearWithArgumentRemovesExistingMetadataForThatKeyOnly()
    {
        $this->storage->foo = 'bar';
        $this->storage->bar = 'baz';
        $this->storage->lock('foo');
        $this->storage->lock('bar');
        $this->storage->setMetadata('foo', 'bar');
        $this->storage->setMetadata('bar', 'baz');
        $this->storage->clear('foo');

        $this->assertFalse($this->storage->isLocked('foo'));
        $this->assertTrue($this->storage->isLocked('bar'));
        $this->assertFalse($this->storage->getMetadata('foo'));
        $this->assertEquals('baz', $this->storage->getMetadata('bar'));
    }

    public function testClearWhenStorageMarkedImmutableRaisesException()
    {
        $this->storage->foo = 'bar';
        $this->storage->bar = 'baz';
        $this->storage->markImmutable();
        $this->setExpectedException('Zend\\Session\\Exception', 'immutable');
        $this->storage->clear();
    }
}
