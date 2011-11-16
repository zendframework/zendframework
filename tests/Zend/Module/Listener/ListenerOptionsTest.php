<?php

namespace ZendTest\Module\Listener;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Module\Listener\ListenerOptions,
    InvalidArgumentException,
    BadMethodCallException;

class ListenerOptionsTest extends TestCase
{
    public function testCanConfigureWithArrayInConstructor()
    {
        $options = new ListenerOptions(array(
            'cache_dir'            => __DIR__,
            'config_cache_enabled' => true,
            'config_cache_key'     => 'foo',
        ));
        $this->assertSame($options->getCacheDir(), __DIR__);
        $this->assertTrue($options->getConfigCacheEnabled());
        $this->assertNotNull(strstr($options->getConfigCacheFile(), __DIR__));
        $this->assertNotNull(strstr($options->getConfigCacheFile(), '.php'));
        $this->assertSame($options->getConfigCacheKey(), 'foo');
    }

    public function testCanAccessKeysAsProperties()
    {
        $options = new ListenerOptions(array(
            'cache_dir'            => __DIR__,
            'config_cache_enabled' => true,
        ));
        $this->assertSame($options->cache_dir, __DIR__);
        $options->cache_dir = 'foo';
        $this->assertSame($options->cache_dir, 'foo');
        $this->assertTrue(isset($options->cache_dir));
        unset($options->cache_dir);
        $this->assertFalse(isset($options->cache_dir));

        $this->assertTrue($options->config_cache_enabled);
        $options->config_cache_enabled = false;
        $this->assertFalse($options->config_cache_enabled);

        $this->assertEquals($options->application_environment, $options->config_cache_key);
    }
}
