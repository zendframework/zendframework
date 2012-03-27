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
 * @package    Zend_Layout
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Layout;

use Zend\Layout,
    Zend\Controller\Front as FrontController,
    Zend\Layout\Controller\Action\Helper;


/**
 * Test class for Zend_Layout_Controller_Action_Helper_Layout
 *
 * @category   Zend
 * @package    Zend_Layout
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Layout
 */
class HelperTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        \Zend\Layout\Layout::resetMvcInstance();
        $front = FrontController::getInstance();
        $front->resetInstance();

        $broker = $front->getHelperBroker();
        if ($broker->hasPlugin('Layout')) {
            $broker->unregister('Layout');
        }
        if ($broker->hasPlugin('viewRenderer')) {
            $broker->unregister('viewRenderer');
        }
    }

    public function tearDown()
    {
    }

    public function testConstructorWithLayoutObject()
    {
        $layout = new Layout\Layout();
        $helper = new Helper\Layout($layout);
        $this->assertSame($layout, $helper->getLayoutInstance());
    }

    public function testGetLayoutCreatesLayoutObjectWhenNoPluginRegistered()
    {
        $helper = new Helper\Layout();
        $layout = $helper->getLayoutInstance();
        $this->assertTrue($layout instanceof Layout\Layout);
    }

    public function testGetLayoutInstancePullsMvcLayoutInstance()
    {
        $layout = Layout\Layout::startMvc();
        $helper = new Helper\Layout();
        $this->assertSame($layout, $helper->getLayoutInstance());
    }

    public function testSetLayoutInstanceReplacesExistingLayoutObject()
    {
        $layout = Layout\Layout::startMvc();
        $helper = new Helper\Layout();
        $this->assertSame($layout, $helper->getLayoutInstance());

        $newLayout = new Layout\Layout();
        $this->assertNotSame($layout, $newLayout);

        $helper->setLayoutInstance($newLayout);
        $this->assertSame($newLayout, $helper->getLayoutInstance());
    }

    public function testDirectFetchesLayoutObject()
    {
        $layout = Layout\Layout::startMvc();
        $helper = new Helper\Layout();

        $received = $helper->direct();
        $this->assertSame($layout, $received);
    }

    public function testHelperProxiesToLayoutObjectMethods()
    {
        $layout = Layout\Layout::startMvc();
        $helper = new Helper\Layout();

        $helper->setOptions(array(
            'layout'     => 'foo.phtml',
            'layoutPath' => __DIR__ . '/_files/layouts',
            'contentKey' => 'foo'
        ));
        $this->assertEquals('foo.phtml', $helper->getLayout());
        $this->assertEquals(__DIR__ . '/_files/layouts', $helper->getLayoutPath());
        $this->assertEquals('foo', $helper->getContentKey());
    }
}
