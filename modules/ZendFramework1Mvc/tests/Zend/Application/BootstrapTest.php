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

namespace ZendTest\Application;

use Zend\Application,
    Zend\Controller\Front as FrontController,
    Zend\Controller\Request\HttpTestCase as HttpRequestTestCase,
    Zend\Controller\Response\HttpTestCase as HttpResponseTestCase;

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

        $this->application = new Application\Application('testing');
        $this->bootstrap   = new Application\Bootstrap($this->application);

        $this->resetFrontController();
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

    public function resetFrontController()
    {
        $front = FrontController::getInstance();
        $front->resetInstance();
        $front->setRequest(new HttpRequestTestCase)
              ->setResponse(new HttpResponseTestCase);
    }

    public function testFrontControllerResourcePluginShouldBeRegisteredByDefault()
    {
        $this->assertTrue($this->bootstrap->getBroker()->hasPlugin('frontcontroller'));
    }

    public function testRunShouldRaiseExceptionIfNoControllerDirectoryRegisteredWithFrontController()
    {
        $this->setExpectedException('Zend\Application\Exception\RuntimeException');
        $this->bootstrap->bootstrap();
        $this->bootstrap->run();
    }

    /**
     * @todo  Re-enable test once all elements of MVC are working
     * @group disable
     */
    public function testRunShouldDispatchFrontController()
    {
        $this->bootstrap->setOptions(array(
            'resources' => array(
                'frontcontroller' => array(
                    'moduleDirectory' => __DIR__ . '/TestAsset/modules',
                ),
            ),
        ));
        $this->bootstrap->bootstrap();

        $front   = $this->bootstrap->getResource('frontcontroller');

        $request = $front->getRequest();
        $request->setRequestUri('/zfappbootstrap');
        $this->bootstrap->run();

        $this->assertTrue($this->bootstrap->getContainer()->zfappbootstrap);
    }

    /**
     * @group ZF-8496
     */
    public function testBootstrapModuleAutoloaderShouldNotBeInitializedByDefault()
    {
        $this->assertFalse($this->bootstrap->getResourceLoader() instanceof Application\Module\Autoloader);
    }

    /**
     * @group ZF-8496
     */
    public function testBootstrapShouldInitializeModuleAutoloaderWhenNamespaceSpecified()
    {
        $application = new Application\Application('testing', array(
            'appnamespace' => 'Application',
        ));
        $bootstrap   = new Application\Bootstrap($application);
        $this->assertTrue($bootstrap->getResourceLoader() instanceof Application\Module\Autoloader);
        $al = $bootstrap->getResourceLoader();
        $this->assertEquals('Application', $al->getNamespace());
    }

    /**
     * @group ZF-8496
     */
    public function testBootstrapAutoloaderNamespaceShouldBeConfigurable()
    {
        $application = new Application\Application('testing', array(
            'appnamespace' => 'Default',
        ));
        $bootstrap   = new Application\Bootstrap($application);
        $al = $bootstrap->getResourceLoader();
        $this->assertEquals('Default', $al->getNamespace());
    }

    /**
     * @group ZF-7367
     */
    public function testBootstrapRunMethodShouldReturnResponseIfFlagEnabled()
    {
        $this->bootstrap->setOptions(array(
            'resources' => array(
                'frontcontroller' => array(
                    'moduleDirectory' => __DIR__ . '/TestAsset/modules',
                    'returnresponse'  => true,
                ),
            ),
        ));
        $this->bootstrap->bootstrap();

        $front   = $this->bootstrap->getResource('frontcontroller');
        $request = $front->getRequest();
        $request->setRequestUri('/zfappbootstrap');

        $result = $this->bootstrap->run();
        $this->assertTrue($result instanceof \Zend\Controller\Response\AbstractResponse);
    }

    public function testFrontControllerSpecShouldNotBeOverwrittenByBootstrap()
    {
        $application = new Application\Application('testing', array(
            'resources' => array(
                'frontcontroller' => array(
                    'controllerDirectory' => __DIR__ . '/TestAsset/modules/application/controllers',
                    'moduleDirectory' => __DIR__ . '/TestAsset/modules',
                ),
                'modules' => array(),
            ),
        ));
        $bootstrap = new Application\Bootstrap($application);
        $bootstrap->bootstrap();
        $front  = $bootstrap->getResource('frontcontroller');
        $module = $front->getDefaultModule();
        $dir    = $front->getControllerDirectory($module);
        $this->assertNotNull($dir);
    }
}
