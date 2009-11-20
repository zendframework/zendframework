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
 * @version    $Id: LayoutTest.php 17667 2009-08-18 21:40:09Z mikaelkael $
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Application_Resource_DojoTest::main');
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
class Zend_Application_Resource_DojoTest extends PHPUnit_Framework_TestCase
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

        $this->bootstrap = new Zend_Application_Bootstrap_Bootstrap($this->application);
        $this->bootstrap->registerPluginResource('view');

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

    public function testInitializationInitializesDojoContainer()
    {
        $resource = new Zend_Application_Resource_Dojo(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->init();
        $this->assertTrue($resource->getDojo() instanceof Zend_Dojo_View_Helper_Dojo_Container);
    }

    public function testInitializationReturnsDojoContainer()
    {
        $resource = new Zend_Application_Resource_Dojo(array());
        $resource->setBootstrap($this->bootstrap);
        $test = $resource->init();
        $this->assertTrue($test instanceof Zend_Dojo_View_Helper_Dojo_Container);
    }

    public function testOptionsPassedToResourceAreUsedToSetDojosContainerState()
    {
        $options = array(
            'requireModules'     => array('DojoTest'),
            'localPath'          => '/ofc/ZF/Rules/',
        );

        $resource = new Zend_Application_Resource_Dojo($options);
        $resource->setBootstrap($this->bootstrap);
        $resource->init();
        $resource->getBootstrap()->bootstrap('view');
        $dojo = $resource->getBootstrap()->view->dojo();

        $test = array(
            'requireModules' => $dojo->getModules(),
            'localPath'      => $dojo->getLocalPath()
        );
        $this->assertEquals($options, $test);
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Application_Resource_DojoTest::main') {
    Zend_Application_Resource_DojoTest::main();
}
