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

use Zend\Loader\Autoloader,
    Zend\Application\Resource\Modules as ModulesResource,
    Zend\Application\Application,
    Zend\Controller\Front as FrontController,
    ZendTest\Application\TestAsset\ZfAppBootstrap;

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application
 */
class ModulesTest extends \PHPUnit_Framework_TestCase
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

        $this->front = FrontController::getInstance();
        $this->front->resetInstance();
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
        Autoloader::resetInstance();
    }

    public function testInitializationTriggersNothingIfNoModulesRegistered()
    {

        $this->bootstrap->registerPluginResource('Frontcontroller', array());
        $resource = new ModulesResource(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->init();
        $this->assertFalse(isset($this->bootstrap->default));
        $this->assertFalse(isset($this->bootstrap->foo));
        $this->assertFalse(isset($this->bootstrap->bar));
    }

    /**
     * @group ZF-6803
     * @group ZF-7158
     */
    public function testInitializationTriggersDefaultModuleBootstrapWhenDiffersFromApplicationBootstrap()
    {
        $this->bootstrap->registerPluginResource('Frontcontroller', array(
            'moduleDirectory' => __DIR__ . '/../TestAsset/modules',
        ));
        $resource = new ModulesResource(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->init();
        $this->assertTrue(isset($this->bootstrap->default));
    }

    public function testInitializationShouldTriggerModuleBootstrapsWhenTheyExist()
    {

        $this->bootstrap->registerPluginResource('Frontcontroller', array(
            'moduleDirectory' => __DIR__ . '/../TestAsset/modules',
        ));
        $resource = new ModulesResource(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->init();
        $this->assertTrue($this->bootstrap->foo, 'foo failed');
        $this->assertTrue($this->bootstrap->bar, 'bar failed');
    }

    /**
     * @group ZF-6803
     * @group ZF-7158
     */
    public function testInitializationShouldSkipModulesWithoutBootstraps()
    {

        $this->bootstrap->registerPluginResource('Frontcontroller', array(
            'moduleDirectory' => __DIR__ . '/../TestAsset/modules',
        ));
        $resource = new ModulesResource(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->init();
        $bootstraps = $resource->getExecutedBootstraps();
        $this->assertEquals(4, count((array)$bootstraps));
        $this->assertArrayHasKey('bar',     (array)$bootstraps);
        $this->assertArrayHasKey('foo-bar', (array)$bootstraps);
        $this->assertArrayHasKey('foo',     (array)$bootstraps);
        $this->assertArrayHasKey('application', (array)$bootstraps);
    }

    /**
     * @group ZF-6803
     * @group ZF-7158
     */
    public function testShouldReturnExecutedBootstrapsWhenComplete()
    {

        $this->bootstrap->registerPluginResource('Frontcontroller', array(
            'moduleDirectory' => __DIR__ . '/../TestAsset/modules',
        ));
        $resource = new ModulesResource(array());
        $resource->setBootstrap($this->bootstrap);
        $bootstraps = $resource->init();
        $this->assertEquals(4, count((array)$bootstraps));
        $this->assertArrayHasKey('bar',     (array)$bootstraps);
        $this->assertArrayHasKey('foo-bar', (array)$bootstraps);
        $this->assertArrayHasKey('foo',     (array)$bootstraps);
        $this->assertArrayHasKey('application', (array)$bootstraps);
    }
}
