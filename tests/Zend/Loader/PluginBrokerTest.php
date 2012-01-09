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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Loader;

use stdClass,
    Zend\Loader\PluginBroker,
    Zend\Loader\PluginClassLoader;

/**
 * @category   Zend
 * @package    Loader
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
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
        $this->assertInstanceOf('Zend\Loader\ShortNameLocator', $loader);
        $this->assertInstanceOf('Zend\Loader\PluginClassLoader', $loader);
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
        $this->assertInstanceOf('ZendTest\Loader\TestAsset\SamplePlugin', $plugin);
    }

    public function testLoadInstantiatesPluginWithPassedOptionsWhenFound()
    {
        $loader = $this->broker->getClassLoader();
        $loader->registerPlugin('sample', 'ZendTest\Loader\TestAsset\SamplePlugin');
        $plugin = $this->broker->load('sample', array('foo'));
        $this->assertInstanceOf('ZendTest\Loader\TestAsset\SamplePlugin', $plugin);
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
        $this->assertInstanceOf('Zend\Loader\PluginBroker', $test);
    }

    public function testLoadingPluginSucceedsWhenPassesValidatorCriteria()
    {
        $loader = $this->broker->getClassLoader();
        $loader->registerPlugin('sample', 'ZendTest\Loader\TestAsset\SamplePlugin');
        $this->broker->setValidator(function($plugin) {
            return ($plugin instanceof TestAsset\SamplePlugin);
        });
        $test = $this->broker->load('sample');
        $this->assertInstanceOf('ZendTest\Loader\TestAsset\SamplePlugin', $test);
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

    public function testCanRetrieveAllLoadedPlugins()
    {
        $this->broker->register('sample', new TestAsset\SamplePlugin());
        $this->broker->load('sample');
        $plugins = $this->broker->getPlugins();
        $this->assertInternalType('array', $plugins);
        $this->assertEquals(1, count($plugins));
    }

    public function testIsLoadedReturnsFalseForUnknownPluginNames()
    {
        $this->assertFalse($this->broker->isLoaded('__foo__'));
    }

    public function testIsLoadedReturnsFalseForUnloadedPlugin()
    {
        $this->broker->getClassLoader()->registerPlugin('sample', 'ZendTest\Loader\TestAsset\SamplePlugin');
        $this->assertFalse($this->broker->isLoaded('sample'));
    }

    public function testIsLoadedReturnsTrueForLoadedPlugin()
    {
        $this->broker->getClassLoader()->registerPlugin('sample', 'ZendTest\Loader\TestAsset\SamplePlugin');
        $this->broker->load('sample');
        $this->assertTrue($this->broker->isLoaded('sample'));
    }

    public function testIsLoadedReturnsTrueForRegisteredPlugin()
    {
        $this->broker->register('sample', new TestAsset\SamplePlugin());
        $this->assertTrue($this->broker->isLoaded('sample'));
    }
    
    public function testRegisterPluginsOnLoadDisabled()
    {
        $this->broker->setRegisterPluginsOnLoad(false);
        
        $loader = $this->broker->getClassLoader();
        $loader->registerPlugin('sample', 'ZendTest\Loader\TestAsset\SamplePlugin');
        
        $plugin1 = $this->broker->load('sample');
        $plugin2 = $this->broker->load('sample');
        
        $this->assertNotSame($plugin1, $plugin2);
    }

    /**
     * Unsure if this is functionality we want to support; requires some form
     * of efficient options hashing, which may negatively impact performance.
     *
     * @group disable
     */
    public function testLoadTakesIntoAccountPassedOptions()
    {
        $loader = $this->broker->getClassLoader();
        $loader->registerPlugin('sample', 'ZendTest\Loader\TestAsset\SamplePlugin');

        $sample1 = $this->broker->load('sample');
        $sample2 = $this->broker->load('sample', 'foo');
        $sample3 = $this->broker->load('sample', array('foo' => 'bar'));

        $this->assertNotSame($sample1, $sample2);
        $this->assertNotSame($sample1, $sample3);
        $this->assertNotSame($sample2, $sample3);

        $this->assertNull($sample1->options);
        $this->assertEquals('foo', $sample2->options);
        $this->assertEquals(array('foo' => 'bar'), $sample3->options);
    }

    public function testAllowsConfigurationViaConstructor()
    {
        $validator = function($plugin) {
            return true;
        };
        $broker = new PluginBroker(array(
            'class_loader' => array(
                'class'   => 'Zend\Loader\PrefixPathLoader',
                'options' => array(
                    'ZendTest\UnusualNamespace' => __DIR__ . '/TestAsset',
                )
            ),
            'plugins'      => array(
                'test' => $this,
            ),
            'validator'    => $validator,
        ));

        $loader = $broker->getClassLoader();
        $this->assertInstanceOf('Zend\Loader\PrefixPathLoader', $loader);
        $this->assertEquals('ZendTest\UnusualNamespace\ClassMappedClass', $loader->load('ClassMappedClass'));

        $this->assertTrue($broker->isLoaded('test'));
        $this->assertSame($validator, $broker->getValidator());

        $broker = new PluginBroker(array(
            'class_loader' => 'ZendTest\Loader\TestAsset\CustomClassLoader',
        ));
        $loader = $broker->getClassLoader();
        $this->assertInstanceOf('ZendTest\Loader\TestAsset\CustomClassLoader', $loader);
    }

    public function testWillPullFromLocatorIfAttached()
    {
        $locator = new TestAsset\ServiceLocator();
        $plugin  = new stdClass;
        $locator->set('ZendTest\Loader\TestAsset\Foo', $plugin);

        $loader = $this->broker->getClassLoader();
        $loader->registerPlugin('foo', 'ZendTest\Loader\TestAsset\Foo');
        $this->broker->setLocator($locator);

        $test = $this->broker->load('foo');
        $this->assertSame($plugin, $test);
    }
}
