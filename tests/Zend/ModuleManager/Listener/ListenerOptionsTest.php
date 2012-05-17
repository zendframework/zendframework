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
 * @package    Zend_ModuleManager
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\ModuleManager\Listener;

use BadMethodCallException;
use InvalidArgumentException;
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
            'module_paths'            => array('foo','bar'),
        ));
        $this->assertSame($options->getCacheDir(), __DIR__);
        $this->assertTrue($options->getConfigCacheEnabled());
        $this->assertNotNull(strstr($options->getConfigCacheFile(), __DIR__));
        $this->assertNotNull(strstr($options->getConfigCacheFile(), '.php'));
        $this->assertSame('foo', $options->getConfigCacheKey());
        $this->assertSame(array('foo', 'bar'), $options->getModulePaths());
    }

    public function testCanAccessKeysAsProperties()
    {
        $options = new ListenerOptions(array(
            'cache_dir'               => __DIR__,
            'config_cache_enabled'    => true,
            'config_cache_key'        => 'foo',
            'module_paths'            => array('foo','bar'),
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
        $this->assertSame(array('foo', 'bar'), $options->module_paths);
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
}
