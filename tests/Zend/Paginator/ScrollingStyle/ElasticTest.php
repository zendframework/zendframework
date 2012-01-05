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
 * @package    Zend_Paginator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Paginator\ScrollingStyle;

/**
 * Test helper
 */

/**
 * @see Zend_Paginator_ScrollingStyle_Elastic
 */

/**
 * @see PHPUnit_Framework_TestCase
 */

/**
 * @see Zend_Paginator
 */

/**
 * @category   Zend
 * @package    Zend_Paginator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Paginator
 */
class ElasticTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Paginator_ScrollingStyle_Elastic
     */
    private $_scrollingStyle;
    /**
     * @var Zend_Paginator
     */
    private $_paginator;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_scrollingStyle = new \Zend\Paginator\ScrollingStyle\Elastic();
        $this->_paginator = \Zend\Paginator\Paginator::factory(range(1, 101));
        $this->_paginator->setItemCountPerPage(5);
        $this->_paginator->setPageRange(5);
    }
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->_scrollingStyle = null;
        $this->_paginator = null;
        parent::tearDown();
    }

    public function testGetsPagesInRangeForFirstPage()
    {
        $this->_paginator->setCurrentPageNumber(1);
        $actual = $this->_scrollingStyle->getPages($this->_paginator);
        $expected = array_combine(range(1, 5), range(1, 5));
        $this->assertEquals($expected, $actual);
    }

    public function testGetsPagesInRangeForSecondPage()
    {
        $this->_paginator->setCurrentPageNumber(2);
        $actual = $this->_scrollingStyle->getPages($this->_paginator);
        $expected = array_combine(range(1, 6), range(1, 6));
        $this->assertEquals($expected, $actual);
    }

    public function testGetsPagesInRangeForTenthPage()
    {
        $this->_paginator->setCurrentPageNumber(10);
        $actual = $this->_scrollingStyle->getPages($this->_paginator);
        $expected = array_combine(range(6, 14), range(6, 14));
        $this->assertEquals($expected, $actual);
    }

    public function testGetsPagesInRangeForLastPage()
    {
        $this->_paginator->setCurrentPageNumber(21);
        $actual = $this->_scrollingStyle->getPages($this->_paginator);
        $expected = array_combine(range(17, 21), range(17, 21));
        $this->assertEquals($expected, $actual);
    }

    public function testGetsNextAndPreviousPageForFirstPage()
    {
        $this->_paginator->setCurrentPageNumber(1);
        $pages = $this->_paginator->getPages('Elastic');

        $this->assertEquals(2, $pages->next);
    }

    public function testGetsNextAndPreviousPageForSecondPage()
    {
        $this->_paginator->setCurrentPageNumber(2);
        $pages = $this->_paginator->getPages('Elastic');
        $this->assertEquals(1, $pages->previous);
        $this->assertEquals(3, $pages->next);
    }

    public function testGetsNextAndPreviousPageForMiddlePage()
    {
        $this->_paginator->setCurrentPageNumber(10);
        $pages = $this->_paginator->getPages('Elastic');
        $this->assertEquals(9, $pages->previous);
        $this->assertEquals(11, $pages->next);
    }

    public function testGetsNextAndPreviousPageForSecondLastPage()
    {
        $this->_paginator->setCurrentPageNumber(20);
        $pages = $this->_paginator->getPages('Elastic');
        $this->assertEquals(19, $pages->previous);
        $this->assertEquals(21, $pages->next);
    }

    public function testGetsNextAndPreviousPageForLastPage()
    {
        $this->_paginator->setCurrentPageNumber(21);
        $pages = $this->_paginator->getPages('Elastic');
        $this->assertEquals(20, $pages->previous);
    }

    public function testNoPagesOnLastPageEqualsPageRange()
    {
        $this->_paginator->setPageRange(3);
        $this->_paginator->setCurrentPageNumber(21);
        $pages = $this->_paginator->getPages('Elastic');
        $this->assertEquals(3, count($pages->pagesInRange));
    }

    public function testNoPagesOnSecondLastPageEqualsPageRangeMinOne()
    {
        $this->_paginator->setPageRange(3);
        $this->_paginator->setCurrentPageNumber(20);
        $pages = $this->_paginator->getPages('Elastic');
        $this->assertEquals(4, count($pages->pagesInRange));
    }

    public function testNoPagesBeforeSecondLastPageEqualsPageRangeMinTwo()
    {
        $this->_paginator->setPageRange(3);
        $this->_paginator->setCurrentPageNumber(19);
        $pages = $this->_paginator->getPages('Elastic');
        $this->assertEquals(5, count($pages->pagesInRange));
    }
}
