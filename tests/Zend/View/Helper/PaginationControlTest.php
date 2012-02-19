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

/**
 * @namespace
 */
namespace ZendTest\View\Helper;

use Zend\Paginator,
    Zend\View\Helper,
    Zend\View\Renderer\PhpRenderer as View,
    Zend\View\Resolver;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class PaginationControlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_View_Helper_PaginationControl
     */
    private $_viewHelper;

    private $_paginator;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $resolver = new Resolver\TemplatePathStack(array('script_paths' => array(
            __DIR__ . '/_files/scripts',
        )));
        $view = new View();
        $view->setResolver($resolver);

        Helper\PaginationControl::setDefaultViewPartial(null);
        $this->_viewHelper = new Helper\PaginationControl();
        $this->_viewHelper->setView($view);
        $this->_paginator = Paginator\Paginator::factory(range(1, 101));
    }

    public function tearDown()
    {
        unset($this->_viewHelper);
        unset($this->_paginator);
    }

    public function testGetsAndSetsView()
    {
        $view   = new View();
        $helper = new Helper\PaginationControl();
        $this->assertNull($helper->getView());
        $helper->setView($view);
        $this->assertInstanceOf('Zend\View\Renderer', $helper->getView());
    }

    public function testGetsAndSetsDefaultViewPartial()
    {
        $this->assertNull(Helper\PaginationControl::getDefaultViewPartial());
        Helper\PaginationControl::setDefaultViewPartial('partial');
        $this->assertEquals('partial', Helper\PaginationControl::getDefaultViewPartial());
        Helper\PaginationControl::setDefaultViewPartial(null);
    }

    public function testUsesDefaultViewPartialIfNoneSupplied()
    {
        Helper\PaginationControl::setDefaultViewPartial('testPagination.phtml');
        $output = $this->_viewHelper->__invoke($this->_paginator);
        $this->assertContains('pagination control', $output, $output);
        Helper\PaginationControl::setDefaultViewPartial(null);
    }

    public function testThrowsExceptionIfNoViewPartialFound()
    {
        try {
            $this->_viewHelper->__invoke($this->_paginator);
        } catch (\Exception $e) {
            $this->assertInstanceOf('Zend\View\Exception', $e);
            $this->assertEquals('No view partial provided and no default set', $e->getMessage());
        }
    }

    /**
     * @group ZF-4037
     */
    public function testUsesDefaultScrollingStyleIfNoneSupplied()
    {
        // First we'll make sure the base case works
        $output = $this->_viewHelper->__invoke($this->_paginator, 'All', 'testPagination.phtml');
        $this->assertContains('page count (11) equals pages in range (11)', $output, $output);

        Paginator\Paginator::setDefaultScrollingStyle('All');
        $output = $this->_viewHelper->__invoke($this->_paginator, null, 'testPagination.phtml');
        $this->assertContains('page count (11) equals pages in range (11)', $output, $output);

        Helper\PaginationControl::setDefaultViewPartial('testPagination.phtml');
        $output = $this->_viewHelper->__invoke($this->_paginator);
        $this->assertContains('page count (11) equals pages in range (11)', $output, $output);
    }

    /**
     * @group ZF-4153
     */
    public function testUsesPaginatorFromViewIfNoneSupplied()
    {
        $this->_viewHelper->getView()->paginator = $this->_paginator;
        Helper\PaginationControl::setDefaultViewPartial('testPagination.phtml');

        try {
            $output = $this->_viewHelper->__invoke();
        } catch (View\Exception $e) {
            $this->fail('Could not find paginator in the view instance');
        }

        $this->assertContains('pagination control', $output, $output);
    }

    /**
     * @group ZF-4153
     */
    public function testThrowsExceptionIfNoPaginatorFound()
    {
        Helper\PaginationControl::setDefaultViewPartial('testPagination.phtml');

        try {
            $output = $this->_viewHelper->__invoke();
        } catch (\Exception $e) {
            $this->assertInstanceOf('Zend\View\Exception', $e);
            $this->assertEquals('No paginator instance provided or incorrect type', $e->getMessage());
        }
    }

    /**
     * @group ZF-4233
     */
    public function testAcceptsViewPartialInOtherModule()
    {
        try {
            $this->_viewHelper->__invoke($this->_paginator, null, array('partial.phtml', 'test'));
        } catch (\Exception $e) {
            /* We don't care whether or not the module exists--we just want to
             * make sure it gets to Zend_View_Helper_Partial and it's recognized
             * as a module. */
            $this->assertInstanceOf('Zend\View\Exception\RuntimeException', $e);
            $this->assertContains('could not resolve', $e->getMessage());
        }
    }

    /**
     * @group ZF-4328
     */
    public function testUsesPaginatorFromViewOnlyIfNoneSupplied()
    {
        $this->_viewHelper->getView()->vars()->paginator  = $this->_paginator;
        $paginator = Paginator\Paginator::factory(range(1, 30));
        Helper\PaginationControl::setDefaultViewPartial('testPagination.phtml');

        $output = $this->_viewHelper->__invoke($paginator);
        $this->assertContains('page count (3)', $output, $output);
    }

    /**
     * @group ZF-4878
     */
    public function testCanUseObjectForScrollingStyle()
    {
        $all = new Paginator\ScrollingStyle\All();

        try {
            $output = $this->_viewHelper->__invoke($this->_paginator, $all, 'testPagination.phtml');
        } catch (\Exception $e) {
            $this->fail('Could not use object for sliding style');
        }

        $this->assertContains('page count (11) equals pages in range (11)', $output, $output);
    }
}

