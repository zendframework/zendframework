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

namespace ZendTest\Application\Resource;

use Zend\Controller\Front as FrontController,
    Zend\Loader\Autoloader,
    ZendTest\Application\TestAsset\ZfAppBootstrap,
    Zend\Application\Application,
    Zend\Application\Resource\FrontController as FrontControllerResource;

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application
 */
class FrontcontrollerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->application = new Application('testing');

        $this->bootstrap = new ZfAppBootstrap($this->application);
    }

    public function tearDown()
    {
        FrontController::getInstance()->resetInstance();
    }

    public function testInitializationCreatesFrontControllerInstance()
    {
        $resource = new FrontControllerResource(array());
        $resource->init();
        $this->assertTrue($resource->getFrontController() instanceof FrontController);
    }

    public function testInitializationPushesFrontControllerToBootstrapWhenPresent()
    {
        $resource = new FrontControllerResource(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->init();
        $this->assertSame($resource->getFrontController(), $this->bootstrap->frontController);
    }

    public function testShouldSetControllerDirectoryWhenStringOptionPresent()
    {
        $resource = new FrontControllerResource(array(
            'controllerDirectory' => __DIR__,
        ));
        $resource->init();
        $front = $resource->getFrontController();
        $dir   = $front->getControllerDirectory('application');
        $this->assertEquals(__DIR__, $dir);
    }

    public function testShouldSetControllerDirectoryWhenArrayOptionPresent()
    {
        $resource = new FrontControllerResource(array(
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
        $resource = new FrontControllerResource(array(
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
        $resource = new FrontControllerResource(array(
            'moduleControllerDirectoryName' => 'foo',
        ));
        $resource->init();
        $front = $resource->getFrontController();
        $dir   = $front->getModuleControllerDirectoryName();
        $this->assertEquals('foo', $dir);
    }

    public function testShouldSetModuleDirectoryWhenOptionPresent()
    {
        $resource = new FrontControllerResource(array(
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
            'zf2-30-module1' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                       . 'TestAsset' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR
                       . 'zf2-30-module1' . DIRECTORY_SEPARATOR . 'controllers',
            'zf2-30-module2' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                       . 'TestAsset' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR
                       . 'zf2-30-module2' . DIRECTORY_SEPARATOR . 'controllers',
            'zfappbootstrap' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                       . 'TestAsset' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR
                       . 'zfappbootstrap' . DIRECTORY_SEPARATOR . 'controllers',
        );
        $this->assertEquals($expected, $dir);
    }

    public function testShouldSetDefaultControllerNameWhenOptionPresent()
    {
        $resource = new FrontControllerResource(array(
            'defaultControllerName' => 'foo',
        ));
        $resource->init();
        $front = $resource->getFrontController();
        $test  = $front->getDefaultControllerName();
        $this->assertEquals('foo', $test);
    }

    public function testShouldSetDefaultActionWhenOptionPresent()
    {
        $resource = new FrontControllerResource(array(
            'defaultAction' => 'foo',
        ));
        $resource->init();
        $front = $resource->getFrontController();
        $test  = $front->getDefaultAction();
        $this->assertEquals('foo', $test);
    }

    public function testShouldSetDefaultModuleWhenOptionPresent()
    {
        $resource = new FrontControllerResource(array(
            'defaultModule' => 'foo',
        ));
        $resource->init();
        $front = $resource->getFrontController();
        $test  = $front->getDefaultModule();
        $this->assertEquals('foo', $test);
    }

    public function testShouldSetBaseUrlWhenOptionPresent()
    {
        $resource = new FrontControllerResource(array(
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
        $resource = new FrontControllerResource(array(
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
        $resource = new FrontControllerResource(array(
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
        $resource = new FrontControllerResource(array(
            'controllerDirectory' => __DIR__,
        ));
        $front = $resource->init();
        $this->assertTrue($front instanceof FrontController);
    }

    public function testNoBaseUrlShouldBeSetIfEmptyBaseUrlProvidedInOptions()
    {
        $resource = new FrontControllerResource(array(
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

        $resource = new FrontControllerResource(array(
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
        $resource = new FrontControllerResource(array(
            'returnresponse' => true,
        ));
        $resource->init();
        $front = $resource->getFrontController();
        $this->assertTrue($front->returnResponse());
    }
}
