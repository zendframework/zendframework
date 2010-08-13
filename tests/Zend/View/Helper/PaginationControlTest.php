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
use Zend\View;
use Zend\View\Helper;
use Zend\Paginator;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
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
        $this->markTestSkipped('Skipped until Zend\Paginator is completed');
        
        $view = new View\View();
        $view->addBasePath(__DIR__ . '/_files');

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
        $view   = new View\View();
        $helper = new Helper\PaginationControl();
        $this->assertNull($helper->view);
        $helper->setView($view);
        $this->assertType('Zend_View_Interface', $helper->view);
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
        $output = $this->_viewHelper->paginationControl($this->_paginator);
        $this->assertContains('pagination control', $output, $output);
        Helper\PaginationControl::setDefaultViewPartial(null);
    }

    public function testThrowsExceptionIfNoViewPartialFound()
    {
        try {
            $this->_viewHelper->paginationControl($this->_paginator);
        } catch (\Exception $e) {
            $this->assertType('Zend_View_Exception', $e);
            $this->assertEquals('No view partial provided and no default set', $e->getMessage());
        }
    }

    /**
     * @group ZF-4037
     */
    public function testUsesDefaultScrollingStyleIfNoneSupplied()
    {
        // First we'll make sure the base case works
        $output = $this->_viewHelper->paginationControl($this->_paginator, 'All', 'testPagination.phtml');
        $this->assertContains('page count (11) equals pages in range (11)', $output, $output);

        Paginator\Paginator::setDefaultScrollingStyle('All');
        $output = $this->_viewHelper->paginationControl($this->_paginator, null, 'testPagination.phtml');
        $this->assertContains('page count (11) equals pages in range (11)', $output, $output);

        Helper\PaginationControl::setDefaultViewPartial('testPagination.phtml');
        $output = $this->_viewHelper->paginationControl($this->_paginator);
        $this->assertContains('page count (11) equals pages in range (11)', $output, $output);
    }

    /**
     * @group ZF-4153
     */
    public function testUsesPaginatorFromViewIfNoneSupplied()
    {
        $this->_viewHelper->view->paginator = $this->_paginator;
        Helper\PaginationControl::setDefaultViewPartial('testPagination.phtml');

        try {
            $output = $this->_viewHelper->paginationControl();
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
            $output = $this->_viewHelper->paginationControl();
        } catch (\Exception $e) {
            $this->assertType('Zend_View_Exception', $e);
            $this->assertEquals('No paginator instance provided or incorrect type', $e->getMessage());
        }
    }

    /**
     * @group ZF-4233
     */
    public function testAcceptsViewPartialInOtherModule()
    {
        try {
            $this->_viewHelper->paginationControl($this->_paginator, null, array('partial.phtml', 'test'));
        } catch (\Exception $e) {
            /* We don't care whether or not the module exists--we just want to
             * make sure it gets to Zend_View_Helper_Partial and it's recognized
             * as a module. */
            $this->assertType('Zend_View_Helper_Partial_Exception', $e);
            $this->assertEquals('Cannot render partial; module does not exist', $e->getMessage());
        }
    }

    /**
     * @group ZF-4328
     */
    public function testUsesPaginatorFromViewOnlyIfNoneSupplied()
    {
        $this->_viewHelper->view->paginator  = $this->_paginator;
        $paginator = Paginator\Paginator::factory(range(1, 30));
        Helper\PaginationControl::setDefaultViewPartial('testPagination.phtml');

        $output = $this->_viewHelper->paginationControl($paginator);
        $this->assertContains('page count (3)', $output, $output);
    }

    /**
     * @group ZF-4878
     */
    public function testCanUseObjectForScrollingStyle()
    {
        $all = new \Zend\Paginator\ScrollingStyle\All();

        try {
            $output = $this->_viewHelper->paginationControl($this->_paginator, $all, 'testPagination.phtml');
        } catch (\Exception $e) {
            $this->fail('Could not use object for sliding style');
        }

        $this->assertContains('page count (11) equals pages in range (11)', $output, $output);
    }
}

// Call Zend_View_Helper_PaginationControlTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_PaginationControlTest::main") {
    \Zend_View_Helper_PaginationControlTest::main();
}
