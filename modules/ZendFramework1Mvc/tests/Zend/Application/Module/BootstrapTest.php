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
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Application\Module;

use Zend\Application\Module\Bootstrap as ModuleBootstrap,
    Zend\Loader\ResourceAutoloader,
    Zend\Application\Application,
    Zend\Controller\Front as FrontController,
    ZendTest\Application\TestAsset\ZfModule_Bootstrap;

require_once __DIR__ . '/../TestAsset/ZfModuleBootstrap.php';

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application
 */
class BootstrapTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // Store original autoloaders
        $this->loaders = spl_autoload_functions();
        if (!is_array($this->loaders)) {
            // spl_autoload_functions does not return empty array when no
            // autoloaders registered...
            $this->loaders = array();
        }

        $this->application = new Application('testing');

        FrontController::getInstance()->resetInstance();
    }

    public function tearDown()
    {
        // Restore original autoloaders
        $loaders = spl_autoload_functions();
        foreach ($loaders as $loader) {
            spl_autoload_unregister($loader);
        }

        foreach ($this->loaders as $loader) {
            spl_autoload_register($loader);
        }
    }

    public function testConstructorShouldInitializeModuleResourceLoaderWithModulePrefix()
    {
        $bootstrap = new \ZfModule\Bootstrap($this->application);
        $module = $bootstrap->getModuleName();
        $loader = $bootstrap->getResourceLoader();
        $this->assertNotNull($loader, "resource loader is unexpectedly NULL");
        $this->assertEquals($module, $loader->getNamespace());
    }

    public function testConstructorShouldAcceptResourceLoaderInOptions()
    {
        $loader = new ResourceAutoloader(array(
            'namespace' => 'Foo',
            'basePath'  => __DIR__,
        ));
        $this->application->setOptions(array('resourceLoader' => $loader));

        $bootstrap = new \ZfModule\Bootstrap($this->application);
        $this->assertSame($loader, $bootstrap->getResourceLoader(), var_export($bootstrap->getOptions(), 1));
    }

    public function testModuleNameShouldBeFirstSegmentOfClassName()
    {
        $bootstrap = new \ZfModule\Bootstrap($this->application);
        $this->assertEquals('ZfModule', $bootstrap->getModuleName());
    }

    public function testShouldPullModuleNamespacedOptionsWhenPresent()
    {
        $options = array(
            'foo' => 'bar',
            'ZfModule' => array(
                'foo' => 'baz',
            )
        );
        $this->application->setOptions($options);
        $bootstrap = new \ZfModule\Bootstrap($this->application);
        $this->assertEquals('baz', $bootstrap->foo);
    }

    /**
     * @group ZF-6545
     */
    public function testFrontControllerPluginResourceShouldBeRegistered()
    {
        $bootstrap = new \ZfModule\Bootstrap($this->application);
        $this->assertTrue($bootstrap->getBroker()->hasPlugin('FrontController'));
    }

    /**
     * @group ZF-6545
     */
    public function testFrontControllerStateRemainsSameIfNoOptionsPassedToModuleBootstrap()
    {
        $this->application->setOptions(array(
            'resources' => array(
                'frontController' => array(
                    'baseUrl'             => '/foo',
                    'controllerDirectory' => __DIR__,
                ),
            ),
            'bootstrap' => array(
                'path'  => __DIR__ . '/../TestAsset/ZfAppBootstrap.php',
                'class' => 'ZendTest\\Application\\TestAsset\\ZfAppBootstrap',
            ),
            'ZfModule' => array(
                'resources' => array(
                    'FrontController' => array(),
                ),
            ),
        ));
        $appBootstrap = $this->application->getBootstrap();
        $appBootstrap->bootstrap('FrontController');
        $front = $appBootstrap->getResource('FrontController');
        $bootstrap = new \ZfModule\Bootstrap($appBootstrap);
        $bootstrap->bootstrap('FrontController');
        $test = $bootstrap->getResource('FrontController');
        $this->assertSame($front, $test);
        $this->assertEquals('/foo', $test->getBaseUrl());
        $this->assertEquals(__DIR__, $test->getControllerDirectory('application'));
    }

    /**
     * @group ZF-6545
     */
    public function testModuleBootstrapsShouldNotAcceptModuleResourceInOrderToPreventRecursion()
    {
        $this->application->setOptions(array(
            'resources' => array(
                'modules' => array(),
                'frontController' => array(
                    'baseUrl'             => '/foo',
                    'moduleDirectory'     => __DIR__ . '/../TestAsset/modules',
                ),
            ),
            'bootstrap' => array(
                'path'  => __DIR__ . '/../TestAsset/ZfAppBootstrap.php',
                'class' => 'ZendTest\\Application\\TestAsset\\ZfAppBootstrap',
            )
        ));
        $appBootstrap = $this->application->getBootstrap();
        $appBootstrap->bootstrap('Modules');
        $modules = $appBootstrap->getResource('Modules');
        foreach ($modules as $module => $bootstrap) {
            if (in_array($module, array('application', 'default'))) {
                // "default" module gets lumped in, and is not a Module_Bootstrap
                continue;
            }
            $this->assertFalse($bootstrap->getBroker()->hasPlugin('Modules'));
        }
    }

    /**
     * @group ZF-6567
     */
    public function testModuleBootstrapShouldInheritApplicationBootstrapPluginBroker()
    {
        $this->application->setOptions(array(
            'resources' => array(
                'modules' => array(),
                'frontController' => array(
                    'baseUrl'             => '/foo',
                    'moduleDirectory'     => __DIR__ . '/../TestAsset/modules',
                ),
            ),
            'pluginPaths' => array(
                'ZfModule\\Bootstrap\\Resource' => __DIR__,
            ),
            'bootstrap' => array(
                'path'  => __DIR__ . '/../TestAsset/ZfAppBootstrap.php',
                'class' => 'ZendTest\\Application\\TestAsset\\ZfAppBootstrap',
            )
        ));
        $appBootstrap = $this->application->getBootstrap();
        $appBroker    = $appBootstrap->getBroker();
        $appBootstrap->bootstrap('Modules');
        $modules = $appBootstrap->getResource('Modules');
        foreach ($modules as $bootstrap) {
            // Skip the default bootstrap
            if ('Bootstrap' === get_class($bootstrap)) {
                continue;
            }

            $broker = $bootstrap->getBroker();
            $this->assertSame($appBroker, $broker);
        }
    }
}
