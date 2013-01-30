<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ModuleManager
 */

namespace ZendTest\ModuleManager\Listener;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Config\Config;
use Zend\ModuleManager\Listener\ListenerOptions;

class ListenerOptionsTest extends TestCase
{
    public function testCanConfigureWithArrayInConstructor()
    {
        $options = new ListenerOptions(array(
            'cache_dir'               => __DIR__,
            'config_cache_enabled'    => true,
            'config_cache_key'        => 'foo',
            'module_paths'            => array('module','paths'),
            'config_glob_paths'       => array('glob','paths'),
            'config_static_paths'       => array('static','custom_paths'),
        ));
        $this->assertSame($options->getCacheDir(), __DIR__);
        $this->assertTrue($options->getConfigCacheEnabled());
        $this->assertNotNull(strstr($options->getConfigCacheFile(), __DIR__));
        $this->assertNotNull(strstr($options->getConfigCacheFile(), '.php'));
        $this->assertSame('foo', $options->getConfigCacheKey());
        $this->assertSame(array('module', 'paths'), $options->getModulePaths());
        $this->assertSame(array('glob', 'paths'), $options->getConfigGlobPaths());
        $this->assertSame(array('static', 'custom_paths'), $options->getConfigStaticPaths());
    }

    public function testCanAccessKeysAsProperties()
    {
        $options = new ListenerOptions(array(
            'cache_dir'               => __DIR__,
            'config_cache_enabled'    => true,
            'config_cache_key'        => 'foo',
            'module_paths'            => array('module','paths'),
            'config_glob_paths'       => array('glob','paths'),
            'config_static_paths'       => array('static','custom_paths'),
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
        $this->assertEquals('foo', $options->config_cache_key);
        $this->assertSame(array('module', 'paths'), $options->module_paths);
        $this->assertSame(array('glob', 'paths'), $options->config_glob_paths);
        $this->assertSame(array('static', 'custom_paths'), $options->config_static_paths);
    }

    public function testSetModulePathsAcceptsConfigOrTraverable()
    {
        $config = new Config(array(__DIR__));
        $options = new ListenerOptions;
        $options->setModulePaths($config);
        $this->assertSame($config, $options->getModulePaths());
    }

    public function testSetModulePathsThrowsInvalidArgumentException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $options = new ListenerOptions;
        $options->setModulePaths('asd');
    }

    public function testSetConfigGlobPathsAcceptsConfigOrTraverable()
    {
        $config = new Config(array(__DIR__));
        $options = new ListenerOptions;
        $options->setConfigGlobPaths($config);
        $this->assertSame($config, $options->getConfigGlobPaths());
    }

    public function testSetConfigGlobPathsThrowsInvalidArgumentException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $options = new ListenerOptions;
        $options->setConfigGlobPaths('asd');
    }

    public function testSetConfigStaticPathsThrowsInvalidArgumentException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $options = new ListenerOptions;
        $options->setConfigStaticPaths('asd');
    }

    public function testSetExtraConfigAcceptsArrayOrTraverable()
    {
        $array = array(__DIR__);
        $traversable = new Config($array);
        $options = new ListenerOptions;

        $this->assertSame($options, $options->setExtraConfig($array));
        $this->assertSame($array, $options->getExtraConfig());

        $this->assertSame($options, $options->setExtraConfig($traversable));
        $this->assertSame($traversable, $options->getExtraConfig());
    }

    public function testSetExtraConfigThrowsInvalidArgumentException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $options = new ListenerOptions;
        $options->setExtraConfig('asd');
    }
}
