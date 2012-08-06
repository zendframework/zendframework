<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Paginator
 */

namespace ZendTest\Paginator\ScrollingStyle;

/**
 * @category   Zend
 * @package    Zend_Paginator
 * @subpackage UnitTests
 * @group      Zend_Paginator
 */
class AllTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\Paginator\ScrollingStyle\All
     */
    private $_scrollingStyle = null;

    /**
     * @var \Zend\Paginator\Paginator
     */
    private $_paginator = null;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_scrollingStyle = new \Zend\Paginator\ScrollingStyle\All();
        $this->_paginator = \Zend\Paginator\Paginator::factory(range(1, 101));
        $this->_paginator->setItemCountPerPage(10);
    }
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        $this->_scrollingStyle = null;
        $this->_paginator = null;
        parent::tearDown();
    }

    public function testGetsPages()
    {
        $expected = array_combine(range(1, 11), range(1, 11));
        $pages = $this->_scrollingStyle->getPages($this->_paginator);
        $this->assertEquals($expected, $pages);
    }

    public function testGetsNextAndPreviousPageForFirstPage()
    {
        $this->_paginator->setCurrentPageNumber(1);
        $pages = $this->_paginator->getPages('All');

        $this->assertEquals(2, $pages->next);
    }

    public function testGetsNextAndPreviousPageForSecondPage()
    {
        $this->_paginator->setCurrentPageNumber(2);
        $pages = $this->_paginator->getPages('All');
        $this->assertEquals(1, $pages->previous);
        $this->assertEquals(3, $pages->next);
    }

    public function testGetsNextAndPreviousPageForMiddlePage()
    {
        $this->_paginator->setCurrentPageNumber(6);
        $pages = $this->_paginator->getPages('All');
        $this->assertEquals(5, $pages->previous);
        $this->assertEquals(7, $pages->next);
    }

    public function testGetsNextAndPreviousPageForSecondLastPage()
    {
        $this->_paginator->setCurrentPageNumber(10);
        $pages = $this->_paginator->getPages('All');
        $this->assertEquals(9, $pages->previous);
        $this->assertEquals(11, $pages->next);
    }

    public function testGetsNextAndPreviousPageForLastPage()
    {
        $this->_paginator->setCurrentPageNumber(11);
        $pages = $this->_paginator->getPages('All');
        $this->assertEquals(10, $pages->previous);
    }
}
