<?php

namespace ZendTest\Module;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Module\ManagerOptions,
    InvalidArgumentException,
    BadMethodCallException;

class ManagerOptionsTest extends TestCase
{
    public function testCanConfigureWithArrayInConstructor()
    {
        $options = new ManagerOptions(array(
            'cache_dir' => __DIR__,
            'cache_config' => true,
        ));
        $this->assertSame($options->getCacheDir(), __DIR__);
        $this->assertTrue($options->getCacheConfig(), true);
        $this->assertNotNull(strstr($options->getCacheFilePath(), __DIR__));
        $this->assertNotNull(strstr($options->getCacheFilePath(), '.php'));
    }

    public function testCanAccessKeysAsProperties()
    {
        $options = new ManagerOptions(array(
            'cache_dir' => __DIR__,
            'cache_config' => true,
        ));
        $this->assertSame($options->cache_dir, __DIR__);
        $this->assertTrue($options->cache_config, true);
        $options->cache_dir = 'foo';
        $options->cache_config = false;
        $this->assertSame($options->cache_dir, 'foo');
        $this->assertFalse($options->cache_config, false);
        $this->assertTrue(isset($options->cache_config));
        unset($options->cache_config);
        $this->assertFalse(isset($options->cache_config));
    }

    public function testContructorThrowsInvalidArgumentException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $options = new ManagerOptions('foo');
    }

    public function testThrowsBadMethodCallExceptionForBadSetterKey()
    {
        $this->setExpectedException('BadMethodCallException');
        $options = new ManagerOptions;
        $options->bad = true;
    }

    public function testThrowsBadMethodCallExceptionForBadGetterKey()
    {
        $this->setExpectedException('BadMethodCallException');
        $options = new ManagerOptions;
        $bad = $options->bad;
    }
}
