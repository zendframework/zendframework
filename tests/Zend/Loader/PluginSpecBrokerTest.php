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

use Zend\Loader\PluginSpecBroker,
    Zend\Loader\PluginClassLoader;

/**
 * @category   Zend
 * @package    Loader
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Loader
 */
class PluginSpecBrokerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->broker = new PluginSpecBroker();
    }

    public function testRegisteringSpecsReflectedWhenRetrievingRegisteredPlugins()
    {
        $this->broker->registerSpec('test', array('foo', 'bar'));
        $plugins = $this->broker->getRegisteredPlugins();
        $this->assertContains('test', $plugins);
    }

    public function testRegisteredPluginsIncludeBothLoadedPluginsAndSpecsForUnloadedPlugins()
    {
        $this->broker->getClassLoader()->registerPlugin('sample', 'ZendTest\Loader\TestAsset\SamplePlugin');
        $this->broker->registerSpec('test', array('foo', 'bar'));
        $this->broker->load('sample');
        $plugins = $this->broker->getRegisteredPlugins();
        $this->assertContains('sample', $plugins);
        $this->assertContains('test', $plugins);
    }

    public function testHasPluginReturnsFalseForUnloadedPlugin()
    {
        $this->broker->getClassLoader()->registerPlugin('sample', 'ZendTest\Loader\TestAsset\SamplePlugin');
        $this->assertFalse($this->broker->hasPlugin('sample'));
    }

    public function testHasPluginReturnsTrueForLoadedPlugins()
    {
        $this->broker->getClassLoader()->registerPlugin('sample', 'ZendTest\Loader\TestAsset\SamplePlugin');
        $this->broker->load('sample');
        $this->assertTrue($this->broker->hasPlugin('sample'));
    }

    public function testHasPluginReturnsTrueForRegisteredSpec()
    {
        $this->broker->registerSpec('test', array('foo', 'bar'));
        $this->assertTrue($this->broker->hasPlugin('test'));
    }

    public function testHasPluginReturnsFalseForUnknownPlugin()
    {
        $this->assertFalse($this->broker->hasPlugin('sample'));
    }

    public function testPassingNullValueToPluginArgumentOfRegisterRegistersSpec()
    {
        $this->broker->getClassLoader()->registerPlugin('sample', 'ZendTest\Loader\TestAsset\SamplePlugin');
        $this->broker->register('sample', null);
        $this->assertTrue($this->broker->hasPlugin('sample'));
        $this->assertFalse($this->broker->isLoaded('sample'));
    }

    public function invalidSpecs()
    {
        return array(
            array(null),
            array(true),
            array(false),
            array(1),
            array(1.0),
            array('string'),
            array(new \stdClass),
        );
    }

    /**
     * @dataProvider invalidSpecs
     */
    public function testPassingInvalidArgumentToRegisterSpecsRaisesException($specs)
    {
        $this->setExpectedException('Zend\Loader\Exception\InvalidArgumentException');
        $this->broker->registerSpecs($specs);
    }

    public function testPassingArrayOfSpecsRegistersEachSpecification()
    {
        $specs = array(
            'test'   => null,
            'sample' => array('foo', 'bar'),
        );

        $this->broker->registerSpecs($specs);
        $this->assertTrue($this->broker->hasPlugin('test'));
        $this->assertTrue($this->broker->hasPlugin('sample'));
    }

    public function testPassingTraversableObjectOfSpecsRegistersEachSpecification()
    {
        $specs = array(
            'test'   => null,
            'sample' => array('foo', 'bar'),
        );

        $this->broker->registerSpecs(new \ArrayIterator($specs));
        $this->assertTrue($this->broker->hasPlugin('test'));
        $this->assertTrue($this->broker->hasPlugin('sample'));
    }

    public function testLoadUsesRegisteredSpecFirstTimePluginIsLoaded()
    {
        $this->broker->getClassLoader()->registerPlugin('sample', 'ZendTest\Loader\TestAsset\SamplePlugin');
        $this->broker->registerSpec('sample', array(array('foo' => 'bar')));
        $plugin = $this->broker->load('sample');
        $this->assertInstanceOf('ZendTest\Loader\TestAsset\SamplePlugin', $plugin);
        $this->assertEquals(array('foo' => 'bar'), $plugin->options);
    }

    public function testLoadUsesPreviousInstanceOnSubsequentRequestsForPluginEvenWithDifferentOptions()
    {
        $this->broker->getClassLoader()->registerPlugin('sample', 'ZendTest\Loader\TestAsset\SamplePlugin');
        $this->broker->registerSpec('sample', array(array('foo' => 'bar')));
        $plugin  = $this->broker->load('sample');
        $plugin2 = $this->broker->load('sample', array('bar' => 'baz'));
        $this->assertSame($plugin, $plugin2);
    }

    public function testRegisteringPluginTakesPrecedenceOverSpecifications()
    {
        $this->broker->getClassLoader()->registerPlugin('sample', 'ZendTest\Loader\TestAsset\SamplePlugin');
        $this->broker->registerSpec('sample', array(array('foo' => 'bar')));
        $plugin = new TestAsset\SamplePlugin(array('bar' => 'baz'));
        $this->broker->register('sample', $plugin);
        $plugin2  = $this->broker->load('sample');
        $this->assertSame($plugin, $plugin2);
    }

    public function testPassingOptionsOverridesSpecIfPluginNotPreviouslyLoaded()
    {
        $this->broker->getClassLoader()->registerPlugin('sample', 'ZendTest\Loader\TestAsset\SamplePlugin');
        $this->broker->registerSpec('sample', array(array('foo' => 'bar')));
        $plugin = $this->broker->load('sample', array(array('bar' => 'baz')));
        $this->assertEquals(array('bar' => 'baz'), $plugin->options);
    }

    public function testAllowsUnregisteringSpecificationsIndividually()
    {
        $this->broker->registerSpec('sample', array('foo'));
        $this->broker->unregisterSpec('sample');
        $this->assertFalse($this->broker->hasPlugin('sample'));
    }

    public function testAllowsConfigurationViaConstructor()
    {
        $validator = function($plugin) {
            return true;
        };
        $broker = new PluginSpecBroker(array(
            'class_loader' => array(
                'class'   => 'Zend\Loader\PrefixPathLoader',
                'options' => array(
                    'ZendTest\UnusualNamespace' => __DIR__ . '/TestAsset',
                )
            ),
            'specs'        => array(
                'ClassMappedClass' => array(array('foo' => 'bar')),
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

        $plugin = $broker->load('ClassMappedClass');
        $this->assertInstanceOf('ZendTest\UnusualNamespace\ClassMappedClass', $plugin);
        $this->assertEquals(array('foo' => 'bar'), $plugin->options);

        $broker = new PluginSpecBroker(array(
            'class_loader' => 'ZendTest\Loader\TestAsset\CustomClassLoader',
        ));
        $loader = $broker->getClassLoader();
        $this->assertInstanceOf('ZendTest\Loader\TestAsset\CustomClassLoader', $loader);
    }
}
