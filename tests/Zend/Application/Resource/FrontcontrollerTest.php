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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

namespace ZendTest\Application\Resource;

use Zend\Controller\Front as FrontController,
    Zend\Loader\Autoloader,
    ZendTest\Application\TestAsset\ZfAppBootstrap,
    Zend\Application\Application,
    Zend\Application\Resource\Frontcontroller as FrontcontrollerResource;

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application
 */
class FrontcontrollerTest extends \PHPUnit_Framework_TestCase
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

        Autoloader::resetInstance();
        $this->autoloader = Autoloader::getInstance();

        $this->application = new Application('testing');

        $this->bootstrap = new ZfAppBootstrap($this->application);
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

        FrontController::getInstance()->resetInstance();

        // Reset autoloader instance so it doesn't affect other tests
        Autoloader::resetInstance();
    }

    public function testInitializationCreatesFrontControllerInstance()
    {
        $resource = new FrontcontrollerResource(array());
        $resource->init();
        $this->assertTrue($resource->getFrontController() instanceof FrontController);
    }

    public function testInitializationPushesFrontControllerToBootstrapWhenPresent()
    {
        $resource = new FrontcontrollerResource(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->init();
        $this->assertSame($resource->getFrontController(), $this->bootstrap->frontController);
    }

    public function testShouldSetControllerDirectoryWhenStringOptionPresent()
    {
        $resource = new FrontcontrollerResource(array(
            'controllerDirectory' => __DIR__,
        ));
        $resource->init();
        $front = $resource->getFrontController();
        $dir   = $front->getControllerDirectory('application');
        $this->assertEquals(__DIR__, $dir);
    }

    public function testShouldSetControllerDirectoryWhenArrayOptionPresent()
    {
        $resource = new FrontcontrollerResource(array(
            'controllerDirectory' => array(
                'foo' => __DIR__,
            ),
        ));
        $resource->init();
        $front = $resource->getFrontController();
        $dir   = $front->getControllerDirectory('foo');
        $this->assertEquals(__DIR__, $dir);
    }

    /**
     * @group ZF-6458
     */
    public function testAllControllerDirectoriesShouldBeSetWhenArrayPassedToControllerDirectoryOption()
    {
        $resource = new FrontcontrollerResource(array(
            'controllerDirectory' => array(
                'foo' => __DIR__,
                'bar' => __DIR__,
            ),
        ));
        $resource->init();
        $front = $resource->getFrontController();
        $dirs  = $front->getControllerDirectory();
        $this->assertEquals(array(
            'foo' => __DIR__,
            'bar' => __DIR__,
        ), $dirs);
    }

    public function testShouldSetModuleControllerDirectoryNameWhenOptionPresent()
    {
        $resource = new FrontcontrollerResource(array(
            'moduleControllerDirectoryName' => 'foo',
        ));
        $resource->init();
        $front = $resource->getFrontController();
        $dir   = $front->getModuleControllerDirectoryName();
        $this->assertEquals('foo', $dir);
    }

    public function testShouldSetModuleDirectoryWhenOptionPresent()
    {
        $resource = new FrontcontrollerResource(array(
            'moduleDirectory' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                               . 'TestAsset' . DIRECTORY_SEPARATOR . 'modules',
        ));
        $resource->init();
        $front = $resource->getFrontController();
        $dir   = $front->getControllerDirectory();
        $expected = array(
            'bar'     => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                       . 'TestAsset' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR
                       . 'bar' . DIRECTORY_SEPARATOR . 'controllers',
            'application' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                       . 'TestAsset' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR
                       . 'application' . DIRECTORY_SEPARATOR . 'controllers',
            'foo-bar' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                       . 'TestAsset' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR
                       . 'foo-bar' . DIRECTORY_SEPARATOR . 'controllers',
            'foo'     => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                       . 'TestAsset' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR
                       . 'foo' . DIRECTORY_SEPARATOR . 'controllers',
            'baz'     => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                       . 'TestAsset' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR
                       . 'baz' . DIRECTORY_SEPARATOR . 'controllers',
            'zfappbootstrap' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                              . 'TestAsset' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR
                              . 'zfappbootstrap' . DIRECTORY_SEPARATOR . 'controllers',
        );
        $this->assertEquals($expected, $dir);
    }

    public function testShouldSetDefaultControllerNameWhenOptionPresent()
    {
        $resource = new FrontcontrollerResource(array(
            'defaultControllerName' => 'foo',
        ));
        $resource->init();
        $front = $resource->getFrontController();
        $test  = $front->getDefaultControllerName();
        $this->assertEquals('foo', $test);
    }

    public function testShouldSetDefaultActionWhenOptionPresent()
    {
        $resource = new FrontcontrollerResource(array(
            'defaultAction' => 'foo',
        ));
        $resource->init();
        $front = $resource->getFrontController();
        $test  = $front->getDefaultAction();
        $this->assertEquals('foo', $test);
    }

    public function testShouldSetDefaultModuleWhenOptionPresent()
    {
        $resource = new FrontcontrollerResource(array(
            'defaultModule' => 'foo',
        ));
        $resource->init();
        $front = $resource->getFrontController();
        $test  = $front->getDefaultModule();
        $this->assertEquals('foo', $test);
    }

    public function testShouldSetBaseUrlWhenOptionPresent()
    {
        $resource = new FrontcontrollerResource(array(
            'baseUrl' => '/foo',
        ));
        $resource->init();
        $front = $resource->getFrontController();
        $test  = $front->getBaseUrl();
        $this->assertEquals('/foo', $test);
    }

    public function testShouldSetParamsWhenOptionPresent()
    {
        $params = array(
            'foo' => 'bar',
            'bar' => 'baz',
        );
        $resource = new FrontcontrollerResource(array(
            'params' => $params,
        ));
        $resource->init();
        $front = $resource->getFrontController();
        $test  = $front->getParams();
        $this->assertEquals($params, $test);
    }

    public function testShouldInstantiateAndRegisterPluginsWhenOptionPassed()
    {
        $plugins = array(
            'Zend\\Controller\\Plugin\\ActionStack',
        );
        $resource = new FrontcontrollerResource(array(
            'plugins' => $plugins,
        ));
        $resource->init();
        $front = $resource->getFrontController();
        foreach ($plugins as $class) {
            $this->assertTrue($front->hasPlugin($class));
        }
    }

    public function testShouldReturnFrontControllerWhenComplete()
    {
        $resource = new FrontcontrollerResource(array(
            'controllerDirectory' => __DIR__,
        ));
        $front = $resource->init();
        $this->assertTrue($front instanceof FrontController);
    }

    public function testNoBaseUrlShouldBeSetIfEmptyBaseUrlProvidedInOptions()
    {
        $resource = new FrontcontrollerResource(array(
            'baseurl' => '',
        ));
        $resource->init();
        $front = $resource->getFrontController();
        $this->assertNull($front->getBaseUrl());
    }
    
    /**
     * @group ZF-9044
     */
    public function testSettingOfRegisterPluginIndexActuallyWorks()
    {
        $plugins = array(
            array('class' => 'Zend\Controller\Plugin\ErrorHandler',
                  'stackindex' => 10),
            'Zend\Controller\Plugin\ActionStack',
            array('class' => 'Zend\Controller\Plugin\PutHandler',
                  'stackIndex' => 5),
        );

        $expected = array(
            1 => 'Zend\Controller\Plugin\ActionStack',
            5 => 'Zend\Controller\Plugin\PutHandler',
            10 => 'Zend\Controller\Plugin\ErrorHandler',
        );
        
        $resource = new FrontcontrollerResource(array(
            'plugins' => $plugins
        ));

        $resource->init();
        $front = $resource->getFrontController();
        $plugins = $front->getPlugins();
        
        $this->assertEquals(count($expected), count($plugins));
        foreach($expected as $index => $class) {
        	$this->assertEquals($class, get_class($plugins[$index]));
        }
    }

    /**
     * @group ZF-7367
     */
    public function testPassingReturnResponseFlagShouldAlterFrontControllerStatus()
    {
        $resource = new FrontcontrollerResource(array(
            'returnresponse' => true,
        ));
        $resource->init();
        $front = $resource->getFrontController();
        $this->assertTrue($front->returnResponse());
    }
}
