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
 * @package    Loader
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Loader;

use Zend\Loader\PluginBroker,
    Zend\Loader\PluginClassLoader;

/**
 * @category   Zend
 * @package    Loader
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Loader
 */
class PluginBrokerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->broker = new PluginBroker();
    }

    public function testUsesEmptyPluginClassLoaderByDefault()
    {
        $loader = $this->broker->getClassLoader();
        $this->assertType('Zend\Loader\ShortNameLocater', $loader);
        $this->assertType('Zend\Loader\PluginClassLoader', $loader);
        $this->assertEquals(array(), $loader->getRegisteredPlugins());
    }

    public function testCanSpecifyPluginClassLoader()
    {
        $loader = new PluginClassLoader();
        $this->broker->setClassLoader($loader);
        $this->assertSame($loader, $this->broker->getClassLoader());
    }

    public function testLoadThrowsExceptionIfPluginNotFound()
    {
        $this->setExpectedException('Zend\Loader\Exception\RuntimeException');
        $this->broker->load('_foo_');
    }

    public function testLoadInstantiatesAndReturnsPluginWhenFound()
    {
        $loader = $this->broker->getClassLoader();
        $loader->registerPlugin('sample', 'ZendTest\Loader\TestAsset\SamplePlugin');
        $plugin = $this->broker->load('sample');
        $this->assertType('ZendTest\Loader\TestAsset\SamplePlugin', $plugin);
    }

    public function testLoadInstantiatesPluginWithPassedOptionsWhenFound()
    {
        $loader = $this->broker->getClassLoader();
        $loader->registerPlugin('sample', 'ZendTest\Loader\TestAsset\SamplePlugin');
        $plugin = $this->broker->load('sample', array('foo'));
        $this->assertType('ZendTest\Loader\TestAsset\SamplePlugin', $plugin);
        $this->assertEquals('foo', $plugin->options);
    }

    public function testCanRegisterPluginInstanceByNameExplicitly()
    {
        $sample = new TestAsset\SamplePlugin;
        $this->broker->register('sample', $sample);
        $test = $this->broker->load('sample');
        $this->assertSame($sample, $test);
    }

    public function testLoadingSamePluginMultipleTimesReturnsSameInstance()
    {
        $sample = new TestAsset\SamplePlugin;
        $this->broker->register('sample', $sample);
        $test1 = $this->broker->load('sample');
        $test2 = $this->broker->load('sample');
        $this->assertSame($test1, $test2);
    }

    public function testUnregisteringPluginInstanceForcesNewInstanceOnNextLoad()
    {
        $loader = $this->broker->getClassLoader();
        $loader->registerPlugin('sample', 'ZendTest\Loader\TestAsset\SamplePlugin');
        $plugin1 = $this->broker->load('sample');
        $this->broker->unregister('sample');
        $plugin2 = $this->broker->load('sample');
        $this->assertNotSame($plugin1, $plugin2);
    }

    public function testSettingValidatorRaisesExceptionForInvalidCallback()
    {
        $this->setExpectedException('Zend\Loader\Exception\InvalidArgumentException');
        $this->broker->setValidator('__bogus__');
    }

    public function testAllowsRegisteringPluginValidatorCallback()
    {
        $this->broker->setValidator('array_key_exists');
        $this->assertEquals('array_key_exists', $this->broker->getValidator());
    }

    public function testRegisteringPluginThatFailsValidatorRaisesException()
    {
        $this->broker->setValidator(function($plugin) {
            return ($plugin instanceof PluginBrokerTest);
        });
        $this->setExpectedException('Zend\Loader\Exception\RuntimeException');
        $this->broker->register('sample', new TestAsset\SamplePlugin());
    }

    public function testLoadingPluginThatFailsValidatorRaisesException()
    {
        $loader = $this->broker->getClassLoader();
        $loader->registerPlugin('sample', 'ZendTest\Loader\TestAsset\SamplePlugin');
        $this->broker->setValidator(function($plugin) {
            return ($plugin instanceof PluginBrokerTest);
        });
        $this->setExpectedException('Zend\Loader\Exception\RuntimeException');
        $this->broker->load('sample');
    }

    public function testRegisteringPluginSucceedsWhenPassesValidatorCriteria()
    {
        $this->broker->setValidator(function($plugin) {
            return ($plugin instanceof TestAsset\SamplePlugin);
        });
        $test = $this->broker->register('sample', new TestAsset\SamplePlugin());
        $this->assertType('Zend\Loader\PluginBroker', $test);
    }

    public function testLoadingPluginSucceedsWhenPassesValidatorCriteria()
    {
        $loader = $this->broker->getClassLoader();
        $loader->registerPlugin('sample', 'ZendTest\Loader\TestAsset\SamplePlugin');
        $this->broker->setValidator(function($plugin) {
            return ($plugin instanceof TestAsset\SamplePlugin);
        });
        $test = $this->broker->load('sample');
        $this->assertType('ZendTest\Loader\TestAsset\SamplePlugin', $test);
    }

    public function testPluginNamesAreCaseInsensitive()
    {
        $loader = $this->broker->getClassLoader();
        $loader->registerPlugin('sample', 'ZendTest\Loader\TestAsset\SamplePlugin');
        $plugin1 = $this->broker->load('sample');
        $plugin2 = $this->broker->load('Sample');
        $this->assertSame($plugin1, $plugin2);
    }

    public function testExplicitlyRegisteredPluginNamesAreCaseInsensitive()
    {
        $test = $this->broker->register('sample', new TestAsset\SamplePlugin());
        $plugin1 = $this->broker->load('sample');
        $plugin2 = $this->broker->load('Sample');
        $this->assertSame($plugin1, $plugin2);
    }
}
