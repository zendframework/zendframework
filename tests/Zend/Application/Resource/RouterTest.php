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
 * @version    $Id: $
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Application_Resource_RouterTest::main');
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
 */
class Zend_Application_Resource_RouterTest extends PHPUnit_Framework_TestCase
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

    public function testInitializationInitializesRouterObject()
    {
        $resource = new Zend_Application_Resource_Router(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->init();
        $this->assertTrue($resource->getRouter() instanceof Zend_Controller_Router_Rewrite);
    }

    public function testInitializationReturnsRouterObject()
    {
        $resource = new Zend_Application_Resource_Router(array());
        $resource->setBootstrap($this->bootstrap);
        $test = $resource->init();
        $this->assertTrue($test instanceof Zend_Controller_Router_Rewrite);
    }
    
    public function testChainNameSeparatorIsParsedOnToRouter()
    {
        $resource = new Zend_Application_Resource_Router(array('chainNameSeparator' => '_unitTestSep_'));
        $resource->setBootstrap($this->bootstrap);
        $router = $resource->init();
        $this->assertEquals('_unitTestSep_', $router->getChainNameSeparator());
    }

    public function testOptionsPassedToResourceAreUsedToCreateRoutes()
    {
        $options = array('routes' => array(
            'archive' => array(
                'route'    => 'archive/:year/*',
                'defaults' => array(
                    'controller' => 'archive',
                    'action'     => 'show',
                    'year'       => 2000,
                ),
                'reqs'     => array(
                    'year' => '\d+',
                ),
            ),
        ));

        $resource = new Zend_Application_Resource_Router($options);
        $resource->setBootstrap($this->bootstrap);
        $resource->init();
        $router   = $resource->getRouter();
        $this->assertTrue($router->hasRoute('archive'));
        $route = $router->getRoute('archive');
        $this->assertTrue($route instanceof Zend_Controller_Router_Route);
        $this->assertEquals($options['routes']['archive']['defaults'], $route->getDefaults());
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Application_Resource_RouterTest::main') {
    Zend_Application_Resource_RouterTest::main();
}
