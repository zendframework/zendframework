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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Application_Module_BootstrapTest::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * Zend_Loader_Autoloader
 */
require_once 'Zend/Loader/Autoloader.php';

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application
 */
class Zend_Application_Module_BootstrapTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        // Store original autoloaders
        $this->loaders = spl_autoload_functions();
        if (!is_array($this->loaders)) {
            // spl_autoload_functions does not return empty array when no
            // autoloaders registered...
            $this->loaders = array();
        }

        Zend_Loader_Autoloader::resetInstance();
        $this->autoloader = Zend_Loader_Autoloader::getInstance();

        $this->application = new Zend_Application('testing');

        Zend_Controller_Front::getInstance()->resetInstance();
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

        // Reset autoloader instance so it doesn't affect other tests
        Zend_Loader_Autoloader::resetInstance();
    }

    public function testConstructorShouldInitializeModuleResourceLoaderWithModulePrefix()
    {
        require_once dirname(__FILE__) . '/../_files/ZfModuleBootstrap.php';
        $bootstrap = new ZfModule_Bootstrap($this->application);
        $module = $bootstrap->getModuleName();
        $loader = $bootstrap->getResourceLoader();
        $this->assertEquals($module, $loader->getNamespace());
    }

    public function testConstructorShouldAcceptResourceLoaderInOptions()
    {
        $loader = new Zend_Loader_Autoloader_Resource(array(
            'namespace' => 'Foo',
            'basePath'  => dirname(__FILE__),
        ));
        $this->application->setOptions(array('resourceLoader' => $loader));

        require_once dirname(__FILE__) . '/../_files/ZfModuleBootstrap.php';
        $bootstrap = new ZfModule_Bootstrap($this->application);
        $this->assertSame($loader, $bootstrap->getResourceLoader(), var_export($bootstrap->getOptions(), 1));
    }

    public function testModuleNameShouldBeFirstSegmentOfClassName()
    {
        require_once dirname(__FILE__) . '/../_files/ZfModuleBootstrap.php';
        $bootstrap = new ZfModule_Bootstrap($this->application);
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
        require_once dirname(__FILE__) . '/../_files/ZfModuleBootstrap.php';
        $bootstrap = new ZfModule_Bootstrap($this->application);
        $this->assertEquals('baz', $bootstrap->foo);
    }

    /**
     * @group ZF-6545
     */
    public function testFrontControllerPluginResourceShouldBeRegistered()
    {
        require_once dirname(__FILE__) . '/../_files/ZfModuleBootstrap.php';
        $bootstrap = new ZfModule_Bootstrap($this->application);
        $this->assertTrue($bootstrap->hasPluginResource('FrontController'));
    }

    /**
     * @group ZF-6545
     */
    public function testFrontControllerStateRemainsSameIfNoOptionsPassedToModuleBootstrap()
    {
        require_once dirname(__FILE__) . '/../_files/ZfModuleBootstrap.php';
        $this->application->setOptions(array(
            'resources' => array(
                'frontController' => array(
                    'baseUrl'             => '/foo',
                    'controllerDirectory' => dirname(__FILE__),
                ),
            ),
            'bootstrap' => array(
                'path'  => dirname(__FILE__) . '/../_files/ZfAppBootstrap.php',
                'class' => 'ZfAppBootstrap',
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
        $bootstrap = new ZfModule_Bootstrap($appBootstrap);
        $bootstrap->bootstrap('FrontController');
        $test = $bootstrap->getResource('FrontController');
        $this->assertSame($front, $test);
        $this->assertEquals('/foo', $test->getBaseUrl());
        $this->assertEquals(dirname(__FILE__), $test->getControllerDirectory('default'));
    }

    /**
     * @group ZF-6545
     */
    public function testModuleBootstrapsShouldNotAcceptModuleResourceInOrderToPreventRecursion()
    {
        require_once dirname(__FILE__) . '/../_files/ZfModuleBootstrap.php';
        $this->application->setOptions(array(
            'resources' => array(
                'modules' => array(),
                'frontController' => array(
                    'baseUrl'             => '/foo',
                    'moduleDirectory'     => dirname(__FILE__) . '/../_files/modules',
                ),
            ),
            'bootstrap' => array(
                'path'  => dirname(__FILE__) . '/../_files/ZfAppBootstrap.php',
                'class' => 'ZfAppBootstrap',
            )
        ));
        $appBootstrap = $this->application->getBootstrap();
        $appBootstrap->bootstrap('Modules');
        $modules = $appBootstrap->getResource('Modules');
        foreach ($modules as $module => $bootstrap) {
            if ($module == 'default') {
                // "default" module gets lumped in, and is not a Module_Bootstrap
                continue;
            }
            $resources = $bootstrap->getPluginResourceNames();
            $this->assertFalse($bootstrap->hasPluginResource('Modules'));
        }
    }

    /**
     * @group ZF-6567
     */
    public function testModuleBootstrapShouldInheritApplicationBootstrapPluginPaths()
    {
        require_once dirname(__FILE__) . '/../_files/ZfModuleBootstrap.php';
        $this->application->setOptions(array(
            'resources' => array(
                'modules' => array(),
                'frontController' => array(
                    'baseUrl'             => '/foo',
                    'moduleDirectory'     => dirname(__FILE__) . '/../_files/modules',
                ),
            ),
            'pluginPaths' => array(
                'ZfModuleBootstrap_Resource' => dirname(__FILE__),
            ),
            'bootstrap' => array(
                'path'  => dirname(__FILE__) . '/../_files/ZfAppBootstrap.php',
                'class' => 'ZfAppBootstrap',
            )
        ));
        $appBootstrap = $this->application->getBootstrap();
        $appBootstrap->bootstrap('Modules');
        $modules = $appBootstrap->getResource('Modules');
        foreach ($modules as $bootstrap) {
            $loader = $bootstrap->getPluginLoader();
            $paths  = $loader->getPaths();
            $this->assertTrue(array_key_exists('ZfModuleBootstrap_Resource_', $paths));
        }
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Application_Module_BootstrapTest::main') {
    Zend_Application_Module_BootstrapTest::main();
}
