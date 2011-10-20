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
            'cache_dir'           => __DIR__,
            'enable_config_cache' => true,
            'manifest_dir'        => __DIR__,
        ));
        $this->assertSame($options->getCacheDir(), __DIR__);
        $this->assertTrue($options->getEnableConfigCache());
        $this->assertNotNull(strstr($options->getCacheFilePath(), __DIR__));
        $this->assertNotNull(strstr($options->getCacheFilePath(), '.php'));
        $this->assertSame($options->getManifestDir(), __DIR__);
    }

    public function testCanAccessKeysAsProperties()
    {
        $options = new ManagerOptions(array(
            'cache_dir'           => __DIR__,
            'enable_config_cache' => true,
            'manifest_dir'        => __DIR__,
        ));
        $this->assertSame($options->cache_dir, __DIR__);
        $options->cache_dir = 'foo';
        $this->assertSame($options->cache_dir, 'foo');
        $this->assertTrue(isset($options->cache_dir));
        unset($options->cache_dir);
        $this->assertFalse(isset($options->cache_dir));

        $this->assertTrue($options->enable_config_cache);
        $options->enable_config_cache = false;
        $this->assertFalse($options->enable_config_cache);

        $this->assertSame($options->manifest_dir, __DIR__);
        $options->manifest_dir = 'foo';
        $this->assertSame($options->manifest_dir, 'foo');
        unset($options->manifest_dir);
        $this->assertFalse(isset($options->manifest_dir));
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
