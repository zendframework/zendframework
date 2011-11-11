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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\View\Helper;

use Zend\View\Helper,
    Zend\Layout\Layout;

/**
 * Test class for Zend_View_Helper_Layout
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
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
    }

    public function testGetLayoutCreatesLayoutObjectWhenNoPluginRegistered()
    {
        $helper = new Helper\Layout();
        $layout = $helper->getLayout();
        $this->assertTrue($layout instanceof Layout);
    }

    public function testSetLayoutReplacesExistingLayoutObject()
    {
        $layout = new Layout;
        $helper = new Helper\Layout();
        $this->assertNotSame($layout, $helper->getLayout());

        $helper->setLayout($layout);
        $this->assertSame($layout, $helper->getLayout());
    }

    public function testHelperMethodFetchesLayoutObject()
    {
        $helper = new Helper\Layout();

        $received = $helper->__invoke();
        $this->assertTrue($received instanceof Layout);
    }
}
