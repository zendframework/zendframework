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
    define('PHPUnit_MAIN_METHOD', 'Zend_Application_Bootstrap_BootstrapTest::main');
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
class Zend_Application_Bootstrap_BootstrapTest extends PHPUnit_Framework_TestCase
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
        $this->bootstrap   = new Zend_Application_Bootstrap_Bootstrap(
            $this->application
        );

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

        // Reset autoloader instance so it doesn't affect other tests
        Zend_Loader_Autoloader::resetInstance();
    }

    public function resetFrontController()
    {
        $front = Zend_Controller_Front::getInstance();
        $front->resetInstance();
        $front->setRequest(new Zend_Controller_Request_HttpTestCase)
              ->setResponse(new Zend_Controller_Response_HttpTestCase);
    }

    public function testFrontControllerResourcePluginShouldBeRegisteredByDefault()
    {
        $this->assertTrue($this->bootstrap->hasPluginResource('FrontController'));
    }

    /**
     * @expectedException Zend_Application_Bootstrap_Exception
     */
    public function testRunShouldRaiseExceptionIfNoControllerDirectoryRegisteredWithFrontController()
    {
        $this->bootstrap->bootstrap();
        $this->bootstrap->run();
    }

    public function testRunShouldDispatchFrontController()
    {
        $this->bootstrap->setOptions(array(
            'resources' => array(
                'frontcontroller' => array(
                    'moduleDirectory' => dirname(__FILE__) . '/../_files/modules',
                ),
            ),
        ));
        $this->bootstrap->bootstrap();

        $front   = $this->bootstrap->getResource('FrontController');

        $request = $front->getRequest();
        $request->setRequestUri('/zfappbootstrap');
        $this->bootstrap->run();

        $this->assertTrue($this->bootstrap->getContainer()->zfappbootstrap);
    }

    /**
     * @group ZF-8496
     */
    public function testBootstrapShouldInitializeModuleAutoloader()
    {
        $this->assertTrue($this->bootstrap->getResourceLoader() instanceof Zend_Application_Module_Autoloader);
    }

    /**
     * @group ZF-8496
     */
    public function testBootstrapAutoloaderShouldHaveApplicationNamespaceByDefault()
    {
        $al = $this->bootstrap->getResourceLoader();
        $this->assertEquals('Application', $al->getNamespace());
    }

    /**
     * @group ZF-8496
     */
    public function testBootstrapAutoloaderNamespaceShouldBeConfigurable()
    {
        $application = new Zend_Application('testing', array(
            'defaultappnamespace' => 'Default',
        ));
        $bootstrap   = new Zend_Application_Bootstrap_Bootstrap(
            $application
        );
        $al = $bootstrap->getResourceLoader();
        $this->assertEquals('Default', $al->getNamespace());
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Application_Bootstrap_BootstrapTest::main') {
    Zend_Application_Bootstrap_BootstrapTest::main();
}
