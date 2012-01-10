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

use Zend\Loader\Autoloader,
    Zend\Application\Resource\FrontController as FrontControllerResource,
    Zend\Application\Resource\Modules as ModulesResource,
    Zend\Application\Application,
    Zend\Controller\Front as FrontController,
    ZendTest\Application\TestAsset\ZfAppBootstrap;


/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application
 */
class ModulesTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->application = new Application('testing');

        $this->bootstrap = new ZfAppBootstrap($this->application);

        $this->front = FrontController::getInstance();
        $this->front->resetInstance();
    }

    public function tearDown()
    {
    }

    public function testInitializationTriggersNothingIfNoModulesRegistered()
    {

        $resource =  new FrontControllerResource();
        $resource->setBootstrap($this->bootstrap);
        $resource->init();

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

        $resource =  new FrontControllerResource(array(
            'moduleDirectory' => __DIR__ . '/../TestAsset/modules',
        ));
        $resource->setBootstrap($this->bootstrap);
        $resource->init();

        $resource = new ModulesResource(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->init();
        $this->assertTrue(isset($this->bootstrap->default));
    }

    public function testInitializationShouldTriggerModuleBootstrapsWhenTheyExist()
    {

        $resource =  new FrontControllerResource(array(
            'moduleDirectory' => __DIR__ . '/../TestAsset/modules',
        ));
        $resource->setBootstrap($this->bootstrap);
        $resource->init();

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

        $resource =  new FrontControllerResource(array(
            'moduleDirectory' => __DIR__ . '/../TestAsset/modules',
        ));
        $resource->setBootstrap($this->bootstrap);
        $resource->init();

        $resource = new ModulesResource(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->init();

        $bootstraps = $resource->getExecutedBootstraps();
        $this->assertEquals(6, count((array)$bootstraps));
        $this->assertArrayHasKey('bar',     (array)$bootstraps);
        $this->assertArrayHasKey('foo-bar', (array)$bootstraps);
        $this->assertArrayHasKey('foo',     (array)$bootstraps);
        $this->assertArrayHasKey('application', (array)$bootstraps);
        $this->assertArrayHasKey('zf2-30-module1', (array)$bootstraps);
        $this->assertArrayHasKey('zf2-30-module2', (array)$bootstraps);
    }

    /**
     * @group ZF-6803
     * @group ZF-7158
     */
    public function testShouldReturnExecutedBootstrapsWhenComplete()
    {

        $resource =  new FrontControllerResource(array(
            'moduleDirectory' => __DIR__ . '/../TestAsset/modules',
        ));
        $resource->setBootstrap($this->bootstrap);
        $resource->init();

        $resource = new ModulesResource(array());
        $resource->setBootstrap($this->bootstrap);
        $bootstraps = $resource->init();
        $this->assertEquals(6, count((array)$bootstraps));
        $this->assertArrayHasKey('bar',     (array)$bootstraps);
        $this->assertArrayHasKey('foo-bar', (array)$bootstraps);
        $this->assertArrayHasKey('foo',     (array)$bootstraps);
        $this->assertArrayHasKey('application', (array)$bootstraps);
        $this->assertArrayHasKey('zf2-30-module1', (array)$bootstraps);
        $this->assertArrayHasKey('zf2-30-module2', (array)$bootstraps);
    }
}
