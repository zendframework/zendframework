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
 */

namespace ZendTest\Application\Resource;

use Zend\Loader\Autoloader,
    ZendTest\Application\TestAsset\ZfAppBootstrap,
    Zend\Application\Application,
    Zend\Application\Resource\View as ViewResource,
    Zend\Controller\Front as FrontController,
    Zend\View\Renderer;

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application
 */
class ViewTest extends \PHPUnit_Framework_TestCase
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

        $this->application = new Application('testing', array(
            'resources' => array('frontcontroller' => array()),
        ));
        $this->bootstrap = new ZfAppBootstrap($this->application);

        $this->front = FrontController::getInstance();
        $this->front->resetInstance();
        $this->broker = $this->front->getHelperBroker();
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

    public function testInitializationInitializesViewObject()
    {
        $resource = new ViewResource(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->init();
        $this->assertTrue($resource->getView() instanceof Renderer);
    }

    public function testInitializationInjectsViewIntoViewRenderer()
    {
        $resource = new ViewResource(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->init();
        $view = $resource->getView();
        $viewRenderer = $this->broker->load('ViewRenderer');
        $this->assertSame($view, $viewRenderer->view);
    }

    /**
     * View API is still in flux
     * @group disable
     */
    public function testOptionsPassedToResourceAreUsedToSetViewState()
    {
        $options = array(
            'scriptPath' => __DIR__,
        );
        $resource = new ViewResource($options);
        $resource->setBootstrap($this->bootstrap);
        $resource->init();
        $view  = $resource->getView();
        $paths = $view->resolver()->getPaths();

        $test = array();
        foreach ($paths as $path) {
            $test[] = $path;
        }
        $this->assertContains(__DIR__ . '/', $test, var_export($test, 1));
    }

    public function testDoctypeIsSet()
    {
        $options = array('doctype' => 'XHTML1_FRAMESET');
        $resource = new ViewResource($options);
        $resource->setBootstrap($this->bootstrap);
        $resource->init();
        $view  = $resource->getView();
        $this->assertEquals('XHTML1_FRAMESET', $view->broker('doctype')->getDoctype());
    }
}
