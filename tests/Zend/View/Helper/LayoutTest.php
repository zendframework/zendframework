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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\View\Helper;

use Zend\View\Helper\Layout;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;

/**
 * Test class for Zend_View_Helper_Layout
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
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
        $this->renderer = $renderer = new PhpRenderer();
        $this->viewModelHelper = $renderer->plugin('view_model');
        $this->helper          = $renderer->plugin('layout');

        $this->parent = new ViewModel();
        $this->parent->setTemplate('layout');
        $this->viewModelHelper->setRoot($this->parent);
    }

    public function testCallingSetTemplateAltersRootModelTemplate()
    {
        $this->helper->setTemplate('alternate/layout');
        $this->assertEquals('alternate/layout', $this->parent->getTemplate());
    }

    public function testCallingGetLayoutReturnsRootModelTemplate()
    {
        $this->assertEquals('layout', $this->helper->getLayout());
    }

    public function testCallingInvokeProxiesToSetTemplate()
    {
        $helper = $this->helper;
        $helper('alternate/layout');
        $this->assertEquals('alternate/layout', $this->parent->getTemplate());
    }

    public function testCallingInvokeWithNoArgumentReturnsViewModel()
    {
        $helper = $this->helper;
        $result = $helper();
        $this->assertSame($this->parent, $result);
    }

    public function testRaisesExceptionIfViewModelHelperHasNoRoot()
    {
        $renderer         = new PhpRenderer();
        $viewModelHelper = $renderer->plugin('view_model');
        $helper          = $renderer->plugin('layout');

        $this->setExpectedException('Zend\View\Exception\RuntimeException', 'view model');
        $helper->setTemplate('foo/bar');
    }
}
