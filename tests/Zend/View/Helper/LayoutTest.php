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
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\View\Helper;
use Zend\Controller\Action\HelperBroker;
use Zend\View\Helper;
use Zend\Layout;


/**
 * Test class for Zend_View_Helper_Layout
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class LayoutTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        \Zend\Controller\Front::getInstance()->resetInstance();
        if (HelperBroker::hasHelper('Layout')) {
            HelperBroker::removeHelper('Layout');
        }
        if (HelperBroker::hasHelper('viewRenderer')) {
            HelperBroker::removeHelper('viewRenderer');
        }

        \Zend\Layout\Layout::resetMvcInstance();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
    }

    public function testGetLayoutCreatesLayoutObjectWhenNoPluginRegistered()
    {
        $helper = new Helper\Layout();
        $layout = $helper->getLayout();
        $this->assertTrue($layout instanceof Layout\Layout);
    }

    public function testGetLayoutPullsLayoutObjectFromRegisteredPlugin()
    {
        $layout = Layout\Layout::startMvc();
        $helper = new Helper\Layout();
        $this->assertSame($layout, $helper->getLayout());
    }

    public function testSetLayoutReplacesExistingLayoutObject()
    {
        $layout = Layout\Layout::startMvc();
        $helper = new Helper\Layout();
        $this->assertSame($layout, $helper->getLayout());

        $newLayout = new Layout\Layout();
        $this->assertNotSame($layout, $newLayout);

        $helper->setLayout($newLayout);
        $this->assertSame($newLayout, $helper->getLayout());
    }

    public function testHelperMethodFetchesLayoutObject()
    {
        $layout = Layout\Layout::startMvc();
        $helper = new Helper\Layout();

        $received = $helper->direct();
        $this->assertSame($layout, $received);
    }
}
