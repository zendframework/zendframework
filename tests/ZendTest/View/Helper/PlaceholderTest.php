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
use Zend\View\Helper;
use Zend\View\Helper\Placeholder\Registry;


/**
 * Test class for Zend_View_Helper_Placeholder.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class PlaceholderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_View_Helper_Placeholder
     */
    public $placeholder;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->placeholder = new Helper\Placeholder();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->placeholder);
        \Zend\Registry::getInstance()->offsetUnset(Registry::REGISTRY_KEY);
    }

    /**
     * @return void
     */
    public function testConstructorCreatesRegistryOffset()
    {
        $this->assertTrue(\Zend\Registry::isRegistered(Registry::REGISTRY_KEY));
    }

    public function testMultiplePlaceholdersUseSameRegistry()
    {
        $this->assertTrue(\Zend\Registry::isRegistered(Registry::REGISTRY_KEY));
        $registry = \Zend\Registry::get(Registry::REGISTRY_KEY);
        $this->assertSame($registry, $this->placeholder->getRegistry());

        $placeholder = new Helper\Placeholder();

        $this->assertSame($registry, $placeholder->getRegistry());
        $this->assertSame($this->placeholder->getRegistry(), $placeholder->getRegistry());
    }

    /**
     * @return void
     */
    public function testSetView()
    {
        $view = new \Zend\View\View();
        $this->placeholder->setView($view);
        $this->assertSame($view, $this->placeholder->view);
    }

    /**
     * @return void
     */
    public function testPlaceholderRetrievesContainer()
    {
        $container = $this->placeholder->direct('foo');
        $this->assertTrue($container instanceof \Zend\View\Helper\Placeholder\Container\AbstractContainer);
    }

    /**
     * @return void
     */
    public function testPlaceholderRetrievesSameContainerOnSubsequentCalls()
    {
        $container1 = $this->placeholder->direct('foo');
        $container2 = $this->placeholder->direct('foo');
        $this->assertSame($container1, $container2);
    }
}
